import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';
import Register from './components/Register.vue';
import Dashboard from './components/Dashboard.vue';
import EventDetail from './components/EventDetail.vue';
import ScanQR from './components/ScanQR.vue';
import Preview from './components/Preview.vue';
import Settings from './components/Settings.vue';
import GuestCheckIn from './components/GuestCheckIn.vue';
import Guide from './components/Guide.vue';
import Doorprize from './components/Doorprize.vue';
import CustomTemplateEditor from './components/CustomTemplateEditor.vue';
import NotFound from './components/NotFound.vue';

// Route guard
const requireAuth = (to, from, next) => {
    const token = localStorage.getItem('token');
    if (token) {
        next();
    } else {
        next('/login');
    }
};

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        beforeEnter: requireAuth
    },
    {
        path: '/event/:slug',
        name: 'EventDetail',
        component: EventDetail,
        beforeEnter: requireAuth
    },
    {
        path: '/scan-qr',
        name: 'ScanQR',
        component: ScanQR
    },
    {
        path: '/guest-checkin',
        name: 'GuestCheckIn',
        component: GuestCheckIn
    },
    {
        path: '/preview/:slug',
        name: 'Preview',
        component: Preview
    },
    {
        path: '/settings',
        name: 'Settings',
        component: Settings,
        beforeEnter: requireAuth
    },
    {
        path: '/guide',
        name: 'Guide',
        component: Guide,
        beforeEnter: requireAuth
    },
    {
        path: '/doorprize',
        name: 'Doorprize',
        component: Doorprize,
        beforeEnter: requireAuth
    },
    {
        path: '/certificate/templates',
        name: 'CustomTemplateEditor',
        component: CustomTemplateEditor,
        beforeEnter: requireAuth
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;

