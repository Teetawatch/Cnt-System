<template>
  <div class="space-y-6">
    <!-- Date Navigation & Filters -->
    <div class="glass-card p-2 sm:p-4 animate-fade-in-up">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Date Navigation -->
        <div class="flex items-center justify-center lg:justify-start gap-3 bg-slate-50/50 p-2 rounded-xl border border-slate-100/50">
          <button @click="previousDay" class="w-10 h-10 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
            <i class="fa-solid fa-chevron-left"></i>
          </button>

          <div class="relative group">
            <i class="fa-solid fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-indigo-500 pointer-events-none"></i>
            <input
              type="date"
              v-model="selectedDate"
              @change="fetchData"
              class="pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm group-hover:border-indigo-200"
            />
          </div>

          <button @click="nextDay" class="w-10 h-10 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
            <i class="fa-solid fa-chevron-right"></i>
          </button>

          <button v-if="!isToday" @click="goToToday" class="px-4 py-2 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-all">
            วันนี้
          </button>
        </div>

        <!-- Staff Filter -->
        <div class="flex items-center justify-center lg:justify-end gap-3">
          <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-sm font-bold text-slate-500">
              <i class="fa-solid fa-filter me-1.5 text-indigo-500"></i>
              กรองตามบุคคล:
            </span>
            <select v-model="filterStaff" @change="fetchData" class="border-0 bg-transparent text-sm font-semibold text-slate-700 focus:ring-0 cursor-pointer py-1 pr-8">
              <option value="">ทั้งหมด</option>
              <option v-for="s in allStaff" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <a :href="pdfUrl" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md transition-all group">
            <i class="fa-solid fa-file-pdf me-2 text-rose-500 group-hover:scale-110 transition-transform"></i>
            พิมพ์ PDF
          </a>
        </div>
      </div>
    </div>

    <!-- Current Date Display -->
    <div class="text-center relative py-4">
      <div class="absolute inset-0 flex items-center justify-center z-0 opacity-10">
        <span class="text-8xl font-bold text-slate-200 select-none">{{ dayNumber }}</span>
      </div>
      <div class="relative z-10">
        <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">
          {{ formattedDate }}
        </h3>
        <div class="inline-flex items-center gap-2 mt-2 px-4 py-1.5 bg-white/60 backdrop-blur rounded-full border border-slate-200 shadow-sm">
          <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
          <span class="text-sm font-medium text-slate-600">{{ totalEvents }} กิจกรรม</span>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-16">
      <div class="flex items-center gap-3 text-slate-400">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-500"></i>
        <span class="font-medium">กำลังโหลด...</span>
      </div>
    </div>

    <!-- Events Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <template v-if="staffWithEvents.length > 0">
        <div
          v-for="(staff, idx) in staffWithEvents"
          :key="staff.id"
          class="glass-card overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col h-full animate-fade-in-up"
          :style="`animation-delay: ${idx * 100}ms`"
        >
          <!-- Staff Header -->
          <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 p-4 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-500/5 to-violet-500/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-150 duration-500"></div>
            <div class="flex items-center gap-4 relative z-10">
              <div class="relative">
                <div class="w-14 h-14 rounded-full p-1 bg-white shadow-sm border border-slate-100">
                  <div class="w-full h-full rounded-full overflow-hidden bg-slate-100">
                    <img v-if="staff.photo_url" :src="staff.photo_url" :alt="staff.name" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                      <i class="fa-solid fa-user text-xl"></i>
                    </div>
                  </div>
                </div>
                <div v-if="staff.events.length > 0" class="absolute -bottom-1 -right-1 bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                  {{ staff.events.length }}
                </div>
              </div>
              <div>
                <h4 class="font-bold text-lg text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">{{ staff.name }}</h4>
                <p class="text-slate-500 text-sm font-medium">{{ staff.position }}</p>
              </div>
            </div>
          </div>

          <!-- Events List -->
          <div class="p-4 space-y-3 flex-1 bg-white/40">
            <div
              v-for="event in staff.events"
              :key="event.id"
              @click="showEvent(event)"
              class="relative bg-white rounded-xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all cursor-pointer group/event overflow-hidden"
            >
              <div class="absolute left-0 top-0 bottom-0 w-1 rounded-r-full" :class="statusBarClass(event.status_color)"></div>
              <div class="flex flex-col sm:flex-row sm:items-start gap-4 pl-2">
                <!-- Time -->
                <div class="flex flex-col items-center justify-center min-w-[4rem] px-2 py-2 bg-slate-50 rounded-lg border border-slate-100">
                  <span class="text-sm font-bold text-slate-700">{{ event.start_time }}</span>
                  <template v-if="event.end_time">
                    <div class="w-4 h-px bg-slate-200 my-1"></div>
                    <span class="text-xs font-semibold text-slate-400">{{ event.end_time }}</span>
                  </template>
                </div>
                <!-- Info -->
                <div class="flex-1 min-w-0">
                  <div class="flex justify-between items-start gap-2 mb-2">
                    <h5 class="font-bold text-slate-800 text-base leading-tight group-hover/event:text-indigo-600 transition-colors">{{ event.title }}</h5>
                    <span :class="statusBadgeClass(event.status_color)" class="inline-flex shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border shadow-sm">
                      {{ event.status_label }}
                    </span>
                  </div>
                  <div class="flex flex-wrap gap-2 mt-2">
                    <span class="text-xs font-medium text-slate-600 flex items-center gap-1.5 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                      <i class="fa-solid fa-location-dot text-rose-400"></i>
                      {{ event.location }}
                    </span>
                    <span v-if="event.organization" class="text-xs font-medium text-slate-600 flex items-center gap-1.5 bg-indigo-50/50 px-2.5 py-1 rounded-lg border border-indigo-100/50">
                      <i class="fa-solid fa-building text-indigo-400"></i>
                      {{ event.organization }}
                    </span>
                  </div>
                  <div v-if="event.description" class="mt-4 p-3 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <div class="flex gap-2.5">
                      <i class="fa-solid fa-quote-left text-xs text-indigo-400 opacity-50 shrink-0 mt-1"></i>
                      <p class="text-sm leading-relaxed text-slate-600 italic font-medium">{{ event.description }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty staff events -->
            <div v-if="staff.events.length === 0" class="h-full flex flex-col items-center justify-center py-12 text-slate-400">
              <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="fa-regular fa-calendar-check text-2xl opacity-30"></i>
              </div>
              <p class="text-sm font-medium">ไม่มีกิจกรรมวันนี้</p>
            </div>
          </div>
        </div>
      </template>

      <!-- No staff -->
      <div v-else class="lg:col-span-3">
        <div class="flex flex-col items-center justify-center py-16 text-center">
          <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-6">
            <i class="fa-solid fa-users-slash text-4xl text-slate-300"></i>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">ไม่พบข้อมูลผู้ปฏิบัติงาน</h3>
          <p class="text-slate-500">ลองปรับเปลี่ยนตัวกรองค้นหาดูใหม่อีกครั้ง</p>
        </div>
      </div>
    </div>

    <!-- No events for existing staff -->
    <div v-if="!loading && staffWithEvents.length > 0 && totalEvents === 0" class="glass-card p-12 text-center text-slate-400 animate-fade-in-up">
      <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fa-regular fa-calendar-xmark text-3xl text-slate-300"></i>
      </div>
      <h3 class="text-lg font-bold text-slate-700">ไม่มีกิจกรรมในวันนี้</h3>
      <p class="text-sm mt-1">วันที่ {{ formattedDate }} ไม่มีรายการกิจกรรม</p>
    </div>

    <!-- Event Detail Modal -->
    <div v-if="showEventModal && selectedEvent" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="closeEventModal"></div>
      <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-lg bg-white shadow-2xl rounded-3xl animate-scale-in overflow-hidden border border-slate-100">
          <!-- Modal Header -->
          <div class="bg-gradient-to-br from-indigo-500 to-violet-600 p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full -ml-10 -mb-10 blur-xl"></div>
            <div class="relative z-10">
              <h3 class="text-xl font-bold leading-tight mb-1">{{ selectedEvent.title }}</h3>
              <div class="flex items-center gap-2 text-indigo-100 text-sm">
                <i class="fa-regular fa-calendar"></i>
                {{ selectedEvent.event_date_display }}
              </div>
            </div>
            <button @click="closeEventModal" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition-colors backdrop-blur-sm">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-6 space-y-5">
            <div class="grid grid-cols-2 gap-4">
              <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">เวลา</div>
                <div class="font-bold text-slate-800 flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                  {{ selectedEvent.time_range }}
                </div>
              </div>
              <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">สถานะ</div>
                <span :class="statusBadgeClass(selectedEvent.status_color)" class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold border">
                  <span class="w-1.5 h-1.5 rounded-full" :class="`bg-${selectedEvent.status_color}-500`"></span>
                  {{ selectedEvent.status_label }}
                </span>
              </div>
            </div>

            <!-- Staff Info -->
            <div v-if="selectedEvent.staff" class="flex items-center gap-4 p-4 bg-white border-2 border-slate-50 rounded-2xl shadow-sm">
              <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-indigo-500 to-violet-500">
                <div class="w-full h-full rounded-full overflow-hidden bg-white">
                  <img v-if="selectedEvent.staff.photo_url" :src="selectedEvent.staff.photo_url" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-slate-300">
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>
              </div>
              <div>
                <div class="text-xs font-bold text-slate-400 uppercase">ผู้ปฏิบัติงาน</div>
                <h4 class="font-bold text-slate-800">{{ selectedEvent.staff.name }}</h4>
                <p class="text-xs text-slate-500 font-medium">{{ selectedEvent.staff.position }}</p>
              </div>
            </div>

            <div class="space-y-3">
              <div class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-400">สถานที่</div>
                  <div class="font-medium text-slate-700">{{ selectedEvent.location }}</div>
                </div>
              </div>
              <div v-if="selectedEvent.organization" class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                  <i class="fa-solid fa-building"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-400">หน่วยงาน</div>
                  <div class="font-medium text-slate-700">{{ selectedEvent.organization }}</div>
                </div>
              </div>
              <div v-if="selectedEvent.description" class="flex gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                  <i class="fa-solid fa-align-left"></i>
                </div>
                <div>
                  <div class="text-xs font-bold text-slate-400">รายละเอียดเพิ่มเติม</div>
                  <div class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl mt-1 border border-slate-100">
                    {{ selectedEvent.description }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button @click="closeEventModal" class="px-6 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all">
              ปิดหน้าต่าง
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CalendarView',
  props: {
    initialDate: { type: String, default: '' },
    calendarPdfBaseUrl: { type: String, default: '' },
  },
  data() {
    const today = new Date().toISOString().split('T')[0];
    return {
      selectedDate: this.initialDate || today,
      filterStaff: '',
      staffWithEvents: [],
      allStaff: [],
      totalEvents: 0,
      formattedDate: '',
      isToday: true,
      loading: false,
      showEventModal: false,
      selectedEvent: null,
    };
  },
  computed: {
    dayNumber() {
      return this.selectedDate ? this.selectedDate.split('-')[2] : '';
    },
    pdfUrl() {
      const base = this.calendarPdfBaseUrl || '/admin/calendar/pdf';
      return `${base}?date=${this.selectedDate}&staff=${this.filterStaff}`;
    },
    appBaseUrl() {
      return window._appBase || '';
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const base = this.appBaseUrl;
        const res = await axios.get(`${base}/api/vue/calendar`, {
          params: { date: this.selectedDate, staff: this.filterStaff },
        });
        this.staffWithEvents = res.data.staff_with_events;
        this.allStaff        = res.data.all_staff;
        this.totalEvents     = res.data.total_events;
        this.formattedDate   = res.data.formatted_date;
        this.isToday         = res.data.is_today;
      } catch (e) {
        console.error('Calendar fetch error', e);
      } finally {
        this.loading = false;
      }
    },
    previousDay() {
      const d = new Date(this.selectedDate);
      d.setDate(d.getDate() - 1);
      this.selectedDate = d.toISOString().split('T')[0];
      this.fetchData();
    },
    nextDay() {
      const d = new Date(this.selectedDate);
      d.setDate(d.getDate() + 1);
      this.selectedDate = d.toISOString().split('T')[0];
      this.fetchData();
    },
    goToToday() {
      this.selectedDate = new Date().toISOString().split('T')[0];
      this.fetchData();
    },
    async showEvent(event) {
      try {
        const base = this.appBaseUrl;
        const res = await axios.get(`${base}/api/vue/calendar/event/${event.id}`);
        this.selectedEvent = res.data;
        this.showEventModal = true;
        document.body.classList.add('overflow-hidden');
      } catch (e) {
        this.selectedEvent = event;
        this.showEventModal = true;
      }
    },
    closeEventModal() {
      this.showEventModal = false;
      this.selectedEvent  = null;
      document.body.classList.remove('overflow-hidden');
    },
    statusBarClass(color) {
      const map = {
        emerald: 'bg-emerald-500',
        amber:   'bg-amber-500',
        rose:    'bg-rose-500',
        indigo:  'bg-indigo-500',
      };
      return map[color] || 'bg-slate-400';
    },
    statusBadgeClass(color) {
      const map = {
        emerald: 'bg-emerald-50 text-emerald-600 border-emerald-100',
        amber:   'bg-amber-50 text-amber-600 border-amber-100',
        rose:    'bg-rose-50 text-rose-600 border-rose-100',
        indigo:  'bg-indigo-50 text-indigo-600 border-indigo-100',
      };
      return map[color] || 'bg-slate-50 text-slate-600 border-slate-100';
    },
  },
};
</script>
