<template>
    <div class="dashboard">
        <!-- Welcome Greeting Card -->
        <div class="welcome-card">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h2 class="greeting">
                        <i :class="greetingIcon"></i>
                        {{ greetingMessage }}
                    </h2>
                    <p class="user-name">{{ userName }}</p>
                    <p class="welcome-subtitle">Selamat datang kembali di MeetPassQR</p>
                </div>
                <div class="welcome-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ totalEvents }}</div>
                            <div class="stat-label">Total Event</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="ri-calendar-check-line"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ upcomingEvents }}</div>
                            <div class="stat-label">Event Mendatang</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="ri-user-line"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ totalGuests }}</div>
                            <div class="stat-label">Total Tamu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-header">
            <h1>Dashboard</h1>
            <button @click="showCreateEvent = true" class="btn btn-primary">
                + Buat Event Baru
            </button>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-group">
                <label class="filter-label">Cari Event</label>
                <input 
                    v-model="searchKeyword" 
                    type="text" 
                    class="form-control filter-input" 
                    placeholder="Cari berdasarkan nama event atau lokasi..."
                />
            </div>
            <div class="filter-group">
                <label class="filter-label">Filter Tanggal</label>
                <DatePicker 
                    v-model="searchDate" 
                    placeholder="Pilih tanggal event" 
                />
            </div>
            <div class="filter-group">
                <label class="filter-label">Status Event</label>
                <select 
                    v-model="searchStatus" 
                    class="form-control filter-input"
                >
                    <option value="">Semua status</option>
                    <option value="today">Berlangsung hari ini</option>
                    <option value="upcoming">Akan datang</option>
                    <option value="past">Sudah berakhir</option>
                </select>
            </div>
            <button v-if="hasActiveFilters" @click="clearFilters" class="btn btn-secondary btn-clear-filter">
                Hapus Filter
            </button>
        </div>

        <!-- Create Event Modal -->
        <Modal :show="showCreateEvent" title="Buat Event Baru" @close="showCreateEvent = false">
            <form @submit.prevent="createEvent">
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
                <button type="button" @click="showCreateEvent = false" class="btn btn-secondary">
                    Batal
                </button>
                <button @click="createEvent" class="btn btn-primary" :disabled="eventLoading">
                    <span v-if="eventLoading" class="loading-spinner"></span>
                    {{ eventLoading ? 'Membuat...' : 'Buat Event' }}
                </button>
            </template>
        </Modal>


        <!-- Events List -->
        <SkeletonLoader v-if="loading" type="grid" :rows="6" />
        <div v-else-if="filteredEvents.length === 0 && !loading" class="empty-state">
            <p v-if="hasActiveFilters">Tidak ada event yang sesuai dengan filter.</p>
            <p v-else>Belum ada event. Buat event baru untuk memulai.</p>
        </div>
        <TransitionGroup v-else name="list" tag="div" class="events-grid">
            <div 
                v-for="event in displayedEvents" 
                :key="event.id" 
                class="event-card"
                :class="`event-card--${getEventStatus(event)}`"
            >
                    <div class="event-header">
                        <h3>{{ event.name_event }}</h3>
                        <div class="event-actions">
                            <span 
                                class="event-status-badge" 
                                :class="`event-status-badge--${getEventStatus(event)}`"
                            >
                                {{ getEventStatusLabel(event) }}
                            </span>
                        </div>
                    </div>
                    <div class="event-card-info">
                        <div class="dashboard-info-item">
                            <div class="dashboard-icon-wrapper">
                                <i class="ri-calendar-line"></i>
                            </div>
                            <div class="dashboard-content">
                                <span class="dashboard-label">Tanggal</span>
                                <span class="dashboard-value">{{ formatDate(event.date_event) }}</span>
                            </div>
                        </div>
                        <div class="dashboard-info-item">
                            <div class="dashboard-icon-wrapper">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div class="dashboard-content">
                                <span class="dashboard-label">Lokasi</span>
                                <span class="dashboard-value">{{ event.location_event }}</span>
                            </div>
                        </div>
                        <div class="dashboard-info-item">
                            <div class="dashboard-icon-wrapper">
                                <i class="ri-user-line"></i>
                            </div>
                            <div class="dashboard-content">
                                <span class="dashboard-label">Total Tamu</span>
                                <span class="dashboard-value">{{ event.guest_total }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="event-footer">
                        <router-link :to="`/event/${event.slug || event.id}`" class="btn btn-primary btn-sm">
                            Kelola Event
                        </router-link>
                        <a :href="`/preview/${event.slug || event.id}`" target="_blank" class="btn btn-success btn-sm">
                            Preview
                        </a>
                    </div>
                </div>
        </TransitionGroup>

        <!-- Pagination -->
        <div v-if="filteredEvents.length > 0 && totalPages > 1" class="pagination-wrapper">
            <div class="pagination-info">
                Menampilkan {{ startIndex + 1 }} - {{ endIndex }} dari {{ filteredEvents.length }} event
            </div>
            <div class="pagination">
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
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Modal from './Modal.vue';
import SkeletonLoader from './SkeletonLoader.vue';
import DatePicker from './DatePicker.vue';
import { showSuccess, showError, showDeleteConfirm } from '../utils/swal.js';

export default {
    name: 'Dashboard',
    components: {
        Modal,
        SkeletonLoader,
        DatePicker
    },
    data() {
        return {
            events: [],
            allEvents: [],
            loading: false,
            showCreateEvent: false,
            eventForm: {
                name_event: '',
                date_event: '',
                location_event: '',
                guest_total: 10
            },
            eventLoading: false,
            eventError: '',
            searchKeyword: '',
            searchDate: '',
            searchStatus: '',
            eventsPerPage: 6,
            currentPage: 1,
            totalEvents: 0,
            loadingMore: false,
            userName: '',
            userEmail: ''
        };
    },
    watch: {
        searchDate() {
            this.filterEvents();
        },
        searchKeyword() {
            this.filterEvents();
        },
        searchStatus() {
            this.filterEvents();
        },
        $route() {
            // Reload user info when route changes (e.g., coming back from settings)
            this.loadUserInfo();
        }
    },
    mounted() {
        this.checkAuth();
        this.loadUserInfo();
        this.fetchEvents();
        
        // Listen for storage changes (when profile is updated in another tab/window)
        window.addEventListener('storage', this.handleStorageChange);
    },
    beforeUnmount() {
        window.removeEventListener('storage', this.handleStorageChange);
    },
    computed: {
        minDate() {
            return new Date().toISOString().split('T')[0];
        },
        greetingMessage() {
            const hour = new Date().getHours();
            if (hour >= 5 && hour < 12) {
                return 'Selamat Pagi';
            } else if (hour >= 12 && hour < 15) {
                return 'Selamat Siang';
            } else if (hour >= 15 && hour < 19) {
                return 'Selamat Sore';
            } else {
                return 'Selamat Malam';
            }
        },
        greetingIcon() {
            const hour = new Date().getHours();
            if (hour >= 5 && hour < 12) {
                return 'ri-sun-line';
            } else if (hour >= 12 && hour < 15) {
                return 'ri-sun-foggy-line';
            } else if (hour >= 15 && hour < 19) {
                return 'ri-moon-cloudy-line';
            } else {
                return 'ri-moon-line';
            }
        },
        totalEvents() {
            return this.allEvents.length;
        },
        upcomingEvents() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return this.allEvents.filter(event => {
                const eventDate = new Date(event.date_event);
                eventDate.setHours(0, 0, 0, 0);
                // Event mendatang = tanggal setelah hari ini
                return eventDate > today;
            }).length;
        },
        totalGuests() {
            return this.allEvents.reduce((total, event) => {
                return total + (event.guest_total || 0);
            }, 0);
        },
        filteredEvents() {
            let filtered = [...this.allEvents];
            
            // Filter by keyword
            if (this.searchKeyword.trim()) {
                const keyword = this.searchKeyword.toLowerCase().trim();
                filtered = filtered.filter(event => 
                    event.name_event.toLowerCase().includes(keyword) ||
                    event.location_event.toLowerCase().includes(keyword)
                );
            }
            
            // Filter by date
            if (this.searchDate) {
                const searchDateStr = new Date(this.searchDate).toISOString().split('T')[0];
                filtered = filtered.filter(event => {
                    const eventDateStr = new Date(event.date_event).toISOString().split('T')[0];
                    return eventDateStr === searchDateStr;
                });
            }

            // Filter by status (past / today / upcoming)
            if (this.searchStatus) {
                filtered = filtered.filter(event => this.getEventStatus(event) === this.searchStatus);
            }
            
            return filtered;
        },
        displayedEvents() {
            // Client-side pagination on filtered results
            const start = (this.currentPage - 1) * this.eventsPerPage;
            const end = start + this.eventsPerPage;
            return this.filteredEvents.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredEvents.length / this.eventsPerPage);
        },
        hasMoreEvents() {
            return this.currentPage < this.totalPages;
        },
        startIndex() {
            return (this.currentPage - 1) * this.eventsPerPage;
        },
        endIndex() {
            const end = this.startIndex + this.eventsPerPage;
            return end > this.filteredEvents.length ? this.filteredEvents.length : end;
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
        },
        hasActiveFilters() {
            return (
                this.searchKeyword.trim() !== '' || 
                this.searchDate !== '' ||
                this.searchStatus !== ''
            );
        }
    },
    methods: {
        getEventStatus(event) {
            if (!event?.date_event) return 'upcoming';

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const eventDate = new Date(event.date_event);
            eventDate.setHours(0, 0, 0, 0);

            if (eventDate.getTime() === today.getTime()) {
                return 'today';
            }

            if (eventDate < today) {
                return 'past';
            }

            return 'upcoming';
        },
        getEventStatusLabel(event) {
            const status = this.getEventStatus(event);
            if (status === 'today') return 'Berlangsung Hari Ini';
            if (status === 'past') return 'Sudah Berakhir';
            return 'Akan Datang';
        },
        checkAuth() {
            const token = localStorage.getItem('token');
            if (!token) {
                this.$router.push('/login');
            }
        },
        loadUserInfo() {
            // Load user info from localStorage
            const userStr = localStorage.getItem('user');
            if (userStr) {
                try {
                    const user = JSON.parse(userStr);
                    const firstName = user.firstname || '';
                    const lastName = user.lastname || '';
                    const email = user.email || '';
                    
                    if (firstName || lastName) {
                        this.userName = `${firstName} ${lastName}`.trim();
                    } else if (email) {
                        // Use email prefix if no name available
                        this.userName = email.split('@')[0];
                    } else {
                        this.userName = 'Pengguna';
                    }
                    
                    this.userEmail = email;
                } catch (e) {
                    this.userName = 'Pengguna';
                }
            } else {
                // Try to fetch from API
                this.fetchUserProfile();
            }
        },
        async fetchUserProfile() {
            try {
                const response = await axios.get('/user/profile', {
                    params: this.getAuthParams()
                });
                if (response.data) {
                    const firstName = response.data.firstname || '';
                    const lastName = response.data.lastname || '';
                    const email = response.data.email || '';
                    
                    if (firstName || lastName) {
                        this.userName = `${firstName} ${lastName}`.trim();
                    } else if (email) {
                        this.userName = email.split('@')[0];
                    } else {
                        this.userName = 'Pengguna';
                    }
                    
                    this.userEmail = email;
                    
                    // Save to localStorage
                    localStorage.setItem('user', JSON.stringify({
                        firstname: firstName,
                        lastname: lastName,
                        email: email
                    }));
                }
            } catch (error) {
                this.userName = 'Pengguna';
            }
        },
        getAuthHeaders() {
            const token = localStorage.getItem('token');
            return {
                params: { token }
            };
        },
        getAuthParams() {
            return { token: localStorage.getItem('token') };
        },
        async fetchEvents() {
            this.loading = true;
            try {
                // Fetch all events for client-side filtering (send large per_page)
                const response = await axios.get('/event', {
                    params: {
                        ...this.getAuthParams(),
                        page: 1,
                        per_page: 10000 // Large number to get all data for client-side filtering
                    }
                });
                // Handle paginated response or array response
                let eventsData = [];
                if (response.data && response.data.data) {
                    eventsData = Array.isArray(response.data.data) ? response.data.data : [];
                    if (response.data.pagination) {
                        this.totalEvents = response.data.pagination.total;
                    } else {
                        this.totalEvents = eventsData.length;
                    }
                } else if (Array.isArray(response.data)) {
                    // Fallback for old API format
                    eventsData = response.data;
                    this.totalEvents = response.data.length;
                } else {
                    eventsData = [];
                    this.totalEvents = 0;
                }
                
                this.allEvents = eventsData;
                this.events = eventsData;
                this.currentPage = 1; // Reset pagination
                
                // Reset to page 1 if current page is beyond available pages
                if (this.currentPage > this.totalPages && this.totalPages > 0) {
                    this.currentPage = 1;
                }
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    localStorage.removeItem('token');
                    this.$router.push('/login');
                } else {
                    this.allEvents = [];
                    this.events = [];
                    this.totalEvents = 0;
                }
            } finally {
                this.loading = false;
            }
        },
        filterEvents() {
            // Reset to first page when filtering
            this.currentPage = 1;
        },
        goToPage(page) {
            if (page === '...' || page < 1 || page > this.totalPages || page === this.currentPage) {
                return;
            }
            this.currentPage = page;
            // Scroll to top of events grid
            const eventsGrid = document.querySelector('.events-grid');
            if (eventsGrid) {
                eventsGrid.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },
        clearFilters() {
            this.searchKeyword = '';
            this.searchDate = '';
            this.searchStatus = '';
            this.currentPage = 1;
        },
        async createEvent() {
            this.eventLoading = true;
            this.eventError = '';
            try {
                const response = await axios.post('/event', this.eventForm, {
                    params: { token: localStorage.getItem('token') }
                });
                if (response.data.success) {
                    this.showCreateEvent = false;
                    this.eventForm = {
                        name_event: '',
                        date_event: '',
                        location_event: '',
                        guest_total: 10
                    };
                    await showSuccess('Berhasil!', 'Event berhasil dibuat');
                    this.fetchEvents();
                }
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal membuat event';
                this.eventError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.eventLoading = false;
            }
        },
        editEvent(event) {
            this.$router.push(`/event/${event.slug || event.id}`);
        },
        async showDeleteConfirm(id) {
            const result = await showDeleteConfirm('Hapus Event', 'Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.');
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/event/${id}`, {
                        params: { token: localStorage.getItem('token') }
                    });
                    await showSuccess('Berhasil!', 'Event berhasil dihapus');
                    this.fetchEvents();
                } catch (error) {
                    await showError('Gagal!', 'Gagal menghapus event');
                }
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
        handleStorageChange(event) {
            if (event.key === 'user') {
                this.loadUserInfo();
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/dashboard.css';
</style>
