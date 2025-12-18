<template>
    <button 
        v-if="shouldShow"
        @click="scrollToTop" 
        class="scroll-to-top"
        :class="{ show: showButton }"
        aria-label="Kembali ke atas"
        title="Kembali ke atas"
    >
        <i class="ri-arrow-up-line"></i>
    </button>
</template>

<script>
export default {
    name: 'ScrollToTop',
    data() {
        return {
            showButton: false
        };
    },
    computed: {
        shouldShow() {
            // Hide on auth pages
            const authRoutes = ['/login', '/register', '/guest-checkin'];
            if (authRoutes.includes(this.$route.path)) {
                return false;
            }
            // Hide on preview pages
            if (this.$route.path.startsWith('/preview/')) {
                return false;
            }
            return true;
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
        // Check initial scroll position
        this.handleScroll();
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.handleScroll);
    },
    methods: {
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        handleScroll() {
            this.showButton = window.scrollY > 300;
        }
    }
};
</script>

<style scoped>
.scroll-to-top {
    position: fixed !important;
    bottom: 2rem;
    right: 2rem;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    box-shadow: 0 4px 16px rgba(var(--primary-rgb, 249, 115, 22), 0.4);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 997 !important;
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px) scale(0.8);
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
    font-family: 'Poppins', sans-serif;
}

.scroll-to-top.show {
    opacity: 1;
    pointer-events: all;
    transform: translateY(0) scale(1);
}

.scroll-to-top.show:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 6px 20px rgba(var(--primary-rgb, 249, 115, 22), 0.5);
}

.scroll-to-top.show:active {
    transform: translateY(-2px) scale(0.95);
}

.scroll-to-top i {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Mobile Responsive - Position scroll to top on left side to avoid theme toggle */
@media (max-width: 768px) {
    .scroll-to-top {
        bottom: 1.5rem !important;
        left: 1.5rem !important;
        right: auto !important;
        width: 52px !important;
        height: 52px !important;
        font-size: 1.375rem !important;
        z-index: 997 !important;
        display: flex !important;
        position: fixed !important;
    }
    
    .scroll-to-top.show {
        display: flex !important;
        opacity: 1 !important;
        pointer-events: all !important;
        visibility: visible !important;
    }
}

@media (max-width: 480px) {
    .scroll-to-top {
        bottom: 1rem !important;
        left: 1rem !important;
        right: auto !important;
        width: 48px !important;
        height: 48px !important;
        font-size: 1.25rem !important;
        z-index: 997 !important;
        display: flex !important;
        position: fixed !important;
    }
    
    .scroll-to-top.show {
        display: flex !important;
        opacity: 1 !important;
        pointer-events: all !important;
        visibility: visible !important;
    }
}

/* Ensure button doesn't overlap with theme settings */
@media (min-width: 769px) {
    .scroll-to-top {
        bottom: calc(2rem + 80px); /* Adjust if theme settings button is visible */
    }
}
</style>
