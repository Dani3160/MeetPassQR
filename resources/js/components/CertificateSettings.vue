<template>
    <div class="certificate-settings">
        <div class="section-header">
            <h2>Pengaturan Sertifikat</h2>
            <p class="section-description">Konfigurasi template dan konten sertifikat untuk event ini</p>
        </div>

        <!-- Template Selection -->
        <div class="settings-card">
            <h3>Pilih Template</h3>
            <div class="form-group">
                <label class="form-label">Template Sertifikat</label>
                <Select2
                    v-model="formData.id_template"
                    :options="{ placeholder: 'Pilih template sertifikat' }"
                    select-class="form-control"
                    @change="onTemplateChange"
                >
                    <option value="">-- Pilih Template --</option>
                    <option 
                        v-for="template in templates" 
                        :key="template.id_template"
                        :value="template.id_template"
                    >
                        {{ template.template_name }}
                    </option>
                </Select2>
                <p v-if="selectedTemplate" class="template-description">
                    {{ selectedTemplate.template_description }}
                </p>
            </div>
        </div>

        <!-- Certificate Content Settings -->
        <div v-if="formData.id_template" class="settings-card">
            <h3>Konten Sertifikat</h3>
            
            <div class="form-group">
                <label class="form-label">Frasa Pembuka</label>
                <input 
                    v-model="formData.introductory_phrase" 
                    type="text" 
                    class="form-control"
                    placeholder="Sertifikat dengan bangga diberikan kepada"
                />
            </div>

            <div class="form-group">
                <label class="form-label">Frasa Penyelesaian</label>
                <input 
                    v-model="formData.completion_phrase" 
                    type="text" 
                    class="form-control"
                    placeholder="telah mengikuti event"
                />
            </div>

            <div class="form-group">
                <label class="form-label">Nama Organisasi</label>
                <input 
                    v-model="formData.organization_name" 
                    type="text" 
                    class="form-control"
                    placeholder="Nama organisasi atau institusi"
                />
            </div>
        </div>

        <!-- Signatory Settings -->
        <div v-if="formData.id_template" class="settings-card">
            <h3>Penandatangan</h3>
            
            <div class="form-group">
                <label class="form-label">Nama Penandatangan</label>
                <input 
                    v-model="formData.signatory_name" 
                    type="text" 
                    class="form-control"
                    placeholder="Nama lengkap penandatangan"
                />
            </div>

            <div class="form-group">
                <label class="form-label">Jabatan</label>
                <input 
                    v-model="formData.signatory_title" 
                    type="text" 
                    class="form-control"
                    placeholder="Co-Founder, Direktur, dll"
                />
            </div>

            <div class="form-group">
                <label class="form-label">Gambar Tanda Tangan</label>
                <div class="signature-upload-section">
                    <div v-if="formData.signature_image" class="signature-preview">
                        <img :src="formData.signature_image" alt="Tanda Tangan" />
                        <button @click="removeSignature" type="button" class="btn-remove-signature" :disabled="uploadingSignature">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div v-else class="upload-area">
                        <input 
                            ref="signatureFileInput"
                            type="file" 
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            @change="handleSignatureUpload"
                            class="file-input"
                            id="signature-upload"
                            :disabled="uploadingSignature"
                        />
                        <label for="signature-upload" class="upload-label" :class="{ 'uploading': uploadingSignature }">
                            <i class="ri-upload-cloud-line"></i>
                            <span v-if="!uploadingSignature">Upload Gambar Tanda Tangan</span>
                            <span v-else>Mengunggah...</span>
                        </label>
                    </div>
                    <div class="signature-info">
                        <i class="ri-information-line"></i>
                        <p>
                            Belum punya gambar tanda tangan? 
                            <a 
                                href="https://signaturely-com.translate.goog/online-signature/draw/?_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=tc" 
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Buat tanda tangan online di sini
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification Settings -->
        <div v-if="formData.id_template" class="settings-card">
            <h3>Verifikasi Sertifikat</h3>
            
            <div class="form-group">
                <label class="form-label">Base URL Verifikasi</label>
                <input 
                    v-model="formData.verification_url_base" 
                    type="text" 
                    class="form-control"
                    placeholder="https://example.com/cek-sertifikat"
                />
                <small class="form-text">URL dasar untuk verifikasi sertifikat</small>
            </div>

            <div class="form-group">
                <label class="form-label">Prefix ID Sertifikat</label>
                <input 
                    v-model="formData.certificate_id_prefix" 
                    type="text" 
                    class="form-control"
                    placeholder="SK-"
                />
                <small class="form-text">Prefix untuk ID sertifikat (contoh: SK-)</small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <input 
                        v-model="formData.auto_generate_id" 
                        type="checkbox"
                        class="form-checkbox"
                    />
                    Auto-generate ID Sertifikat
                </label>
                <small class="form-text">Jika dicentang, ID sertifikat akan dibuat otomatis</small>
            </div>
        </div>

        <!-- Actions -->
        <div v-if="formData.id_template" class="settings-actions">
            <button @click="saveCertificate" class="btn btn-primary" :disabled="saving">
                <span v-if="saving" class="loading-spinner"></span>
                {{ saving ? 'Menyimpan...' : 'Simpan Konfigurasi' }}
            </button>
            <button v-if="hasCertificate" @click="deleteCertificate" class="btn btn-danger" :disabled="deleting">
                {{ deleting ? 'Menghapus...' : 'Hapus Konfigurasi' }}
            </button>
        </div>
        <br>
        <!-- Preview Data Settings -->
        <div v-if="formData.id_template && selectedTemplate" class="settings-card">
            <h3>Data Preview</h3>
            <p class="section-description">Atur data contoh untuk preview sertifikat (tidak disimpan)</p>
            
            <div class="form-group">
                <label class="form-label">Nama Penerima (Preview)</label>
                <input 
                    v-model="sampleData.recipient_name" 
                    type="text" 
                    class="form-control"
                    placeholder="Nama penerima sertifikat"
                />
            </div>

            <div class="form-group">
                <label class="form-label">Judul Kursus/Program (Preview)</label>
                <input 
                    v-model="sampleData.course_title" 
                    type="text" 
                    class="form-control"
                    placeholder="Judul kursus atau program"
                />
            </div>

            <div class="form-group">
                <label class="form-label">ID Sertifikat (Preview)</label>
                <input 
                    v-model="sampleData.certificate_id" 
                    type="text" 
                    class="form-control"
                    placeholder="ID sertifikat contoh"
                />
            </div>
        </div>

        <!-- Preview Section -->
        <div v-if="formData.id_template && selectedTemplate" class="settings-card preview-section">
            <h3>Preview Sertifikat</h3>
            <div class="preview-container" ref="previewContainer">
                <CertificatePreview 
                    ref="certificatePreview"
                    :template="selectedTemplate"
                    :config="formData"
                    :sample-data="sampleData"
                />
            </div>
        </div>

        <!-- Download Certificates Section -->
        <div v-if="hasCertificate && formData.id_template && selectedTemplate" class="settings-card download-section">
            <h3>Download Sertifikat</h3>
            <p class="section-description">Pilih tamu yang sudah check-in untuk download sertifikat mereka</p>
            
            <!-- Search and Actions -->
            <div class="download-controls">
                <div class="search-box">
                    <input 
                        v-model="guestSearchKeyword" 
                        type="text" 
                        class="form-control"
                        placeholder="Cari tamu berdasarkan nama..."
                    />
                </div>
                <div class="download-actions">
                    <button 
                        @click="selectAllGuests" 
                        class="btn btn-secondary btn-sm"
                        :disabled="filteredCheckedInGuests.length === 0"
                    >
                        Pilih Semua
                    </button>
                    <button 
                        @click="deselectAllGuests" 
                        class="btn btn-secondary btn-sm"
                        :disabled="selectedGuestIds.length === 0"
                    >
                        Batal Pilih
                    </button>
                    <button 
                        @click="downloadSelectedCertificates" 
                        class="btn btn-primary"
                        :disabled="selectedGuestIds.length === 0 || downloading"
                    >
                        <span v-if="downloading" class="loading-spinner"></span>
                        <i class="ri-download-line"></i>
                        {{ downloading ? 'Mengunduh...' : `Download (${selectedGuestIds.length})` }}
                    </button>
                    <button 
                        @click="downloadAllCertificates" 
                        class="btn btn-success"
                        :disabled="checkedInGuests.length === 0 || downloading"
                    >
                        <i class="ri-download-2-line"></i>
                        Download Semua ({{ checkedInGuests.length }})
                    </button>
                </div>
            </div>

            <!-- Guests List -->
            <div v-if="loadingGuests" class="loading-state">
                <div class="spinner"></div>
                <p>Memuat data tamu...</p>
            </div>
            <div v-else-if="filteredCheckedInGuests.length === 0" class="empty-state">
                <i class="ri-user-line"></i>
                <p v-if="guestSearchKeyword">Tidak ada tamu yang sesuai dengan pencarian</p>
                <p v-else>Tidak ada tamu yang sudah check-in</p>
            </div>
            <div v-else class="guests-list">
                <div class="guests-table-header">
                    <div class="col-checkbox">
                        <input 
                            type="checkbox" 
                            :checked="allSelected"
                            @change="toggleSelectAll"
                        />
                    </div>
                    <div class="col-name">Nama</div>
                    <div class="col-email">Email</div>
                    <div class="col-phone">Telepon</div>
                </div>
                <div 
                    v-for="guest in filteredCheckedInGuests" 
                    :key="guest.id_guest || guest.id"
                    class="guest-row"
                    :class="{ 'selected': isGuestSelected(guest) }"
                >
                    <div class="col-checkbox">
                        <input 
                            type="checkbox" 
                            :value="guest.id_guest || guest.id"
                            v-model="selectedGuestIds"
                        />
                    </div>
                    <div class="col-name">{{ guest.guest_name }}</div>
                    <div class="col-email">{{ guest.guest_email || '-' }}</div>
                    <div class="col-phone">{{ guest.guest_phone || '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import html2canvas from 'html2canvas';
import Select2 from './Select2.vue';
import CertificatePreview from './CertificatePreview.vue';
import { showSuccess, showError, showDeleteConfirm } from '../utils/swal.js';

export default {
    name: 'CertificateSettings',
    components: {
        Select2,
        CertificatePreview
    },
    props: {
        eventId: {
            type: [String, Number],
            required: true
        }
    },
    data() {
        return {
            templates: [],
            selectedTemplate: null,
            hasCertificate: false,
            saving: false,
            deleting: false,
            uploadingSignature: false,
            formData: {
                id_template: '',
                introductory_phrase: 'Sertifikat dengan bangga diberikan kepada',
                completion_phrase: 'telah mengikuti event',
                organization_name: '',
                signatory_name: '',
                signatory_title: '',
                signature_image: '',
                verification_url_base: '',
                certificate_id_prefix: 'SK-',
                auto_generate_id: true,
                custom_fields: {}
            },
            sampleData: {
                recipient_name: 'Nama Peserta',
                course_title: 'Membangun Koperasi Di Era Digital',
                certificate_id: 'SK-7959APTI4W12KEJ'
            },
            checkedInGuests: [],
            selectedGuestIds: [],
            guestSearchKeyword: '',
            loadingGuests: false,
            downloading: false,
            eventName: ''
        };
    },
    computed: {
        filteredCheckedInGuests() {
            if (!this.guestSearchKeyword.trim()) {
                return this.checkedInGuests;
            }
            const keyword = this.guestSearchKeyword.toLowerCase().trim();
            return this.checkedInGuests.filter(guest => 
                guest.guest_name.toLowerCase().includes(keyword) ||
                (guest.guest_email && guest.guest_email.toLowerCase().includes(keyword)) ||
                (guest.guest_phone && guest.guest_phone.includes(keyword))
            );
        },
        allSelected() {
            return this.filteredCheckedInGuests.length > 0 && 
                   this.filteredCheckedInGuests.every(guest => 
                       this.isGuestSelected(guest)
                   );
        }
    },
    watch: {
        hasCertificate(newVal) {
            if (newVal && this.formData.id_template) {
                this.fetchCheckedInGuests();
            }
        },
        'formData.id_template'() {
            if (this.hasCertificate && this.formData.id_template) {
                this.fetchCheckedInGuests();
            }
        }
    },
    mounted() {
        this.fetchTemplates();
        this.fetchCertificate();
        this.fetchEventName();
    },
    methods: {
        getAuthParams() {
            return { token: localStorage.getItem('token') };
        },
        async fetchTemplates() {
            try {
                const response = await axios.get('/certificate/templates', {
                    params: this.getAuthParams()
                });
                this.templates = Array.isArray(response.data) ? response.data : [];
            } catch (error) {
                await showError('Error', 'Gagal memuat template sertifikat');
            }
        },
        async fetchCertificate() {
            try {
                const response = await axios.get(`/event/${this.eventId}/certificate`, {
                    params: this.getAuthParams()
                });
                if (response.data.data) {
                    this.hasCertificate = true;
                    const cert = response.data.data;
                    // Convert path to URL jika masih berupa path
                    let signatureUrl = cert.signature_image || '';
                    if (signatureUrl && !signatureUrl.startsWith('http') && !signatureUrl.startsWith('data:') && !signatureUrl.startsWith('/')) {
                        signatureUrl = `/storage/${signatureUrl}`;
                    } else if (signatureUrl && !signatureUrl.startsWith('http') && !signatureUrl.startsWith('data:') && !signatureUrl.startsWith('/storage/')) {
                        signatureUrl = signatureUrl.startsWith('/') ? signatureUrl : `/storage/${signatureUrl}`;
                    }
                    
                    this.formData = {
                        id_template: cert.id_template,
                        introductory_phrase: cert.introductory_phrase || this.formData.introductory_phrase,
                        completion_phrase: cert.completion_phrase || this.formData.completion_phrase,
                        organization_name: cert.organization_name || '',
                        signatory_name: cert.signatory_name || '',
                        signatory_title: cert.signatory_title || '',
                        signature_image: signatureUrl,
                        verification_url_base: cert.verification_url_base || '',
                        certificate_id_prefix: cert.certificate_id_prefix || 'SK-',
                        auto_generate_id: cert.auto_generate_id !== undefined ? cert.auto_generate_id : true,
                        custom_fields: cert.custom_fields || {}
                    };
                    this.onTemplateChange();
                }
            } catch (error) {
                // No certificate yet, that's okay
            }
        },
        onTemplateChange() {
            if (this.formData.id_template) {
                this.selectedTemplate = this.templates.find(
                    t => t.id_template == this.formData.id_template
                );
            } else {
                this.selectedTemplate = null;
            }
        },
        async saveCertificate() {
            this.saving = true;
            try {
                const response = await axios.post(
                    `/event/${this.eventId}/certificate`,
                    this.formData,
                    { params: this.getAuthParams() }
                );
                if (response.data.success) {
                    this.hasCertificate = true;
                    await showSuccess('Berhasil!', 'Konfigurasi sertifikat berhasil disimpan');
                }
            } catch (error) {
                await showError('Error', 'Gagal menyimpan konfigurasi sertifikat');
            } finally {
                this.saving = false;
            }
        },
        async deleteCertificate() {
            const result = await showDeleteConfirm(
                'Hapus Konfigurasi',
                'Apakah Anda yakin ingin menghapus konfigurasi sertifikat ini?'
            );
            if (result.isConfirmed) {
                this.deleting = true;
                try {
                    const response = await axios.delete(
                        `/event/${this.eventId}/certificate`,
                        { params: this.getAuthParams() }
                    );
                    if (response.data.success) {
                        this.hasCertificate = false;
                        this.formData.id_template = '';
                        this.selectedTemplate = null;
                        await showSuccess('Berhasil!', 'Konfigurasi sertifikat berhasil dihapus');
                    }
                } catch (error) {
                    await showError('Error', 'Gagal menghapus konfigurasi sertifikat');
                } finally {
                    this.deleting = false;
                }
            }
        },
        async handleSignatureUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validasi file
            if (!file.type.startsWith('image/')) {
                await showError('Error', 'File harus berupa gambar');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB
                await showError('Error', 'Ukuran file maksimal 5MB');
                return;
            }

            this.uploadingSignature = true;
            try {
                // Upload ke server dengan nama file random
                const formData = new FormData();
                formData.append('signature', file);

                const response = await axios.post(
                    `/event/${this.eventId}/certificate/signature`,
                    formData,
                    {
                        params: this.getAuthParams(),
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    }
                );

                if (response.data.success) {
                    // Simpan URL dari server (bukan base64)
                    this.formData.signature_image = response.data.data.signature_url;
                    await showSuccess('Berhasil!', 'Gambar tanda tangan berhasil diunggah');
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal mengupload gambar tanda tangan';
                await showError('Error', errorMessage);
            } finally {
                this.uploadingSignature = false;
                // Reset file input
                if (this.$refs.signatureFileInput) {
                    this.$refs.signatureFileInput.value = '';
                }
            }
        },
        async removeSignature() {
            // Hapus file dari server jika ada
            if (this.formData.signature_image && !this.formData.signature_image.startsWith('data:')) {
                try {
                    // File akan dihapus otomatis saat certificate dihapus atau di-update
                    // Untuk sekarang cukup clear dari form
                } catch (error) {
                    // Ignore error saat hapus
                }
            }
            
            this.formData.signature_image = '';
            if (this.$refs.signatureFileInput) {
                this.$refs.signatureFileInput.value = '';
            }
        },
        async fetchEventName() {
            try {
                const response = await axios.get(`/event/${this.eventId}`, {
                    params: this.getAuthParams()
                });
                if (response.data && response.data.name_event) {
                    this.eventName = response.data.name_event;
                }
            } catch (error) {
                // Ignore error, use default
            }
        },
        async fetchCheckedInGuests() {
            this.loadingGuests = true;
            try {
                const response = await axios.get('/guest', {
                    params: {
                        ...this.getAuthParams(),
                        eventId: this.eventId,
                        attend: true // Hanya tamu yang sudah check-in
                    }
                });
                
                let guestsData = [];
                if (Array.isArray(response.data)) {
                    guestsData = response.data;
                } else if (response.data && response.data.data) {
                    guestsData = response.data.data;
                }
                
                this.checkedInGuests = guestsData;
            } catch (error) {
                await showError('Error', 'Gagal memuat data tamu');
                this.checkedInGuests = [];
            } finally {
                this.loadingGuests = false;
            }
        },
        isGuestSelected(guest) {
            return this.selectedGuestIds.includes(guest.id_guest || guest.id);
        },
        selectAllGuests() {
            this.selectedGuestIds = this.filteredCheckedInGuests.map(
                guest => guest.id_guest || guest.id
            );
        },
        deselectAllGuests() {
            this.selectedGuestIds = [];
        },
        toggleSelectAll() {
            if (this.allSelected) {
                this.deselectAllGuests();
            } else {
                this.selectAllGuests();
            }
        },
        generateCertificateId(guest) {
            if (this.formData.auto_generate_id) {
                // Generate random ID dengan prefix
                const randomString = Math.random().toString(36).substring(2, 15).toUpperCase();
                return `${this.formData.certificate_id_prefix}${randomString}`;
            }
            // Jika tidak auto-generate, gunakan ID guest dengan prefix
            return `${this.formData.certificate_id_prefix}${guest.id_guest || guest.id}`;
        },
        async downloadCertificateAsPNG(guest) {
            // Update sample data dengan data tamu
            const oldSampleData = { ...this.sampleData };
            this.sampleData = {
                recipient_name: guest.guest_name || 'Nama Tamu',
                course_title: this.eventName || 'Event',
                certificate_id: this.generateCertificateId(guest)
            };
            
            // Ensure signature_image URL is properly formatted
            if (this.formData.signature_image && !this.formData.signature_image.startsWith('http') && !this.formData.signature_image.startsWith('data:')) {
                if (!this.formData.signature_image.startsWith('/')) {
                    this.formData.signature_image = `/storage/${this.formData.signature_image}`;
                }
            }
            
            // Wait for Vue to update DOM
            await this.$nextTick();
            await new Promise(resolve => setTimeout(resolve, 300)); // Extra delay untuk memastikan render selesai
            
            try {
                // Find the certificate preview element
                let previewElement = null;
                
                // Try to get from component ref
                if (this.$refs.certificatePreview?.$el) {
                    previewElement = this.$refs.certificatePreview.$el.querySelector('.certificate-preview');
                }
                
                // Fallback: try from preview container
                if (!previewElement && this.$refs.previewContainer) {
                    previewElement = this.$refs.previewContainer.querySelector('.certificate-preview');
                }
                
                // Last fallback: try to find in document
                if (!previewElement) {
                    previewElement = document.querySelector('.certificate-preview');
                }
                
                if (!previewElement) {
                    throw new Error('Preview element not found. Pastikan preview sertifikat sudah dimuat.');
                }
                
                // Wait for all images in preview to load first
                const previewImages = previewElement.querySelectorAll('img');
                for (const img of previewImages) {
                    if (img.src && !img.complete) {
                        await new Promise((resolve) => {
                            img.onload = resolve;
                            img.onerror = resolve;
                            setTimeout(resolve, 3000); // Timeout after 3 seconds
                        });
                    }
                }
                
                // Clone element untuk isolate dari CSS global yang mungkin menggunakan oklch
                const clonedElement = previewElement.cloneNode(true);
                
                // Set styles untuk cloned element
                clonedElement.style.position = 'absolute';
                clonedElement.style.left = '-9999px';
                clonedElement.style.top = '0';
                clonedElement.style.width = previewElement.offsetWidth + 'px';
                clonedElement.style.height = previewElement.offsetHeight + 'px';
                
                // Create wrapper untuk isolate
                const wrapper = document.createElement('div');
                wrapper.style.position = 'absolute';
                wrapper.style.left = '-9999px';
                wrapper.style.top = '0';
                wrapper.style.width = previewElement.offsetWidth + 'px';
                wrapper.style.height = previewElement.offsetHeight + 'px';
                wrapper.style.backgroundColor = '#ffffff';
                wrapper.style.overflow = 'hidden';
                wrapper.appendChild(clonedElement);
                document.body.appendChild(wrapper);
                
                // Wait a bit for layout
                await new Promise(resolve => setTimeout(resolve, 50));
                
                // Convert all image URLs to base64 to avoid CORS issues
                const images = clonedElement.querySelectorAll('img');
                const imagePromises = [];
                
                for (const img of images) {
                    // Skip if no src
                    if (!img.src || img.src === 'undefined' || img.src === 'null') {
                        console.warn('Image has invalid src, skipping:', img);
                        continue;
                    }
                    
                    if (!img.src.startsWith('data:')) {
                        // Get the actual src (might be relative)
                        let imgSrc = img.src;
                        // If it's a relative URL, make it absolute
                        if (imgSrc.startsWith('/')) {
                            imgSrc = window.location.origin + imgSrc;
                        } else if (!imgSrc.startsWith('http') && !imgSrc.startsWith('data:')) {
                            imgSrc = window.location.origin + '/' + imgSrc;
                        }
                        
                        imagePromises.push(
                            this.loadImageAsBase64(imgSrc)
                                .then(base64 => {
                                    if (base64 && base64.startsWith('data:')) {
                                        // Replace image src with base64
                                        return new Promise((resolve) => {
                                            const newImg = new Image();
                                            newImg.crossOrigin = 'anonymous';
                                            newImg.onload = () => {
                                                img.src = base64;
                                                img.style.display = 'block';
                                                img.style.visibility = 'visible';
                                                img.style.opacity = '1';
                                                resolve();
                                            };
                                            newImg.onerror = (error) => {
                                                console.error('Failed to load converted image:', error);
                                                resolve(); // Continue even if fails
                                            };
                                            newImg.src = base64;
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Failed to convert image to base64:', imgSrc, error);
                                })
                        );
                    } else if (img.src.startsWith('data:')) {
                        // Already base64, just wait for it to load
                        imagePromises.push(
                            new Promise((resolve) => {
                                const newImg = new Image();
                                newImg.onload = () => {
                                    img.style.display = 'block';
                                    img.style.visibility = 'visible';
                                    img.style.opacity = '1';
                                    resolve();
                                };
                                newImg.onerror = () => {
                                    console.error('Failed to load base64 image');
                                    resolve(); // Continue even if fails
                                };
                                newImg.src = img.src;
                                setTimeout(() => resolve(), 2000); // Timeout after 2 seconds
                            })
                        );
                    }
                }
                
                // Wait for all image conversions and loads
                await Promise.all(imagePromises);
                
                // Wait for all images to load after conversion
                await this.waitForImagesToLoad(clonedElement);
                
                // Extra delay to ensure everything is rendered
                await new Promise(resolve => setTimeout(resolve, 500));
                
                try {
                    // Convert to canvas using html2canvas
                    const canvas = await html2canvas(clonedElement, {
                        scale: 2, // Higher quality (2x)
                        useCORS: true,
                        logging: false,
                        backgroundColor: '#ffffff',
                        allowTaint: true, // Allow taint karena kita sudah convert ke base64
                        removeContainer: false,
                        onclone: async (clonedDoc, element) => {
                            // Replace semua oklch di style tags dan inline styles
                            try {
                                // Replace di style tags
                                const styleTags = clonedDoc.querySelectorAll('style');
                                styleTags.forEach(styleTag => {
                                    if (styleTag.textContent) {
                                        styleTag.textContent = styleTag.textContent.replace(/oklch\([^)]+\)/gi, (match) => {
                                            return this.convertOklchString(match);
                                        });
                                    }
                                });
                                
                                // Replace di inline styles pada semua elemen
                                const allElements = clonedDoc.querySelectorAll('*');
                                allElements.forEach(el => {
                                    if (el.style) {
                                        // Process each style property
                                        for (let i = 0; i < el.style.length; i++) {
                                            const prop = el.style[i];
                                            const value = el.style.getPropertyValue(prop);
                                            if (value && value.includes('oklch')) {
                                                const converted = value.replace(/oklch\([^)]+\)/gi, (match) => {
                                                    return this.convertOklchString(match);
                                                });
                                                el.style.setProperty(prop, converted);
                                            }
                                        }
                                        
                                        // Also check cssText
                                        if (el.style.cssText) {
                                            el.style.cssText = el.style.cssText.replace(/oklch\([^)]+\)/gi, (match) => {
                                                return this.convertOklchString(match);
                                            });
                                        }
                                    }
                                });
                                
                                // Ensure all images in cloned document are loaded
                                const clonedImages = clonedDoc.querySelectorAll('img');
                                for (const img of clonedImages) {
                                    if (img.src) {
                                        if (!img.complete) {
                                            await new Promise((resolve) => {
                                                img.onload = () => {
                                                    resolve();
                                                };
                                                img.onerror = () => {
                                                    console.error('Cloned image failed to load:', img.src);
                                                    resolve(); // Continue even if image fails
                                                };
                                                setTimeout(() => {
                                                    console.warn('Image load timeout:', img.src);
                                                    resolve();
                                                }, 3000); // Timeout after 3 seconds
                                            });
                                        } else {
                                            
                                        }
                                        // Ensure image is visible
                                        img.style.display = 'block';
                                        img.style.visibility = 'visible';
                                        img.style.opacity = '1';
                                    }
                                }
                            } catch (e) {
                                console.warn('Error processing cloned document:', e);
                            }
                        }
                    });
                    
                    // Convert canvas to blob
                    const blob = await new Promise((resolve, reject) => {
                        canvas.toBlob((blob) => {
                            if (blob) {
                                resolve(blob);
                            } else {
                                reject(new Error('Failed to create blob'));
                            }
                        }, 'image/png', 1.0);
                    });
                    
                    // Restore old sample data
                    this.sampleData = oldSampleData;
                    
                    return blob;
                } finally {
                    // Ensure wrapper (dan cloned element) is removed even on error
                    if (wrapper && wrapper.parentNode) {
                        wrapper.parentNode.removeChild(wrapper);
                    }
                }
            } catch (error) {
                // Restore old sample data on error
                this.sampleData = oldSampleData;
                throw error;
            }
        },
        async downloadSelectedCertificates() {
            if (this.selectedGuestIds.length === 0) {
                await showError('Error', 'Pilih minimal satu tamu');
                return;
            }
            
            this.downloading = true;
            try {
                const selectedGuests = this.checkedInGuests.filter(guest =>
                    this.selectedGuestIds.includes(guest.id_guest || guest.id)
                );
                
                await this.downloadCertificatesForGuests(selectedGuests);
                await showSuccess('Berhasil!', `Sertifikat ${selectedGuests.length} tamu berhasil diunduh`);
            } catch (error) {
                await showError('Error', 'Gagal mengunduh sertifikat');
            } finally {
                this.downloading = false;
            }
        },
        async downloadAllCertificates() {
            if (this.checkedInGuests.length === 0) {
                await showError('Error', 'Tidak ada tamu yang sudah check-in');
                return;
            }
            
            this.downloading = true;
            try {
                await this.downloadCertificatesForGuests(this.checkedInGuests);
                await showSuccess('Berhasil!', `Sertifikat ${this.checkedInGuests.length} tamu berhasil diunduh`);
            } catch (error) {
                await showError('Error', 'Gagal mengunduh sertifikat');
            } finally {
                this.downloading = false;
            }
        },
        convertOklchString(oklchMatch) {
            // Helper function untuk convert oklch string ke rgb
            // Digunakan di onclone callback html2canvas
            try {
                const match = oklchMatch.match(/oklch\(([^)]+)\)/i);
                if (!match || !match[1]) {
                    return 'rgb(128, 128, 128)';
                }
                
                const content = match[1];
                const parts = content.split('/').map(p => p.trim());
                const colorPart = parts[0];
                const alphaPart = parts[1] ? parseFloat(parts[1]) : 1;
                
                const values = colorPart.trim().split(/\s+/).filter(v => v && v.trim() !== '');
                if (values.length === 0) {
                    return 'rgb(128, 128, 128)';
                }
                
                const lightnessStr = values[0] || '50%';
                const lightness = parseFloat(lightnessStr.replace('%', '').trim());
                
                if (isNaN(lightness)) {
                    return 'rgb(128, 128, 128)';
                }
                
                const grayValue = Math.max(0, Math.min(255, Math.round((lightness / 100) * 255)));
                
                if (alphaPart < 1 && alphaPart > 0 && !isNaN(alphaPart)) {
                    return `rgba(${grayValue}, ${grayValue}, ${grayValue}, ${alphaPart})`;
                } else {
                    return `rgb(${grayValue}, ${grayValue}, ${grayValue})`;
                }
            } catch (error) {
                console.warn('Error converting oklch string:', oklchMatch, error);
                return 'rgb(128, 128, 128)';
            }
        },
        async loadImageAsBase64(url) {
            // Convert image URL ke base64 untuk menghindari CORS issues
            try {
                // Handle relative URLs
                let fullUrl = url;
                if (url && !url.startsWith('http') && !url.startsWith('data:')) {
                    // If relative URL, make it absolute
                    if (url.startsWith('/')) {
                        fullUrl = window.location.origin + url;
                    } else {
                        fullUrl = window.location.origin + '/' + url;
                    }
                }
                
                const response = await fetch(fullUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'image/*',
                    },
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const blob = await response.blob();
                
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onloadend = () => {
                        resolve(reader.result);
                    };
                    reader.onerror = (error) => {
                        console.error('FileReader error:', error);
                        reject(error);
                    };
                    reader.readAsDataURL(blob);
                });
            } catch (error) {
                console.error('Error loading image as base64:', url, error);
                // Return original URL if conversion fails
                return url;
            }
        },
        async waitForImagesToLoad(element) {
            // Wait for all images in element to load
            const images = element.querySelectorAll('img');
            const imagePromises = Array.from(images).map(img => {
                if (img.complete) {
                    return Promise.resolve();
                }
                return new Promise((resolve, reject) => {
                    img.onload = resolve;
                    img.onerror = reject;
                    // Timeout after 5 seconds
                    setTimeout(() => reject(new Error('Image load timeout')), 5000);
                });
            });
            
            try {
                await Promise.all(imagePromises);
            } catch (error) {
                console.warn('Some images failed to load:', error);
            }
        },
        async downloadCertificatesForGuests(guests) {
            const total = guests.length;
            for (let i = 0; i < guests.length; i++) {
                const guest = guests[i];
                try {
                    // Show progress
                    if (total > 1) {
                        console.log(`Generating certificate`);
                    }
                    
                    const blob = await this.downloadCertificateAsPNG(guest);
                    
                    // Create download link
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    const fileName = `Sertifikat_${(guest.guest_name || 'Guest').replace(/[^a-zA-Z0-9]/g, '_')}_${this.generateCertificateId(guest)}.png`;
                    link.download = fileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    
                    // Small delay between downloads to prevent browser blocking
                    if (i < guests.length - 1) {
                        await new Promise(resolve => setTimeout(resolve, 800));
                    }
                } catch (error) {
                    console.error(`Error downloading certificate for ${guest.guest_name}:`, error);
                    // Continue with next guest even if one fails
                }
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/certificate-settings.css';
</style>
