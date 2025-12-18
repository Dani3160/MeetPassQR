<template>
    <div class="admin-panel">
        <div class="card">
            <h1>Kelola Peserta Event</h1>

            <div class="form-section">
                <h2>Tambah Peserta Baru</h2>
                <form @submit.prevent="addAttendee">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input v-model="form.email" type="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input v-model="form.phone" type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nomor Tiket</label>
                            <input v-model="form.ticket_number" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Event</label>
                        <input v-model="form.event_name" type="text" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        <span v-if="loading" class="loading-spinner"></span>
                        {{ loading ? 'Menambahkan...' : 'Tambah Peserta' }}
                    </button>
                </form>
            </div>

            <div v-if="message" :class="['alert', messageType]">
                {{ message }}
            </div>

            <div class="attendees-section">
                <h2>Daftar Peserta ({{ attendees.length }})</h2>
                
                <SkeletonLoader v-if="loadingAttendees" type="table" :rows="5" :cols="7" />

                <div v-else class="attendees-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nomor Tiket</th>
                                <th>Event</th>
                                <th>Status</th>
                                <th>QR Code</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="attendee in attendees" :key="attendee.id">
                                <td>{{ attendee.name }}</td>
                                <td>{{ attendee.email }}</td>
                                <td>{{ attendee.ticket_number }}</td>
                                <td>{{ attendee.event_name }}</td>
                                <td>
                                    <span :class="['status-badge', attendee.is_validated ? 'validated' : 'pending']">
                                        {{ attendee.is_validated ? 'Validated' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <a :href="`/api/attendees/${attendee.id}/qr-code`" target="_blank" class="btn btn-success btn-sm">
                                        Lihat QR
                                    </a>
                                </td>
                                <td>
                                    <button @click="showDeleteConfirm(attendee.id)" class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import SkeletonLoader from './SkeletonLoader.vue';
import Modal from './Modal.vue';
import { showSuccess, showError, showDeleteConfirm } from '../utils/swal.js';

export default {
    name: 'AdminPanel',
    components: {
        SkeletonLoader,
        Modal
    },
    data() {
        return {
            form: {
                name: '',
                email: '',
                phone: '',
                ticket_number: '',
                event_name: ''
            },
            attendees: [],
            loading: false,
            loadingAttendees: false,
            message: '',
            messageType: 'alert-info'
        };
    },
    mounted() {
        this.fetchAttendees();
    },
    methods: {
        async addAttendee() {
            this.loading = true;
            this.message = '';

            try {
                const response = await axios.post('/attendees', this.form);
                this.form = {
                    name: '',
                    email: '',
                    phone: '',
                    ticket_number: '',
                    event_name: ''
                };
                await showSuccess('Berhasil!', response.data.message || 'Peserta berhasil ditambahkan');
                this.fetchAttendees();
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan saat menambahkan peserta';
                if (error.response && error.response.data) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join(', ');
                    } else {
                        errorMessage = error.response.data.message || 'Gagal menambahkan peserta';
                    }
                }
                await showError('Gagal!', errorMessage);
            } finally {
                this.loading = false;
            }
        },
        async fetchAttendees() {
            this.loadingAttendees = true;
            try {
                const response = await axios.get('/attendees');
                this.attendees = response.data.data || [];
            } catch (error) {
                this.message = 'Gagal memuat data peserta';
                this.messageType = 'alert-error';
            } finally {
                this.loadingAttendees = false;
            }
        },
        async showDeleteConfirm(id) {
            const result = await showDeleteConfirm('Hapus Peserta', 'Apakah Anda yakin ingin menghapus peserta ini? Tindakan ini tidak dapat dibatalkan.');
            if (result.isConfirmed) {
                try {
                    await axios.delete(`/attendees/${id}`);
                    await showSuccess('Berhasil!', 'Peserta berhasil dihapus');
                    this.fetchAttendees();
                } catch (error) {
                    await showError('Gagal!', 'Gagal menghapus peserta');
                }
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/admin-panel.css';
</style>

