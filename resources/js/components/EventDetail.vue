<template>
    <div class="event-detail">
        <div class="header-actions">
            <router-link to="/dashboard" class="btn btn-secondary">
                <i class="ri-arrow-left-line"></i> Kembali
            </router-link>
            <div class="actions">
                <template v-if="loading">
                    <div class="skeleton-btn"></div>
                    <div class="skeleton-btn"></div>
                    <div class="skeleton-btn"></div>
                </template>
                <template v-else>
                    <a v-if="event" :href="`/preview/${event.slug || event.id}`" target="_blank" class="btn btn-success">
                        <i class="ri-eye-line"></i> Preview
                    </a>
                    <button @click="showEditModal = true" class="btn btn-primary" :disabled="!event">
                        <i class="ri-edit-line"></i> Edit Event
                    </button>
                    <button @click="deleteEvent" class="btn btn-danger" :disabled="!event">
                        <i class="ri-delete-bin-line"></i> Hapus Event
                    </button>
                </template>
            </div>
        </div>

        <transition name="fade" mode="out-in">
            <div v-if="loading" class="loading-container" key="loading">
                <SkeletonLoader type="card" />
                <div class="skeleton-tabs">
                    <div class="skeleton-tab" v-for="i in 5" :key="i"></div>
                </div>
                <div class="skeleton-content">
                    <div class="skeleton-line" style="width: 100%; height: 40px; margin-bottom: 20px;"></div>
                    <div class="skeleton-line" style="width: 80%; height: 40px; margin-bottom: 20px;"></div>
                    <div class="skeleton-line" style="width: 60%; height: 40px;"></div>
                </div>
            </div>
        </transition>
        <transition name="fade" mode="out-in">
            <div v-if="!loading && event" key="content">
                <div class="event-info-card">
                <h1>{{ event.name_event }}</h1>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Tanggal:</span>
                        <span class="info-value">{{ formatDate(event.date_event) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Lokasi:</span>
                        <span class="info-value">{{ event.location_event }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total Tamu:</span>
                        <span class="info-value">{{ event.guest_total }}</span>
                    </div>
                </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                <button 
                    @click="activeTab = 'guests'" 
                    :class="['tab', { active: activeTab === 'guests' }]"
                >
                    Tamu ({{ guests.length }})
                </button>
                <button 
                    @click="activeTab = 'sessions'" 
                    :class="['tab', { active: activeTab === 'sessions' }]"
                >
                    Sesi
                </button>
                <button 
                    @click="activeTab = 'custom-fields'" 
                    :class="['tab', { active: activeTab === 'custom-fields' }]"
                >
                    Field Kustom
                </button>
                <button 
                    @click="activeTab = 'settings'" 
                    :class="['tab', { active: activeTab === 'settings' }]"
                >
                    Pengaturan
                </button>
                <button 
                    @click="activeTab = 'certificate'" 
                    :class="['tab', { active: activeTab === 'certificate' }]"
                >
                    Sertifikat
                </button>
                </div>

                <!-- Custom Fields Tab -->
                <div v-if="activeTab === 'custom-fields'" class="tab-content">
                <CustomFieldManager 
                    :event-id="event.id" 
                    @fields-updated="handleFieldsUpdated"
                />
                </div>

                <!-- Settings Tab -->
                <div v-if="activeTab === 'settings'" class="tab-content">
                <SettingsTab 
                    :event="event"
                    :custom-fields="customFields"
                    @event-updated="() => { fetchEvent(); fetchFieldOrders('add'); fetchFieldOrders('edit'); }"
                    @field-orders-updated="() => { fetchFieldOrders('add'); fetchFieldOrders('edit'); }"
                />
                </div>

                <!-- Certificate Tab -->
                <div v-if="activeTab === 'certificate'" class="tab-content">
                <CertificateSettings 
                    :event-id="event.id"
                />
                </div>

                <!-- Guests Tab -->
                <div v-if="activeTab === 'guests'" class="tab-content">
                <div class="section-header">
                    <h2>Daftar Tamu</h2>
                    <div class="actions-group">
                        <!-- Primary Action -->
                        <button @click="openAddGuestModal" class="btn btn-primary btn-primary-action">
                            <i class="ri-add-line"></i> Tambah Tamu
                        </button>
                        
                        <!-- Excel Actions Dropdown -->
                        <div class="dropdown-wrapper">
                            <button class="btn btn-secondary dropdown-toggle" @click="toggleExcelDropdown">
                                <i class="ri-file-excel-line"></i> Excel
                                <i class="ri-arrow-down-s-line dropdown-icon"></i>
                            </button>
                            <!-- Overlay for mobile -->
                            <div 
                                v-if="showExcelDropdown" 
                                class="dropdown-overlay" 
                                @click="showExcelDropdown = false"
                            ></div>
                            <div 
                                v-if="showExcelDropdown" 
                                class="dropdown-menu" 
                                @click.stop
                            >
                                <a :href="getTemplateUrl()" target="_blank" class="dropdown-item" @click="showExcelDropdown = false">
                                    <i class="ri-file-download-line"></i> Download Template
                                </a>
                                <button @click="showImportModal = true; showExcelDropdown = false" class="dropdown-item">
                                    <i class="ri-file-upload-line"></i> Import Excel
                                </button>
                                <button @click="exportExcel(); showExcelDropdown = false" class="dropdown-item" :disabled="filteredGuests.length === 0">
                                    <i class="ri-download-line"></i> Export Excel
                                </button>
                            </div>
                        </div>
                        
                        <!-- QR Actions Dropdown -->
                        <div class="dropdown-wrapper">
                            <button class="btn btn-secondary dropdown-toggle" @click="toggleQRDropdown" :disabled="filteredGuests.length === 0">
                                <i class="ri-qr-code-line"></i> QR Code
                                <i class="ri-arrow-down-s-line dropdown-icon"></i>
                                <span v-if="selectedGuests.length > 0" class="badge-count">{{ selectedGuests.length }}</span>
                            </button>
                            <!-- Overlay for mobile -->
                            <div 
                                v-if="showQRDropdown" 
                                class="dropdown-overlay" 
                                @click="showQRDropdown = false"
                            ></div>
                            <div 
                                v-if="showQRDropdown" 
                                class="dropdown-menu" 
                                @click.stop
                            >
                                <button @click="downloadAllQR(); showQRDropdown = false" class="dropdown-item" :disabled="downloadQRLoading">
                                    <i class="ri-qr-code-line"></i> Download All QR
                                </button>
                                <button @click="downloadSelectedQR(); showQRDropdown = false" class="dropdown-item" :disabled="selectedGuests.length === 0 || downloadQRLoading">
                                    <i class="ri-checkbox-multiple-line"></i> Download Selected ({{ selectedGuests.length }})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Filter -->
                <div class="search-filter">
                    <div class="filter-row">
                        <div class="search-input-wrapper">
                            <i class="ri-search-line search-icon"></i>
                            <input 
                                v-model="searchKeyword" 
                                type="text" 
                                class="search-input" 
                                placeholder="Cari tamu berdasarkan nama, email, telepon, atau alamat..."
                            />
                            <button 
                                v-if="searchKeyword" 
                                @click="searchKeyword = ''" 
                                class="clear-search"
                                data-tooltip="Hapus Pencarian"
                            >
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                        <select v-model="statusFilter" class="status-filter-select">
                            <option value="all">Semua Status</option>
                            <option value="present">Hadir</option>
                            <option value="absent">Tidak Hadir</option>
                        </select>
                        <select v-model="sessionFilter" class="status-filter-select">
                            <option value="all">Semua Sesi</option>
                            <option value="">Tanpa Sesi</option>
                            <option v-for="session in sessions" :key="session.id || session.id_session" :value="String(session.id || session.id_session)">
                                {{ getSessionDisplayText(session.id || session.id_session) || (session.name || session.name_session) }}
                            </option>
                        </select>
                    </div>
                    <div class="search-info" v-if="searchKeyword || statusFilter !== 'all' || sessionFilter !== 'all'">
                        Menemukan {{ filteredGuests.length }} dari {{ guests.length }} tamu
                        <span v-if="statusFilter !== 'all'" class="filter-status-badge">
                            ({{ statusFilter === 'present' ? 'Hadir' : 'Tidak Hadir' }})
                        </span>
                        <span v-if="sessionFilter !== 'all'" class="filter-status-badge">
                            ({{ getSessionFilterLabel() }})
                        </span>
                    </div>
                </div>

                <!-- Add Guest Modal -->
                <Modal :show="showAddGuestModal" title="Tambah Tamu" @close="showAddGuestModal = false">
                        <form @submit.prevent="addGuest">
                            <!-- Sorted Fields Based on Field Orders -->
                            <div v-if="getSortedFields('add').length > 0">
                                <template v-for="fieldItem in getSortedFields('add')" :key="`add-${fieldItem.type}-${fieldItem.key}`">
                                    <!-- Default Fields -->
                                    <div v-if="fieldItem.type === 'default'" class="form-group">
                                    <label class="form-label">
                                        {{ fieldItem.label }}
                                        <span v-if="fieldItem.required" class="required">*</span>
                                    </label>
                                    <!-- Input Field -->
                                    <input 
                                        v-if="fieldItem.component === 'input' && fieldItem.key !== 'guest_label'"
                                        v-model="guestForm[fieldItem.key]" 
                                        :type="fieldItem.key === 'guest_email' ? 'email' : 'text'"
                                        class="form-control" 
                                        :required="fieldItem.required"
                                    />
                                    <!-- Number Input for Label VIP -->
                                    <input 
                                        v-else-if="fieldItem.component === 'input' && fieldItem.key === 'guest_label'"
                                        v-model.number="guestForm[fieldItem.key]" 
                                        type="number" 
                                        class="form-control" 
                                        min="0" 
                                        max="1"
                                    />
                                    <!-- Select2 for Session -->
                                    <Select2 
                                        v-else-if="fieldItem.component === 'select' && fieldItem.key === 'id_session'"
                                        v-model="guestForm[fieldItem.key]" 
                                        :options="{ placeholder: 'Pilih Sesi', allowClear: true }"
                                    >
                                        <option value="">Pilih Sesi</option>
                                        <option v-for="session in sessions" :key="session.id || session.id_session" :value="session.id || session.id_session">
                                            {{ getSessionDisplayText(session.id || session.id_session) || (session.name || session.name_session) }}
                                        </option>
                                    </Select2>
                                    </div>
                                    <!-- Custom Fields -->
                                    <div v-else-if="fieldItem.type === 'custom' && fieldItem.field" class="form-group">
                                    <DynamicFormField
                                        :field="fieldItem.field"
                                        :model-value="guestForm.custom_fields[parseInt(fieldItem.key)] || getDefaultValue(fieldItem.field)"
                                        @update:model-value="updateCustomField(parseInt(fieldItem.key), $event)"
                                    />
                                    </div>
                            </template>
                            </div>
                            <div v-else class="empty-state">
                                <p>Memuat field... ({{ getSortedFields('add').length }} fields)</p>
                            </div>

                            <div v-if="guestError" class="alert alert-error">{{ guestError }}</div>
                        </form>
                        <template #footer>
                            <button type="button" @click="showAddGuestModal = false; resetGuestForm();" class="btn btn-secondary">
                                Batal
                            </button>
                            <button @click="addGuest" class="btn btn-primary" :disabled="guestLoading">
                                <span v-if="guestLoading" class="loading-spinner"></span>
                                {{ guestLoading ? 'Menambah...' : 'Tambah' }}
                            </button>
                        </template>
                </Modal>

                <!-- Import Modal -->
                <Modal :show="showImportModal" title="Import Tamu dari Excel" size="large" @close="showImportModal = false">
                        <form @submit.prevent="importExcel">
                            <div class="form-group">
                                <label class="form-label">File Excel</label>
                                <input type="file" ref="fileInput" @change="handleFileChange" accept=".xlsx,.xls,.csv" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Default Label</label>
                                <input v-model.number="importForm.default_label" type="number" class="form-control" min="0" max="1" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Default Sesi</label>
                                <Select2 v-model="importForm.default_session" :options="{ placeholder: 'Pilih Sesi', allowClear: true }">
                                    <option value="">Pilih Sesi</option>
                                    <option v-for="session in sessions" :key="session.id || session.id_session" :value="session.id || session.id_session">
                                        {{ getSessionDisplayText(session.id || session.id_session) || (session.name || session.name_session) }}
                                    </option>
                                </Select2>
                            </div>
                            <div v-if="importError" class="alert alert-error">{{ importError }}</div>
                            <div v-if="importSuccess" class="alert alert-success">{{ importSuccess }}</div>
                        </form>
                        <template #footer>
                            <button type="button" @click="showImportModal = false" class="btn btn-secondary">
                                Batal
                            </button>
                            <button @click="importExcel" class="btn btn-primary" :disabled="importLoading">
                                <span v-if="importLoading" class="loading-spinner"></span>
                                {{ importLoading ? 'Mengimport...' : 'Import' }}
                            </button>
                        </template>
                </Modal>

                <!-- Edit Guest Modal -->
                <Modal :show="showEditGuestModal" title="Edit Tamu" size="large" @close="closeEditGuestModal">
                    <form @submit.prevent="updateGuest">
                        <!-- Sorted Fields Based on Field Orders -->
                        <div v-if="getSortedFields('edit').length > 0">
                            <template v-for="fieldItem in getSortedFields('edit')" :key="`edit-${fieldItem.type}-${fieldItem.key}`">
                            <!-- Default Fields -->
                            <div v-if="fieldItem.type === 'default'" class="form-group">
                                <label class="form-label">
                                    {{ fieldItem.label }}
                                    <span v-if="fieldItem.required" class="required">*</span>
                                </label>
                                <!-- Input Field -->
                                <input 
                                    v-if="fieldItem.component === 'input' && fieldItem.key !== 'guest_label'"
                                    v-model="guestForm[fieldItem.key]" 
                                    :type="fieldItem.key === 'guest_email' ? 'email' : 'text'"
                                    class="form-control" 
                                    :required="fieldItem.required"
                                />
                                <!-- Number Input for Label VIP -->
                                <input 
                                    v-else-if="fieldItem.component === 'input' && fieldItem.key === 'guest_label'"
                                    v-model.number="guestForm[fieldItem.key]" 
                                    type="number" 
                                    class="form-control" 
                                    min="0" 
                                    max="1"
                                />
                                <!-- Select2 for Session -->
                                <Select2 
                                    v-else-if="fieldItem.component === 'select' && fieldItem.key === 'id_session'"
                                    v-model="guestForm[fieldItem.key]" 
                                    :options="{ placeholder: 'Pilih Sesi', allowClear: true }"
                                >
                                    <option value="">Pilih Sesi</option>
                                    <option v-for="session in sessions" :key="session.id" :value="session.id">
                                        {{ session.name || session.name_session }}
                                    </option>
                                </Select2>
                                </div>
                                <!-- Custom Fields -->
                                <div v-else-if="fieldItem.type === 'custom' && fieldItem.field" class="form-group">
                                <DynamicFormField
                                    :field="fieldItem.field"
                                    :model-value="guestForm.custom_fields[parseInt(fieldItem.key)] || getDefaultValue(fieldItem.field)"
                                    @update:model-value="updateCustomField(parseInt(fieldItem.key), $event)"
                                />
                                </div>
                            </template>
                        </div>
                        <div v-else class="empty-state">
                            <p>Memuat field... ({{ getSortedFields('edit').length }} fields)</p>
                        </div>

                        <div v-if="guestError" class="alert alert-error">{{ guestError }}</div>
                    </form>
                    <template #footer>
                        <button type="button" @click="closeEditGuestModal" class="btn btn-secondary">
                            Batal
                        </button>
                        <button @click="updateGuest" class="btn btn-primary" :disabled="guestLoading">
                            <span v-if="guestLoading" class="loading-spinner"></span>
                            {{ guestLoading ? 'Mengupdate...' : 'Update' }}
                        </button>
                    </template>
                </Modal>

                <!-- Guests Table -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedGuests.length === filteredGuests.length && filteredGuests.length > 0"
                                        @change="toggleSelectAll"
                                        title="Pilih Semua"
                                    >
                                </th>
                                <th style="width: 60px; text-align: center;">No</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Alamat</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th v-for="field in customFields" :key="`header-${field.id_field}`">
                                    {{ field.field_label }}
                                </th>
                                <th>Status</th>
                                <th>Sesi&nbsp;Acara</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th style="text-align: center !important;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(guest, index) in paginatedGuests" :key="guest.id_guest || guest.id">
                                <td class="text-center">
                                    <input 
                                        type="checkbox" 
                                        :value="guest.id_guest || guest.id"
                                        v-model="selectedGuests"
                                    >
                                </td>
                                <td class="text-center">{{ getRowNumber(index) }}</td>
                                <td>{{ guest.guest_name }}</td>
                                <td>{{ guest.guest_title || '-' }}</td>
                                <td>{{ guest.guest_address }}</td>
                                <td>{{ guest.guest_email || '-' }}</td>
                                <td>{{ guest.guest_phone || '-' }}</td>
                                <td v-for="field in customFields" :key="`cell-${field.id_field}-${guest.id_guest || guest.id}`">
                                    <template v-if="guest.custom_fields && guest.custom_fields[field.field_name] && typeof guest.custom_fields[field.field_name] === 'object'">
                                        <a 
                                            v-if="field.field_type === 'file' && guest.custom_fields[field.field_name].file_path"
                                            :href="getFileUrl(guest.custom_fields[field.field_name].file_path)"
                                            target="_blank"
                                            class="file-link"
                                        >
                                            <i class="ri-attachment-line"></i> {{ getFileName(guest.custom_fields[field.field_name].file_path) }}
                                        </a>
                                        <span v-else-if="guest.custom_fields[field.field_name].value">
                                            {{ guest.custom_fields[field.field_name].value }}
                                        </span>
                                        <span v-else-if="guest.custom_fields[field.field_name].raw_value">
                                            {{ guest.custom_fields[field.field_name].raw_value }}
                                        </span>
                                        <span v-else>-</span>
                                    </template>
                                    <span v-else>-</span>
                                </td>
                                <td>
                                    <span :class="['badge', guest.guest_status ? 'badge-success' : 'badge-warning']">
                                        {{ guest.guest_status ? 'Hadir' : 'Belum Hadir' }}
                                    </span>
                                </td>
                                <td>{{ getSessionDisplayText(guest.id_session) || '-' }}</td>
                                <td>
                                    <span v-if="guest.guest_time_arrival && guest.guest_time_arrival !== '1970-01-02 00:00:00'">
                                        {{ formatDateTime(guest.guest_time_arrival) }}
                                    </span>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <span v-if="guest.guest_time_leave && guest.guest_time_leave !== '1970-01-02 00:00:00'">
                                        {{ formatDateTime(guest.guest_time_leave) }}
                                    </span>
                                    <span v-else class="text-muted">-</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a :href="getQRCodeUrl(guest.id_guest || guest.id)" target="_blank" class="btn-icon btn-icon-download" data-tooltip="Download QR">
                                            <i class="ri-download-line"></i>
                                        </a>
                                        <a :href="getPrintUrl(guest.id_guest || guest.id)" target="_blank" class="btn-icon btn-icon-print" data-tooltip="Cetak">
                                            <i class="ri-printer-line"></i>
                                        </a>
                                        <button @click="editGuest(guest)" class="btn-icon btn-icon-edit" data-tooltip="Edit">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button @click="deleteGuest(guest.id_guest || guest.id)" class="btn-icon btn-icon-delete" data-tooltip="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="guests.length > 0" class="pagination-wrapper">
                    <div class="pagination-info">
                        Menampilkan {{ startIndex + 1 }} - {{ endIndex }} dari {{ filteredGuests.length }} tamu
                    </div>
                    <div v-if="totalPages > 1" class="pagination">
                        <button 
                            @click="goToPage(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="pagination-btn"
                            title="Halaman Sebelumnya"
                        >
                            <i class="ri-arrow-left-s-line"></i>
                        </button>
                        <div class="pagination-pages">
                            <button
                                v-for="page in visiblePages"
                                :key="page"
                                @click="goToPage(page)"
                                :class="['pagination-page', { active: page === currentPage }]"
                                :disabled="page === '...'"
                            >
                                {{ page }}
                            </button>
                        </div>
                        <button 
                            @click="goToPage(currentPage + 1)" 
                            :disabled="currentPage === totalPages"
                            class="pagination-btn"
                            title="Halaman Selanjutnya"
                        >
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                    <div class="pagination-per-page">
                        <label>Per halaman:</label>
                        <select v-model="itemsPerPage" @change="currentPage = 1" class="per-page-select">
                            <option :value="10">10</option>
                            <option :value="25">25</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>

                <!-- Sessions Tab -->
                <div v-if="activeTab === 'sessions'" class="tab-content">
                <div class="section-header">
                    <h2>Kelola Sesi</h2>
                    <button @click="showAddSessionModal = true" class="btn btn-primary">
                        <i class="ri-add-line"></i> Tambah Sesi
                    </button>
                </div>

                <!-- Add Session Modal -->
                <Modal :show="showAddSessionModal" title="Tambah Sesi" @close="closeAddSessionModal">
                        <form @submit.prevent="addSession">
                            <div class="form-group">
                                <label class="form-label">Nama Sesi</label>
                                <input v-model="sessionForm.name_session" type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Mulai</label>
                                <input v-model="sessionForm.time_started_session" type="time" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Selesai</label>
                                <input v-model="sessionForm.time_ended_session" type="time" class="form-control" required>
                            </div>
                            <div v-if="sessionError" class="alert alert-error">{{ sessionError }}</div>
                        </form>
                        <template #footer>
                            <button type="button" @click="closeAddSessionModal" class="btn btn-secondary">
                                Batal
                            </button>
                            <button @click="addSession" class="btn btn-primary" :disabled="sessionLoading">
                                <span v-if="sessionLoading" class="loading-spinner"></span>
                                {{ sessionLoading ? 'Menambah...' : 'Tambah' }}
                            </button>
                        </template>
                </Modal>

                <!-- Edit Session Modal -->
                <Modal :show="showEditSessionModal" title="Edit Sesi" @close="closeEditSessionModal">
                        <form @submit.prevent="updateSession">
                            <div class="form-group">
                                <label class="form-label">Nama Sesi</label>
                                <input v-model="sessionForm.name_session" type="text" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Mulai</label>
                                <input v-model="sessionForm.time_started_session" type="time" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Selesai</label>
                                <input v-model="sessionForm.time_ended_session" type="time" class="form-control" required>
                            </div>
                            <div v-if="sessionError" class="alert alert-error">{{ sessionError }}</div>
                        </form>
                        <template #footer>
                            <button type="button" @click="closeEditSessionModal" class="btn btn-secondary">
                                Batal
                            </button>
                            <button @click="updateSession" class="btn btn-primary" :disabled="sessionLoading">
                                <span v-if="sessionLoading" class="loading-spinner"></span>
                                {{ sessionLoading ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                        </template>
                </Modal>

                <!-- Sessions List -->
                <div class="sessions-list">
                    <div v-if="sessions.length === 0" class="empty-state">
                        <i class="ri-calendar-event-line"></i>
                        <p>Belum ada sesi. Klik "Tambah Sesi" untuk membuat sesi baru.</p>
                    </div>
                    <div v-for="session in sessions" :key="session.id || session.id_session" class="session-card">
                        <div class="session-info">
                        <h3>{{ getSessionDisplayText(session.id || session.id_session) || (session.name || session.name_session) }}</h3>
                        </div>
                        <div class="session-actions">
                            <button @click="editSession(session)" class="btn btn-primary btn-sm">
                                <i class="ri-edit-line"></i> Edit
                            </button>
                            <button @click="deleteSession(session.id || session.id_session)" class="btn btn-danger btn-sm">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </transition>

        <!-- Edit Event Modal -->
        <Modal :show="showEditModal && event" title="Edit Event" @close="showEditModal = false">
            <form @submit.prevent="updateEvent">
                    <div class="form-group">
                        <label class="form-label">Nama Event</label>
                        <input v-model="eventForm.name_event" type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Event</label>
                        <DatePicker v-model="eventForm.date_event" :min-date="minDate" placeholder="Pilih tanggal event" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lokasi</label>
                        <input v-model="eventForm.location_event" type="text" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Tamu</label>
                        <input v-model.number="eventForm.guest_total" type="number" class="form-control" min="10" required>
                    </div>
                    <div v-if="eventError" class="alert alert-error">{{ eventError }}</div>
            </form>
            <template #footer>
                    <button type="button" @click="showEditModal = false" class="btn btn-secondary">
                        Batal
                    </button>
                    <button @click="updateEvent" class="btn btn-primary" :disabled="eventLoading">
                        <span v-if="eventLoading" class="loading-spinner"></span>
                        {{ eventLoading ? 'Menyimpan...' : 'Simpan' }}
                    </button>
            </template>
        </Modal>
        
    </div>
</template>

<script>
import axios from 'axios';
import SkeletonLoader from './SkeletonLoader.vue';
import Modal from './Modal.vue';
import DatePicker from './DatePicker.vue';
import CustomFieldManager from './CustomFieldManager.vue';
import DynamicFormField from './DynamicFormField.vue';
import Select2 from './Select2.vue';
import SettingsTab from './SettingsTab.vue';
import CertificateSettings from './CertificateSettings.vue';
import { showSuccess, showError, showDeleteConfirm, showInfo } from '../utils/swal.js';

export default {
    name: 'EventDetail',
    components: {
        SkeletonLoader,
        Modal,
        DatePicker,
        CustomFieldManager,
        DynamicFormField,
        Select2,
        SettingsTab,
        CertificateSettings
    },
    computed: {
        sortedAddGuestFields() {
            // Force reactivity by accessing fieldOrders
            const orders = this.fieldOrders?.add || [];
            const loaded = this.fieldOrdersLoaded?.add || false;
            
            try {
                // Access fieldOrders directly to ensure reactivity
                const result = this.getSortedFields('add');
                return Array.isArray(result) && result.length > 0 ? result : [];
            } catch (error) {
                console.error('Error in sortedAddGuestFields:', error);
                return [];
            }
        },
        sortedEditGuestFields() {
            // Force reactivity by accessing fieldOrders
            const orders = this.fieldOrders?.edit || [];
            const loaded = this.fieldOrdersLoaded?.edit || false;
            
            try {
                // Access fieldOrders directly to ensure reactivity
                const fieldOrdersForEdit = this.fieldOrders?.edit || [];
                const result = this.getSortedFields('edit');
                return Array.isArray(result) && result.length > 0 ? result : [];
            } catch (error) {
                console.error('Error in sortedEditGuestFields:', error);
                return [];
            }
        },
    },
    data() {
        return {
            event: null,
            guests: [],
            sessions: [],
            customFields: [],
            fieldOrders: {
                add: [],
                edit: [],
                complete_profile: []
            },
            loading: true,
            activeTab: 'guests',
            showEditModal: false,
            showAddGuestModal: false,
            showEditGuestModal: false,
            showImportModal: false,
            showAddSessionModal: false,
            showEditSessionModal: false,
            editingSession: null,
            currentPage: 1,
            itemsPerPage: 10,
            totalGuests: 0,
            editingGuest: null,
            eventForm: {},
            guestForm: {
                guest_name: '',
                guest_title: '',
                guest_address: '',
                guest_email: '',
                guest_phone: '',
                id_session: '',
                guest_label: 0,
                custom_fields: {}
            },
            importForm: {
                default_label: 0,
                default_session: ''
            },
            pusher: null,
            channel: null,
            sessionForm: {
                name_session: '',
                time_started_session: '',
                time_ended_session: ''
            },
            eventLoading: false,
            guestLoading: false,
            sessionLoading: false,
            importLoading: false,
            eventError: '',
            guestError: '',
            sessionError: '',
            importError: '',
            importSuccess: '',
            selectedFile: null,
            searchKeyword: '',
            statusFilter: 'all', // 'all', 'present', 'absent'
            sessionFilter: 'all', // 'all', session id, or '' for no session
            selectedGuests: [],
            downloadQRLoading: false,
            showExcelDropdown: false,
            showQRDropdown: false
        };
    },
    computed: {
        minDate() {
            return new Date().toISOString().split('T')[0];
        },
        filteredGuests() {
            let filtered = this.guests;
            
            // Filter by status
            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(guest => {
                    if (this.statusFilter === 'present') {
                        return guest.guest_status === true || guest.guest_status === 1;
                    } else if (this.statusFilter === 'absent') {
                        return !guest.guest_status || guest.guest_status === false || guest.guest_status === 0;
                    }
                    return true;
                });
            }
            
            // Filter by session
            if (this.sessionFilter !== 'all') {
                filtered = filtered.filter(guest => {
                    if (this.sessionFilter === '' || this.sessionFilter === null) {
                        // Filter for guests without session
                        return !guest.id_session || guest.id_session === null || guest.id_session === '';
                    } else {
                        // Filter for specific session (compare as string to handle both string and number)
                        return String(guest.id_session) === String(this.sessionFilter);
                    }
                });
            }
            
            // Filter by keyword
            if (this.searchKeyword && this.searchKeyword.trim() !== '') {
                const keyword = this.searchKeyword.toLowerCase().trim();
                filtered = filtered.filter(guest => {
                    const name = (guest.guest_name || '').toLowerCase();
                    const email = (guest.guest_email || '').toLowerCase();
                    const phone = (guest.guest_phone || '').toLowerCase();
                    const address = (guest.guest_address || '').toLowerCase();
                    const title = (guest.guest_title || '').toLowerCase();
                    const sessionText = this.getSessionDisplayText(guest.id_session)?.toLowerCase() || '';
                
                    return name.includes(keyword) || 
                           email.includes(keyword) || 
                           phone.includes(keyword) || 
                           address.includes(keyword) ||
                           title.includes(keyword) ||
                           sessionText.includes(keyword);
                });
            }
            
            return filtered;
        },
        paginatedGuests() {
            // Client-side pagination on filtered results
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredGuests.slice(start, end);
        },
        totalPages() {
            const total = Math.ceil(this.filteredGuests.length / this.itemsPerPage);
            return total > 0 ? total : 1;
        },
        startIndex() {
            return (this.currentPage - 1) * this.itemsPerPage;
        },
        endIndex() {
            const end = this.startIndex + this.itemsPerPage;
            return end > this.filteredGuests.length ? this.filteredGuests.length : end;
        },
        visiblePages() {
            const pages = [];
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 7) {
                // Show all pages if 7 or less
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first page
                pages.push(1);
                
                if (current <= 4) {
                    // Near the start
                    for (let i = 2; i <= 5; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(total);
                } else if (current >= total - 3) {
                    // Near the end
                    pages.push('...');
                    for (let i = total - 4; i <= total; i++) {
                        pages.push(i);
                    }
                } else {
                    // In the middle
                    pages.push('...');
                    for (let i = current - 1; i <= current + 1; i++) {
                        pages.push(i);
                    }
                    pages.push('...');
                    pages.push(total);
                }
            }
            
            return pages;
        }
    },
    watch: {
        // Watch for custom fields changes to ensure realtime update
        customFields: {
            handler(newFields) {
                // Custom fields changed
            },
            deep: true,
            immediate: true
        },
        // Watch fieldOrders to force computed property update
        'fieldOrders.add': {
            handler() {
                this.$nextTick(() => {
                    this.$forceUpdate();
                });
            },
            deep: true,
        },
        'fieldOrders.edit': {
            handler() {
                this.$nextTick(() => {
                    this.$forceUpdate();
                });
            },
            deep: true,
        },
        searchKeyword() {
            // Reset to first page when search changes
            this.currentPage = 1;
        },
        statusFilter() {
            // Reset to first page when status filter changes
            this.currentPage = 1;
        },
        sessionFilter() {
            // Reset to first page when session filter changes
            this.currentPage = 1;
        }
    },
    async mounted() {
        this.checkAuth();
        
        // Start loading immediately
        this.loading = true;
        const startTime = Date.now();
        const minLoadingTime = 300; // Minimum 300ms untuk smooth transition
        
        try {
            // Step 1: Fetch event first (required for other calls)
            await this.fetchEvent();
            
            // Step 2: Fetch guests and custom fields in parallel (both can run simultaneously)
            const [guestsResult, customFieldsResult] = await Promise.allSettled([
                this.fetchGuests(),
                this.fetchCustomFields()
            ]);
            
            // Step 3: Fetch field orders after custom fields are loaded
            if (customFieldsResult.status === 'fulfilled') {
                await Promise.all([
                    this.fetchFieldOrders('add'),
                    this.fetchFieldOrders('edit')
                ]);
            }
            
            // Ensure minimum loading time for smooth transition
            const elapsedTime = Date.now() - startTime;
            if (elapsedTime < minLoadingTime) {
                await new Promise(resolve => setTimeout(resolve, minLoadingTime - elapsedTime));
            }
        } catch (error) {
            console.error('Error loading event data:', error);
        } finally {
            this.loading = false;
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        if (this.pusher) {
            this.pusher.disconnect();
        }
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        checkAuth() {
            const token = localStorage.getItem('token');
            if (!token) {
                this.$router.push('/login');
            }
        },
        getAuthParams() {
            return { token: localStorage.getItem('token') };
        },
        async fetchEvent() {
            try {
                const eventSlug = this.$route.params.slug;
                const response = await axios.get(`/event/${eventSlug}`, { params: this.getAuthParams() });
                if (response.data) {
                    this.event = response.data;
                    this.eventForm = {
                        name_event: response.data.name_event || '',
                        date_event: response.data.date_event || '',
                        location_event: response.data.location_event || '',
                        guest_total: response.data.guest_total || 10,
                    };
                    // Fetch sessions after event is loaded (non-blocking)
                    this.fetchSessions().catch(err => console.error('Error fetching sessions:', err));
                    // Initialize Pusher after event is loaded (non-blocking)
                    if (!this.pusher) {
                        this.initPusher().catch(err => console.error('Error initializing Pusher:', err));
                    }
                }
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    this.$router.push('/login');
                } else if (error.response && error.response.status === 404) {
                    await showError('Error', 'Event tidak ditemukan');
                    this.$router.push('/dashboard');
                }
                throw error; // Re-throw untuk ditangani di mounted()
            }
        },
        async fetchGuests() {
            try {
                const eventSlug = this.$route.params.slug;
                // Use event.id_event, event.id, or slug as fallback (support both ID and slug)
                const eventId = this.event?.id_event || this.event?.id || eventSlug;
                
                // Fetch all guests for client-side filtering
                // Send large per_page to get all data (or we can fetch without pagination)
                const response = await axios.get('/guest', {
                    params: { 
                        ...this.getAuthParams(), 
                        eventId: eventId || eventSlug,
                        page: 1,
                        per_page: 10000 // Large number to get all data for client-side filtering
                        // Explicitly don't send 'attend' parameter to get all guests
                    }
                });
                // Handle paginated response or array response
                if (response.data && response.data.data) {
                    this.guests = Array.isArray(response.data.data) ? response.data.data : [];
                    if (response.data.pagination) {
                        this.totalGuests = response.data.pagination.total;
                    } else {
                        this.totalGuests = this.guests.length;
                    }
                } else if (Array.isArray(response.data)) {
                    // Fallback for old API format
                    this.guests = response.data;
                    this.totalGuests = response.data.length;
                } else {
                    this.guests = [];
                    this.totalGuests = 0;
                }
                
                // Reset to page 1 if current page is beyond available pages
                if (this.currentPage > this.totalPages && this.totalPages > 0) {
                    this.currentPage = 1;
                }
            } catch (error) {
                this.guests = [];
                this.totalGuests = 0;
            }
        },
        async fetchSessions() {
            try {
                const eventSlug = this.$route.params.slug;
                // Use event.id if available, otherwise use slug (will be resolved by backend)
                const eventId = this.event?.id || eventSlug;
                
                if (!eventId) {
                    this.sessions = [];
                    return;
                }
                
                const response = await axios.get('/session', {
                    params: { ...this.getAuthParams(), eventId: eventId }
                });
                // Handle both array response and object with data property
                if (Array.isArray(response.data)) {
                    this.sessions = response.data;
                } else if (response.data && response.data.data) {
                    this.sessions = response.data.data;
                } else {
                    this.sessions = [];
                }
            } catch (error) {
                console.error('Error fetching sessions:', error);
                this.sessions = [];
            }
        },
        async fetchCustomFields() {
            try {
                const eventSlug = this.$route.params.slug;
                const response = await axios.get(`event/${eventSlug}/custom-fields`, {
                    params: this.getAuthParams()
                });
                if (response.data.success) {
                    this.customFields = response.data.data || [];
                } else {
                    this.customFields = [];
                }
            } catch (error) {
                this.customFields = [];
            }
        },
        async fetchFieldOrders(formType) {
            try {
                const eventSlug = this.$route.params.slug;
                const response = await axios.get(`/event/${eventSlug}/field-orders`, {
                    params: {
                        ...this.getAuthParams(),
                        form_type: formType,
                    },
                });
                
                if (response.data && response.data.success && Array.isArray(response.data.data)) {
                    // Create new object to ensure reactivity
                    this.fieldOrders = {
                        ...this.fieldOrders,
                        [formType]: [...response.data.data]
                    };
                    this.fieldOrdersLoaded = {
                        ...this.fieldOrdersLoaded,
                        [formType]: true
                    };
                } else {
                    this.fieldOrders = {
                        ...this.fieldOrders,
                        [formType]: []
                    };
                    this.fieldOrdersLoaded = {
                        ...this.fieldOrdersLoaded,
                        [formType]: true
                    };
                }
            } catch (error) {
                console.error('Error fetching field orders:', error);
                this.fieldOrders = {
                    ...this.fieldOrders,
                    [formType]: []
                };
                this.fieldOrdersLoaded = {
                    ...this.fieldOrdersLoaded,
                    [formType]: true
                };
            }
        },
        async initPusher() {
            if (!this.event) return;
            
            try {
                const eventSlug = this.$route.params.slug;
                const userToken = localStorage.getItem('token');
                if (!userToken) return;
                
                // Load Pusher dynamically
                import('pusher-js').then((Pusher) => {
                    // Try to get Pusher config from environment or window
                    const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || 
                                     window.PUSHER_KEY || 
                                     (window.Laravel && window.Laravel.pusher && window.Laravel.pusher.key);
                    const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 
                                         window.PUSHER_CLUSTER || 
                                         (window.Laravel && window.Laravel.pusher && window.Laravel.pusher.cluster) ||
                                         'ap1';
                    
                    if (!pusherKey) {
                        // Pusher not configured, skip real-time updates
                        return;
                    }
                    
                    this.pusher = new Pusher.default(pusherKey, {
                        cluster: pusherCluster
                    });
                    
                    // Subscribe to channel using user token
                    this.channel = this.pusher.subscribe(userToken);
                    
                    // Listen for check-in/check-out events
                    const eventId = this.event?.id;
                    this.channel.bind(String(eventId), (data) => {
                        if (data && data.message) {
                            // Refresh guests list when check-in/check-out happens
                            setTimeout(() => {
                                this.fetchGuests();
                                this.fetchEvent(); // Also refresh event to update total
                            }, 500); // Small delay to ensure backend has processed
                        }
                    });
                }).catch(() => {
                    // If Pusher fails to load, skip real-time updates
                });
            } catch (error) {
                // Pusher initialization failed, skip real-time updates
            }
        },
        async handleFieldsUpdated() {
            // Refresh custom fields first
            await this.fetchCustomFields();
            // Refresh field orders as custom fields might have changed
            await this.fetchFieldOrders('add');
            await this.fetchFieldOrders('edit');
            // Then refresh guests to get updated custom field columns
            await this.fetchGuests();
            // Force Vue to re-render
            this.$nextTick(() => {
                this.$forceUpdate();
            });
        },
        async updateEvent() {
            this.eventLoading = true;
            this.eventError = '';
            try {
                const eventSlug = this.$route.params.slug;
                await axios.put(`/event/${eventSlug}`, this.eventForm, {
                    params: this.getAuthParams()
                });
                this.showEditModal = false;
                await showSuccess('Berhasil!', 'Event berhasil diperbarui');
                this.fetchEvent();
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal update event';
                this.eventError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.eventLoading = false;
            }
        },
        async deleteEvent() {
            const result = await showDeleteConfirm('Hapus Event', 'Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.');
            if (result.isConfirmed) {
                try {
                    const eventSlug = this.$route.params.slug;
                    await axios.delete(`/event/${eventSlug}`, { params: this.getAuthParams() });
                    await showSuccess('Berhasil!', 'Event berhasil dihapus');
                    this.$router.push('/dashboard');
                } catch (error) {
                    await showError('Gagal!', 'Gagal menghapus event');
                }
            }
        },
        async addGuest() {
            this.guestLoading = true;
            this.guestError = '';
            try {
                const eventSlug = this.$route.params.slug;
                
                // Prepare form data for file upload
                const formData = new FormData();
                
                // Add regular guest fields
                Object.keys(this.guestForm).forEach(key => {
                    if (key !== 'custom_fields') {
                        const val = this.guestForm[key];
                        if (val !== null && val !== undefined) {
                            formData.append(key, val);
                        }
                    }
                });
                
                // Handle custom fields
                const customFieldsData = {};
                for (const [fieldId, value] of Object.entries(this.guestForm.custom_fields || {})) {
                    const field = this.customFields.find(f => f.id_field == fieldId);
                    if (!field) continue;
                    
                    if (field.field_type === 'file' && value instanceof File) {
                        formData.append(`custom_fields[${fieldId}]`, value);
                        customFieldsData[fieldId] = 'file_upload';
                    } else if (field.field_type === 'file' && typeof value === 'string' && value) {
                        // Keep existing file
                        customFieldsData[fieldId] = value;
                    } else if (value !== null && value !== undefined && value !== '') {
                        customFieldsData[fieldId] = value;
                    }
                }
                
                if (Object.keys(customFieldsData).length > 0) {
                    formData.append('custom_fields', JSON.stringify(customFieldsData));
                }
                
                // Use event.id_event, event.id, or slug as fallback (support both ID and slug)
                const eventId = this.event?.id_event || this.event?.id || eventSlug;
                await axios.post('/guest', formData, {
                    params: { ...this.getAuthParams(), eventId },
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                
                this.showAddGuestModal = false;
                this.resetGuestForm();
                await showSuccess('Berhasil!', 'Tamu berhasil ditambahkan');
                this.fetchGuests();
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal menambah tamu';
                this.guestError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.guestLoading = false;
            }
        },
        async openAddGuestModal() {
            // Ensure custom fields are loaded before opening modal
            if (this.customFields.length === 0) {
                await this.fetchCustomFields();
            }
            // Always fetch field orders to ensure we have the latest data
            await this.fetchFieldOrders('add');
            // Force reactivity update
            this.$forceUpdate();
            this.showAddGuestModal = true;
        },
        resetGuestForm() {
            this.guestForm = {
                guest_name: '',
                guest_title: '',
                guest_address: '',
                guest_email: '',
                guest_phone: '',
                id_session: '',
                guest_label: 0,
                custom_fields: {}
            };
        },
        async editGuest(guest) {
            try {
                // Ensure custom fields are loaded before opening edit modal
                if (this.customFields.length === 0) {
                    await this.fetchCustomFields();
                }
                // Always fetch field orders to ensure we have the latest data
                await this.fetchFieldOrders('edit');
                // Wait for Vue to update reactivity
                await this.$nextTick();
                
                // Validate guest object
                if (!guest) {
                    await showError('Error', 'Data tamu tidak valid');
                    return;
                }
                
                this.editingGuest = guest;
                this.guestForm = {
                    guest_name: guest.guest_name || '',
                    guest_title: guest.guest_title || '',
                    guest_address: guest.guest_address || '',
                    guest_email: guest.guest_email || '',
                    guest_phone: guest.guest_phone || '',
                    id_session: guest.id_session || '',
                    guest_label: guest.guest_label || 0,
                    custom_fields: {}
                };
                
                // Initialize all custom fields with default values first
                for (const field of this.customFields) {
                    const fieldId = parseInt(field.id_field);
                    this.guestForm.custom_fields[fieldId] = this.getDefaultValue(field);
                }
                
                // Load custom field values from guest data
                if (guest.custom_fields && typeof guest.custom_fields === 'object') {
                for (const [fieldName, fieldData] of Object.entries(guest.custom_fields)) {
                    // Skip if fieldData is null or undefined
                    if (!fieldData || typeof fieldData !== 'object') {
                        continue;
                    }
                    
                    const field = this.customFields.find(f => f.field_name === fieldName);
                    if (field) {
                        const fieldId = parseInt(field.id_field);
                        if (field.field_type === 'checkbox') {
                            // Parse checkbox values
                            try {
                                if (fieldData.raw_value) {
                                    this.guestForm.custom_fields[fieldId] = JSON.parse(fieldData.raw_value);
                                } else if (fieldData.value) {
                                    // Try to parse if it's a string
                                    try {
                                        this.guestForm.custom_fields[fieldId] = JSON.parse(fieldData.value);
                                    } catch {
                                        this.guestForm.custom_fields[fieldId] = Array.isArray(fieldData.value) ? fieldData.value : [];
                                    }
                                } else {
                                    this.guestForm.custom_fields[fieldId] = [];
                                }
                            } catch {
                                this.guestForm.custom_fields[fieldId] = [];
                            }
                        } else if (field.field_type === 'file') {
                            // Keep existing file path
                            this.guestForm.custom_fields[fieldId] = fieldData.file_path || '';
                        } else if (field.field_type === 'select' || field.field_type === 'radio') {
                            // For select/radio fields, ALWAYS use raw_value (field_value from database)
                            // because value contains the display label, not the actual option value
                            let fieldValue = (fieldData.raw_value !== null && fieldData.raw_value !== undefined) 
                                ? fieldData.raw_value 
                                : '';
                            
                            // Convert to string to match option values
                            if (fieldValue !== null && fieldValue !== undefined && fieldValue !== '') {
                                fieldValue = String(fieldValue);
                        } else {
                                fieldValue = '';
                            }
                            
                            this.guestForm.custom_fields[fieldId] = fieldValue;
                        } else {
                            // For other field types (input, textarea), use raw_value first, then value
                            let fieldValue = (fieldData.raw_value !== null && fieldData.raw_value !== undefined) 
                                    ? fieldData.raw_value 
                                : (fieldData.value !== null && fieldData.value !== undefined) 
                                    ? fieldData.value 
                                    : '';
                            
                            this.guestForm.custom_fields[fieldId] = fieldValue;
                        }
                    }
                }
            }
                
                this.showEditGuestModal = true;
                
                // Force update Select2 components after modal is opened and DOM is ready
                this.$nextTick(() => {
                    // Wait a bit more to ensure Select2 components are fully rendered
                    setTimeout(() => {
                        // Trigger reactive update for all custom fields
                        // This ensures Select2 components re-initialize with correct values
                        this.$forceUpdate();
                    }, 300);
                });
            } catch (error) {
                await showError('Error', 'Gagal membuka form edit tamu');
            }
        },
        async updateGuest() {
            this.guestLoading = true;
            this.guestError = '';
            try {
                const eventSlug = this.$route.params.slug;
                const guestId = this.editingGuest.id_guest || this.editingGuest.id;
                
                // Prepare form data for file upload
                const formData = new FormData();
                
                // Add regular guest fields
                Object.keys(this.guestForm).forEach(key => {
                    if (key !== 'custom_fields') {
                        const val = this.guestForm[key];
                        if (val !== null && val !== undefined) {
                            formData.append(key, val);
                        }
                    }
                });
                
                // Handle custom fields
                const customFieldsData = {};
                // Process all custom fields, including empty ones
                
                for (const field of this.customFields) {
                    // Ensure fieldId is a number (not string)
                    const fieldId = parseInt(field.id_field);
                    const value = this.guestForm.custom_fields[fieldId] || this.guestForm.custom_fields[field.id_field];
                    
                    if (field.field_type === 'file' && value instanceof File) {
                        formData.append(`custom_fields[${fieldId}]`, value);
                        customFieldsData[fieldId] = 'file_upload';
                    } else if (field.field_type === 'file' && typeof value === 'string' && value) {
                        // Keep existing file
                        customFieldsData[fieldId] = value;
                    } else if (value !== null && value !== undefined && value !== '') {
                        // For checkbox, ensure it's an array
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
                
                // Always send custom_fields, even if empty, to ensure backend processes all fields
                // Append as JSON string - make sure it's a string
                const customFieldsJson = JSON.stringify(customFieldsData);
                formData.append('custom_fields', customFieldsJson);
                
                // Don't set Content-Type header manually - let axios set it with boundary
                // Use event.id_event, event.id, or slug as fallback (support both ID and slug)
                const eventId = this.event?.id_event || this.event?.id || eventSlug;
                await axios.put(`/guest/${guestId}`, formData, {
                    params: { ...this.getAuthParams(), eventId }
                    // Remove Content-Type header - axios will set it automatically with boundary
                });
                
                this.showEditGuestModal = false;
                this.editingGuest = null;
                this.resetGuestForm();
                await showSuccess('Berhasil!', 'Tamu berhasil diperbarui');
                this.fetchGuests();
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal update tamu';
                this.guestError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.guestLoading = false;
            }
        },
        async deleteGuest(id) {
            const result = await showDeleteConfirm('Hapus Tamu', 'Apakah Anda yakin ingin menghapus tamu ini?');
            if (result.isConfirmed) {
                try {
                    const eventSlug = this.$route.params.slug;
                    // Use event.id_event, event.id, or slug as fallback (support both ID and slug)
                    const eventId = this.event?.id_event || this.event?.id || eventSlug;
                    await axios.delete(`/guest/${id}`, { 
                        params: { ...this.getAuthParams(), eventId } 
                    });
                    await showSuccess('Berhasil!', 'Tamu berhasil dihapus');
                    this.fetchGuests();
                } catch (error) {
                    await showError('Gagal!', 'Gagal menghapus tamu');
                }
            }
        },
        handleFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        async importExcel() {
            if (!this.selectedFile) {
                this.importError = 'Pilih file terlebih dahulu';
                return;
            }
            this.importLoading = true;
            this.importError = '';
            this.importSuccess = '';
            try {
                const formData = new FormData();
                formData.append('excel_file', this.selectedFile);
                formData.append('default_label', this.importForm.default_label);
                if (this.importForm.default_session) {
                    formData.append('default_session', this.importForm.default_session);
                }
                const eventSlug = this.$route.params.slug;
                // Use event.id_event, event.id, or slug as fallback (support both ID and slug)
                const eventId = this.event?.id_event || this.event?.id || eventSlug;
                const response = await axios.post('/import-excel', formData, {
                    params: { ...this.getAuthParams(), eventId },
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                if (response.data.success) {
                    this.showImportModal = false;
                    await showSuccess('Berhasil!', `${response.data.data.total_imported} tamu berhasil diimport`);
                    this.fetchGuests();
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal import';
                this.importError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.importLoading = false;
            }
        },
        closeAddSessionModal() {
            this.showAddSessionModal = false;
            this.sessionForm = {
                name_session: '',
                time_started_session: '',
                time_ended_session: ''
            };
            this.sessionError = '';
        },
        async addSession() {
            this.sessionLoading = true;
            this.sessionError = '';
            try {
                const eventSlug = this.$route.params.slug;
                const eventId = this.event?.id || eventSlug;
                await axios.post('/session', this.sessionForm, {
                    params: { ...this.getAuthParams(), eventId }
                });
                this.closeAddSessionModal();
                await showSuccess('Berhasil!', 'Sesi berhasil ditambahkan');
                await this.fetchSessions();
            } catch (error) {
                // Handle validation errors
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    if (errors.name_session) {
                        this.sessionError = errors.name_session[0];
                    } else {
                        this.sessionError = Object.values(errors).flat()[0];
                    }
                } else {
                    this.sessionError = error.response?.data?.message || 'Gagal menambah sesi';
                }
                await showError('Gagal!', this.sessionError);
            } finally {
                this.sessionLoading = false;
            }
        },
        editSession(session) {
            this.editingSession = session;
            
            // Format waktu menjadi H:i (jam:menit) tanpa detik untuk input type="time"
            const formatTimeForInput = (timeStr) => {
                if (!timeStr) return '';
                // Jika sudah format H:i, return langsung
                if (timeStr.match(/^\d{2}:\d{2}$/)) return timeStr;
                // Jika format H:i:s, ambil hanya H:i
                if (timeStr.match(/^\d{2}:\d{2}:\d{2}/)) return timeStr.substring(0, 5);
                // Jika format lain, coba parse
                try {
                    const date = new Date('2000-01-01 ' + timeStr);
                    if (!isNaN(date.getTime())) {
                        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                    }
                } catch (e) {
                    // Jika gagal parse, return as is
                }
                return timeStr;
            };
            
            this.sessionForm = {
                name_session: session.name || session.name_session || '',
                time_started_session: formatTimeForInput(session.time_started || session.time_started_session || ''),
                time_ended_session: formatTimeForInput(session.time_ended || session.time_ended_session || '')
            };
            this.sessionError = '';
            this.showEditSessionModal = true;
        },
        closeEditSessionModal() {
            this.showEditSessionModal = false;
            this.editingSession = null;
                this.sessionForm = {
                    name_session: '',
                    time_started_session: '',
                    time_ended_session: ''
                };
            this.sessionError = '';
        },
        async updateSession() {
            if (!this.editingSession) return;
            
            this.sessionLoading = true;
            this.sessionError = '';
            try {
                const eventSlug = this.$route.params.slug;
                const sessionId = this.editingSession.id || this.editingSession.id_session;
                
                const eventId = this.event?.id || eventSlug;
                await axios.put(`/session/${sessionId}`, this.sessionForm, {
                    params: { ...this.getAuthParams(), eventId }
                });
                
                this.closeEditSessionModal();
                await showSuccess('Berhasil!', 'Sesi berhasil diperbarui');
                await this.fetchSessions();
            } catch (error) {
                // Handle validation errors
                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    if (errors.name_session) {
                        this.sessionError = errors.name_session[0];
                    } else {
                        this.sessionError = Object.values(errors).flat()[0];
                    }
                } else {
                    this.sessionError = error.response?.data?.message || 'Gagal memperbarui sesi';
                }
                await showError('Gagal!', this.sessionError);
            } finally {
                this.sessionLoading = false;
            }
        },
        async deleteSession(id) {
            const result = await showDeleteConfirm('Hapus Sesi', 'Apakah Anda yakin ingin menghapus sesi ini? Tamu yang terdaftar dalam sesi ini tidak akan terpengaruh.');
            if (result.isConfirmed) {
                try {
                    const eventSlug = this.$route.params.slug;
                    const eventId = this.event?.id || eventSlug;
                    await axios.delete(`/session/${id}`, {
                        params: { ...this.getAuthParams(), eventId }
                    });
                    await showSuccess('Berhasil!', 'Sesi berhasil dihapus');
                    await this.fetchSessions();
                } catch (error) {
                    const errorMessage = error.response?.data?.message || 'Gagal menghapus sesi';
                    await showError('Gagal!', errorMessage);
                }
            }
        },
        getQRCodeUrl(guestId) {
            const token = localStorage.getItem('token');
            const eventId = this.event?.id;
            return `/api/download-qr?token=${token}&eventId=${eventId}&guestId=${guestId}`;
        },
        getPrintUrl(guestId) {
            const token = localStorage.getItem('token');
            const eventId = this.event?.id;
            return `/api/print-invitation?token=${token}&eventId=${eventId}&guestId=${guestId}`;
        },
        getTemplateUrl() {
            const token = localStorage.getItem('token');
            const eventId = this.event?.id;
            // Use full path since this is used in href attribute
            return `/api/download-template?token=${token}&eventId=${eventId}`;
        },
        toggleExcelDropdown() {
            this.showExcelDropdown = !this.showExcelDropdown;
            if (this.showExcelDropdown) {
                this.showQRDropdown = false;
            }
        },
        toggleQRDropdown() {
            this.showQRDropdown = !this.showQRDropdown;
            if (this.showQRDropdown) {
                this.showExcelDropdown = false;
            }
        },
        handleClickOutside(event) {
            // Close dropdowns if clicking outside or on overlay
            if (!event.target.closest('.dropdown-wrapper') || event.target.classList.contains('dropdown-overlay')) {
                this.showExcelDropdown = false;
                this.showQRDropdown = false;
            }
        },
        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedGuests = this.filteredGuests.map(guest => guest.id_guest || guest.id);
            } else {
                this.selectedGuests = [];
            }
        },
        async downloadAllQR() {
            this.downloadQRLoading = true;
            try {
                const token = localStorage.getItem('token');
                const eventSlug = this.$route.params.slug;
                
                // Use axios to download with proper baseURL
                const eventId = this.event?.id;
                const response = await axios.get('/download-all-qr', {
                    params: { token, eventId },
                    responseType: 'blob'
                });
                
                // Create blob and download
                const blob = new Blob([response.data], { type: 'application/zip' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                const eventName = this.event?.name_event || 'Event';
                link.download = `QR_Codes_All_${eventName}.zip`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                await showSuccess('Berhasil!', 'Download semua QR code berhasil');
            } catch (error) {
                await showError('Gagal!', 'Gagal download QR code');
            } finally {
                this.downloadQRLoading = false;
            }
        },
        async downloadSelectedQR() {
            if (this.selectedGuests.length === 0) {
                await showError('Peringatan', 'Pilih tamu terlebih dahulu');
                return;
            }
            
            this.downloadQRLoading = true;
            try {
                const token = localStorage.getItem('token');
                const eventSlug = this.$route.params.slug;
                
                const eventId = this.event?.id;
                const response = await axios.post('/download-selected-qr', {
                    guestIds: this.selectedGuests
                }, {
                    params: { token, eventId },
                    responseType: 'blob'
                });
                
                // Create blob and download
                const blob = new Blob([response.data], { type: 'application/zip' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                const eventName = this.event?.name_event || 'Event';
                link.download = `QR_Codes_Selected_${eventName}.zip`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
                
                await showSuccess('Berhasil!', `${this.selectedGuests.length} QR code berhasil didownload`);
                this.selectedGuests = [];
            } catch (error) {
                await showError('Gagal!', 'Gagal download QR code');
            } finally {
                this.downloadQRLoading = false;
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        updateCustomField(fieldId, value) {
            if (!this.guestForm.custom_fields) {
                this.guestForm.custom_fields = {};
            }
            // Ensure fieldId is integer
            const id = parseInt(fieldId);
            // Vue 3: Direct assignment works with reactivity
            this.guestForm.custom_fields[id] = value;
        },
        getDefaultValue(field) {
            if (field.field_type === 'checkbox') {
                return [];
            }
            return '';
        },
        getFileName(filePath) {
            if (!filePath) return '';
            return filePath.split('/').pop();
        },
        getFileUrl(filePath) {
            if (!filePath) return '#';
            return `/storage/${filePath}`;
        },
        getSortedFields(formType) {
            // Ensure fieldOrders[formType] exists and is an array
            const orders = (this.fieldOrders && this.fieldOrders[formType] && Array.isArray(this.fieldOrders[formType])) 
                ? this.fieldOrders[formType] 
                : [];
            
            const defaultFieldMap = {
                add: {
                    guest_name: { type: 'default', key: 'guest_name', label: 'Nama', required: true, component: 'input' },
                    guest_title: { type: 'default', key: 'guest_title', label: 'Jabatan', required: false, component: 'input' },
                    guest_address: { type: 'default', key: 'guest_address', label: 'Alamat', required: true, component: 'input' },
                    guest_email: { type: 'default', key: 'guest_email', label: 'Email', required: false, component: 'input' },
                    guest_phone: { type: 'default', key: 'guest_phone', label: 'Telepon', required: false, component: 'input' },
                    id_session: { type: 'default', key: 'id_session', label: 'Sesi', required: false, component: 'select' },
                    guest_label: { type: 'default', key: 'guest_label', label: 'Label VIP', required: false, component: 'input' },
                },
                edit: {
                    guest_name: { type: 'default', key: 'guest_name', label: 'Nama', required: true, component: 'input' },
                    guest_title: { type: 'default', key: 'guest_title', label: 'Jabatan', required: false, component: 'input' },
                    guest_address: { type: 'default', key: 'guest_address', label: 'Alamat', required: true, component: 'input' },
                    guest_email: { type: 'default', key: 'guest_email', label: 'Email', required: false, component: 'input' },
                    guest_phone: { type: 'default', key: 'guest_phone', label: 'Telepon', required: false, component: 'input' },
                    id_session: { type: 'default', key: 'id_session', label: 'Sesi', required: false, component: 'select' },
                    guest_label: { type: 'default', key: 'guest_label', label: 'Label VIP', required: false, component: 'input' },
                }
            };

            const fieldMap = defaultFieldMap[formType] || {};
            const sortedFields = [];

            // Check if we have saved orders
            if (orders && orders.length > 0 && Array.isArray(orders)) {
                // Use saved order
                const sortedOrders = [...orders].sort((a, b) => (a.field_order || 0) - (b.field_order || 0));
                
                for (const order of sortedOrders) {
                    // Skip if not visible
                    if (order.is_visible === false) continue;

                    if (order.field_type === 'default') {
                        const fieldDef = fieldMap[order.field_key];
                        if (fieldDef) {
                            sortedFields.push({ ...fieldDef, order: order.field_order || 0 });
                        }
                    } else if (order.field_type === 'custom') {
                        const customField = this.customFields.find(cf => cf.id_field && cf.id_field.toString() === order.field_key.toString());
                        if (customField) {
                            sortedFields.push({
                                type: 'custom',
                                key: order.field_key.toString(),
                                field: customField,
                                order: order.field_order || 0,
                            });
                        }
                    }
                }
            }
            
            // Always use default order as fallback or if no saved orders
            // This ensures fields are always shown even if orders haven't been saved yet
            if (sortedFields.length === 0) {
                // Use default order
                const defaultOrder = Object.keys(fieldMap);
                defaultOrder.forEach((key, index) => {
                    sortedFields.push({ ...fieldMap[key], order: index });
                });
                
                // Add custom fields at the end
                if (this.customFields && this.customFields.length > 0) {
                    this.customFields.forEach((customField, index) => {
                        sortedFields.push({
                            type: 'custom',
                            key: customField.id_field.toString(),
                            field: customField,
                            order: defaultOrder.length + index,
                        });
                    });
                }
            } else {
                // We have saved orders, but check if all default fields are included
                // If not, add missing ones (in case new default fields were added)
                const includedKeys = sortedFields.filter(f => f.type === 'default').map(f => f.key);
                const defaultOrder = Object.keys(fieldMap);
                defaultOrder.forEach((key) => {
                    if (!includedKeys.includes(key)) {
                        // Add missing default field at the end
                        sortedFields.push({ ...fieldMap[key], order: sortedFields.length });
                    }
                });
            }

            const result = sortedFields.sort((a, b) => (a.order || 0) - (b.order || 0));
            
            // Always return an array, never undefined or null
            return result.length > 0 ? result : [];
        },
        closeEditGuestModal() {
            this.showEditGuestModal = false;
            this.editingGuest = null;
            this.resetGuestForm();
            this.guestError = '';
        },
        formatDateTime(dateTime) {
            if (!dateTime) return '-';
            
            // Check if it's the default/null timestamp
            if (typeof dateTime === 'string' && dateTime === '1970-01-02 00:00:00') {
                return '-';
            }
            
            // Handle both string and Date object
            let date;
            if (typeof dateTime === 'string') {
                // Handle different date formats
                if (dateTime.includes('T')) {
                    date = new Date(dateTime);
                } else {
                    // Format: YYYY-MM-DD HH:mm:ss
                    date = new Date(dateTime.replace(' ', 'T'));
                }
            } else if (dateTime instanceof Date) {
                date = dateTime;
            } else {
                return '-';
            }
            
            if (isNaN(date.getTime())) return '-';
            
            // Check if it's the default timestamp (1970-01-02)
            if (date.getFullYear() === 1970 && date.getMonth() === 0 && date.getDate() === 2) {
                return '-';
            }
            
            // Format: DD/MM/YYYY HH:mm
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        },
        formatExcelDateTime(dateTime) {
            if (!dateTime) return '-';
            
            // Check if it's the default/null timestamp
            if (typeof dateTime === 'string' && dateTime === '1970-01-02 00:00:00') {
                return '-';
            }
            
            // Handle both string and Date object
            let date;
            if (typeof dateTime === 'string') {
                // Handle different date formats
                if (dateTime.includes('T')) {
                    date = new Date(dateTime);
                } else {
                    // Format: YYYY-MM-DD HH:mm:ss
                    date = new Date(dateTime.replace(' ', 'T'));
                }
            } else if (dateTime instanceof Date) {
                date = dateTime;
            } else {
                return '-';
            }
            
            if (isNaN(date.getTime())) return '-';
            
            // Check if it's the default timestamp (1970-01-02)
            if (date.getFullYear() === 1970 && date.getMonth() === 0 && date.getDate() === 2) {
                return '-';
            }
            
            // Format untuk Excel: DD/MM/YYYY HH:mm:ss (dengan detik dan format standar)
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        },
        getRowNumber(index) {
            return this.startIndex + index + 1;
        },
        getSessionName(sessionId) {
            if (!sessionId) return null;
            const session = this.sessions.find(s => String(s.id || s.id_session) === String(sessionId));
            return session ? (session.name || session.name_session) : null;
        },
        getSessionDisplayText(sessionId) {
            if (!sessionId) return null;
            const session = this.sessions.find(s => String(s.id || s.id_session) === String(sessionId));
            if (!session) return null;
            
            const sessionName = session.name || session.name_session || '';
            let timeStart = session.time_started || session.time_started_session || '';
            let timeEnd = session.time_ended || session.time_ended_session || '';
            
            // Format waktu menjadi H:i (jam:menit) tanpa detik
            const formatTime = (timeStr) => {
                if (!timeStr) return '';
                // Jika sudah format H:i, return langsung
                if (timeStr.match(/^\d{2}:\d{2}$/)) return timeStr;
                // Jika format H:i:s, ambil hanya H:i
                if (timeStr.match(/^\d{2}:\d{2}:\d{2}/)) return timeStr.substring(0, 5);
                // Jika format lain, coba parse
                try {
                    const date = new Date('2000-01-01 ' + timeStr);
                    if (!isNaN(date.getTime())) {
                        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
                    }
                } catch (e) {
                    // Jika gagal parse, return as is
                }
                return timeStr;
            };
            
            timeStart = formatTime(timeStart);
            timeEnd = formatTime(timeEnd);
            
            if (timeStart && timeEnd) {
                return `${sessionName}, ${timeStart} - ${timeEnd}`;
            } else if (timeStart) {
                return `${sessionName}, ${timeStart}`;
            }
            return sessionName;
        },
        getSessionFilterLabel() {
            if (this.sessionFilter === 'all') return '';
            if (this.sessionFilter === '' || this.sessionFilter === null) return 'Tanpa Sesi';
            return this.getSessionDisplayText(this.sessionFilter) || 'Sesi';
        },
        goToPage(page) {
            if (page === '...' || page < 1 || page > this.totalPages || page === this.currentPage) {
                return;
            }
            this.currentPage = page;
            // Scroll to top of table
            this.$nextTick(() => {
                const tableContainer = document.querySelector('.table-container');
                if (tableContainer) {
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        },
        async exportExcel() {
            try {
                const eventSlug = this.$route.params.slug;
                const guestsToExport = this.filteredGuests;
                
                if (guestsToExport.length === 0) {
                    await showError('Error', 'Tidak ada data untuk diekspor');
                    return;
                }
                
                // Prepare data for export
                const exportData = guestsToExport.map(guest => {
                    const row = {
                        'Nama': guest.guest_name || '',
                        'Title': guest.guest_title || '',
                        'Alamat': guest.guest_address || '',
                        'Email': guest.guest_email || '',
                        'Telepon': guest.guest_phone || '',
                        'Sesi': this.getSessionDisplayText(guest.id_session) || '-',
                        'Status': guest.guest_status ? 'Hadir' : 'Belum Hadir',
                        'Check-in': guest.guest_time_arrival && guest.guest_time_arrival !== '1970-01-02 00:00:00' 
                            ? this.formatExcelDateTime(guest.guest_time_arrival) 
                            : '-',
                        'Check-out': guest.guest_time_leave && guest.guest_time_leave !== '1970-01-02 00:00:00' 
                            ? this.formatExcelDateTime(guest.guest_time_leave) 
                            : '-',
                    };
                    
                    // Add custom fields
                    this.customFields.forEach(field => {
                        const fieldValue = guest.custom_fields && guest.custom_fields[field.field_name];
                        if (fieldValue) {
                            if (typeof fieldValue === 'object' && fieldValue.value) {
                                row[field.field_label] = fieldValue.value;
                            } else if (typeof fieldValue === 'object' && fieldValue.raw_value) {
                                row[field.field_label] = fieldValue.raw_value;
                            } else if (Array.isArray(fieldValue)) {
                                row[field.field_label] = fieldValue.join(', ');
                            } else {
                                row[field.field_label] = fieldValue;
                            }
                        } else {
                            row[field.field_label] = '-';
                        }
                    });
                    
                    return row;
                });
                
                // Convert to CSV format with semicolon separator for better Excel compatibility
                const headers = Object.keys(exportData[0]);
                const separator = ';'; // Use semicolon for Excel compatibility
                const csvContent = [
                    headers.join(separator),
                    ...exportData.map(row => 
                        headers.map(header => {
                            const value = row[header] || '';
                            // Escape quotes and wrap in quotes if contains separator, comma, newline, or quotes
                            const escaped = String(value).replace(/"/g, '""');
                            // Wrap in quotes if value contains separator, comma, newline, or quotes
                            if (escaped.includes(separator) || escaped.includes(',') || escaped.includes('\n') || escaped.includes('"')) {
                            return `"${escaped}"`;
                            }
                            return escaped;
                        }).join(separator)
                    )
                ].join('\n');
                
                // Add BOM for Excel UTF-8 support
                const BOM = '\uFEFF';
                const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                
                link.setAttribute('href', url);
                link.setAttribute('download', `Daftar_Tamu_${this.event.name_event || 'Event'}_${new Date().toISOString().split('T')[0]}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                await showSuccess('Berhasil!', `Data ${guestsToExport.length} tamu berhasil diekspor`);
            } catch (error) {
                await showError('Error', 'Gagal mengekspor data');
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/event-detail.css';
</style>
