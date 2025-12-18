<template>
    <div class="custom-field-manager">
        <div class="section-header">
            <h2>Field Kustom</h2>
            <button @click="showAddFieldModal = true" class="btn btn-primary">
                + Tambah Field
            </button>
        </div>

        <div v-if="loading" class="loading-state">
            <p>Memuat field...</p>
        </div>

        <div v-else-if="fields.length === 0" class="empty-state">
            <p>Belum ada field kustom. Tambahkan field untuk mengumpulkan data tambahan dari tamu.</p>
        </div>

        <div v-else class="fields-list">
            <div 
                v-for="field in fields" 
                :key="field.id_field" 
                class="field-item"
            >
                <div class="field-info">
                    <div class="field-header">
                        <h3>{{ field.field_label }}</h3>
                        <span class="field-type-badge" :class="getTypeClass(field.field_type)">
                            {{ getTypeLabel(field.field_type) }}
                        </span>
                    </div>
                    <div class="field-details">
                        <span class="field-name">{{ field.field_name }}</span>
                        <span v-if="field.is_required" class="required-badge">Wajib</span>
                    </div>
                    <div v-if="field.field_placeholder" class="field-placeholder">
                        Placeholder: {{ field.field_placeholder }}
                    </div>
                </div>
                <div class="field-actions">
                    <button @click="editField(field)" class="btn-icon" data-tooltip="Edit Field">
                        <i class="ri-edit-line"></i>
                    </button>
                    <button @click="deleteField(field.id_field)" class="btn-icon btn-danger" data-tooltip="Hapus Field">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add/Edit Field Modal -->
        <Modal 
            :show="showAddFieldModal || editingField" 
            :title="editingField ? 'Edit Field' : 'Tambah Field Baru'" 
            size="medium"
            @close="closeFieldModal"
        >
            <form @submit.prevent="saveField">
                <div class="form-group">
                    <label class="form-label">Nama Field (Slug)</label>
                    <input 
                        v-model="fieldForm.field_name" 
                        type="text" 
                        class="form-control" 
                        placeholder="contoh: provinsi, kode_pos"
                        :disabled="!!editingField"
                        required
                        pattern="[a-z0-9_]+"
                    >
                    <small class="form-text">Hanya huruf kecil, angka, dan underscore. Tidak bisa diubah setelah dibuat.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Label Field</label>
                    <input 
                        v-model="fieldForm.field_label" 
                        type="text" 
                        class="form-control" 
                        placeholder="Contoh: Provinsi"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Field</label>
                    <Select2 
                        v-model="fieldForm.field_type"
                        @change="onTypeChange"
                        :options="{ placeholder: 'Pilih Tipe Field' }"
                    >
                        <option value="input">Input Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="file">File Upload</option>
                        <option value="select">Select Dropdown</option>
                        <option value="radio">Radio Button</option>
                        <option value="checkbox">Checkbox</option>
                    </Select2>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <input 
                            type="checkbox" 
                            v-model="fieldForm.is_required"
                        >
                        Field Wajib Diisi
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">Placeholder (Opsional)</label>
                    <input 
                        v-model="fieldForm.field_placeholder" 
                        type="text" 
                        class="form-control" 
                        placeholder="Contoh: Masukkan provinsi"
                    >
                </div>

                <!-- Options for select/radio/checkbox -->
                <div v-if="needsOptions" class="form-group">
                    <label class="form-label">Options</label>
                    <div class="options-list">
                        <div 
                            v-for="(option, index) in fieldForm.field_options.options" 
                            :key="index"
                            class="option-item"
                        >
                            <input 
                                v-model="option.value" 
                                type="text" 
                                class="form-control" 
                                placeholder="Value (contoh: jawa_barat)"
                                required
                            >
                            <input 
                                v-model="option.label" 
                                type="text" 
                                class="form-control" 
                                placeholder="Label (contoh: Jawa Barat)"
                                required
                            >
                            <button 
                                type="button" 
                                @click="removeOption(index)" 
                                class="btn-icon btn-danger"
                                v-if="fieldForm.field_options.options.length > 1"
                                data-tooltip="Hapus Option"
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                        <button 
                            type="button" 
                            @click="addOption" 
                            class="btn btn-secondary btn-sm"
                        >
                            + Tambah Option
                        </button>
                    </div>
                </div>

                <div v-if="fieldError" class="alert alert-error">{{ fieldError }}</div>
            </form>

            <template #footer>
                <button type="button" @click="closeFieldModal" class="btn btn-secondary">
                    Batal
                </button>
                <button @click="saveField" class="btn btn-primary" :disabled="fieldLoading">
                    <span v-if="fieldLoading" class="loading-spinner"></span>
                    {{ fieldLoading ? 'Menyimpan...' : (editingField ? 'Update' : 'Simpan') }}
                </button>
            </template>
        </Modal>
    </div>
</template>

<script>
import axios from 'axios';
import Modal from './Modal.vue';
import Select2 from './Select2.vue';
import { showSuccess, showError, showDeleteConfirm } from '../utils/swal.js';

export default {
    name: 'CustomFieldManager',
    components: {
        Modal,
        Select2
    },
    props: {
        eventId: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            fields: [],
            loading: false,
            showAddFieldModal: false,
            editingField: null,
            fieldForm: {
                field_name: '',
                field_label: '',
                field_type: 'input',
                field_options: {
                    options: []
                },
                is_required: false,
                field_placeholder: ''
            },
            fieldError: '',
            fieldLoading: false
        };
    },
    computed: {
        needsOptions() {
            return ['select', 'radio', 'checkbox'].includes(this.fieldForm.field_type);
        }
    },
    mounted() {
        this.fetchFields();
    },
    methods: {
        async fetchFields() {
            this.loading = true;
            try {
                const token = localStorage.getItem('token');
                const response = await axios.get(`event/${this.eventId}/custom-fields`, {
                    params: { token }
                });
                
                if (response.data.success) {
                    this.fields = response.data.data || [];
                }
            } catch (error) {
                // Error fetching custom fields
            } finally {
                this.loading = false;
            }
        },
        onTypeChange() {
            this.$nextTick(() => {
                if (this.needsOptions && this.fieldForm.field_options.options.length === 0) {
                    this.addOption();
                } else if (!this.needsOptions) {
                    this.fieldForm.field_options = { options: [] };
                }
            });
        },
        addOption() {
            if (!this.fieldForm.field_options) {
                this.fieldForm.field_options = { options: [] };
            }
            this.fieldForm.field_options.options.push({
                value: '',
                label: ''
            });
        },
        removeOption(index) {
            this.fieldForm.field_options.options.splice(index, 1);
        },
        editField(field) {
            this.editingField = field;
            this.fieldForm = {
                field_name: field.field_name,
                field_label: field.field_label,
                field_type: field.field_type,
                field_options: field.field_options || { options: [] },
                is_required: field.is_required,
                field_placeholder: field.field_placeholder || ''
            };
            
            if (this.needsOptions && (!this.fieldForm.field_options.options || this.fieldForm.field_options.options.length === 0)) {
                this.addOption();
            }
        },
        closeFieldModal() {
            this.showAddFieldModal = false;
            this.editingField = null;
            this.fieldForm = {
                field_name: '',
                field_label: '',
                field_type: 'input',
                field_options: { options: [] },
                is_required: false,
                field_placeholder: ''
            };
            this.fieldError = '';
        },
        async saveField() {
            this.fieldError = '';
            
            // Validation
            if (!this.fieldForm.field_name || !this.fieldForm.field_label) {
                this.fieldError = 'Nama field dan label wajib diisi';
                return;
            }

            if (this.needsOptions) {
                if (!this.fieldForm.field_options.options || this.fieldForm.field_options.options.length === 0) {
                    this.fieldError = 'Minimal 1 option diperlukan untuk select/radio/checkbox';
                    return;
                }

                for (let option of this.fieldForm.field_options.options) {
                    if (!option.value || !option.label) {
                        this.fieldError = 'Semua option harus memiliki value dan label';
                        return;
                    }
                }
            }

            this.fieldLoading = true;
            try {
                const token = localStorage.getItem('token');
                let response;

                if (this.editingField) {
                    response = await axios.put(
                        `event/${this.eventId}/custom-fields/${this.editingField.id_field}`,
                        this.fieldForm,
                        { params: { token } }
                    );
                } else {
                    response = await axios.post(
                        `event/${this.eventId}/custom-fields`,
                        this.fieldForm,
                        { params: { token } }
                    );
                }

                if (response.data.success) {
                    await showSuccess('Berhasil!', this.editingField ? 'Field berhasil diupdate' : 'Field berhasil ditambahkan');
                    this.closeFieldModal();
                    this.fetchFields();
                    this.$emit('fields-updated');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal menyimpan field';
                this.fieldError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.fieldLoading = false;
            }
        },
        async deleteField(fieldId) {
            const result = await showDeleteConfirm('Hapus Field', 'Apakah Anda yakin ingin menghapus field ini? Semua data yang terkait akan dihapus.');
            
            if (result.isConfirmed) {
                try {
                    const token = localStorage.getItem('token');
                    const response = await axios.delete(
                        `event/${this.eventId}/custom-fields/${fieldId}`,
                        { params: { token } }
                    );

                    if (response.data.success) {
                        await showSuccess('Berhasil!', 'Field berhasil dihapus');
                        this.fetchFields();
                        this.$emit('fields-updated');
                    }
                } catch (error) {
                    await showError('Gagal!', 'Gagal menghapus field');
                }
            }
        },
        getTypeLabel(type) {
            const labels = {
                'input': 'Input',
                'textarea': 'Textarea',
                'file': 'File',
                'select': 'Select',
                'radio': 'Radio',
                'checkbox': 'Checkbox'
            };
            return labels[type] || type;
        },
        getTypeClass(type) {
            return `type-${type}`;
        }
    }
}
</script>

<style scoped>
@import '../styles/custom-field-manager.css';
</style>
