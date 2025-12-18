<template>
    <div id="app">
        <Navbar v-if="showNavbar" />
        <!-- Auth pages (login/register) - full width without main-content -->
        <template v-if="isAuthPage">
            <router-view v-slot="{ Component, route }">
                <Transition name="page" mode="out-in">
                    <component :is="Component" :key="route.path" />
                </Transition>
            </router-view>
        </template>
        <!-- Other pages - with main-content wrapper -->
        <main v-else class="main-content">
            <router-view v-slot="{ Component, route }">
                <Transition name="page" mode="out-in">
                    <component :is="Component" :key="route.path" />
                </Transition>
            </router-view>
        </main>
        <!-- Theme Settings - Always visible -->
        <ThemeSettings />
        <!-- Scroll to Top Button - Available on all pages -->
        <ScrollToTop />
    </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import ThemeSettings from './components/ThemeSettings.vue';
import ScrollToTop from './components/ScrollToTop.vue';

export default {
    name: 'App',
    components: {
        Navbar,
        ThemeSettings,
        ScrollToTop
    },
    computed: {
        showNavbar() {
            const noNavbarRoutes = ['/login', '/register', '/guest-checkin'];
            // Hide navbar on preview pages and 404 page
            if (this.$route.path.startsWith('/preview/') || this.$route.name === 'NotFound') {
                return false;
            }
            return !noNavbarRoutes.includes(this.$route.path);
        },
        isAuthPage() {
            const authRoutes = ['/login', '/register', '/guest-checkin'];
            // Preview pages and 404 page should also be full screen without main-content wrapper
            if (this.$route.path.startsWith('/preview/') || this.$route.name === 'NotFound') {
                return true;
            }
            return authRoutes.includes(this.$route.path);
        }
    }
}
</script>

<style src="./styles/global.css"></style>

