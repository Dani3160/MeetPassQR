<template>
    <div class="background-body" :class="{ 'splash-screen-active': currentGuest }">
        <!-- Animated Background Circles -->
        <div class="area">
            <ul class="circles">
                <li v-for="n in 10" :key="n"></li>
            </ul>
        </div>

        <!-- Background Decorative Elements -->
        <div class="background-decorative"></div>

        <!-- Guest Preview Splash Screen -->
        <div 
            v-if="currentGuest" 
            class="content-preview-guest" 
            id="content-guest"
            :class="{ 'fade-in': currentGuest }"
        >
            <img 
                v-if="eventLogo" 
                :src="eventLogo" 
                class="event-logo"
                alt="Event Logo"
            />
            <h1 class="header" id="title-header">{{ welcomeTitle }}</h1>
            <div class="img-guest">
                <div class="thumbnail-img">
                    <img 
                        :src="currentGuest.image" 
                        :alt="currentGuest.name" 
                        id="guest-pic"
                        @error="handleImageError"
                    >
                </div>
            </div>
            <h1 class="header" id="guest-name">{{ currentGuest.name }}</h1>
            <p 
                v-if="currentGuest.title" 
                id="guest-cooperative" 
                class="session-guest-preview"
            >
                {{ currentGuest.title }}
            </p>
        </div>

        <!-- Main Content -->
        <div v-if="loading" class="loading">Memuat data...</div>
        <div v-else-if="eventData" class="content" id="content-table">
            <img 
                v-if="eventLogo" 
                :src="eventLogo" 
                class="event-logo-main"
                alt="Event Logo"
            />
            <h1 class="header">DAFTAR HADIR PESERTA</h1>
            
            <!-- Statistics -->
            <div class="info-count">
                <div class="item-count">
                    <div class="box-count">{{ eventData.statistics.total_guests }}</div>
                    <div class="title-count">Tamu undangan</div>
                </div>
                <div class="item-count">
                    <div class="box-count">{{ eventData.statistics.total_check_in }}</div>
                    <div class="title-count">Tamu Hadir</div>
                </div>
                <div class="item-count">
                    <div class="box-count">{{ eventData.statistics.total_check_out }}</div>
                    <div class="title-count">Tamu Pulang</div>
                </div>
                <div class="item-count">
                    <div class="box-count" id="box-important">{{ eventData.statistics.total_stay }}</div>
                    <div class="title-count">Tamu Ditempat</div>
                </div>
                <div class="item-count">
                    <div class="box-count">{{ eventData.statistics.total_not_attend }}</div>
                    <div class="title-count">Tamu Belum Hadir</div>
                </div>
            </div>

            <!-- Guest Slider -->
            <div class="slide-guest">
                <div class="swiper-container swiper-1">
                    <div class="swiper-wrapper">
                        <div 
                            v-for="guest in eventData.guests" 
                            :key="guest.id" 
                            class="swiper-slide"
                        >
                            <div class="item-guest">
                                <div class="avatar-guest">
                                    <div class="thumbnail-img">
                                        <img 
                                            :src="guest.image" 
                                            :alt="guest.name"
                                            @error="handleImageError"
                                        >
                                    </div>
                                </div>
                                <h5 class="name-guest">{{ guest.name }}</h5>
                                <span v-if="guest.title" class="session-guest">{{ guest.title }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="box-4vm">
            <span>MeetPassQR By</span>
            <a href="https://www.4visionmedia.com/id" class="logo-4vm" target="_blank">
                <img src="/img/event/logo-4vm-black.svg" alt="4Vision Media">
            </a>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Preview',
    data() {
        return {
            eventData: null,
            loading: false,
            currentGuest: null,
            welcomeTitle: 'Selamat Datang',
            pusher: null,
            channel: null,
            swiper: null,
            swiperInterval: null
        };
    },
    computed: {
        eventLogo() {
            if (this.eventData?.event?.logo) {
                return this.eventData.event.logo.startsWith('http') 
                    ? this.eventData.event.logo 
                    : `/storage/${this.eventData.event.logo}`;
            }
            return '/img/event/avatar.png'; // Default logo
        }
    },
    mounted() {
        this.fetchEventData();
    },
    beforeUnmount() {
        if (this.pusher) {
            this.pusher.disconnect();
        }
        if (this.swiper) {
            this.swiper.destroy();
        }
        if (this.swiperInterval) {
            clearInterval(this.swiperInterval);
        }
    },
    methods: {
        async fetchEventData() {
            this.loading = true;
            try {
                const eventSlug = this.$route.params.slug;
                const response = await axios.get('/preview-event', {
                    params: { eventId: eventSlug }
                });
                
                if (response.data.success) {
                    this.eventData = response.data;
                    this.initPusher();
                    this.$nextTick(() => {
                        // Small delay to ensure DOM is fully rendered
                        setTimeout(() => {
                            this.initSwiper();
                        }, 500);
                    });
                }
            } catch (error) {
                console.error('Error fetching event data:', error);
            } finally {
                this.loading = false;
            }
        },
        initPusher() {
            if (!this.eventData.pusher) return;

            import('pusher-js').then((Pusher) => {
                this.pusher = new Pusher.default(this.eventData.pusher.key, {
                    cluster: this.eventData.pusher.cluster
                });

                this.channel = this.pusher.subscribe(this.eventData.pusher.channel);
                
                this.channel.bind(this.eventData.pusher.event, (data) => {
                    if (data && data.message) {
                        this.displayGuest(data.message);
                    }
                });

                // Log connection status
                this.pusher.connection.bind('connected', () => {
                    console.log('Pusher connected successfully');
                });
                
                this.pusher.connection.bind('error', (err) => {
                    console.error('Pusher connection error:', err);
                });
            });
        },
        initSwiper() {
            // Clear existing interval if any
            if (this.swiperInterval) {
                clearInterval(this.swiperInterval);
                this.swiperInterval = null;
            }

            const swiperContainer = this.$el?.querySelector('.swiper-container');
            const swiperWrapper = this.$el?.querySelector('.swiper-wrapper');
            
            if (!swiperContainer || !swiperWrapper) return;

            const slides = swiperWrapper.querySelectorAll('.swiper-slide');
            if (slides.length === 0) {
                // Reset transform if no slides
                swiperWrapper.style.transform = 'translateX(0)';
                return;
            }

            // Reset to start position
            swiperWrapper.style.transform = 'translateX(0)';

            // Calculate slides per view
            const containerWidth = swiperContainer.offsetWidth;
            const slideWidth = slides[0].offsetWidth;
            const slidesPerView = Math.floor(containerWidth / slideWidth) || 3;

            // Only auto-scroll if there are more slides than visible
            if (slides.length <= slidesPerView) {
                // Reset transform if all slides fit
                swiperWrapper.style.transform = 'translateX(0)';
                return;
            }

            let currentIndex = 0;
            const maxIndex = slides.length - slidesPerView;

            // Auto-scroll implementation using transform
            this.swiperInterval = setInterval(() => {
                // Move to next set of slides
                currentIndex += slidesPerView;
                
                // If we've reached or passed the end, loop back to start
                if (currentIndex > maxIndex) {
                    currentIndex = 0;
                }
                
                // Use transform for smooth sliding
                const translateX = -(currentIndex * slideWidth);
                swiperWrapper.style.transform = `translateX(${translateX}px)`;
            }, 5000);
        },
        displayGuest(guestData) {
            // Parse guest data
            let guest;
            try {
                if (typeof guestData === 'string') {
                    guest = JSON.parse(guestData);
                } else {
                    guest = guestData;
                }
            } catch (e) {
                console.error('Error parsing guest data:', e, guestData);
                return;
            }

            if (guest.status === 'checkIn') {
                this.welcomeTitle = 'Selamat Datang';
                // Show for 10 seconds
                setTimeout(() => {
                    this.currentGuest = null;
                    this.fetchEventData(); // This will reinit swiper
                }, 10000);
            } else if (guest.status === 'checkOut') {
                this.welcomeTitle = 'Terima kasih atas kehadirannya';
                // Show for 5 seconds
                setTimeout(() => {
                    this.currentGuest = null;
                    this.fetchEventData(); // This will reinit swiper
                }, 5000);
            }

            // Build image URL - ensure consistent path format
            let imageUrl = '/img/event/avatar.png'; // default
            if (guest.guest_pic && 
                guest.guest_pic !== 'avatar.png' && 
                guest.guest_pic !== '/event/avatar.png' &&
                guest.guest_pic !== 'event/avatar.png') {
                // guest_pic might be like '/guest/photo.jpg' or 'guest/photo.jpg'
                // Ensure it starts with '/' for proper path construction
                const guestPic = guest.guest_pic.startsWith('/') ? guest.guest_pic : `/${guest.guest_pic}`;
                imageUrl = `/img${guestPic}`;
            }
            
            // Debug: log image path
            console.log('Guest image path:', {
                guest_pic: guest.guest_pic,
                imageUrl: imageUrl,
                guest_name: guest.guest_name
            });
            
            this.currentGuest = {
                name: guest.guest_name,
                title: guest.guest_title,
                image: imageUrl
            };
        },
        handleImageError(event) {
            event.target.src = '/img/event/avatar.png';
        }
    }
};
</script>

<style scoped>
@import '../styles/preview-new.css';
</style>
