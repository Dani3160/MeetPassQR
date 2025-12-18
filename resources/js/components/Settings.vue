<template>
    <div class="settings">
        <h1>Pengaturan</h1>

        <div class="settings-tabs">
            <button 
                @click="activeTab = 'profile'" 
                :class="['tab', { active: activeTab === 'profile' }]"
            >
                Profil
            </button>
            <button 
                @click="activeTab = 'password'" 
                :class="['tab', { active: activeTab === 'password' }]"
            >
                Ubah Password
            </button>
        </div>

        <!-- Profile Tab -->
        <div v-if="activeTab === 'profile'" class="tab-content">
            <div class="card">
                <h2>Informasi Profil</h2>
                <form @submit.prevent="updateProfile">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nama Depan</label>
                            <input v-model="profileForm.firstname" type="text" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Belakang</label>
                            <input v-model="profileForm.lastname" type="text" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input v-model="profileForm.email" type="email" class="form-control" disabled>
                        <small class="form-text">Email tidak dapat diubah</small>
                    </div>
                    <div v-if="profileError" class="alert alert-error">{{ profileError }}</div>
                    <div v-if="profileSuccess" class="alert alert-success">{{ profileSuccess }}</div>
                    <button type="submit" class="btn btn-primary" :disabled="profileLoading">
                        <span v-if="profileLoading" class="loading-spinner"></span>
                        {{ profileLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Password Tab -->
        <div v-if="activeTab === 'password'" class="tab-content">
            <div class="card">
                <h2>Ubah Password</h2>
                <form @submit.prevent="updatePassword">
                    <div class="form-group">
                        <label class="form-label">Password Lama</label>
                        <input v-model="passwordForm.old_password" type="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input v-model="passwordForm.new_password" type="password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input v-model="passwordForm.re_new_password" type="password" class="form-control" required minlength="6">
                    </div>
                    <div v-if="passwordError" class="alert alert-error">{{ passwordError }}</div>
                    <div v-if="passwordSuccess" class="alert alert-success">{{ passwordSuccess }}</div>
                    <button type="submit" class="btn btn-primary" :disabled="passwordLoading">
                        <span v-if="passwordLoading" class="loading-spinner"></span>
                        {{ passwordLoading ? 'Mengubah...' : 'Ubah Password' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="logout-section">
            <button @click="handleLogout" class="btn btn-danger">
                Logout
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { showSuccess, showError, showLogoutConfirm } from '../utils/swal.js';

export default {
    name: 'Settings',
    data() {
        return {
            activeTab: 'profile',
            profileForm: {
                firstname: '',
                lastname: '',
                email: ''
            },
            passwordForm: {
                old_password: '',
                new_password: '',
                re_new_password: ''
            },
            profileLoading: false,
            passwordLoading: false,
            profileError: '',
            profileSuccess: '',
            passwordError: '',
            passwordSuccess: ''
        };
    },
    mounted() {
        this.checkAuth();
        this.fetchProfile();
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
        async fetchProfile() {
            try {
                const response = await axios.get('/user/profile', {
                    params: this.getAuthParams()
                });
                this.profileForm = {
                    firstname: response.data.firstname,
                    lastname: response.data.lastname,
                    email: response.data.email
                };
            } catch (error) {
                if (error.response && error.response.status === 401) {
                    this.$router.push('/login');
                }
            }
        },
        async updateProfile() {
            this.profileLoading = true;
            this.profileError = '';
            this.profileSuccess = '';
            try {
                await axios.put('/user/update-name', this.profileForm, {
                    params: this.getAuthParams()
                });
                // Update localStorage
                const user = JSON.parse(localStorage.getItem('user') || '{}');
                user.firstname = this.profileForm.firstname;
                user.lastname = this.profileForm.lastname;
                localStorage.setItem('user', JSON.stringify(user));
                await showSuccess('Berhasil!', 'Profil berhasil diperbarui');
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal memperbarui profil';
                this.profileError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.profileLoading = false;
            }
        },
        async updatePassword() {
            this.passwordLoading = true;
            this.passwordError = '';
            this.passwordSuccess = '';

            if (this.passwordForm.new_password !== this.passwordForm.re_new_password) {
                this.passwordError = 'Password baru dan konfirmasi tidak cocok';
                this.passwordLoading = false;
                await showError('Gagal!', 'Password baru dan konfirmasi tidak cocok');
                return;
            }

            try {
                await axios.put('/user/update-password', this.passwordForm, {
                    params: this.getAuthParams()
                });
                this.passwordForm = {
                    old_password: '',
                    new_password: '',
                    re_new_password: ''
                };
                await showSuccess('Berhasil!', 'Password berhasil diubah');
            } catch (error) {
                const errorMessage = error.response?.data?.message || 'Gagal mengubah password';
                this.passwordError = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.passwordLoading = false;
            }
        },
        async handleLogout() {
            const result = await showLogoutConfirm();
            if (result.isConfirmed) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                this.$router.push('/login');
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/settings.css';
</style>
