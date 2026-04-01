<template>
  <div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">กิจกรรมทั้งหมด</p>
          <h3 class="text-3xl font-bold text-slate-800">{{ stats.total_all }}</h3>
        </div>
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-calendar-days"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl"></div>
      </div>
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">เดือนนี้</p>
          <h3 class="text-3xl font-bold text-emerald-600">{{ stats.total_month }}</h3>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-calendar-week"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl"></div>
      </div>
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">รอดำเนินการ</p>
          <h3 class="text-3xl font-bold text-amber-500">{{ stats.total_pending }}</h3>
        </div>
        <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-clock"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl"></div>
      </div>
    </div>

    <!-- Main Card -->
    <div class="glass-card animate-fade-in-up relative overflow-hidden">
      <!-- Header with Search -->
      <div class="p-6 border-b border-slate-100 bg-slate-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
          <h3 class="font-bold text-slate-800">รายการงานทั้งหมด</h3>
        </div>
        <div class="flex items-center gap-3">
          <div class="relative w-full md:w-80">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input
              type="text"
              v-model="filters.search"
              @input="debouncedFetch"
              placeholder="ค้นหาชื่อเรื่อง, สถานที่ หรือหน่วยงาน..."
              class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm"
            />
          </div>
          <button @click="openCreateModal" class="btn-primary group whitespace-nowrap">
            <i class="fa-solid fa-plus me-2 transition-transform group-hover:rotate-90"></i>
            เพิ่มกิจกรรมใหม่
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-6 border-b border-slate-100 bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="space-y-1.5">
            <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">กรองวันที่</label>
            <input type="date" v-model="filters.filter_date" @change="fetchData" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer" />
          </div>
          <div class="space-y-1.5">
            <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">เลือกผู้ปฏิบัติ</label>
            <select v-model="filters.filter_staff" @change="fetchData" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
              <option value="">ผู้ปฏิบัติงานทั้งหมด</option>
              <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">สถานะกิจกรรม</label>
            <select v-model="filters.filter_status" @change="fetchData" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
              <option value="">ทุกสถานะ</option>
              <option value="pending">🟡 รอยืนยัน</option>
              <option value="confirmed">🟢 ยืนยันแล้ว</option>
              <option value="cancelled">🔴 ยกเลิก</option>
            </select>
          </div>
          <div class="flex items-end gap-2">
            <button v-if="hasFilters" @click="clearFilters" class="w-full px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
              <i class="fa-solid fa-rotate-left text-xs"></i>
              ล้างตัวกรอง
            </button>
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div v-if="!loading" class="hidden lg:block">
        <table class="w-full border-separate border-spacing-0">
          <thead>
            <tr class="bg-slate-50/50 text-left border-b border-slate-100">
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100">วัน/เวลา</th>
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100">กิจกรรม/รายละเอียด</th>
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100">ผู้รับผิดชอบ</th>
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100">สถานที่</th>
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100 text-center">สถานะ</th>
              <th class="px-6 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-widest border-b border-slate-100 text-right">จัดการ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr v-for="event in events" :key="event.id" class="hover:bg-indigo-50/30 transition-all duration-300 group">
              <td class="px-6 py-5 whitespace-nowrap align-top">
                <div class="flex flex-col">
                  <div class="text-base font-bold text-slate-700">{{ event.event_date ? event.event_date.split('-')[2] : '' }}</div>
                  <div class="text-[10px] uppercase font-bold text-indigo-500 tracking-tighter">{{ event.event_month }}</div>
                  <div class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-md px-1.5 py-0.5 w-fit">
                    <i class="fa-regular fa-clock"></i> {{ event.time_range }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 align-top">
                <div class="flex flex-col gap-1.5">
                  <div class="flex flex-wrap items-baseline gap-x-4">
                    <p class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors leading-snug">{{ event.title }}</p>
                    <span v-if="event.organization" class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 flex items-center gap-1">
                      <i class="fa-solid fa-building-circle-check"></i> {{ event.organization }}
                    </span>
                  </div>
                  <p v-if="event.description" class="text-xs text-slate-400 max-w-2xl leading-relaxed italic">
                    <i class="fa-solid fa-quote-left text-[8px] opacity-30 me-1"></i>{{ event.description }}
                  </p>
                </div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap align-top">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100">
                    <img v-if="event.staff_photo_url" :src="event.staff_photo_url" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                      <i class="fa-solid fa-user-tie"></i>
                    </div>
                  </div>
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-700">{{ event.staff_name }}</span>
                    <span class="text-[10px] text-slate-400 uppercase font-bold tracking-tighter">Personnel</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap align-top">
                <div class="flex items-center gap-1.5 text-sm font-medium text-slate-500">
                  <div class="w-6 h-6 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center text-[10px]">
                    <i class="fa-solid fa-location-dot"></i>
                  </div>
                  {{ event.location }}
                </div>
              </td>
              <td class="px-6 py-5 whitespace-nowrap align-top text-center">
                <span :class="statusBadge(event.status)" class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm">
                  <span class="w-1.5 h-1.5 rounded-full me-1.5 animate-pulse" :class="statusDot(event.status)"></span>
                  {{ event.status_label }}
                </span>
              </td>
              <td class="px-6 py-5 whitespace-nowrap align-top text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openEditModal(event)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm active:scale-95" title="แก้ไข">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button @click="confirmDelete(event)" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-95" title="ลบ">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <div v-if="!loading" class="lg:hidden p-4 space-y-4">
        <div v-for="event in events" :key="event.id" class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4">
          <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
              <div class="bg-indigo-50 text-indigo-600 w-12 h-12 rounded-2xl flex flex-col items-center justify-center border border-indigo-100">
                <span class="text-lg font-bold leading-none">{{ event.event_date ? event.event_date.split('-')[2] : '' }}</span>
                <span class="text-[8px] uppercase font-bold">{{ event.event_month ? event.event_month.split(' ')[0] : '' }}</span>
              </div>
              <div>
                <h4 class="font-bold text-slate-800 leading-tight">{{ event.title }}</h4>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                    <i class="fa-regular fa-clock me-1"></i>{{ event.time_range }}
                  </span>
                  <span :class="statusTextClass(event.status)" class="text-[10px] font-bold uppercase tracking-wider">
                    ● {{ event.status_label }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-50">
            <div>
              <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-1">ผู้ปฏิบัติงาน</p>
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-slate-100 overflow-hidden border border-white ring-1 ring-slate-100">
                  <img v-if="event.staff_photo_url" :src="event.staff_photo_url" class="w-full h-full object-cover" />
                  <i v-else class="fa-solid fa-user text-[10px] text-slate-300 flex items-center justify-center h-full"></i>
                </div>
                <span class="text-xs font-bold text-slate-600">{{ event.staff_name }}</span>
              </div>
            </div>
            <div>
              <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-1">สถานที่</p>
              <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                <i class="fa-solid fa-location-dot text-rose-400"></i>
                {{ event.location?.substring(0, 20) }}
              </div>
            </div>
          </div>
          <div class="flex items-center justify-end gap-2">
            <button @click="openEditModal(event)" class="flex-1 py-2 bg-slate-50 border border-slate-100 rounded-xl text-indigo-600 font-bold text-xs hover:bg-indigo-50">
              <i class="fa-solid fa-pen-to-square me-1"></i> แก้ไข
            </button>
            <button @click="confirmDelete(event)" class="w-10 py-2 bg-slate-50 border border-slate-100 rounded-xl text-rose-500 hover:bg-rose-50">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="py-20 text-center text-slate-400">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-400 mb-2 block"></i>
        กำลังโหลด...
      </div>

      <!-- Empty State -->
      <div v-if="!loading && events.length === 0" class="px-6 py-24 text-center bg-white rounded-b-2xl">
        <div class="flex flex-col items-center justify-center">
          <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-sm ring-1 ring-slate-100">
            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-200"></i>
          </div>
          <p class="text-xl font-bold text-slate-800 mb-2">ไม่พบข้อมูลกิจกรรมที่คุณค้นหา</p>
          <p class="text-slate-500 mb-8 max-w-sm mx-auto">ลองเปลี่ยนเงื่อนไขการค้นหา หรือเพิ่มกิจกรรมใหม่</p>
          <button @click="openCreateModal" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-600/20 flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> สร้างกิจกรรมใหม่
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-6 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
        <p class="text-sm text-slate-500">
          แสดง {{ (pagination.current_page - 1) * 10 + 1 }} – {{ Math.min(pagination.current_page * 10, pagination.total) }} จาก {{ pagination.total }} รายการ
        </p>
        <div class="flex gap-1">
          <button @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center">
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="page === pagination.current_page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:bg-indigo-50'"
            class="w-9 h-9 rounded-lg border text-sm font-medium flex items-center justify-center transition-all"
          >{{ page }}</button>
          <button @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center">
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
      <div class="flex items-start sm:items-center justify-center min-h-screen p-2 sm:p-4">
        <div class="relative w-full max-w-2xl mx-auto my-2 sm:my-8 bg-white shadow-2xl rounded-2xl max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col border border-slate-100">
          <!-- Header -->
          <div class="flex items-center justify-between p-5 bg-gradient-to-r from-indigo-500 to-violet-600 flex-shrink-0">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <i :class="editMode ? 'fa-pen-to-square' : 'fa-calendar-plus'" class="fa-solid text-white text-lg"></i>
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">{{ editMode ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรมใหม่' }}</h3>
                <p class="text-xs text-white/80 hidden sm:block">กรอกรายละเอียดกิจกรรมด้านล่าง</p>
              </div>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <!-- Form Body -->
          <form @submit.prevent="save" class="flex-1 overflow-y-auto">
            <div class="p-6 space-y-6">
              <!-- Staff Selection -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user"></i></div>
                  ผู้ปฏิบัติงาน <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                  <label v-for="s in staffList" :key="s.id" class="cursor-pointer group relative">
                    <input type="radio" v-model="form.staff_id" :value="s.id" class="sr-only peer" />
                    <div class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200 text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 border-slate-100 hover:border-indigo-200">
                      <div class="w-12 h-12 rounded-full overflow-hidden mb-2 border-2 border-slate-100 transition-colors">
                        <img v-if="s.photo_url" :src="s.photo_url" :alt="s.name" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full bg-slate-100 flex items-center justify-center">
                          <i class="fa-solid fa-user text-slate-400"></i>
                        </div>
                      </div>
                      <p class="text-xs sm:text-sm font-bold truncate w-full peer-checked:text-indigo-700 text-slate-700">{{ s.name.substring(0, 12) }}</p>
                      <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                        <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center text-white text-xs shadow-sm">
                          <i class="fa-solid fa-check"></i>
                        </div>
                      </div>
                    </div>
                  </label>
                </div>
                <p v-if="errors.staff_id" class="text-xs mt-2 text-rose-500 font-medium"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ errors.staff_id[0] }}</p>
              </div>

              <div class="h-px bg-slate-100 w-full"></div>

              <!-- Date & Time -->
              <div class="bg-indigo-50/30 rounded-2xl p-6 border border-indigo-100">
                <div class="flex items-center gap-2 mb-6">
                  <div class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-calendar-day"></i>
                  </div>
                  <h4 class="font-bold text-slate-800">กำหนดวันและเวลา</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">วันที่เริ่มต้น <span class="text-rose-500">*</span></label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-calendar-alt"></i></span>
                          <input type="date" v-model="form.event_date" class="form-input-custom pl-10" />
                        </div>
                        <p v-if="errors.event_date" class="text-rose-500 text-xs mt-1">{{ errors.event_date[0] }}</p>
                      </div>
                      <div v-if="!editMode">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">วันที่สิ้นสุด</label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-calendar-check"></i></span>
                          <input type="date" v-model="form.end_date" :min="form.event_date" class="form-input-custom pl-10" />
                        </div>
                      </div>
                    </div>
                    <div v-if="!editMode && form.event_date && form.end_date && form.event_date !== form.end_date" class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-100 rounded-lg text-amber-700 text-xs font-medium">
                      <i class="fa-solid fa-info-circle"></i>
                      จะสร้างกิจกรรมทั้งหมด {{ daysDiff }} รายการ (แยกตามวัน)
                    </div>
                  </div>
                  <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">เวลาเริ่ม <span class="text-rose-500">*</span></label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-clock"></i></span>
                          <input type="time" v-model="form.start_time" class="form-input-custom pl-10" />
                        </div>
                        <p v-if="errors.start_time" class="text-rose-500 text-xs mt-1">{{ errors.start_time[0] }}</p>
                      </div>
                      <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">เวลาสิ้นสุด</label>
                        <div class="relative">
                          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-clock-rotate-left"></i></span>
                          <input type="time" v-model="form.end_time" class="form-input-custom pl-10" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Details -->
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">รายการงาน <span class="text-rose-500">*</span></label>
                  <input type="text" v-model="form.title" class="form-input-custom" placeholder="เช่น ประชุมคณะกรรมการ, ตรวจราชการ" />
                  <p v-if="errors.title" class="text-rose-500 text-xs mt-1">{{ errors.title[0] }}</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">สถานที่ <span class="text-rose-500">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-location-dot text-sm"></i></span>
                      <input type="text" v-model="form.location" class="form-input-custom pl-9" placeholder="ห้องประชุม, โรงเรียน" />
                    </div>
                    <p v-if="errors.location" class="text-rose-500 text-xs mt-1">{{ errors.location[0] }}</p>
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">หน่วยงาน</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-building text-sm"></i></span>
                      <input type="text" v-model="form.organization" class="form-input-custom pl-9" placeholder="สพม., เทศบาล" />
                    </div>
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">รายละเอียดเพิ่มเติม</label>
                  <textarea v-model="form.description" rows="3" class="form-input-custom rounded-xl" placeholder="รายละเอียดอื่นๆ..."></textarea>
                </div>
                <!-- Status -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">สถานะ</label>
                  <div class="flex flex-wrap gap-2">
                    <label class="cursor-pointer">
                      <input type="radio" v-model="form.status" value="confirmed" class="sr-only peer" />
                      <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 border-slate-200 text-slate-600 hover:border-emerald-200">
                        <i class="fa-solid fa-circle-check me-1.5"></i>ยืนยันแล้ว
                      </div>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" v-model="form.status" value="pending" class="sr-only peer" />
                      <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700 border-slate-200 text-slate-600 hover:border-amber-200">
                        <i class="fa-solid fa-clock me-1.5"></i>รอยืนยัน
                      </div>
                    </label>
                    <label class="cursor-pointer">
                      <input type="radio" v-model="form.status" value="cancelled" class="sr-only peer" />
                      <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 border-slate-200 text-slate-600 hover:border-rose-200">
                        <i class="fa-solid fa-xmark me-1.5"></i>ยกเลิก
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end gap-3 p-5 border-t border-slate-100 bg-slate-50/80 sticky bottom-0 backdrop-blur-sm z-10">
              <button type="button" @click="closeModal" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-white text-sm">ยกเลิก</button>
              <button type="submit" class="btn-primary" :disabled="saving">
                <i v-if="!saving" class="fa-solid fa-save me-2"></i>
                <i v-else class="fa-solid fa-spinner fa-spin me-2"></i>
                {{ saving ? 'กำลังบันทึก...' : 'บันทึกข้อมูล' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeDeleteModal"></div>
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative inline-block w-full max-w-sm p-6 text-center bg-white shadow-2xl rounded-2xl">
          <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white">
            <i class="fa-solid fa-trash-can text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">ยืนยันการลบ?</h3>
          <p class="text-slate-500 text-sm mb-6 leading-relaxed">
            คุณต้องการลบกิจกรรม <span class="font-bold text-slate-800">"{{ deleteTarget?.title }}"</span> ใช่หรือไม่?<br />การกระทำนี้ไม่สามารถเรียกคืนได้
          </p>
          <div class="grid grid-cols-2 gap-3">
            <button @click="closeDeleteModal" class="px-4 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50">ยกเลิก</button>
            <button @click="deleteEvent" :disabled="saving" class="px-4 py-2.5 bg-rose-500 text-white rounded-xl font-semibold hover:bg-rose-600 shadow-lg shadow-rose-500/30">
              <i v-if="saving" class="fa-solid fa-circle-notch fa-spin me-1"></i>
              {{ saving ? 'กำลังลบ...' : 'ลบกิจกรรม' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="toast.show" class="fixed top-4 right-4 z-[100] animate-fade-in-up">
      <div :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'" class="flex items-center gap-3 text-white px-5 py-3 rounded-2xl shadow-lg font-medium">
        <i :class="toast.type === 'success' ? 'fa-circle-check' : 'fa-circle-xmark'" class="fa-solid text-lg"></i>
        {{ toast.message }}
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'EventIndex',
  data() {
    const today = new Date().toISOString().split('T')[0];
    return {
      events: [],
      staffList: [],
      pagination: { total: 0, per_page: 10, current_page: 1, last_page: 1 },
      stats: { total_all: 0, total_month: 0, total_pending: 0 },
      filters: { search: '', filter_date: today, filter_staff: '', filter_status: '' },
      loading: false,
      saving: false,
      searchTimer: null,
      showModal: false,
      showDeleteModal: false,
      editMode: false,
      editId: null,
      deleteTarget: null,
      form: { staff_id: '', event_date: today, end_date: today, start_time: '', end_time: '', title: '', description: '', location: '', organization: '', status: 'confirmed' },
      errors: {},
      toast: { show: false, type: 'success', message: '' },
    };
  },
  computed: {
    appBase() { return window._appBase || ''; },
    hasFilters() {
      return this.filters.search || this.filters.filter_date || this.filters.filter_staff || this.filters.filter_status;
    },
    visiblePages() {
      const { current_page, last_page } = this.pagination;
      const pages = [];
      for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) {
        pages.push(i);
      }
      return pages;
    },
    daysDiff() {
      if (!this.form.event_date || !this.form.end_date) return 1;
      const start = new Date(this.form.event_date);
      const end   = new Date(this.form.end_date);
      return Math.max(1, Math.floor((end - start) / 86400000) + 1);
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const res = await axios.get(`${this.appBase}/api/vue/events`, {
          params: { ...this.filters, page: this.pagination.current_page },
        });
        this.events    = res.data.data;
        this.pagination = { total: res.data.total, per_page: res.data.per_page, current_page: res.data.current_page, last_page: res.data.last_page };
        this.stats     = { total_all: res.data.total_all, total_month: res.data.total_month, total_pending: res.data.total_pending };
        this.staffList = res.data.staff_list;
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    debouncedFetch() {
      clearTimeout(this.searchTimer);
      this.pagination.current_page = 1;
      this.searchTimer = setTimeout(() => this.fetchData(), 350);
    },
    goToPage(page) {
      if (page < 1 || page > this.pagination.last_page) return;
      this.pagination.current_page = page;
      this.fetchData();
    },
    clearFilters() {
      this.filters = { search: '', filter_date: '', filter_staff: '', filter_status: '' };
      this.pagination.current_page = 1;
      this.fetchData();
    },
    openCreateModal() {
      const today = new Date().toISOString().split('T')[0];
      this.editMode   = false;
      this.editId     = null;
      this.form       = { staff_id: '', event_date: today, end_date: today, start_time: '', end_time: '', title: '', description: '', location: '', organization: '', status: 'confirmed' };
      this.errors     = {};
      this.showModal  = true;
      document.body.classList.add('overflow-hidden');
    },
    openEditModal(event) {
      this.editMode  = true;
      this.editId    = event.id;
      this.form      = {
        staff_id:     event.staff_id,
        event_date:   event.event_date,
        end_date:     event.event_date,
        start_time:   event.start_time || '',
        end_time:     event.end_time || '',
        title:        event.title,
        description:  event.description || '',
        location:     event.location,
        organization: event.organization || '',
        status:       event.status,
      };
      this.errors    = {};
      this.showModal = true;
      document.body.classList.add('overflow-hidden');
    },
    closeModal() {
      this.showModal = false;
      document.body.classList.remove('overflow-hidden');
    },
    confirmDelete(event) {
      this.deleteTarget    = event;
      this.showDeleteModal = true;
      document.body.classList.add('overflow-hidden');
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.deleteTarget    = null;
      document.body.classList.remove('overflow-hidden');
    },
    async save() {
      this.saving = true;
      this.errors = {};
      try {
        if (this.editMode) {
          await axios.put(`${this.appBase}/api/vue/events/${this.editId}`, this.form);
          this.showToast('success', 'แก้ไขกิจกรรมสำเร็จ');
        } else {
          await axios.post(`${this.appBase}/api/vue/events`, this.form);
          this.showToast('success', `เพิ่มกิจกรรมสำเร็จ (${this.daysDiff} วัน)`);
          this.filters.filter_date = this.form.event_date;
        }
        this.closeModal();
        this.fetchData();
      } catch (e) {
        if (e.response?.status === 422) {
          this.errors = e.response.data.errors;
        } else {
          this.showToast('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
      } finally {
        this.saving = false;
      }
    },
    async deleteEvent() {
      if (!this.deleteTarget) return;
      this.saving = true;
      try {
        await axios.delete(`${this.appBase}/api/vue/events/${this.deleteTarget.id}`);
        this.showToast('success', 'ลบกิจกรรมสำเร็จ');
        this.closeDeleteModal();
        this.fetchData();
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาด');
      } finally {
        this.saving = false;
      }
    },
    showToast(type, message) {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },
    statusBadge(status) {
      return { confirmed: 'bg-emerald-100 text-emerald-700', pending: 'bg-amber-100 text-amber-700', cancelled: 'bg-rose-100 text-rose-700' }[status] || 'bg-slate-100 text-slate-700';
    },
    statusDot(status) {
      return { confirmed: 'bg-emerald-500', pending: 'bg-amber-500', cancelled: 'bg-rose-500' }[status] || 'bg-slate-400';
    },
    statusTextClass(status) {
      return { confirmed: 'text-emerald-500', pending: 'text-amber-500', cancelled: 'text-rose-500' }[status] || 'text-slate-400';
    },
  },
};
</script>
