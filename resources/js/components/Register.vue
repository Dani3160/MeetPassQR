<template>
    <div class="auth-container">
        <!-- Animated Background -->
        <div class="bg-animation">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-pattern"></div>
        </div>

        <!-- Main Content -->
        <div class="auth-wrapper">
            <div class="auth-card">
                <!-- Logo/Icon Section -->
                <div class="auth-header">
                    <div class="logo-container">
                        <div class="logo-icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="auth-title">Buat Akun Baru</h1>
                    <p class="auth-subtitle">Bergabunglah dengan kami dan mulai perjalanan Anda</p>
                </div>

                <!-- Form Section -->
                <form @submit.prevent="handleRegister" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="auth-form-label">
                                <svg class="label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Nama Depan
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    v-model="form.firstname" 
                                    type="text" 
                                    class="form-input" 
                                    required
                                    placeholder="John"
                                >
                                <div class="input-border"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="auth-form-label">
                                <svg class="label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Nama Belakang
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    v-model="form.lastname" 
                                    type="text" 
                                    class="form-input" 
                                    required
                                    placeholder="Doe"
                                >
                                <div class="input-border"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="auth-form-label">
                            <svg class="label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="L22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Email
                        </label>
                        <div class="input-wrapper">
                            <input 
                                v-model="form.email" 
                                type="email" 
                                class="form-input" 
                                required
                                placeholder="nama@email.com"
                            >
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="auth-form-label">
                            <svg class="label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <input 
                                v-model="form.password" 
                                type="password" 
                                class="form-input" 
                                required
                                minlength="6"
                                placeholder="Minimal 6 karakter"
                            >
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="auth-form-label">
                            <svg class="label-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Konfirmasi Password
                        </label>
                        <div class="input-wrapper">
                            <input 
                                v-model="form.repassword" 
                                type="password" 
                                class="form-input" 
                                required
                                placeholder="Ulangi password"
                            >
                            <div class="input-border"></div>
                        </div>
                    </div>

                    <div v-if="error" class="alert alert-error">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>{{ error }}</span>
                    </div>

                    <div v-if="success" class="alert alert-success">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.7088 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1004 1.98232 16.07 2.85999" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ success }}</span>
                    </div>

                    <button type="submit" class="auth-button" :disabled="loading">
                        <span v-if="loading" class="button-loader"></span>
                        <span v-else class="button-content">
                            <span>Daftar</span>
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </button>

                    <div class="auth-divider">
                        <span>atau</span>
                    </div>

                    <p class="auth-footer">
                        Sudah punya akun? 
                        <router-link to="/login" class="auth-link">
                            Masuk sekarang
                        </router-link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { showError, showSuccess } from '../utils/swal.js';

export default {
    name: 'Register',
    data() {
        return {
            form: {
                firstname: '',
                lastname: '',
                email: '',
                password: '',
                repassword: ''
            },
            loading: false,
            error: '',
            success: ''
        };
    },
    methods: {
        async handleRegister() {
            this.loading = true;
            this.error = '';
            this.success = '';

            // Validate password match
            if (this.form.password !== this.form.repassword) {
                this.error = 'Password dan konfirmasi password tidak cocok';
                this.loading = false;
                return;
            }

            try {
                const response = await axios.post('/register', this.form);
                
                if (response.data.success) {
                    // Clear form
                    this.form = {
                        firstname: '',
                        lastname: '',
                        email: '',
                        password: '',
                        repassword: ''
                    };
                    await showSuccess('Berhasil!', 'Pendaftaran berhasil! Silakan login.');
                    // Redirect to login after 1 second
                    setTimeout(() => {
                        this.$router.push('/login');
                    }, 1000);
                } else {
                    this.error = response.data.message || 'Pendaftaran gagal';
                    await showError('Gagal!', this.error);
                }
            } catch (error) {
                let errorMessage = 'Terjadi kesalahan saat mendaftar';
                if (error.response && error.response.data) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join(', ');
                    } else {
                        errorMessage = error.response.data.message || 'Pendaftaran gagal';
                    }
                }
                this.error = errorMessage;
                await showError('Gagal!', errorMessage);
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/register.css';
</style>
