<template>
    <div class="guest-checkin-container">
        <div class="card">
            <!-- Loading State -->
            <div v-if="loading" class="loading-state">
                <div class="spinner"></div>
                <p>Memuat data...</p>
            </div>

            <!-- Profile Completion Form -->
            <div v-else-if="showProfileForm" class="profile-form">
                <div class="header">
                    <h1>Lengkapi Profil</h1>
                    <p class="subtitle">Silakan lengkapi data profil Anda untuk melanjutkan check-in</p>
                </div>
                
                <form @submit.prevent="submitProfile" class="form">
                    <!-- Sorted Fields Based on Field Orders -->
                    <template v-for="fieldItem in sortedCompleteProfileFields" :key="`complete-${fieldItem.type}-${fieldItem.key}`">
                        <!-- Default Fields -->
                        <div v-if="fieldItem.type === 'default'" class="form-group">
                            <label class="form-label">
                                {{ fieldItem.label }}
                                <span v-if="fieldItem.required" class="required">*</span>
                            </label>
                            <input 
                                v-model="profileForm[fieldItem.key]" 
                                :type="fieldItem.key === 'guestEmail' ? 'email' : fieldItem.key === 'guestPhone' ? 'tel' : 'text'"
                                class="form-control" 
                                :required="fieldItem.required"
                                :placeholder="`Masukkan ${fieldItem.label.toLowerCase()}${fieldItem.required ? '' : ' (opsional)'}`"
                            />
                        </div>
                        <!-- Custom Fields -->
                        <div v-else-if="fieldItem.type === 'custom' && fieldItem.field" class="form-group">
                            <DynamicFormField
                                :field="fieldItem.field"
                                :model-value="profileForm.custom_fields[parseInt(fieldItem.key)] || getDefaultValue(fieldItem.field)"
                                @update:model-value="updateCustomField(parseInt(fieldItem.key), $event)"
                            />
                        </div>
                    </template>

                    <div v-if="profileError" class="alert alert-error">
                        {{ profileError }}
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" :disabled="profileLoading">
                            <span v-if="profileLoading">Memproses...</span>
                            <span v-else>✓ Lengkapi & Check-in</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Check-in Interface -->
            <div v-else-if="guestData && !checkInResult" class="checkin-interface">
                <div class="header">
                    <h1>Check-in Event</h1>
                    <p class="subtitle">Selamat datang di {{ eventData?.name || 'Event' }}</p>
                </div>

                <div class="guest-info">
                    <div class="info-card">
                        <div class="info-item">
                            <span class="label">Nama</span><span class="colon">:</span>
                            <span class="value">{{ guestData.name }}</span>
                        </div>
                        <div v-if="guestData.title" class="info-item">
                            <span class="label">Title</span><span class="colon">:</span>
                            <span class="value">{{ guestData.title }}</span>
                        </div>
                        <div v-if="eventData" class="info-item">
                            <span class="label">Event</span><span class="colon">:</span>
                            <span class="value">{{ eventData.name }}</span>
                        </div>
                        <div v-if="eventData" class="info-item">
                            <span class="label">Tanggal</span><span class="colon">:</span>
                            <span class="value">{{ formatDate(eventData.date) }}</span>
                        </div>
                        <div v-if="eventData" class="info-item">
                            <span class="label">Lokasi</span><span class="colon">:</span>
                            <span class="value">{{ eventData.location }}</span>
                        </div>
                    </div>
                </div>

                <div class="checkin-status" v-if="guestData.attend">
                    <div class="alert alert-warning">
                        <div class="alert-warning-header">
                            <i class="ri-information-line"></i>
                            <h3>Sudah Check-in</h3>
                        </div>
                        <p class="alert-message">Anda sudah melakukan check-in sebelumnya.</p>
                        <div class="alert-times">
                        <p v-if="guestData.arrival" class="arrival-time">
                                <span class="time-label">Waktu check-in:</span>
                                <span class="time-value">{{ formatDateTime(guestData.arrival) }}</span>
                        </p>
                        <p v-if="guestData.leave && !isDefaultLeaveTime(guestData.leave)" class="arrival-time">
                                <span class="time-label">Waktu check-out:</span>
                                <span class="time-value">{{ formatDateTime(guestData.leave) }}</span>
                        </p>
                        </div>
                    </div>
                </div>

                <div v-else class="checkin-actions">
                    <button @click="performCheckIn" class="btn btn-primary btn-large" :disabled="checkInLoading">
                        <span v-if="checkInLoading">Memproses...</span>
                        <span v-else>✓ Check-in Sekarang</span>
                    </button>
                </div>

                <!-- Show checkout button if already checked in but not checked out -->
                <div v-if="shouldShowCheckoutButton() && !checkInResult" class="checkout-section">
                    <p class="checkout-description">Selesai dengan event? Lakukan check-out untuk mencatat waktu kepergian Anda.</p>
                    <button @click="performCheckOut" class="btn btn-checkout" :disabled="checkOutLoading">
                        <i v-if="!checkOutLoading" class="ri-logout-box-r-line"></i>
                        <span v-if="checkOutLoading">Memproses...</span>
                        <span v-else>Check-out Sekarang</span>
                    </button>
                </div>
            </div>

            <!-- Check-in Result -->
            <div v-if="checkInResult" class="checkin-result">
                <div v-if="checkInResult.success" class="success-container">
                    <div class="success-header">
                        <div class="success-icon-wrapper">
                            <div class="success-icon">✓</div>
                        </div>
                        <div class="success-title-section">
                            <h2>Check-in Berhasil!</h2>
                            <p class="success-message">{{ checkInResult.message || 'Selamat datang!' }}</p>
                        </div>
                    </div>
                    
                    <div v-if="checkInResult.data" class="result-info-card">
                        <div class="info-row">
                            <span class="info-label">Nama</span><span class="colon">:</span>
                            <span class="info-value">{{ checkInResult.data.guest_name || guestData?.name }}</span>
                        </div>
                        <div v-if="checkInResult.data.event_name" class="info-row">
                            <span class="info-label">Event</span><span class="colon">:</span>
                            <span class="info-value">{{ checkInResult.data.event_name }}</span>
                        </div>
                        <div v-if="checkInResult.data.is_first_time" class="first-time-indicator">
                            <i class="ri-checkbox-circle-line"></i>
                            <span>Check-in pertama kali</span>
                        </div>
                    </div>

                    <div v-if="checkInResult.success && shouldShowCheckoutButton()" class="checkout-section">
                        <p class="checkout-description">Selesai dengan event? Lakukan check-out untuk mencatat waktu kepergian Anda.</p>
                        <button @click="performCheckOut" class="btn btn-checkout" :disabled="checkOutLoading">
                            <i v-if="!checkOutLoading" class="ri-logout-box-r-line"></i>
                            <span v-if="checkOutLoading">Memproses...</span>
                            <span v-else>Check-out Sekarang</span>
                        </button>
                    </div>
                    
                    <div v-if="checkInResult.data?.checked_out || (guestData && !shouldShowCheckoutButton() && guestData.attend)" class="checkout-success-badge">
                        <i class="ri-checkbox-circle-line"></i>
                        <span>Check-out berhasil!</span>
                    </div>
                </div>
                <div v-else class="error-container">
                    <div class="error-icon-wrapper">
                        <div class="error-icon">✗</div>
                    </div>
                    <h2>Check-in Gagal</h2>
                    <p>{{ checkInResult.message }}</p>
                </div>
            </div>

            <!-- Error State -->
            <div v-if="error && !loading" class="error-state">
                <div class="alert alert-error">
                    <h2>Terjadi Kesalahan</h2>
                    <p>{{ error }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import DynamicFormField from './DynamicFormField.vue';

export default {
    name: 'GuestCheckIn',
    components: {
        DynamicFormField
    },
    data() {
        return {
            loading: true,
            guestData: null,
            eventData: null,
            customFields: [],
            showProfileForm: false,
            profileForm: {
                guestTitle: '',
                guestName: '',
                guestEmail: '',
                guestPhone: '',
                custom_fields: {}
            },
            profileLoading: false,
            profileError: '',
            checkInLoading: false,
            checkInResult: null,
            checkOutLoading: false,
            error: null,
            urlParams: {
                guestId: null,
                eventId: null,
                token: null
            }
        };
    },
    computed: {
        sortedCompleteProfileFields() {
            return this.getSortedFields();
        },
    },
    mounted() {
        this.checkUrlParams();
    },
    methods: {
        checkUrlParams() {
            const params = new URLSearchParams(window.location.search);
            const guestId = params.get('guestId');
            const eventId = params.get('eventId');
            const token = params.get('token');

            if (!guestId || !eventId || !token) {
                this.error = 'Parameter tidak lengkap. Pastikan QR code valid.';
                this.loading = false;
                return;
            }

            this.urlParams = { guestId, eventId, token };
            this.loadGuestData();
        },
        async loadGuestData() {
            this.loading = true;
            this.error = null;

            try {
                // Get guest data
                const guestResponse = await axios.get('/detail-guest', {
                    params: {
                        token: this.urlParams.token,
                        guestId: this.urlParams.guestId
                    }
                });

                const guest = guestResponse.data;
                
                // Load custom fields
                await this.loadCustomFields();
                // Load field orders for complete_profile form
                await this.fetchFieldOrders();

                // Pre-fill form with existing data
                this.profileForm.guestTitle = guest?.title || '';
                this.profileForm.guestName = guest?.name || '';
                this.profileForm.guestEmail = guest?.email || '';
                this.profileForm.guestPhone = guest?.phone || '';

                // Pre-fill custom fields with existing values
                this.customFields.forEach(field => {
                    const fieldId = parseInt(field.id_field);
                    if (guest?.custom_fields && guest.custom_fields[fieldId]) {
                        const existingValue = guest.custom_fields[fieldId];
                        if (field.field_type === 'checkbox') {
                            // Parse checkbox values
                            try {
                                if (existingValue.raw_value) {
                                    this.profileForm.custom_fields[fieldId] = JSON.parse(existingValue.raw_value);
                                } else if (existingValue.value) {
                                    try {
                                        this.profileForm.custom_fields[fieldId] = JSON.parse(existingValue.value);
                                    } catch {
                                        this.profileForm.custom_fields[fieldId] = Array.isArray(existingValue.value) ? existingValue.value : [];
                                    }
                                } else {
                                    this.profileForm.custom_fields[fieldId] = [];
                                }
                            } catch {
                                this.profileForm.custom_fields[fieldId] = [];
                            }
                        } else if (field.field_type === 'file') {
                            // Keep existing file path
                            this.profileForm.custom_fields[fieldId] = existingValue.file_path || '';
                        } else {
                            // Use raw_value or value
                            this.profileForm.custom_fields[fieldId] = existingValue.raw_value || existingValue.value || '';
                        }
                    } else {
                        // Initialize with default value
                        this.profileForm.custom_fields[fieldId] = this.getDefaultValue(field);
                    }
                });

                // Check if profile is complete
                const isComplete = this.isProfileComplete(guest);
                if (!isComplete) {
                    // Profile incomplete, show form
                    this.showProfileForm = true;
                    this.loading = false;
                    return;
                }

                // Profile complete, get event data
                this.guestData = {
                    name: guest.name,
                    title: guest.title || '',
                    email: guest.email || '',
                    phone: guest.phone || '',
                    attend: guest.attend,
                    arrival: guest.arrival,
                    leave: guest.leave || guest.guest_time_leave || null,
                    guest_time_leave: guest.guest_time_leave || guest.leave || null
                };

                // If guest already checked in but not checked out, prepare to show checkout option
                // Note: We don't set checkInResult here to avoid showing success message
                // Button checkout will be shown in template based on guestData.attend && !isCheckedOut

                // Get event data
                try {
                    const eventResponse = await axios.get(`/event/${this.urlParams.eventId}`, {
                        params: {
                            token: this.urlParams.token
                        }
                    });
                    
                    if (eventResponse.data) {
                        this.eventData = {
                            name: eventResponse.data.name_event,
                            date: eventResponse.data.date_event,
                            location: eventResponse.data.location_event
                        };
                    }
                } catch (err) {
                    // Event data not critical, continue without it
                }

                this.loading = false;
            } catch (error) {
                this.error = error.response?.data?.message || 'Gagal memuat data tamu';
                this.loading = false;
            }
        },
        async performCheckIn() {
            this.checkInLoading = true;
            this.checkInResult = null;

            try {
                const response = await axios.post('/update-guest', {}, {
                    params: {
                        token: this.urlParams.token,
                        eventId: this.urlParams.eventId,
                        guestId: this.urlParams.guestId
                    }
                });

                if (response.data.success) {
                    this.checkInResult = {
                        success: true,
                        message: response.data.message || 'Check-in berhasil!',
                        data: {
                            ...(response.data.data || {}),
                            guest_name: response.data.data?.guest_name || this.guestData?.name,
                            event_name: response.data.data?.event_name || this.eventData?.name,
                            is_first_time: response.data.data?.is_first_time !== undefined ? response.data.data.is_first_time : true,
                            checked_out: false // Explicitly set to false to show checkout button
                        }
                    };
                    // Update guest data
                    if (this.guestData) {
                        this.guestData.attend = true;
                        this.guestData.arrival = new Date().toISOString();
                        this.guestData.leave = null; // Reset leave time on new check-in
                    }
                } else {
                    this.checkInResult = {
                        success: false,
                        message: response.data.message || 'Check-in gagal'
                    };
                }
            } catch (error) {
                this.checkInResult = {
                    success: false,
                    message: error.response?.data?.message || 'Terjadi kesalahan saat check-in'
                };
            } finally {
                this.checkInLoading = false;
            }
        },
        async performCheckOut() {
            this.checkOutLoading = true;

            try {
                const response = await axios.post('/update-guest-leave', {}, {
                    params: {
                        token: this.urlParams.token,
                        eventId: this.urlParams.eventId,
                        guestId: this.urlParams.guestId
                    }
                });

                if (response.data.success) {
                    // Update check-in result to show check-out success
                    this.checkInResult = {
                        success: true,
                        message: response.data.message || 'Check-out berhasil!',
                        data: {
                            guest_name: this.guestData?.name,
                            checked_out: true
                        }
                    };
                    // Update guest data
                    if (this.guestData) {
                        this.guestData.leave = new Date().toISOString();
                    }
                } else {
                    this.checkInResult = {
                        success: false,
                        message: response.data.message || 'Check-out gagal'
                    };
                }
            } catch (error) {
                this.checkInResult = {
                    success: false,
                    message: error.response?.data?.message || 'Terjadi kesalahan saat check-out'
                };
            } finally {
                this.checkOutLoading = false;
            }
        },
        async loadCustomFields() {
            try {
                const response = await axios.get(`/event/${this.urlParams.eventId}/custom-fields`, {
                    params: {
                        token: this.urlParams.token
                    }
                });
                
                if (response.data.success) {
                    this.customFields = response.data.data || [];
                }
            } catch (error) {
                // Custom fields not critical, continue without them
                this.customFields = [];
            }
        },
        async fetchFieldOrders() {
            try {
                const response = await axios.get(`/event/${this.urlParams.eventId}/field-orders`, {
                    params: {
                        token: this.urlParams.token,
                        form_type: 'complete_profile',
                    },
                });
                if (response.data.success) {
                    this.fieldOrders = response.data.data || [];
                } else {
                    this.fieldOrders = [];
                }
            } catch (error) {
                this.fieldOrders = [];
            }
        },
        getSortedFields() {
            const defaultFieldMap = {
                guestName: { type: 'default', key: 'guestName', label: 'Nama', required: true },
                guestTitle: { type: 'default', key: 'guestTitle', label: 'Jabatan', required: false },
                guestEmail: { type: 'default', key: 'guestEmail', label: 'Email', required: false },
                guestPhone: { type: 'default', key: 'guestPhone', label: 'Telepon', required: false },
            };

            const sortedFields = [];

            if (this.fieldOrders.length > 0) {
                // Use saved order
                const sortedOrders = [...this.fieldOrders].sort((a, b) => a.field_order - b.field_order);
                
                for (const order of sortedOrders) {
                    if (!order.is_visible) continue;

                    if (order.field_type === 'default') {
                        const fieldDef = defaultFieldMap[order.field_key];
                        if (fieldDef) {
                            sortedFields.push({ ...fieldDef, order: order.field_order });
                        }
                    } else if (order.field_type === 'custom') {
                        const customField = this.customFields.find(cf => cf.id_field.toString() === order.field_key);
                        if (customField) {
                            sortedFields.push({
                                type: 'custom',
                                key: order.field_key,
                                field: customField,
                                order: order.field_order,
                            });
                        }
                    }
                }
            } else {
                // Use default order if no saved order
                const defaultOrder = ['guestName', 'guestTitle', 'guestEmail', 'guestPhone'];
                defaultOrder.forEach((key, index) => {
                    sortedFields.push({ ...defaultFieldMap[key], order: index });
                });
                
                // Add custom fields at the end
                this.customFields.forEach((customField, index) => {
                    sortedFields.push({
                        type: 'custom',
                        key: customField.id_field.toString(),
                        field: customField,
                        order: defaultOrder.length + index,
                    });
                });
            }

            return sortedFields.sort((a, b) => (a.order || 0) - (b.order || 0));
        },
        async submitProfile() {
            this.profileLoading = true;
            this.profileError = '';

            try {
                // Prepare FormData for file uploads
                const formData = new FormData();
                formData.append('guestName', this.profileForm.guestName);
                if (this.profileForm.guestTitle) {
                    formData.append('guestTitle', this.profileForm.guestTitle);
                }
                if (this.profileForm.guestEmail) {
                    formData.append('guestEmail', this.profileForm.guestEmail);
                }
                if (this.profileForm.guestPhone) {
                    formData.append('guestPhone', this.profileForm.guestPhone);
                }

                // Prepare custom fields data
                const customFieldsData = {};
                for (const field of this.customFields) {
                    const fieldId = parseInt(field.id_field);
                    const value = this.profileForm.custom_fields[fieldId];
                    
                    if (field.field_type === 'file' && value instanceof File) {
                        // File will be handled separately
                        formData.append(`custom_fields[${fieldId}]`, value);
                        customFieldsData[fieldId] = 'file_upload';
                    } else if (value !== null && value !== undefined && value !== '') {
                        if (field.field_type === 'checkbox' && !Array.isArray(value)) {
                            customFieldsData[fieldId] = [value];
                        } else {
                            customFieldsData[fieldId] = value;
                        }
                    } else {
                        // Include empty values for non-file fields to ensure backend processes them
                        if (field.field_type !== 'file') {
                            if (field.field_type === 'checkbox') {
                                customFieldsData[fieldId] = [];
                            } else {
                                customFieldsData[fieldId] = '';
                            }
                        }
                    }
                }

                // Append custom fields as JSON string (always send if there are custom fields)
                if (this.customFields.length > 0) {
                    formData.append('custom_fields', JSON.stringify(customFieldsData));
                }

                const response = await axios.post('/update-profile-and-checkin', formData, {
                    params: {
                        token: this.urlParams.token,
                        eventId: this.urlParams.eventId,
                        guestId: this.urlParams.guestId
                    },
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                if (response.data.success) {
                    // Profile updated and check-in successful
                    this.showProfileForm = false;
                    this.checkInResult = {
                        success: true,
                        message: response.data.message || 'Profil berhasil dilengkapi dan check-in berhasil!',
                        data: {
                            guest_name: this.profileForm.guestName,
                            event_name: this.eventData?.name,
                            is_first_time: true,
                            checked_out: false // Explicitly set to false to show checkout button
                        }
                    };
                    // Update guest data
                    if (this.guestData) {
                        this.guestData.attend = true;
                        this.guestData.arrival = new Date().toISOString();
                    } else {
                        // Initialize guestData if not exists
                        this.guestData = {
                            name: this.profileForm.guestName,
                            title: this.profileForm.guestTitle || '',
                            email: this.profileForm.guestEmail || '',
                            phone: this.profileForm.guestPhone || '',
                            attend: true,
                            arrival: new Date().toISOString()
                        };
                    }
                } else {
                    this.profileError = response.data.message || 'Gagal menyimpan profil';
                }
            } catch (error) {
                this.profileError = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan profil';
            } finally {
                this.profileLoading = false;
            }
        },
        updateCustomField(fieldId, value) {
            if (!this.profileForm.custom_fields) {
                this.profileForm.custom_fields = {};
            }
            const id = parseInt(fieldId);
            this.profileForm.custom_fields[id] = value;
        },
        isProfileComplete(guest) {
            // Check required fields: guestName is always required
            if (!guest || !guest.name || guest.name.trim() === '') {
                return false;
            }

            // Check other basic fields
            if (!guest.title || guest.title.trim() === '') {
                return false;
            }

            if (!guest.email || guest.email.trim() === '') {
                return false;
            }

            if (!guest.phone || guest.phone.trim() === '') {
                return false;
            }

            // Check custom fields that are required
            for (const field of this.customFields) {
                if (field.is_required) {
                    const fieldId = parseInt(field.id_field);
                    let value = null;
                    
                    // First check if value exists in guest custom_fields (from API)
                    if (guest?.custom_fields && guest.custom_fields[fieldId]) {
                        const existingValue = guest.custom_fields[fieldId];
                        if (field.field_type === 'checkbox') {
                            try {
                                if (existingValue.raw_value) {
                                    value = JSON.parse(existingValue.raw_value);
                                } else if (existingValue.value) {
                                    try {
                                        value = JSON.parse(existingValue.value);
                                    } catch {
                                        value = Array.isArray(existingValue.value) ? existingValue.value : [];
                                    }
                                } else {
                                    value = [];
                                }
                            } catch {
                                value = [];
                            }
                        } else if (field.field_type === 'file') {
                            value = existingValue.file_path || '';
                        } else {
                            value = existingValue.raw_value || existingValue.value || '';
                        }
                    } else {
                        // If not in guest data, check profileForm (might be pre-filled)
                        value = this.profileForm.custom_fields[fieldId];
                    }
                    
                    if (field.field_type === 'checkbox') {
                        // Checkbox must have at least one value
                        if (!value || !Array.isArray(value) || value.length === 0) {
                            return false;
                        }
                    } else if (field.field_type === 'file') {
                        // File must have a file_path or a File object
                        if (!value || (typeof value === 'string' && value.trim() === '')) {
                            return false;
                        }
                    } else {
                        // Other fields must have a non-empty value
                        if (!value || (typeof value === 'string' && value.trim() === '')) {
                            return false;
                        }
                    }
                }
            }

            return true;
        },
        getDefaultValue(field) {
            if (field.field_type === 'checkbox') {
                return [];
            }
            return '';
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        formatDateTime(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        isCheckedOut(guest) {
            if (!guest) return false;
            const leaveTime = guest.leave || guest.guest_time_leave;
            if (!leaveTime) return false;
            // If leaveTime is the default timestamp (1970-01-02 00:00:00), guest hasn't checked out
            return !this.isDefaultLeaveTime(leaveTime);
        },
        isDefaultLeaveTime(leaveTime) {
            if (!leaveTime) return true;
            // Check if it's exactly the default timestamp '1970-01-02 00:00:00'
            if (typeof leaveTime === 'string') {
                // Exact match for default timestamp
                if (leaveTime === '1970-01-02 00:00:00') {
                    return true;
                }
                // Also check if it starts with '1970-01-02' (for variations like '1970-01-02 00:00:00.000000')
                if (leaveTime.trim().startsWith('1970-01-02')) {
                    return true;
                }
            }
            // Check if it's a Date object with default timestamp
            try {
                const date = new Date(leaveTime);
                if (isNaN(date.getTime())) return true;
                // Check if it's the default timestamp (1970-01-02 00:00:00)
                // Year 1970, Month 0 (January), Day 2, Time 00:00:00
                if (date.getFullYear() === 1970 && 
                    date.getMonth() === 0 && 
                    date.getDate() === 2 &&
                    date.getHours() === 0 && 
                    date.getMinutes() === 0 && 
                    date.getSeconds() === 0) {
                    return true;
                }
            } catch {
                return true;
            }
            // If leaveTime is not '1970-01-02 00:00:00', return false (already checked out)
            return false;
        },
        shouldShowCheckoutButton() {
            // Show checkout button if:
            // 1. Guest has checked in (attend = true)
            // 2. guest_time_leave is exactly '1970-01-02 00:00:00' (default timestamp = belum check-out)
            if (!this.guestData || !this.guestData.attend) {
                return false;
            }
            const leaveTime = this.guestData.leave || this.guestData.guest_time_leave;
            // If no leave time, show button (belum check-out)
            if (!leaveTime) {
                return true;
            }
            // If leave time is exactly '1970-01-02 00:00:00', show button (belum check-out)
            // If leave time is something else, don't show button (sudah check-out)
            return this.isDefaultLeaveTime(leaveTime);
        }
    }
};
</script>

<style scoped>
@import '../styles/guest-check-in.css';
</style>
