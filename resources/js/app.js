import './bootstrap';
import { createApp } from 'vue';

import CalendarViewComponent from './components/CalendarView.vue';
import StaffIndexComponent from './components/StaffIndex.vue';
import EventIndexComponent from './components/EventIndex.vue';
import LineNotifyComponent from './components/LineNotify.vue';
import AdminSidebarComponent from './components/AdminSidebar.vue';

// Mount each component if its mount point exists
const mountIfExists = (selector, Component, props = {}) => {
    const el = document.querySelector(selector);
    if (el) {
        const app = createApp(Component, props);
        app.mount(el);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Calendar View
    const calendarEl = document.getElementById('vue-calendar-view');
    if (calendarEl) {
        createApp(CalendarViewComponent, {
            initialDate: calendarEl.dataset.date || '',
            calendarPdfBaseUrl: calendarEl.dataset.pdfUrl || '',
        }).mount(calendarEl);
    }

    // Staff Index
    const staffEl = document.getElementById('vue-staff-index');
    if (staffEl) {
        createApp(StaffIndexComponent).mount(staffEl);
    }

    // Event Index
    const eventEl = document.getElementById('vue-event-index');
    if (eventEl) {
        createApp(EventIndexComponent).mount(eventEl);
    }

    // Line Notify
    const lineEl = document.getElementById('vue-line-notify');
    if (lineEl) {
        createApp(LineNotifyComponent, {
            initialSettings: JSON.parse(lineEl.dataset.settings || '{}'),
        }).mount(lineEl);
    }

    // Admin Sidebar (mobile toggle)
    const sidebarEl = document.getElementById('vue-admin-sidebar');
    if (sidebarEl) {
        createApp(AdminSidebarComponent).mount(sidebarEl);
    }
});
