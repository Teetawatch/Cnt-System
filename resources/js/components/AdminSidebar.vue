<template>
  <div>
    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 lg:hidden bg-slate-900/50 backdrop-blur-sm"
      @click="sidebarOpen = false"
    ></div>

    <!-- Mobile Sidebar Content -->
    <aside
      v-show="sidebarOpen"
      class="fixed inset-y-0 left-0 w-72 bg-white z-50 lg:hidden flex flex-col shadow-2xl transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white">
            <i class="fa-solid fa-calendar-check text-sm"></i>
          </div>
          <span class="font-bold text-lg text-slate-800">Admin Panel</span>
        </div>
        <button
          @click="sidebarOpen = false"
          class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 transition-colors"
        >
          <i class="fa-solid fa-xmark text-sm"></i>
        </button>
      </div>

      <div class="flex-grow overflow-y-auto pt-4 custom-scrollbar">
        <nav class="flex flex-col px-3 space-y-1">
          <div class="px-4 py-2 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em]">Navigation</div>
          <a :href="routes.adminDashboard"
             :class="isActive('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'"
             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
            <i class="fa-solid fa-chart-line w-5 text-center"></i>
            <span class="text-sm font-medium">แดชบอร์ด</span>
          </a>

          <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Management</div>
          <a :href="routes.staffIndex"
             :class="isActive('staff') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'"
             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
            <i class="fa-solid fa-user-gear w-5 text-center"></i>
            <span class="text-sm font-medium">จัดการผู้ปฏิบัติ</span>
          </a>
          <a :href="routes.calendarManage"
             :class="isActive('events') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'"
             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
            <i class="fa-solid fa-calendar-plus w-5 text-center"></i>
            <span class="text-sm font-medium">จัดการกิจกรรม</span>
          </a>
          <a :href="routes.lineNotify"
             :class="isActive('line-notify') ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-emerald-600'"
             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
            <i class="fa-brands fa-line w-5 text-center"></i>
            <span class="text-sm font-medium">แจ้งเตือน LINE</span>
          </a>

          <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Personal</div>
          <a :href="routes.calendarIndex"
             :class="isActive('calendar') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600'"
             class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
            <i class="fa-solid fa-calendar-days w-5 text-center"></i>
            <span class="text-sm font-medium">ดูปฏิทินรวม</span>
          </a>
        </nav>
      </div>
    </aside>

    <!-- Mobile Topbar hamburger button (slot-like via teleport) -->
    <div id="sidebar-toggle-target"></div>
  </div>
</template>

<script>
export default {
  name: 'AdminSidebar',
  data() {
    return {
      sidebarOpen: false,
      currentPath: window.location.pathname,
      routes: {
        adminDashboard: window._routes?.adminDashboard || '/admin/dashboard',
        staffIndex:     window._routes?.staffIndex     || '/admin/staff',
        calendarManage: window._routes?.calendarManage || '/admin/events',
        lineNotify:     window._routes?.lineNotify     || '/admin/line-notify',
        calendarIndex:  window._routes?.calendarIndex  || '/admin/calendar',
      },
    };
  },
  mounted() {
    // Expose toggle function to global scope for the Blade button
    window.toggleAdminSidebar = () => { this.sidebarOpen = !this.sidebarOpen; };
  },
  methods: {
    isActive(segment) {
      return this.currentPath.includes(segment);
    },
  },
};
</script>
