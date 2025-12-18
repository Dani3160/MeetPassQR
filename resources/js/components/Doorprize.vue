<template>
    <div class="doorprize-page">
        <div class="doorprize-header">
            <h1>Doorprize</h1>
            <p class="subtitle">Pilih event dan acak pemenang doorprize</p>
        </div>

        <!-- Event Selection -->
        <div class="doorprize-section">
            <div class="section-header">
                <h2>Pilih Event</h2>
            </div>
            <div class="event-selector">
                <label class="form-label">Event (berlangsung hari ini)</label>
                <Select2
                    v-model="selectedEventId"
                    :disabled="loading"
                    :options="{ placeholder: 'Pilih event yang berlangsung hari ini' }"
                    select-class="form-control"
                    @change="onEventChange"
                >
                    <option value="">-- Pilih Event --</option>
                    <option 
                        v-for="event in events" 
                        :key="event.id_event || event.id"
                        :value="event.id_event || event.id"
                    >
                        {{ event.name_event }} - {{ formatDate(event.date_event) }}
                    </option>
                </Select2>
            </div>
        </div>

        <!-- Participants Info -->
        <div v-if="selectedEventId && participants.length > 0" class="doorprize-section">
            <div class="section-header">
                <h2>Peserta Event</h2>
                <span class="participant-count">{{ participants.length }} peserta</span>
            </div>
            <div v-if="participants.length > 50" class="participant-warning">
                <i class="ri-information-line"></i>
                <p>Jumlah peserta sangat banyak ({{ participants.length }}). Nama peserta mungkin tidak terlihat jelas di wheel. Disarankan untuk membagi peserta menjadi beberapa kelompok.</p>
            </div>
        </div>

        <!-- Spinning Wheel -->
        <div v-if="selectedEventId && participants.length > 0" class="doorprize-section wheel-section">
            <div class="section-header">
                <h2>Putar Undian</h2>
            </div>
            <SpinningWheel 
                :segments="wheelSegments"
                :spinning="isSpinning"
                @spin="onSpinStart"
                @spin-complete="onSpinComplete"
            />
            <div class="spin-instructions">
                <p>Klik pada wheel atau tekan <kbd>Ctrl + Enter</kbd> untuk memutar</p>
            </div>
        </div>

        <!-- Winner Result -->
        <div v-if="winners.length > 0" class="doorprize-section winners-section">
            <div class="section-header">
                <h2>Hasil Undian</h2>
                <button @click="downloadResults" class="btn btn-primary btn-download">
                    <i class="ri-download-line"></i> Download Hasil
                </button>
            </div>
            <div class="winners-list">
                <div 
                    v-for="(winner, index) in winners" 
                    :key="index"
                    class="winner-card"
                >
                    <div class="winner-rank">{{ index + 1 }}</div>
                    <div class="winner-info">
                        <div class="winner-name">{{ winner.name }}</div>
                        <div class="winner-details">
                            <span v-if="winner.email">{{ winner.email }}</span>
                            <span v-if="winner.phone">{{ winner.phone }}</span>
                        </div>
                    </div>
                    <div class="winner-badge">
                        <i class="ri-trophy-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty States -->
        <div v-if="!selectedEventId" class="empty-state">
            <i class="ri-calendar-event-line"></i>
            <p>Pilih event terlebih dahulu untuk memulai undian doorprize</p>
        </div>
        <div v-else-if="selectedEventId && participants.length === 0 && !loading" class="empty-state">
            <i class="ri-user-line"></i>
            <p>Event ini belum memiliki peserta</p>
        </div>
        <div v-if="loading" class="loading-state">
            <div class="spinner"></div>
            <p>Memuat data...</p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import SpinningWheel from './SpinningWheel.vue';
import Select2 from './Select2.vue';
import { showSuccess, showError } from '../utils/swal.js';

export default {
    name: 'Doorprize',
    components: {
        SpinningWheel,
        Select2
    },
    data() {
        return {
            events: [],
            selectedEventId: '',
            participants: [],
            wheelSegments: [],
            isSpinning: false,
            winners: [],
            loading: false
        };
    },
    mounted() {
        this.checkAuth();
        this.fetchEvents();
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
        async fetchEvents() {
            this.loading = true;
            try {
                const response = await axios.get('/event', {
                    params: this.getAuthParams()
                });
                let eventsData = [];
                if (Array.isArray(response.data)) {
                    eventsData = response.data;
                } else if (response.data && response.data.data) {
                    eventsData = response.data.data;
                }

                // Filter hanya event yang tanggalnya hari ini
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                this.events = eventsData.filter(event => {
                    if (!event.date_event) return false;
                    const eventDate = new Date(event.date_event);
                    eventDate.setHours(0, 0, 0, 0);
                    return eventDate.getTime() === today.getTime();
                });

                // Reset pilihan jika event sebelumnya tidak lagi tersedia
                const hasSelected = this.events.some(
                    e => String(e.id_event || e.id) === String(this.selectedEventId)
                );
                if (!hasSelected) {
                    this.selectedEventId = '';
                    this.participants = [];
                    this.wheelSegments = [];
                    this.winners = [];
                }
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    localStorage.removeItem('token');
                    this.$router.push('/login');
                } else {
                    this.events = [];
                }
            } finally {
                this.loading = false;
            }
        },
        async onEventChange() {
            if (!this.selectedEventId) {
                this.participants = [];
                this.wheelSegments = [];
                this.winners = [];
                return;
            }
            
            await this.fetchParticipants();
        },
        async fetchParticipants() {
            this.loading = true;
            try {
                const response = await axios.get('/guest', {
                    params: {
                        ...this.getAuthParams(),
                        eventId: this.selectedEventId,
                        // Hanya peserta yang sudah check-in
                        attend: true
                    }
                });
                
                let guestsData = [];
                if (Array.isArray(response.data)) {
                    guestsData = response.data;
                } else if (response.data && response.data.data) {
                    guestsData = response.data.data;
                }
                
                this.participants = guestsData.map(guest => ({
                    id: guest.id_guest || guest.id,
                    name: guest.guest_name || 'Tanpa Nama',
                    email: guest.guest_email || '',
                    phone: guest.guest_phone || ''
                }));
                
                // Prepare wheel segments
                this.prepareWheelSegments();
            } catch (error) {
                await showError('Error', 'Gagal memuat data peserta');
                this.participants = [];
                this.wheelSegments = [];
            } finally {
                this.loading = false;
            }
        },
        prepareWheelSegments() {
            // Create segments from participants
            this.wheelSegments = this.participants.map((participant, index) => ({
                label: participant.name.length > 15 
                    ? participant.name.substring(0, 15) + '...' 
                    : participant.name,
                fullName: participant.name,
                data: participant
            }));
        },
        onSpinStart() {
            this.isSpinning = true;
        },
        onSpinComplete(result) {
            this.isSpinning = false;
            if (!result || !result.winner) {
                return;
            }

            const winnerData = result.winner.data;

            const winner = {
                id: winnerData.id,
                name: result.winner.fullName,
                email: winnerData.email,
                phone: winnerData.phone,
                timestamp: new Date().toLocaleString('id-ID')
            };

            // Tambahkan ke daftar pemenang
            this.winners.push(winner);

            // Hapus pemenang dari peserta & segmen wheel agar tidak terpilih lagi
            this.participants = this.participants.filter(p => p.id !== winner.id);
            this.prepareWheelSegments();

            showSuccess('Selamat!', `Pemenang: ${winner.name}`);
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        downloadResults() {
            if (this.winners.length === 0) return;
            
            const selectedEvent = this.events.find(
                e => (e.id_event || e.id) == this.selectedEventId
            );
            const eventName = selectedEvent ? selectedEvent.name_event : 'Event';
            const eventDate = selectedEvent ? this.formatDate(selectedEvent.date_event) : '';
            
            // Create CSV content
            let csvContent = 'Hasil Undian Doorprize\n';
            csvContent += `Event: ${eventName}\n`;
            csvContent += `Tanggal: ${eventDate}\n`;
            csvContent += `Waktu Undian: ${new Date().toLocaleString('id-ID')}\n\n`;
            csvContent += 'No,Nama,Email,Telepon,Waktu\n';
            
            this.winners.forEach((winner, index) => {
                csvContent += `${index + 1},"${winner.name}","${winner.email || ''}","${winner.phone || ''}","${winner.timestamp}"\n`;
            });
            
            // Create and download file
            const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `Hasil_Doorprize_${eventName.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showSuccess('Berhasil!', 'File hasil undian berhasil diunduh');
        }
    }
};
</script>

<style scoped>
@import '../styles/doorprize.css';
</style>
