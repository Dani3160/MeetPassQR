<template>
    <div class="not-found-container">
        <!-- Animated Background -->
        <div class="bg-animation">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
            <div class="grid-pattern"></div>
        </div>

        <!-- Main Content -->
        <div class="not-found-wrapper">
            <div class="not-found-content">
                <!-- 404 Number -->
                <div class="error-number-wrapper">
                    <div class="error-number">404</div>
                </div>

                <!-- Error Message -->
                <h1 class="error-title">Halaman Tidak Ditemukan</h1>
                <p class="error-description">
                    Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan.
                </p>

                <!-- Action Button -->
                <div class="error-actions">
                    <button @click="goBack" class="btn btn-primary">
                        <i class="ri-arrow-left-line"></i>
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NotFound',
    methods: {
        goBack() {
            if (window.history.length > 1) {
                this.$router.go(-1);
            } else {
                this.$router.push('/dashboard');
            }
        }
    }
}
</script>

<style scoped>
.not-found-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, oklch(98% 0 0) 0%, oklch(96% 0 0) 100%);
}

/* Animated Background */
.bg-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.gradient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    animation: float 20s ease-in-out infinite;
}

.orb-1 {
    width: 400px;
    height: 400px;
    background: linear-gradient(135deg, var(--primary, #f97316) 0%, var(--primary-light, #fb923c) 100%);
    top: -200px;
    left: -200px;
    animation-delay: 0s;
}

.orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, var(--secondary, #3b82f6) 0%, var(--secondary-light, #60a5fa) 100%);
    bottom: -150px;
    right: -150px;
    animation-delay: 7s;
}

.orb-3 {
    width: 250px;
    height: 250px;
    background: linear-gradient(135deg, var(--accent, #8b5cf6) 0%, var(--accent-light, #a78bfa) 100%);
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 14s;
}

.grid-pattern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        linear-gradient(oklch(92% 0 0 / 0.5) 1px, transparent 1px),
        linear-gradient(90deg, oklch(92% 0 0 / 0.5) 1px, transparent 1px);
    background-size: 50px 50px;
    opacity: 0.3;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
}

.not-found-wrapper {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 800px;
    padding: 2rem;
    text-align: center;
}

.not-found-content {
    background: var(--card-bg, white);
    border-radius: 2rem;
    padding: 4rem 2rem;
    box-shadow: 0 20px 60px oklch(0% 0 0 / 0.1);
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-number-wrapper {
    margin-bottom: 3rem;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: fadeInUp 0.6s ease-out both;
}

.error-number {
    font-size: 10rem;
    font-weight: 900;
    line-height: 1;
    font-family: 'Poppins', sans-serif;
    color: var(--primary, #f97316);
    letter-spacing: 0.5rem;
    animation: fadeInUp 0.6s ease-out both;
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary, oklch(20% 0 0));
    margin-top: 0;
    margin-bottom: 1rem;
    font-family: 'Poppins', sans-serif;
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.error-description {
    font-size: 1.125rem;
    color: var(--text-secondary, oklch(50% 0 0));
    margin-bottom: 2.5rem;
    line-height: 1.6;
    font-family: 'Poppins', sans-serif;
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.75rem;
    border-radius: 0.75rem;
    font-size: 1rem;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px oklch(0% 0 0 / 0.1);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary, #f97316) 0%, var(--primary-light, #fb923c) 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(var(--primary-rgb, 249, 115, 22), 0.4);
}

.btn-secondary {
    background: var(--input-bg, oklch(96% 0 0));
    color: var(--text-primary, oklch(30% 0 0));
    border: 2px solid var(--border-color, oklch(92% 0 0));
}

.btn-secondary:hover {
    background: var(--table-row-hover, oklch(98% 0 0));
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-2px);
}

.btn i {
    font-size: 1.25rem;
}


/* Responsive */
@media (max-width: 768px) {
    .not-found-wrapper {
        padding: 1rem;
    }

    .not-found-content {
        padding: 3rem 1.5rem;
    }

    .error-number-wrapper {
        margin-bottom: 2rem;
    }

    .error-number {
        font-size: 6rem;
        letter-spacing: 0.3rem;
    }

    .error-title {
        font-size: 2rem;
    }

    .error-description {
        font-size: 1rem;
    }

    .error-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .error-number-wrapper {
        margin-bottom: 1.5rem;
    }

    .error-number {
        font-size: 5rem;
        letter-spacing: 0.2rem;
    }

    .error-title {
        font-size: 1.75rem;
    }

    .not-found-content {
        padding: 2rem 1rem;
    }
}
</style>
