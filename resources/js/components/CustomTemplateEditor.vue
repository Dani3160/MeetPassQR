<template>
    <div class="custom-template-editor">
        <div class="section-header">
            <h2>Editor Template Sertifikat Custom</h2>
            <p class="section-description">Buat dan edit template sertifikat dengan HTML dan CSS kustom</p>
        </div>

        <!-- Template Selection/Creation -->
        <div class="settings-card">
            <h3>Pilih atau Buat Template</h3>
            <div class="form-group">
                <label class="form-label">Template</label>
                <Select2
                    v-model="selectedTemplateId"
                    :options="{ placeholder: 'Pilih template atau buat baru' }"
                    select-class="form-control"
                    @change="onTemplateSelect"
                >
                    <option value="">-- Buat Template Baru --</option>
                    <option 
                        v-for="template in templates" 
                        :key="template.id_template"
                        :value="template.id_template"
                    >
                        {{ template.template_name }}
                    </option>
                </Select2>
            </div>

            <div v-if="!selectedTemplateId" class="form-group">
                <label class="form-label">Nama Template</label>
                <input 
                    v-model="templateForm.template_name" 
                    type="text" 
                    class="form-control"
                    placeholder="Contoh: Template Custom Saya"
                />
            </div>

            <div v-if="!selectedTemplateId" class="form-group">
                <label class="form-label">Deskripsi Template</label>
                <textarea 
                    v-model="templateForm.template_description" 
                    class="form-control"
                    rows="2"
                    placeholder="Deskripsi singkat tentang template ini"
                ></textarea>
            </div>
        </div>

        <!-- Editor Section -->
        <div v-if="selectedTemplateId || templateForm.template_name" class="editor-section">
            <div class="editor-layout">
                <!-- HTML Editor -->
                <div class="editor-panel">
                    <div class="editor-header">
                        <h3>HTML Structure</h3>
                        <small class="editor-hint">Gunakan placeholder: {{recipient_name}}, {{course_title}}, {{certificate_id}}, dll</small>
                    </div>
                    <textarea
                        v-model="templateForm.html_structure"
                        class="code-editor"
                        placeholder="Masukkan struktur HTML template..."
                        @input="updatePreview"
                    ></textarea>
                </div>

                <!-- CSS Editor -->
                <div class="editor-panel">
                    <div class="editor-header">
                        <h3>CSS Styles</h3>
                        <small class="editor-hint">Tambahkan styling untuk template</small>
                    </div>
                    <textarea
                        v-model="templateForm.css_styles"
                        class="code-editor"
                        placeholder="Masukkan CSS untuk template..."
                        @input="updatePreview"
                    ></textarea>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="preview-panel">
                <div class="preview-header">
                    <h3>Preview Template</h3>
                    <div class="preview-actions">
                        <button @click="refreshPreview" class="btn btn-secondary btn-sm">
                            <i class="ri-refresh-line"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="preview-container" ref="previewContainer">
                    <CertificatePreview 
                        ref="certificatePreview"
                        :template="previewTemplate"
                        :config="previewConfig"
                        :sample-data="sampleData"
                    />
                </div>
            </div>
        </div>

        <!-- Sample Data for Preview -->
        <div v-if="selectedTemplateId || templateForm.template_name" class="settings-card">
            <h3>Data Preview (untuk preview saja)</h3>
            <div class="form-group">
                <label class="form-label">Nama Penerima</label>
                <input 
                    v-model="sampleData.recipient_name" 
                    type="text" 
                    class="form-control"
                    @input="updatePreview"
                />
            </div>
            <div class="form-group">
                <label class="form-label">Judul Kursus/Program</label>
                <input 
                    v-model="sampleData.course_title" 
                    type="text" 
                    class="form-control"
                    @input="updatePreview"
                />
            </div>
            <div class="form-group">
                <label class="form-label">ID Sertifikat</label>
                <input 
                    v-model="sampleData.certificate_id" 
                    type="text" 
                    class="form-control"
                    @input="updatePreview"
                />
            </div>
            <div class="form-group">
                <label class="form-label">Gambar Tanda Tangan (Preview)</label>
                <div class="signature-upload-section">
                    <div v-if="previewConfig.signature_image" class="signature-preview">
                        <img :src="previewConfig.signature_image" alt="Tanda Tangan Preview" />
                        <button @click="removePreviewSignature" type="button" class="btn-remove-signature">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div v-else class="upload-area">
                        <input 
                            ref="previewSignatureFileInput"
                            type="file" 
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            @change="handlePreviewSignatureUpload"
                            class="file-input"
                            id="preview-signature-upload"
                        />
                        <label for="preview-signature-upload" class="upload-label">
                            <i class="ri-upload-cloud-line"></i>
                            <span>Upload Gambar Tanda Tangan untuk Preview</span>
                        </label>
                    </div>
                </div>
                <small class="form-text">Gambar ini hanya untuk preview di editor, tidak disimpan</small>
            </div>
        </div>

        <!-- Actions -->
        <div v-if="selectedTemplateId || templateForm.template_name" class="settings-actions">
            <button @click="saveTemplate" class="btn btn-primary" :disabled="saving || !canSave">
                <span v-if="saving" class="loading-spinner"></span>
                {{ saving ? 'Menyimpan...' : 'Simpan Template' }}
            </button>
            <button 
                v-if="selectedTemplateId" 
                @click="deleteTemplate" 
                class="btn btn-danger" 
                :disabled="deleting"
            >
                {{ deleting ? 'Menghapus...' : 'Hapus Template' }}
            </button>
            <button @click="resetForm" class="btn btn-secondary">
                Reset
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Select2 from './Select2.vue';
import CertificatePreview from './CertificatePreview.vue';
import { showSuccess, showError, showDeleteConfirm } from '../utils/swal.js';

export default {
    name: 'CustomTemplateEditor',
    components: {
        Select2,
        CertificatePreview
    },
    data() {
        return {
            templates: [],
            selectedTemplateId: '',
            saving: false,
            deleting: false,
            templateForm: {
                template_name: '',
                template_description: '',
                html_structure: '',
                css_styles: '',
                configurable_fields: [
                    'recipient_name',
                    'course_title',
                    'certificate_id',
                    'introductory_phrase',
                    'completion_phrase',
                    'organization_name',
                    'signatory_name',
                    'signatory_title',
                    'verification_url'
                ],
                is_active: true
            },
            sampleData: {
                recipient_name: 'Nama Penerima',
                course_title: 'Judul Kursus/Program',
                certificate_id: 'SK-XXXXX'
            },
            previewConfig: {
                introductory_phrase: 'Sertifikat dengan bangga diberikan kepada',
                completion_phrase: 'telah mengikuti event',
                organization_name: 'Nama Organisasi',
                signatory_name: 'Nama Penandatangan',
                signatory_title: 'Jabatan',
                signature_image: '',
                verification_url_base: 'https://example.com/cek-sertifikat'
            }
        };
    },
    computed: {
        previewTemplate() {
            return {
                id_template: this.selectedTemplateId || 'preview',
                template_name: this.templateForm.template_name || 'Preview',
                html_structure: this.templateForm.html_structure || '',
                css_styles: this.templateForm.css_styles || '',
                configurable_fields: this.templateForm.configurable_fields || []
            };
        },
        canSave() {
            return this.templateForm.template_name.trim() !== '' && 
                   this.templateForm.html_structure.trim() !== '';
        }
    },
    mounted() {
        this.fetchTemplates();
        // Load default template HTML/CSS if creating new
        this.loadDefaultTemplate();
    },
    methods: {
        getAuthParams() {
            return { token: localStorage.getItem('token') };
        },
        async fetchTemplates() {
            try {
                const response = await axios.get('/certificate/templates/all', {
                    params: this.getAuthParams()
                });
                this.templates = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                await showError('Error', 'Gagal memuat template sertifikat');
            }
        },
        onTemplateSelect() {
            if (this.selectedTemplateId) {
                const template = this.templates.find(
                    t => t.id_template == this.selectedTemplateId
                );
                if (template) {
                    this.templateForm = {
                        template_name: template.template_name,
                        template_description: template.template_description || '',
                        html_structure: template.html_structure || '',
                        css_styles: template.css_styles || '',
                        configurable_fields: template.configurable_fields || [],
                        is_active: template.is_active !== undefined ? template.is_active : true
                    };
                    this.updatePreview();
                }
            } else {
                this.resetForm();
            }
        },
        loadDefaultTemplate() {
            // Load a basic template structure as starting point
            if (!this.templateForm.html_structure) {
                this.templateForm.html_structure = `<div class="certificate-container">
    <div class="cert-header">
        <h1 class="cert-title">SERTIFIKAT</h1>
    </div>
    <div class="cert-body">
        <p class="intro-text">{{introductory_phrase}}</p>
        <h2 class="recipient-name">{{recipient_name}}</h2>
        <p class="completion-text">{{completion_phrase}}</p>
        <p class="course-title">{{course_title}}</p>
    </div>
    <div class="cert-footer">
        <div class="cert-info">
            <p class="cert-id">ID: <strong>{{certificate_id}}</strong></p>
            <p class="verification-url">{{verification_url}}</p>
        </div>
        <div class="signatory-block">
            <div class="signature-container">
                {{signature_image}}
            </div>
            <p class="signatory-name">{{signatory_name}}</p>
            <p class="signatory-title">{{signatory_title}}</p>
        </div>
    </div>
</div>`;
            }
            
            if (!this.templateForm.css_styles) {
                this.templateForm.css_styles = `.certificate-container {
    width: 800px;
    margin: 0 auto;
    padding: 3rem;
    background: white;
    border: 2px solid #000;
    font-family: 'Times New Roman', serif;
}

.cert-title {
    font-size: 3rem;
    font-weight: bold;
    text-align: center;
    margin-bottom: 2rem;
}

.recipient-name {
    font-size: 2rem;
    font-weight: bold;
    text-align: center;
    margin: 2rem 0;
}

.cert-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #000;
}`;
            }
            
            this.updatePreview();
        },
        updatePreview() {
            // Preview will update automatically via computed property
            this.$nextTick(() => {
                if (this.$refs.certificatePreview) {
                    // Force re-render
                    this.$forceUpdate();
                }
            });
        },
        refreshPreview() {
            this.updatePreview();
        },
        async saveTemplate() {
            if (!this.canSave) {
                await showError('Error', 'Nama template dan HTML structure harus diisi');
                return;
            }

            this.saving = true;
            try {
                const url = this.selectedTemplateId 
                    ? `/certificate/templates/${this.selectedTemplateId}`
                    : '/certificate/templates';
                
                const method = this.selectedTemplateId ? 'put' : 'post';
                
                const response = await axios[method](
                    url,
                    this.templateForm,
                    { params: this.getAuthParams() }
                );

                if (response.data.success || response.data.id_template || response.data.data?.id_template) {
                    await showSuccess('Berhasil!', 'Template berhasil disimpan');
                    await this.fetchTemplates();
                    const templateId = response.data.id_template || response.data.data?.id_template;
                    if (!this.selectedTemplateId && templateId) {
                        this.selectedTemplateId = templateId;
                    }
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal menyimpan template';
                await showError('Error', errorMessage);
            } finally {
                this.saving = false;
            }
        },
        async deleteTemplate() {
            if (!this.selectedTemplateId) return;

            const result = await showDeleteConfirm(
                'Hapus Template',
                'Apakah Anda yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan.'
            );

            if (result.isConfirmed) {
                this.deleting = true;
                try {
                    const response = await axios.delete(
                        `/certificate/templates/${this.selectedTemplateId}`,
                        { params: this.getAuthParams() }
                    );

                    if (response.data.success) {
                        await showSuccess('Berhasil!', 'Template berhasil dihapus');
                        this.resetForm();
                        await this.fetchTemplates();
                    }
                } catch (error) {
                    const errorMessage = error.response?.data?.message || 'Gagal menghapus template';
                    await showError('Error', errorMessage);
                } finally {
                    this.deleting = false;
                }
            }
        },
        resetForm() {
            this.selectedTemplateId = '';
            this.templateForm = {
                template_name: '',
                template_description: '',
                html_structure: '',
                css_styles: '',
                configurable_fields: [
                    'recipient_name',
                    'course_title',
                    'certificate_id',
                    'introductory_phrase',
                    'completion_phrase',
                    'organization_name',
                    'signatory_name',
                    'signatory_title',
                    'verification_url'
                ],
                is_active: true
            };
            this.previewConfig.signature_image = '';
            this.loadDefaultTemplate();
        },
        handlePreviewSignatureUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validasi file
            if (!file.type.startsWith('image/')) {
                showError('Error', 'File harus berupa gambar');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB
                showError('Error', 'Ukuran file maksimal 5MB');
                return;
            }

            // Convert to base64 for preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewConfig.signature_image = e.target.result;
                this.updatePreview();
            };
            reader.onerror = () => {
                showError('Error', 'Gagal membaca file');
            };
            reader.readAsDataURL(file);
        },
        removePreviewSignature() {
            this.previewConfig.signature_image = '';
            if (this.$refs.previewSignatureFileInput) {
                this.$refs.previewSignatureFileInput.value = '';
            }
            this.updatePreview();
        }
    }
};
</script>

<style scoped>
@import '../styles/custom-template-editor.css';
</style>
