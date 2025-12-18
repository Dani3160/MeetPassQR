<template>
    <nav class="navbar" :class="{ 'mobile-open': mobileMenuOpen }">
        <div class="navbar-container">
            <router-link to="/dashboard" class="navbar-brand" @click="closeMobileMenu">
                <span class="brand-text">MeetPassQR</span>
            </router-link>
            <button @click="toggleMobileMenu" class="mobile-menu-toggle" :class="{ 'active': mobileMenuOpen }" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <!-- Desktop: Regular navbar nav -->
            <div 
                class="navbar-nav" 
                :class="{ 'mobile-open': mobileMenuOpen }"
                @click.stop
            >
                <!-- Desktop: Direct nav links -->
                <router-link to="/dashboard" class="nav-link" @click="closeMobileMenu">
                    <span class="nav-text">Beranda</span>
                </router-link>
                <router-link to="/scan-qr" class="nav-link" @click="closeMobileMenu">
                    <span class="nav-text">Scan QR</span>
                </router-link>
                <router-link to="/doorprize" class="nav-link" @click="closeMobileMenu">
                    <span class="nav-text">Doorprize</span>
                </router-link>
                <router-link to="/guide" class="nav-link" @click="closeMobileMenu">
                    <span class="nav-text">Panduan</span>
                </router-link>
                <router-link to="/settings" class="nav-link" @click="closeMobileMenu">
                    <span class="nav-text">Pengaturan</span>
                </router-link>
                <button @click="handleLogout" class="nav-link btn-logout">
                    <span class="nav-text">Keluar</span>
                </button>
            </div>
        </div>
        
        <!-- Mobile: Overlay and Navbar nav teleported to body to avoid parent positioning issues -->
        <Teleport to="body" v-if="mobileMenuOpen">
            <!-- Mobile Overlay with blur effect -->
            <div 
                class="mobile-overlay active" 
                @click="closeMobileMenu"
                @touchstart="closeMobileMenu"
            ></div>
            
            <!-- Mobile Navbar Nav -->
            <div 
                class="navbar-nav mobile-nav-teleported" 
                :class="{ 'mobile-open': mobileMenuOpen }"
                @click.stop
            >
                <!-- Mobile Menu Header (like modal) -->
                <div class="navbar-nav-header">
                    <h2 class="navbar-nav-title">Menu</h2>
                    <button @click="closeMobileMenu" class="navbar-nav-close" aria-label="Tutup menu">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                
                <!-- Mobile Menu Body -->
                <div class="navbar-nav-body">
                    <router-link to="/dashboard" class="nav-link" @click="closeMobileMenu">
                        <span class="nav-text">Beranda</span>
                    </router-link>
                    <router-link to="/scan-qr" class="nav-link" @click="closeMobileMenu">
                        <span class="nav-text">Scan QR</span>
                    </router-link>
                    <router-link to="/doorprize" class="nav-link" @click="closeMobileMenu">
                        <span class="nav-text">Doorprize</span>
                    </router-link>
                    <router-link to="/guide" class="nav-link" @click="closeMobileMenu">
                        <span class="nav-text">Panduan</span>
                    </router-link>
                    <router-link to="/settings" class="nav-link" @click="closeMobileMenu">
                        <span class="nav-text">Pengaturan</span>
                    </router-link>
                    <button @click="handleLogout" class="nav-link btn-logout">
                        <span class="nav-text">Keluar</span>
                    </button>
                </div>
            </div>
        </Teleport>
    </nav>
</template>

<script>
import { showLogoutConfirm } from '../utils/swal.js';

export default {
    name: 'Navbar',
    data() {
        return {
            mobileMenuOpen: false
        };
    },
    watch: {
        $route() {
            this.closeMobileMenu();
        },
        mobileMenuOpen(newVal) {
            // Lock body scroll when menu is open
            if (newVal) {
                const scrollY = window.scrollY;
                document.body.classList.add('menu-open');
                // Prevent scroll on iOS - but don't set top to 0, let navbar appear from bottom
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.maxWidth = '100%';
                document.body.style.top = `-${scrollY}px`;
                // Ensure navbar-nav appears at bottom of viewport
                document.documentElement.style.overflowX = 'hidden';
            } else {
                const scrollY = document.body.style.top;
                document.body.classList.remove('menu-open');
                document.body.style.position = '';
                document.body.style.width = '';
                document.body.style.maxWidth = '';
                document.body.style.top = '';
                document.documentElement.style.overflowX = '';
                if (scrollY) {
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);
                }
            }
        }
    },
    mounted() {
        // Close menu on escape key
        document.addEventListener('keydown', this.handleEscape);
        // Close menu when clicking outside
        document.addEventListener('click', this.handleClickOutside);
        // Close menu on window resize to desktop
        window.addEventListener('resize', this.handleResize);
    },
    beforeUnmount() {
        // Clean up event listeners
        document.removeEventListener('keydown', this.handleEscape);
        document.removeEventListener('click', this.handleClickOutside);
        window.removeEventListener('resize', this.handleResize);
        // Ensure body scroll is unlocked when component is destroyed
        const scrollY = document.body.style.top;
        document.body.classList.remove('menu-open');
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.top = '';
        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }
    },
    methods: {
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },
        closeMobileMenu() {
            this.mobileMenuOpen = false;
        },
        async handleLogout() {
            const result = await showLogoutConfirm();
            if (result.isConfirmed) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                this.$router.push('/login');
            }
        },
        handleEscape(event) {
            if (event.key === 'Escape' && this.mobileMenuOpen) {
                this.closeMobileMenu();
            }
        },
        handleClickOutside(event) {
            // Close menu if clicking outside navbar-nav or on overlay when menu is open
            if (this.mobileMenuOpen) {
                const isNavbarClick = event.target.closest('.navbar-nav');
                const isToggleClick = event.target.closest('.mobile-menu-toggle');
                const isOverlayClick = event.target.classList.contains('mobile-overlay');
                
                if (!isNavbarClick && !isToggleClick || isOverlayClick) {
                    this.closeMobileMenu();
                }
            }
        },
        handleResize() {
            // Close menu when resizing to desktop view
            if (window.innerWidth > 768 && this.mobileMenuOpen) {
                this.closeMobileMenu();
            }
        }
    }
}
</script>

<style src="../styles/navbar.css" scoped></style>
