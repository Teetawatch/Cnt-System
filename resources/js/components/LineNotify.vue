<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
          <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
            <i class="fa-brands fa-line text-white text-lg"></i>
          </div>
          แจ้งเตือนผ่าน LINE
        </h1>
        <p class="text-slate-500 text-sm mt-1">ส่งตารางปฏิบัติงานผ่าน LINE Messaging API</p>
      </div>
      <button @click="openSettings" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all duration-300 text-sm font-medium shadow-sm">
        <i class="fa-solid fa-gear"></i>
        ตั้งค่า
      </button>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Bot Status -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">สถานะ Bot</span>
          <div class="w-8 h-8 rounded-lg flex items-center justify-center" :class="settings.channel_access_token ? 'bg-emerald-50' : 'bg-slate-100'">
            <i class="fa-solid fa-robot text-sm" :class="settings.channel_access_token ? 'text-emerald-500' : 'text-slate-400'"></i>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full" :class="settings.channel_access_token ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300'"></div>
          <span class="font-semibold text-sm" :class="settings.channel_access_token ? 'text-emerald-600' : 'text-slate-500'">
            {{ settings.channel_access_token ? 'เชื่อมต่อแล้ว' : 'ยังไม่ได้เชื่อมต่อ' }}
          </span>
        </div>
        <p v-if="settings.channel_access_token" class="text-xs text-slate-400 mt-2">
          <i class="fa-solid mr-1" :class="settings.send_mode === 'broadcast' ? 'fa-bullhorn' : 'fa-paper-plane'"></i>
          โหมด: {{ settings.send_mode === 'broadcast' ? 'Broadcast (ทุกคน)' : 'Push (' + (settings.destination_name || 'กลุ่ม/บุคคล') + ')' }}
        </p>
      </div>

      <!-- Notification Toggle -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">การแจ้งเตือน</span>
          <button
            @click="toggleEnabled"
            :disabled="toggling"
            class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none"
            :class="settings.is_enabled ? 'bg-emerald-500' : 'bg-slate-300'"
          >
            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300" :class="settings.is_enabled ? 'translate-x-6' : 'translate-x-1'"></span>
          </button>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-2.5 h-2.5 rounded-full" :class="settings.is_enabled ? 'bg-emerald-400' : 'bg-slate-300'"></div>
          <span class="font-semibold text-sm" :class="settings.is_enabled ? 'text-emerald-600' : 'text-slate-500'">
            {{ settings.is_enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
          </span>
        </div>
      </div>

      <!-- Schedule Status -->
      <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ส่งอัตโนมัติ</span>
          <button
            @click="toggleSchedule"
            :disabled="toggling"
            class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none"
            :class="settings.schedule_enabled ? 'bg-indigo-500' : 'bg-slate-300'"
          >
            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300" :class="settings.schedule_enabled ? 'translate-x-6' : 'translate-x-1'"></span>
          </button>
        </div>
        <div class="flex items-center gap-2">
          <i class="fa-regular fa-clock text-xs" :class="settings.schedule_enabled ? 'text-indigo-500' : 'text-slate-400'"></i>
          <span class="font-semibold text-sm" :class="settings.schedule_enabled ? 'text-indigo-600' : 'text-slate-500'">
            {{ settings.schedule_enabled ? 'ส่งทุกวัน เวลา ' + settings.schedule_time + ' น.' : 'ปิดอยู่' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
      <button @click="activeTab = 'send'" :class="activeTab === 'send' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-300">
        <i class="fa-solid fa-paper-plane mr-1.5"></i>
        ส่งข้อความ
      </button>
      <button @click="activeTab = 'logs'; fetchLogs()" :class="activeTab === 'logs' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-300">
        <i class="fa-solid fa-clock-rotate-left mr-1.5"></i>
        ประวัติการส่ง
      </button>
    </div>

    <!-- Send Tab -->
    <div v-if="activeTab === 'send'" class="grid grid-cols-1 lg:grid-cols-5 gap-6">
      <!-- Send Control Panel -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
              <i class="fa-solid fa-paper-plane text-emerald-500"></i>
              ส่งข้อความด้วยตนเอง
            </h3>
            <p class="text-xs text-slate-500 mt-1">เลือกวันที่แล้วกดส่งเพื่อแจ้งเตือนผ่าน LINE</p>
          </div>
          <div class="p-6 space-y-5">
            <!-- Date Picker -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-2">
                <i class="fa-regular fa-calendar mr-1 text-indigo-500"></i>
                เลือกวันที่ส่ง
              </label>
              <input type="date" v-model="sendDate" @change="fetchEventsForDate" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm shadow-sm" />
            </div>
            <!-- Quick Date Buttons -->
            <div class="flex flex-wrap gap-2">
              <button @click="setSendDate('today')" :class="isToday ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                วันนี้
              </button>
              <button @click="setSendDate('tomorrow')" :class="isTomorrow ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all">
                พรุ่งนี้
              </button>
            </div>
            <!-- Event Count -->
            <div class="bg-slate-50 rounded-xl p-4">
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">กิจกรรมในวันที่เลือก</span>
                <span class="text-lg font-bold text-indigo-600">{{ todayEvents.length }} รายการ</span>
              </div>
            </div>
            <!-- Send Mode Info -->
            <div class="bg-slate-50 rounded-xl p-4 flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" :class="settings.send_mode === 'broadcast' ? 'bg-violet-100' : 'bg-blue-100'">
                <i class="fa-solid text-xs" :class="settings.send_mode === 'broadcast' ? 'fa-bullhorn text-violet-500' : 'fa-paper-plane text-blue-500'"></i>
              </div>
              <div>
                <p class="text-sm font-medium text-slate-700">{{ settings.send_mode === 'broadcast' ? 'Broadcast' : 'Push Message' }}</p>
                <p class="text-xs text-slate-500">
                  {{ settings.send_mode === 'broadcast' ? 'ส่งถึงเพื่อนทุกคนของ Bot' : 'ส่งไปที่ ' + (settings.destination_name || settings.destination_id || 'ยังไม่ได้ตั้งค่า') }}
                </p>
              </div>
            </div>
            <!-- Send Button -->
            <button
              @click="sendNow"
              :disabled="!settings.is_enabled || sending"
              class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
            >
              <template v-if="!sending">
                <i class="fa-brands fa-line text-lg"></i>
                ส่งแจ้งเตือน LINE ตอนนี้
              </template>
              <template v-else>
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                กำลังส่ง...
              </template>
            </button>
            <p v-if="!settings.is_enabled" class="text-xs text-amber-600 bg-amber-50 rounded-lg p-3 flex items-center gap-2">
              <i class="fa-solid fa-triangle-exclamation"></i>
              กรุณาเปิดการแจ้งเตือนก่อนส่งข้อความ
            </p>
          </div>
        </div>
      </div>

      <!-- Event Preview -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-violet-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
              <i class="fa-solid fa-eye text-indigo-500"></i>
              ตัวอย่างข้อมูลที่จะส่ง
            </h3>
            <p class="text-xs text-slate-500 mt-1">{{ formattedSendDate }}</p>
          </div>
          <div class="p-6">
            <div v-if="loadingEvents" class="text-center py-12 text-slate-400">
              <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block"></i>
              กำลังโหลด...
            </div>
            <div v-else-if="groupedEvents.length === 0" class="text-center py-12">
              <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-regular fa-calendar-xmark text-2xl text-slate-400"></i>
              </div>
              <p class="text-slate-500 font-medium">ไม่มีกิจกรรมในวันที่เลือก</p>
              <p class="text-slate-400 text-sm mt-1">ข้อความที่ส่งจะแจ้งว่าไม่มีกิจกรรม</p>
            </div>
            <div v-else class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
              <div v-for="group in groupedEvents" :key="group.staff_id" class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                  <img v-if="group.staff_photo_url" :src="group.staff_photo_url" :alt="group.staff_name" class="w-9 h-9 rounded-lg object-cover border-2 border-white shadow-sm" />
                  <div v-else class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-user text-indigo-500 text-sm"></i>
                  </div>
                  <div>
                    <p class="font-bold text-slate-800 text-sm">{{ group.staff_name }}</p>
                    <p v-if="group.staff_position" class="text-xs text-slate-500">{{ group.staff_position }}</p>
                  </div>
                  <span class="ml-auto text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">{{ group.events.length }} งาน</span>
                </div>
                <div class="space-y-2">
                  <div v-for="event in group.events" :key="event.id" class="bg-white rounded-lg p-3 border border-slate-100 text-sm">
                    <div class="flex items-center gap-2 text-slate-800 font-medium">
                      <i class="fa-regular fa-clock text-xs text-indigo-400"></i>
                      {{ event.time_range }}
                    </div>
                    <p class="text-slate-600 mt-1"><i class="fa-solid fa-thumbtack text-xs text-amber-400 mr-1"></i>{{ event.title }}</p>
                    <p class="text-slate-500 text-xs mt-0.5"><i class="fa-solid fa-location-dot text-xs text-rose-400 mr-1"></i>{{ event.location }}</p>
                    <p v-if="event.organization" class="text-slate-500 text-xs mt-0.5"><i class="fa-regular fa-building text-xs text-blue-400 mr-1"></i>{{ event.organization }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Logs Tab -->
    <div v-if="activeTab === 'logs'" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100">
        <h3 class="font-bold text-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i>
          ประวัติการส่งแจ้งเตือน
        </h3>
      </div>

      <div v-if="logsLoading" class="py-16 text-center text-slate-400">
        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block"></i>
        กำลังโหลด...
      </div>
      <div v-else-if="logs.length === 0" class="text-center py-16">
        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <i class="fa-regular fa-clock text-2xl text-slate-400"></i>
        </div>
        <p class="text-slate-500 font-medium">ยังไม่มีประวัติการส่ง</p>
        <p class="text-slate-400 text-sm mt-1">เมื่อส่งแจ้งเตือน ประวัติจะแสดงที่นี่</p>
      </div>
      <template v-else>
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-slate-50 text-left">
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">วันที่ส่ง</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">วันที่ข้อมูล</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">ประเภท</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">กิจกรรม</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">ส่งโดย</th>
                <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">จัดการ</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 text-sm text-slate-600">{{ log.created_at }}</td>
                <td class="px-6 py-4 text-sm text-slate-800 font-medium">{{ log.notification_date }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" :class="log.send_type === 'manual' ? 'bg-blue-50 text-blue-600' : 'bg-violet-50 text-violet-600'">
                    <i class="fa-solid text-[10px]" :class="log.send_type === 'manual' ? 'fa-hand-pointer' : 'fa-robot'"></i>
                    {{ log.send_type_label }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold" :class="log.status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                    <i class="fa-solid text-[10px]" :class="log.status === 'success' ? 'fa-check-circle' : 'fa-times-circle'"></i>
                    {{ log.status_label }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ log.events_count }} รายการ</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ log.sender_name }}</td>
                <td class="px-6 py-4">
                  <button @click="deleteLog(log.id)" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fa-regular fa-trash-can text-sm"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-slate-100">
          <div v-for="log in logs" :key="log.id" class="p-4 space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-800">{{ log.notification_date }}</span>
              <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold" :class="log.status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                <i class="fa-solid" :class="log.status === 'success' ? 'fa-check' : 'fa-times'"></i>
                {{ log.status_label }}
              </span>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
              <span><i class="fa-regular fa-clock mr-1"></i>{{ log.created_at }}</span>
              <span>{{ log.events_count }} กิจกรรม</span>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="logsPagination.last_page > 1" class="px-6 py-4 border-t border-slate-100 flex justify-end gap-1">
          <button @click="fetchLogs(logsPagination.current_page - 1)" :disabled="logsPagination.current_page === 1" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 flex items-center justify-center">
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>
          <button @click="fetchLogs(logsPagination.current_page + 1)" :disabled="logsPagination.current_page === logsPagination.last_page" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 disabled:opacity-40 flex items-center justify-center">
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </button>
        </div>
      </template>
    </div>

    <!-- Settings Modal -->
    <div v-if="showSettingsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeSettings"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto z-10">
        <!-- Header -->
        <div class="sticky top-0 z-10 bg-white border-b border-slate-100 px-6 py-4 rounded-t-2xl flex items-center justify-between">
          <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
            <i class="fa-solid fa-gear text-indigo-500"></i>
            ตั้งค่า LINE Messaging API
          </h3>
          <button @click="closeSettings" class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-all">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <!-- Channel Access Token -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">
              <i class="fa-solid fa-key text-amber-500 mr-1"></i>
              Channel Access Token
            </label>
            <p class="text-xs text-slate-500 mb-3">
              สร้าง Bot และรับ Token ได้ที่
              <a href="https://developers.line.biz/console/" target="_blank" class="text-indigo-600 hover:underline font-medium">LINE Developers Console</a>
            </p>
            <div class="relative">
              <input
                :type="showToken ? 'text' : 'password'"
                v-model="settingsForm.channel_access_token"
                placeholder="กรอก Channel Access Token (Long-lived)"
                class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm pr-28 shadow-sm"
              />
              <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                <button @click="showToken = !showToken" type="button" class="px-2 py-1 text-slate-400 hover:text-slate-600">
                  <i class="fa-solid text-sm" :class="showToken ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
                <button @click="testToken" type="button" :disabled="testingToken" class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-semibold hover:bg-indigo-100 transition-colors">
                  <i v-if="testingToken" class="fa-solid fa-spinner animate-spin"></i>
                  <span v-else>ตรวจสอบ</span>
                </button>
              </div>
            </div>
            <p v-if="tokenTestResult" class="text-xs mt-2 font-medium" :class="tokenTestResult.success ? 'text-emerald-600' : 'text-rose-500'">
              <i class="fa-solid mr-1" :class="tokenTestResult.success ? 'fa-check-circle' : 'fa-times-circle'"></i>
              {{ tokenTestResult.message }}
            </p>
          </div>

          <!-- Send Mode -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">
              <i class="fa-solid fa-route text-indigo-500 mr-1"></i>
              โหมดการส่ง
            </label>
            <div class="grid grid-cols-2 gap-3">
              <label class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="settingsForm.send_mode === 'broadcast' ? 'border-violet-500 bg-violet-50' : 'border-slate-200 hover:border-slate-300'">
                <input type="radio" v-model="settingsForm.send_mode" value="broadcast" class="sr-only" />
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="settingsForm.send_mode === 'broadcast' ? 'bg-violet-100' : 'bg-slate-100'">
                  <i class="fa-solid fa-bullhorn" :class="settingsForm.send_mode === 'broadcast' ? 'text-violet-600' : 'text-slate-400'"></i>
                </div>
                <span class="text-sm font-semibold" :class="settingsForm.send_mode === 'broadcast' ? 'text-violet-700' : 'text-slate-600'">Broadcast</span>
                <span class="text-[10px] text-slate-500 text-center">ส่งถึงเพื่อนทุกคนของ Bot</span>
              </label>
              <label class="relative flex flex-col items-center gap-2 p-4 rounded-xl border-2 cursor-pointer transition-all" :class="settingsForm.send_mode === 'push' ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'">
                <input type="radio" v-model="settingsForm.send_mode" value="push" class="sr-only" />
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="settingsForm.send_mode === 'push' ? 'bg-blue-100' : 'bg-slate-100'">
                  <i class="fa-solid fa-paper-plane" :class="settingsForm.send_mode === 'push' ? 'text-blue-600' : 'text-slate-400'"></i>
                </div>
                <span class="text-sm font-semibold" :class="settingsForm.send_mode === 'push' ? 'text-blue-700' : 'text-slate-600'">Push</span>
                <span class="text-[10px] text-slate-500 text-center">ส่งไปที่กลุ่มหรือบุคคลเฉพาะ</span>
              </label>
            </div>
          </div>

          <!-- Destination (Push mode only) -->
          <div v-if="settingsForm.send_mode === 'push'" class="space-y-3 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">
                <i class="fa-solid fa-id-badge text-blue-500 mr-1"></i>
                User ID / Group ID
              </label>
              <input type="text" v-model="settingsForm.destination_id" placeholder="U1234... หรือ C1234..." class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500/20 text-sm shadow-sm font-mono" />
            </div>
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1.5">
                <i class="fa-solid fa-tag text-blue-500 mr-1"></i>
                ชื่อปลายทาง (ไม่บังคับ)
              </label>
              <input type="text" v-model="settingsForm.destination_name" placeholder="เช่น กลุ่มพนักงาน" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500/20 text-sm shadow-sm" />
            </div>
          </div>

          <!-- Test Message -->
          <div>
            <button @click="sendTestMessage" type="button" :disabled="sendingTest" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-700 rounded-xl text-sm font-semibold hover:bg-emerald-100 border border-emerald-200 transition-all">
              <template v-if="!sendingTest">
                <i class="fa-solid fa-vial mr-1"></i>
                ส่งข้อความทดสอบ
              </template>
              <template v-else>
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                กำลังส่ง...
              </template>
            </button>
          </div>

          <hr class="border-slate-100" />

          <!-- Schedule Time -->
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">
              <i class="fa-regular fa-clock text-indigo-500 mr-1"></i>
              เวลาส่งอัตโนมัติ
            </label>
            <p class="text-xs text-slate-500 mb-3">ระบบจะส่งข้อมูลกิจกรรมของวันนั้นๆ ตามเวลาที่กำหนด</p>
            <input type="time" v-model="settingsForm.schedule_time" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm shadow-sm" />
          </div>

          <!-- Message Template -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-bold text-slate-700">
                <i class="fa-solid fa-message text-emerald-500 mr-1"></i>
                รูปแบบข้อความ
              </label>
              <button @click="resetTemplate" type="button" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                <i class="fa-solid fa-rotate-right mr-1"></i>รีเซ็ต
              </button>
            </div>
            <p class="text-xs text-slate-500 mb-3">
              ตัวแปรที่ใช้ได้:
              <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{date}</code>
              <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{events}</code>
              <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{total}</code>
            </p>
            <textarea v-model="settingsForm.message_template" rows="6" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm font-mono shadow-sm resize-none"></textarea>
          </div>

          <!-- Save Button -->
          <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <button @click="closeSettings" class="px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl font-medium hover:bg-slate-50">ยกเลิก</button>
            <button @click="saveSettings" :disabled="savingSettings" class="btn-primary">
              <i v-if="!savingSettings" class="fa-solid fa-save me-2"></i>
              <i v-else class="fa-solid fa-spinner fa-spin me-2"></i>
              {{ savingSettings ? 'กำลังบันทึก...' : 'บันทึกการตั้งค่า' }}
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
  name: 'LineNotify',
  props: {
    initialSettings: { type: Object, default: () => ({}) },
  },
  data() {
    const today    = new Date().toISOString().split('T')[0];
    const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    return {
      settings: {
        channel_access_token: '',
        is_enabled: false,
        send_mode: 'broadcast',
        destination_id: '',
        destination_name: '',
        schedule_enabled: false,
        schedule_time: '07:00',
        message_template: '',
        ...this.initialSettings,
      },
      activeTab: 'send',
      sendDate: today,
      todayEvents: [],
      groupedEvents: [],
      loadingEvents: false,
      sending: false,
      toggling: false,
      logs: [],
      logsPagination: { current_page: 1, last_page: 1 },
      logsLoading: false,
      showSettingsModal: false,
      settingsForm: {},
      showToken: false,
      testingToken: false,
      tokenTestResult: null,
      sendingTest: false,
      savingSettings: false,
      toast: { show: false, type: 'success', message: '' },
      today,
      tomorrow,
    };
  },
  computed: {
    appBase() { return window._appBase || ''; },
    isToday()    { return this.sendDate === this.today; },
    isTomorrow() { return this.sendDate === this.tomorrow; },
    formattedSendDate() {
      if (!this.sendDate) return '';
      const d = new Date(this.sendDate);
      return d.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    },
  },
  mounted() {
    this.loadInitialSettings();
    this.fetchEventsForDate();
  },
  methods: {
    async loadInitialSettings() {
      try {
        const res = await axios.get(`${this.appBase}/api/vue/line-notify`);
        this.settings = res.data.settings;
      } catch (e) {
        console.error(e);
      }
    },
    async fetchEventsForDate() {
      this.loadingEvents = true;
      try {
        const res = await axios.get(`${this.appBase}/api/vue/line-notify/events`, {
          params: { send_date: this.sendDate },
        });
        this.todayEvents   = res.data.events;
        this.groupedEvents = res.data.grouped;
      } catch (e) {
        console.error(e);
      } finally {
        this.loadingEvents = false;
      }
    },
    setSendDate(which) {
      this.sendDate = which === 'today' ? this.today : this.tomorrow;
      this.fetchEventsForDate();
    },
    async toggleEnabled() {
      this.toggling = true;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/toggle-enabled`);
        this.settings.is_enabled = res.data.is_enabled;
        this.showToast('success', res.data.message);
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาด');
      } finally {
        this.toggling = false;
      }
    },
    async toggleSchedule() {
      this.toggling = true;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/toggle-schedule`);
        this.settings.schedule_enabled = res.data.schedule_enabled;
        this.showToast('success', res.data.message);
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาด');
      } finally {
        this.toggling = false;
      }
    },
    async sendNow() {
      this.sending = true;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/send-now`, { send_date: this.sendDate });
        this.showToast('success', res.data.message || 'ส่งแจ้งเตือนสำเร็จ');
      } catch (e) {
        this.showToast('error', e.response?.data?.message || 'เกิดข้อผิดพลาดในการส่ง');
      } finally {
        this.sending = false;
      }
    },
    async fetchLogs(page = 1) {
      this.logsLoading = true;
      try {
        const res = await axios.get(`${this.appBase}/api/vue/line-notify/logs`, { params: { page } });
        this.logs           = res.data.data;
        this.logsPagination = { current_page: res.data.current_page, last_page: res.data.last_page };
      } catch (e) {
        console.error(e);
      } finally {
        this.logsLoading = false;
      }
    },
    async deleteLog(id) {
      if (!confirm('ต้องการลบ Log นี้หรือไม่?')) return;
      try {
        await axios.delete(`${this.appBase}/api/vue/line-notify/log/${id}`);
        this.showToast('success', 'ลบ Log สำเร็จ');
        this.fetchLogs();
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาด');
      }
    },
    openSettings() {
      this.settingsForm     = { ...this.settings };
      this.showToken        = false;
      this.tokenTestResult  = null;
      this.showSettingsModal = true;
      document.body.classList.add('overflow-hidden');
    },
    closeSettings() {
      this.showSettingsModal = false;
      document.body.classList.remove('overflow-hidden');
    },
    async testToken() {
      this.testingToken    = true;
      this.tokenTestResult = null;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/test-token`, {
          token: this.settingsForm.channel_access_token,
        });
        this.tokenTestResult = res.data;
      } catch (e) {
        this.tokenTestResult = { success: false, message: e.response?.data?.message || 'ตรวจสอบไม่สำเร็จ' };
      } finally {
        this.testingToken = false;
      }
    },
    async sendTestMessage() {
      this.sendingTest = true;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/send-test`, {
          channel_access_token: this.settingsForm.channel_access_token,
          send_mode:            this.settingsForm.send_mode,
          destination_id:       this.settingsForm.destination_id,
        });
        this.showToast('success', res.data.message || 'ส่งข้อความทดสอบสำเร็จ');
      } catch (e) {
        this.showToast('error', e.response?.data?.message || 'เกิดข้อผิดพลาดในการส่งทดสอบ');
      } finally {
        this.sendingTest = false;
      }
    },
    async saveSettings() {
      this.savingSettings = true;
      try {
        const res = await axios.post(`${this.appBase}/api/vue/line-notify/save-settings`, this.settingsForm);
        this.settings = res.data.settings;
        this.showToast('success', res.data.message || 'บันทึกการตั้งค่าสำเร็จ');
        this.closeSettings();
      } catch (e) {
        this.showToast('error', e.response?.data?.message || 'เกิดข้อผิดพลาด');
      } finally {
        this.savingSettings = false;
      }
    },
    resetTemplate() {
      this.settingsForm.message_template =
        "📅 ตารางปฏิบัติงาน {date}\n\n{events}\n\n✅ รวม {total} กิจกรรม";
    },
    showToast(type, message) {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },
  },
};
</script>
