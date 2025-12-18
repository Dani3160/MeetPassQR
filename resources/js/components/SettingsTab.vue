<template>
    <div class="settings-tab">
        <div class="settings-section">
            <h2 class="section-title">Logo Event</h2>
            <div class="logo-upload-section">
                <div class="logo-preview">
                    <img 
                        :src="logoPreview || defaultLogo" 
                        alt="Event Logo" 
                        class="logo-image"
                    />
                </div>
                <div class="logo-upload-controls">
                    <input 
                        ref="fileInput"
                        type="file" 
                        accept="image/jpeg,image/jpg,image/png,image/webp" 
                        @change="handleFileSelect"
                        class="file-input"
                    />
                    <button 
                        @click="$refs.fileInput.click()" 
                        class="btn btn-primary"
                        :disabled="logoUploading"
                    >
                        <i class="ri-upload-line"></i> 
                        {{ logoUploading ? 'Mengunggah...' : 'Unggah Logo' }}
                    </button>
                    <button 
                        v-if="logoPreview && logoPreview !== defaultLogo"
                        @click="removeLogo" 
                        class="btn btn-secondary"
                        :disabled="logoUploading"
                    >
                        <i class="ri-delete-bin-line"></i> Hapus Logo
                    </button>
                </div>
                <p class="help-text">Format yang didukung: JPEG, PNG, WebP. Maksimal 2MB</p>
            </div>
        </div>

        <div class="settings-section">
            <h2 class="section-title">Pengaturan Urutan Form Field</h2>
            <div class="form-type-tabs">
                <button 
                    v-for="formType in formTypes"
                    :key="formType.value"
                    @click="selectedFormType = formType.value"
                    :class="['form-type-tab', { active: selectedFormType === formType.value }]"
                >
                    {{ formType.label }}
                </button>
            </div>

            <div class="field-order-section">
                <div class="field-list" v-if="sortedFields.length > 0">
                    <div
                        v-for="(field, index) in sortedFields"
                        :key="`${field.field_type}-${field.field_key}`"
                        :draggable="true"
                        @dragstart="handleDragStart(index, $event)"
                        @dragover.prevent="handleDragOver(index, $event)"
                        @drop="handleDrop(index, $event)"
                        :class="['field-item', { dragging: draggedIndex === index }]"
                    >
                        <div class="field-handle">
                            <i class="ri-drag-move-2-line"></i>
                        </div>
                        <div class="field-info">
                            <span class="field-label">{{ getFieldLabel(field) }}</span>
                            <span class="field-type-badge" :class="field.field_type">
                                {{ field.field_type === 'default' ? 'Default' : 'Custom' }}
                            </span>
                        </div>
                        <div class="field-actions">
                            <button 
                                @click="toggleFieldVisibility(field)"
                                :class="['btn-icon', field.is_visible ? 'visible' : 'hidden']"
                                :title="field.is_visible ? 'Sembunyikan' : 'Tampilkan'"
                            >
                                <i :class="field.is_visible ? 'ri-eye-line' : 'ri-eye-off-line'"></i>
                            </button>
                            <button 
                                v-if="index > 0"
                                @click="moveFieldUp(index)"
                                class="btn-icon"
                                title="Pindah ke atas"
                            >
                                <i class="ri-arrow-up-line"></i>
                            </button>
                            <button 
                                v-if="index < sortedFields.length - 1"
                                @click="moveFieldDown(index)"
                                class="btn-icon"
                                title="Pindah ke bawah"
                            >
                                <i class="ri-arrow-down-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="empty-state">
                    <p>Tidak ada field yang diatur. Field default akan digunakan.</p>
                </div>
                <div class="field-order-actions">
                    <button 
                        @click="saveFieldOrders" 
                        class="btn btn-primary"
                        :disabled="savingOrders || sortedFields.length === 0"
                    >
                        <i class="ri-save-line"></i> 
                        {{ savingOrders ? 'Menyimpan...' : 'Simpan Urutan' }}
                    </button>
                    <button 
                        @click="resetToDefault" 
                        class="btn btn-secondary"
                        :disabled="savingOrders"
                    >
                        <i class="ri-refresh-line"></i> Reset ke Default
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { showSuccess, showError } from '../utils/swal.js';

// Default field definitions
const DEFAULT_FIELDS = {
    add: [
        { field_type: 'default', field_key: 'guest_name', label: 'Nama', is_visible: true },
        { field_type: 'default', field_key: 'guest_title', label: 'Jabatan', is_visible: true },
        { field_type: 'default', field_key: 'guest_address', label: 'Alamat', is_visible: true },
        { field_type: 'default', field_key: 'guest_email', label: 'Email', is_visible: true },
        { field_type: 'default', field_key: 'guest_phone', label: 'Telepon', is_visible: true },
        { field_type: 'default', field_key: 'id_session', label: 'Sesi', is_visible: true },
        { field_type: 'default', field_key: 'guest_label', label: 'Label VIP', is_visible: true },
    ],
    edit: [
        { field_type: 'default', field_key: 'guest_name', label: 'Nama', is_visible: true },
        { field_type: 'default', field_key: 'guest_title', label: 'Jabatan', is_visible: true },
        { field_type: 'default', field_key: 'guest_address', label: 'Alamat', is_visible: true },
        { field_type: 'default', field_key: 'guest_email', label: 'Email', is_visible: true },
        { field_type: 'default', field_key: 'guest_phone', label: 'Telepon', is_visible: true },
        { field_type: 'default', field_key: 'id_session', label: 'Sesi', is_visible: true },
        { field_type: 'default', field_key: 'guest_label', label: 'Label VIP', is_visible: true },
    ],
    complete_profile: [
        { field_type: 'default', field_key: 'guestName', label: 'Nama', is_visible: true },
        { field_type: 'default', field_key: 'guestTitle', label: 'Jabatan', is_visible: true },
        { field_type: 'default', field_key: 'guestEmail', label: 'Email', is_visible: true },
        { field_type: 'default', field_key: 'guestPhone', label: 'Telepon', is_visible: true },
    ]
};

const FIELD_LABELS = {
    // Default fields for add/edit
    guest_name: 'Nama',
    guest_title: 'Jabatan',
    guest_address: 'Alamat',
    guest_email: 'Email',
    guest_phone: 'Telepon',
    id_session: 'Sesi',
    guest_label: 'Label VIP',
    // Default fields for complete_profile
    guestName: 'Nama',
    guestTitle: 'Jabatan',
    guestEmail: 'Email',
    guestPhone: 'Telepon',
};

export default {
    name: 'SettingsTab',
    props: {
        event: {
            type: Object,
            required: true
        },
        customFields: {
            type: Array,
            default: () => []
        }
    },
    emits: ['event-updated', 'field-orders-updated'],
    data() {
        return {
            logoPreview: null,
            logoUploading: false,
            selectedFormType: 'add',
            fieldOrders: [],
            savingOrders: false,
            draggedIndex: null,
            formTypes: [
                { value: 'add', label: 'Tambah Tamu' },
                { value: 'edit', label: 'Edit Tamu' },
                { value: 'complete_profile', label: 'Lengkapi Profil' },
            ]
        };
    },
    computed: {
        sortedFields() {
            return [...this.fieldOrders].sort((a, b) => a.field_order - b.field_order);
        },
        defaultLogo() {
            return this.event?.event_default_guest_pic 
                ? `/storage/${this.event.event_default_guest_pic}`
                : '/storage/event/avatar.jpg';
        }
    },
    watch: {
        selectedFormType() {
            this.fetchFieldOrders();
        },
        event: {
            immediate: true,
            handler() {
                if (this.event?.event_default_guest_pic) {
                    this.logoPreview = `/storage/${this.event.event_default_guest_pic}`;
                }
                this.fetchFieldOrders();
            }
        },
        customFields: {
            immediate: true,
            handler() {
                if (this.fieldOrders.length === 0) {
                    this.initializeDefaultFields();
                } else {
                    this.mergeCustomFields();
                }
            }
        }
    },
    methods: {
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                showError('Error', 'Ukuran file maksimal 2MB');
                return;
            }

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                showError('Error', 'Format file harus JPEG, PNG, atau WebP');
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = (e) => {
                this.logoPreview = e.target.result;
                this.uploadLogo(file);
            };
            reader.readAsDataURL(file);
        },
        async uploadLogo(file) {
            this.logoUploading = true;
            try {
                const formData = new FormData();
                formData.append('logo', file);

                const response = await axios.post(
                    `/event/${this.event.id}/logo`,
                    formData,
                    {
                        params: this.getAuthParams(),
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );

                if (response.data.success) {
                    this.logoPreview = `/storage/${response.data.data.logo_path}`;
                    await showSuccess('Berhasil!', 'Logo berhasil diunggah');
                    this.$emit('event-updated');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal mengunggah logo';
                await showError('Error', errorMessage);
                // Reset preview on error
                if (this.event?.event_default_guest_pic) {
                    this.logoPreview = `/storage/${this.event.event_default_guest_pic}`;
                } else {
                    this.logoPreview = null;
                }
            } finally {
                this.logoUploading = false;
                // Reset file input
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            }
        },
        async removeLogo() {
            // Set to default
            try {
                await axios.put(
                    `/event/${this.event.id}`,
                    { event_default_guest_pic: '/event/avatar.jpg' },
                    { params: this.getAuthParams() }
                );
                this.logoPreview = '/storage/event/avatar.jpg';
                await showSuccess('Berhasil!', 'Logo dihapus, menggunakan logo default');
                this.$emit('event-updated');
            } catch (error) {
                await showError('Error', 'Gagal menghapus logo');
            }
        },
        async fetchFieldOrders() {
            try {
                const response = await axios.get(
                    `/event/${this.event.id}/field-orders`,
                    {
                        params: {
                            ...this.getAuthParams(),
                            form_type: this.selectedFormType,
                        },
                    }
                );

                if (response.data.success) {
                    this.fieldOrders = response.data.data.map((order, index) => ({
                        ...order,
                        field_order: order.field_order !== undefined ? order.field_order : index,
                    }));

                    // If no orders exist, initialize with defaults
                    if (this.fieldOrders.length === 0) {
                        this.initializeDefaultFields();
                    } else {
                        this.mergeCustomFields();
                    }
                }
            } catch (error) {
                console.error('Error fetching field orders:', error);
                // Initialize with defaults on error
                this.initializeDefaultFields();
            }
        },
        initializeDefaultFields() {
            const defaultFields = DEFAULT_FIELDS[this.selectedFormType] || [];
            const fields = defaultFields.map((field, index) => ({
                field_type: field.field_type,
                field_key: field.field_key,
                field_order: index,
                is_visible: field.is_visible !== undefined ? field.is_visible : true,
            }));

            // Add custom fields
            this.customFields.forEach((customField, index) => {
                fields.push({
                    field_type: 'custom',
                    field_key: customField.id_field.toString(),
                    field_order: defaultFields.length + index,
                    is_visible: true,
                });
            });

            this.fieldOrders = fields;
        },
        mergeCustomFields() {
            // Get existing custom field keys
            const existingCustomKeys = this.fieldOrders
                .filter(f => f.field_type === 'custom')
                .map(f => f.field_key);

            // Add new custom fields that don't exist
            this.customFields.forEach((customField, index) => {
                const fieldKey = customField.id_field.toString();
                if (!existingCustomKeys.includes(fieldKey)) {
                    const maxOrder = Math.max(...this.fieldOrders.map(f => f.field_order), -1);
                    this.fieldOrders.push({
                        field_type: 'custom',
                        field_key: fieldKey,
                        field_order: maxOrder + 1,
                        is_visible: true,
                    });
                }
            });
        },
        getFieldLabel(field) {
            if (field.field_type === 'default') {
                return FIELD_LABELS[field.field_key] || field.field_key;
            } else {
                // Custom field
                const customField = this.customFields.find(
                    cf => cf.id_field.toString() === field.field_key
                );
                return customField ? customField.field_name : `Custom Field ${field.field_key}`;
            }
        },
        toggleFieldVisibility(field) {
            field.is_visible = !field.is_visible;
        },
        moveFieldUp(index) {
            if (index === 0) return;
            const fields = [...this.sortedFields];
            [fields[index - 1], fields[index]] = [fields[index], fields[index - 1]];
            this.updateFieldOrders(fields);
        },
        moveFieldDown(index) {
            const fields = [...this.sortedFields];
            if (index >= fields.length - 1) return;
            [fields[index], fields[index + 1]] = [fields[index + 1], fields[index]];
            this.updateFieldOrders(fields);
        },
        updateFieldOrders(fields) {
            fields.forEach((field, index) => {
                const existing = this.fieldOrders.find(
                    f => f.field_type === field.field_type && f.field_key === field.field_key
                );
                if (existing) {
                    existing.field_order = index;
                }
            });
        },
        handleDragStart(index, event) {
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/html', event.target);
        },
        handleDragOver(index, event) {
            if (this.draggedIndex === null || this.draggedIndex === index) return;
            event.dataTransfer.dropEffect = 'move';
        },
        handleDrop(index, event) {
            event.preventDefault();
            if (this.draggedIndex === null || this.draggedIndex === index) {
                this.draggedIndex = null;
                return;
            }

            const fields = [...this.sortedFields];
            const draggedItem = fields[this.draggedIndex];
            fields.splice(this.draggedIndex, 1);
            fields.splice(index, 0, draggedItem);
            
            this.updateFieldOrders(fields);
            this.draggedIndex = null;
        },
        async saveFieldOrders() {
            this.savingOrders = true;
            try {
                const fieldOrdersData = this.sortedFields.map((field, index) => ({
                    field_type: field.field_type,
                    field_key: field.field_key,
                    field_order: index,
                    is_visible: field.is_visible !== undefined ? field.is_visible : true,
                }));

                const response = await axios.put(
                    `/event/${this.event.id}/field-orders`,
                    {
                        form_type: this.selectedFormType,
                        field_orders: fieldOrdersData,
                    },
                    {
                        params: this.getAuthParams(),
                    }
                );

                if (response.data.success) {
                    await showSuccess('Berhasil!', 'Urutan field berhasil disimpan');
                    await this.fetchFieldOrders();
                    // Emit event to parent to refresh field orders
                    this.$emit('field-orders-updated');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal menyimpan urutan field';
                await showError('Error', errorMessage);
            } finally {
                this.savingOrders = false;
            }
        },
        async resetToDefault() {
            this.initializeDefaultFields();
            await this.saveFieldOrders();
        },
        getAuthParams() {
            const token = localStorage.getItem('token');
            return { token };
        },
    },
};
</script>

<style scoped>
.settings-tab {
    padding: 2rem;
}

.settings-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary, oklch(20% 0 0));
    margin-bottom: 1.5rem;
    font-family: 'Poppins', sans-serif;
}

/* Logo Upload Section */
.logo-upload-section {
    background: var(--card-bg, white);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.logo-preview {
    margin-bottom: 1.5rem;
    display: flex;
    justify-content: center;
}

.logo-image {
    max-width: 200px;
    max-height: 200px;
    width: auto;
    height: auto;
    border-radius: 0.75rem;
    border: 2px solid var(--border-color, oklch(92% 0 0));
    object-fit: contain;
}

.logo-upload-controls {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.file-input {
    display: none;
}

.help-text {
    margin-top: 1rem;
    text-align: center;
    font-size: 0.875rem;
    color: var(--text-secondary, oklch(50% 0 0));
}

/* Form Type Tabs */
.form-type-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid var(--border-color, oklch(92% 0 0));
}

.form-type-tab {
    padding: 0.75rem 1.5rem;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 0.9375rem;
    font-weight: 500;
    color: var(--text-secondary, oklch(50% 0 0));
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: 'Poppins', sans-serif;
    margin-bottom: -2px;
}

.form-type-tab:hover {
    color: var(--primary);
}

.form-type-tab.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

/* Field Order Section */
.field-order-section {
    background: var(--card-bg, white);
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.field-list {
    margin-bottom: 2rem;
}

.field-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--input-bg, white);
    border: 2px solid var(--border-color, oklch(92% 0 0));
    border-radius: 0.75rem;
    margin-bottom: 0.75rem;
    cursor: move;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.field-item:hover {
    border-color: var(--primary);
    box-shadow: 0 2px 8px rgba(var(--primary-rgb, 249, 115, 22), 0.1);
}

.field-item.dragging {
    opacity: 0.5;
    border-color: var(--primary);
}

.field-handle {
    color: var(--text-secondary, oklch(50% 0 0));
    font-size: 1.25rem;
    cursor: grab;
}

.field-handle:active {
    cursor: grabbing;
}

.field-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.field-label {
    font-weight: 500;
    color: var(--text-primary, oklch(20% 0 0));
    font-family: 'Poppins', sans-serif;
}

.field-type-badge {
    padding: 0.25rem 0.625rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
}

.field-type-badge.default {
    background: oklch(95% 0.05 250);
    color: oklch(50% 0.2 250);
}

.field-type-badge.custom {
    background: oklch(95% 0.05 150);
    color: oklch(50% 0.2 150);
}

.field-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--border-color, oklch(92% 0 0));
    background: var(--input-bg, white);
    border-radius: 0.5rem;
    color: var(--text-primary, oklch(20% 0 0));
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1.125rem;
}

.btn-icon:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--input-bg-focus, white);
}

.btn-icon.visible {
    color: oklch(50% 0.2 150);
}

.btn-icon.hidden {
    color: var(--text-secondary, oklch(50% 0 0));
}

.field-order-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--text-secondary, oklch(50% 0 0));
}

/* Responsive */
@media (max-width: 768px) {
    .settings-tab {
        padding: 1rem;
    }

    .logo-upload-controls {
        flex-direction: column;
    }

    .logo-upload-controls .btn {
        width: 100%;
    }

    .form-type-tabs {
        flex-wrap: wrap;
    }

    .form-type-tab {
        flex: 1;
        min-width: calc(50% - 0.25rem);
    }

    .field-item {
        flex-wrap: wrap;
    }

    .field-actions {
        width: 100%;
        justify-content: flex-end;
    }

    .field-order-actions {
        flex-direction: column;
    }

    .field-order-actions .btn {
        width: 100%;
    }
}
</style>
