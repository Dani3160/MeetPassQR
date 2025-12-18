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
                                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="auth-title">Selamat Datang</h1>
                    <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <!-- Form Section -->
                <form @submit.prevent="handleLogin" class="auth-form">
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
                                placeholder="Masukkan password Anda"
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

                    <button type="submit" class="auth-button" :disabled="loading">
                        <span v-if="loading" class="button-loader"></span>
                        <span v-else class="button-content">
                            <span>Masuk</span>
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
                        Belum punya akun? 
                        <router-link to="/register" class="auth-link">
                            Daftar sekarang
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
    name: 'Login',
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            loading: false,
            error: ''
        };
    },
    methods: {
        async handleLogin() {
            this.loading = true;
            this.error = '';

            try {
                const response = await axios.post('/login', this.form);
                
                // Check if response has success field or just token (for backward compatibility)
                if (response.data.success !== false && response.data.token) {
                    // Store token in localStorage
                    localStorage.setItem('token', response.data.token);
                    localStorage.setItem('user', JSON.stringify({
                        email: response.data.email || this.form.email,
                        firstname: response.data.firstname || '',
                        lastname: response.data.lastname || ''
                    }));
                    
                    // Redirect to dashboard
                    this.$router.push('/dashboard');
                } else {
                    this.error = response.data.message || 'Login gagal';
                    await showError('Login Gagal', this.error);
                }
            } catch (error) {
                if (error.response) {
                    if (error.response.status === 401) {
                        this.error = 'Email atau password salah';
                    } else if (error.response.data && error.response.data.message) {
                        this.error = error.response.data.message;
                    } else {
                        this.error = 'Email atau password salah';
                    }
                } else {
                    this.error = 'Terjadi kesalahan saat login. Pastikan server berjalan.';
                }
                await showError('Login Gagal', this.error);
            } finally {
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/login.css';
</style>
