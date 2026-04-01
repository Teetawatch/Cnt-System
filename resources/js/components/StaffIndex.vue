<template>
  <div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">บุคลากรทั้งหมด</p>
          <h3 class="text-3xl font-bold text-slate-800">{{ pagination.total }}</h3>
        </div>
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-users"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl"></div>
      </div>
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">ใช้งานปกติ</p>
          <h3 class="text-3xl font-bold text-emerald-600">{{ totalActive }}</h3>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl"></div>
      </div>
      <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
        <div class="relative z-10">
          <p class="text-slate-500 font-medium mb-1">ปิดการใช้งาน</p>
          <h3 class="text-3xl font-bold text-slate-400">{{ totalInactive }}</h3>
        </div>
        <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
          <i class="fa-solid fa-user-slash"></i>
        </div>
        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-slate-500/5 rounded-full blur-2xl"></div>
      </div>
    </div>

    <!-- Main Table Card -->
    <div class="glass-card animate-fade-in-up">
      <!-- Table Header & Search -->
      <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <div class="relative">
            <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input
              type="text"
              v-model="search"
              @input="debouncedFetch"
              placeholder="ค้นหาชื่อ, ตำแหน่ง..."
              class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-full sm:w-72 transition-all"
            />
          </div>
        </div>
        <div class="flex items-center gap-2">
          <select v-model="perPage" @change="fetchData" class="bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
            <option value="10">10 รายการ</option>
            <option value="25">25 รายการ</option>
            <option value="50">50 รายการ</option>
            <option value="100">100 รายการ</option>
          </select>
          <button @click="openImportModal" class="px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-semibold text-sm hover:bg-emerald-600 hover:text-white hover:border-emerald-600 transition-all flex items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            นำเข้า Excel
          </button>
          <button @click="openCreateModal" class="btn-primary group">
            <i class="fa-solid fa-plus me-2 transition-transform group-hover:rotate-90"></i>
            เพิ่มผู้ปฏิบัติใหม่
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-slate-50/50 border-b border-slate-100 text-left">
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ลำดับ <i class="fa-solid fa-sort text-slate-400 ml-1"></i></th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้ปฏิบัติงาน</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ตำแหน่ง/หน่วยงาน</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">สถานะ</th>
              <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">จัดการ</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <template v-if="loading">
              <tr>
                <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                  <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-400 mb-2 block"></i>
                  กำลังโหลด...
                </td>
              </tr>
            </template>
            <template v-else-if="staffList.length > 0">
              <tr v-for="staff in staffList" :key="staff.id" class="hover:bg-slate-50/80 transition-colors group">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                  <div class="flex items-center gap-1.5">
                    <div class="flex flex-col gap-0.5">
                      <button @click="moveUp(staff)" :disabled="reordering || isFirst(staff)" class="w-6 h-5 rounded bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-indigo-100 hover:text-indigo-600 disabled:opacity-30 disabled:cursor-not-allowed transition-all text-[10px]" title="เลื่อนขึ้น">
                        <i class="fa-solid fa-chevron-up"></i>
                      </button>
                      <button @click="moveDown(staff)" :disabled="reordering || isLast(staff)" class="w-6 h-5 rounded bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-indigo-100 hover:text-indigo-600 disabled:opacity-30 disabled:cursor-not-allowed transition-all text-[10px]" title="เลื่อนลง">
                        <i class="fa-solid fa-chevron-down"></i>
                      </button>
                    </div>
                    <span class="bg-slate-100 text-slate-600 py-1 px-2 rounded-lg text-xs">{{ staff.sort_order }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-4">
                    <div class="relative">
                      <div class="w-12 h-12 rounded-xl overflow-hidden shadow-sm border border-slate-100 group-hover:shadow-md transition-all">
                        <img v-if="staff.photo_url" :src="staff.photo_url" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                          <i class="fa-solid fa-user text-slate-400"></i>
                        </div>
                      </div>
                      <div class="absolute -bottom-1 -right-1 w-4 h-4 border-2 border-white rounded-full" :class="staff.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></div>
                    </div>
                    <div>
                      <p class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors">{{ staff.name }}</p>
                      <p class="text-xs text-slate-400">{{ staff.email || '-' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <div class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                      <span class="text-sm font-medium text-slate-700">{{ staff.position }}</span>
                    </div>
                    <div v-if="staff.department" class="flex items-center gap-2">
                      <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                      <span class="text-sm text-slate-500">{{ staff.department }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <span v-if="staff.is_active" class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    ใช้งานปกติ
                  </span>
                  <span v-else class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                    ระงับการใช้งาน
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <button @click="openEditModal(staff)" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="แก้ไข">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button @click="confirmDelete(staff)" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm" title="ลบ">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
            <template v-else>
              <tr>
                <td colspan="5" class="px-6 py-24 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                      <i class="fa-solid fa-users-slash text-3xl text-slate-300"></i>
                    </div>
                    <p class="text-lg font-medium text-slate-900 mb-1">ไม่พบข้อมูลผู้ปฏิบัติงาน</p>
                    <p class="text-slate-500 mb-6">ลองเปลี่ยนคำค้นหา หรือเพิ่มผู้ปฏิบัติงานใหม่</p>
                    <button @click="openCreateModal" class="btn-primary">
                      <i class="fa-solid fa-plus me-2"></i>เพิ่มผู้ปฏิบัติงาน
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-6 border-t border-slate-100 flex items-center justify-between">
        <p class="text-sm text-slate-500">
          แสดง {{ (pagination.current_page - 1) * pagination.per_page + 1 }} – {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} จาก {{ pagination.total }} รายการ
        </p>
        <div class="flex gap-1">
          <button @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition-all">
            <i class="fa-solid fa-chevron-left text-xs"></i>
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="page === pagination.current_page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-200 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600'"
            class="w-9 h-9 rounded-lg border text-sm font-medium flex items-center justify-center transition-all"
          >{{ page }}</button>
          <button @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="w-9 h-9 rounded-lg border border-slate-200 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition-all">
            <i class="fa-solid fa-chevron-right text-xs"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative inline-block w-full max-w-lg overflow-hidden text-left align-middle bg-white shadow-2xl rounded-2xl border border-slate-100">
          <!-- Header -->
          <div class="bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <i :class="editMode ? 'fa-user-pen' : 'fa-user-plus'" class="fa-solid"></i>
              {{ editMode ? 'แก้ไขข้อมูลผู้ปฏิบัติงาน' : 'เพิ่มผู้ปฏิบัติงานใหม่' }}
            </h3>
            <button @click="closeModal" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg w-8 h-8 flex items-center justify-center">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="p-6">
            <form @submit.prevent="save">
              <div class="space-y-5">
                <!-- Photo Upload -->
                <div class="flex flex-col items-center justify-center mb-6">
                  <div class="relative group cursor-pointer" @click="$refs.photoInput.click()">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-100 flex items-center justify-center group-hover:border-indigo-100 transition-all">
                      <img v-if="photoPreview" :src="photoPreview" class="w-full h-full object-cover" />
                      <img v-else-if="currentPhotoUrl" :src="currentPhotoUrl" class="w-full h-full object-cover" />
                      <i v-else class="fa-solid fa-camera text-3xl text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                    </div>
                    <div class="absolute bottom-0 right-0 bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-md border-2 border-white group-hover:scale-110 transition-transform">
                      <i class="fa-solid fa-plus text-xs"></i>
                    </div>
                  </div>
                  <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="onPhotoChange" />
                  <p class="text-xs text-slate-500 mt-2">คลิกเพื่ออัปโหลดรูปภาพ (สูงสุด 2MB)</p>
                  <p v-if="errors.photo" class="text-rose-500 text-xs mt-1">{{ errors.photo[0] }}</p>
                </div>

                <!-- Inputs -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อ-นามสกุล <span class="text-rose-500">*</span></label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-user text-sm"></i></span>
                      <input type="text" v-model="form.name" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5" placeholder="ระบุชื่อ-นามสกุล" />
                    </div>
                    <p v-if="errors.name" class="text-rose-500 text-xs mt-1">{{ errors.name[0] }}</p>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-semibold text-slate-700 mb-1">ตำแหน่ง <span class="text-rose-500">*</span></label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-id-badge text-sm"></i></span>
                        <input type="text" v-model="form.position" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5" placeholder="ระบุตำแหน่ง" />
                      </div>
                      <p v-if="errors.position" class="text-rose-500 text-xs mt-1">{{ errors.position[0] }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-slate-700 mb-1">หน่วยงาน</label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-building text-sm"></i></span>
                        <input type="text" v-model="form.department" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5" placeholder="ระบุหน่วยงาน" />
                      </div>
                    </div>
                  </div>
                  <div class="grid grid-cols-2 gap-4 items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <div>
                      <label class="block text-sm font-semibold text-slate-700 mb-1">ลำดับการแสดงผล</label>
                      <input type="number" v-model="form.sort_order" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm" min="0" />
                    </div>
                    <div>
                      <label class="block text-sm font-semibold text-slate-700 mb-2">สถานะการใช้งาน</label>
                      <label class="inline-flex relative items-center cursor-pointer">
                        <input type="checkbox" v-model="form.is_active" class="sr-only peer" />
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-600">{{ form.is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
                <button type="button" @click="closeModal" class="px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl font-medium hover:bg-slate-50 transition-all">ยกเลิก</button>
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
    </div>

    <!-- Import Excel Modal -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeImportModal"></div>
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 sm:p-0">
        <div class="relative inline-block w-full max-w-lg overflow-hidden text-left align-middle bg-white shadow-2xl rounded-2xl border border-slate-100">
          <!-- Header -->
          <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <i class="fa-solid fa-file-excel"></i>
              นำเข้าข้อมูลผู้ปฏิบัติงาน (Excel)
            </h3>
            <button @click="closeImportModal" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg w-8 h-8 flex items-center justify-center">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="p-6 space-y-5">
            <!-- Step 1: Download Template -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
              <p class="text-sm font-semibold text-indigo-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-1 text-indigo-500"></i>
                ดาวน์โหลดแม่แบบ (Template)
              </p>
              <p class="text-xs text-indigo-600 mb-3">ดาวน์โหลดไฟล์ Excel แม่แบบและกรอกข้อมูลตามรูปแบบที่กำหนด</p>
              <a :href="`${appBase}/api/vue/staff/template`" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                <i class="fa-solid fa-download"></i>
                ดาวน์โหลดแม่แบบ .xlsx
              </a>
            </div>

            <!-- Step 2: Upload File -->
            <div>
              <p class="text-sm font-semibold text-slate-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-circle-2 text-slate-500"></i>
                อัปโหลดไฟล์ Excel ที่กรอกข้อมูลแล้ว
              </p>
              <div
                class="border-2 border-dashed rounded-xl p-6 text-center transition-all cursor-pointer"
                :class="importFile ? 'border-emerald-400 bg-emerald-50' : 'border-slate-200 bg-slate-50 hover:border-indigo-300 hover:bg-indigo-50'"
                @click="$refs.importFileInput.click()"
                @dragover.prevent
                @drop.prevent="onImportDrop"
              >
                <div v-if="!importFile">
                  <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-300 mb-2 block"></i>
                  <p class="text-sm text-slate-500">คลิกหรือลาก-วางไฟล์ .xlsx / .xls ที่นี่</p>
                  <p class="text-xs text-slate-400 mt-1">ขนาดสูงสุด 5MB</p>
                </div>
                <div v-else class="flex items-center justify-center gap-3">
                  <i class="fa-solid fa-file-excel text-2xl text-emerald-500"></i>
                  <div class="text-left">
                    <p class="text-sm font-semibold text-emerald-700">{{ importFile.name }}</p>
                    <p class="text-xs text-slate-400">{{ (importFile.size / 1024).toFixed(1) }} KB</p>
                  </div>
                  <button type="button" @click.stop="clearImportFile" class="ml-auto text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </div>
              </div>
              <input ref="importFileInput" type="file" accept=".xlsx,.xls" class="hidden" @change="onImportFileChange" />
              <p v-if="importErrors.file" class="text-rose-500 text-xs mt-1">{{ importErrors.file[0] }}</p>
            </div>

            <!-- Import Result -->
            <div v-if="importResult" class="rounded-xl p-4 border" :class="importResult.skipped > 0 ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200'">
              <p class="text-sm font-bold mb-1" :class="importResult.skipped > 0 ? 'text-amber-700' : 'text-emerald-700'">
                <i class="fa-solid" :class="importResult.skipped > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check'"></i>
                {{ importResult.message }}
              </p>
              <div v-if="importResult.row_errors && importResult.row_errors.length > 0" class="mt-2 space-y-1 max-h-32 overflow-y-auto">
                <div v-for="re in importResult.row_errors" :key="re.row" class="text-xs text-amber-700">
                  <span class="font-semibold">แถว {{ re.row }}:</span> {{ re.errors.join(', ') }}
                </div>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
              <button type="button" @click="closeImportModal" class="px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl font-medium hover:bg-slate-50 transition-all">ยกเลิก</button>
              <button type="button" @click="submitImport" :disabled="!importFile || importing" class="px-5 py-2 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <i v-if="!importing" class="fa-solid fa-upload me-2"></i>
                <i v-else class="fa-solid fa-spinner fa-spin me-2"></i>
                {{ importing ? 'กำลังนำเข้า...' : 'นำเข้าข้อมูล' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeDeleteModal"></div>
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-center bg-white shadow-2xl rounded-2xl">
          <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-5 border-4 border-white shadow-sm">
            <i class="fa-solid fa-trash-can text-3xl"></i>
          </div>
          <h3 class="text-xl font-bold text-slate-800 mb-2">ยืนยันการลบ?</h3>
          <p class="text-slate-500 text-sm mb-6 leading-relaxed">
            คุณต้องการลบข้อมูลของ <span class="font-bold text-slate-800">"{{ deleteTarget?.name }}"</span> ใช่หรือไม่?<br />
            การกระทำนี้ไม่สามารถเรียกคืนได้
          </p>
          <div class="grid grid-cols-2 gap-3">
            <button @click="closeDeleteModal" class="px-4 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50 transition-all">ยกเลิก</button>
            <button @click="deleteStaff" :disabled="saving" class="px-4 py-2.5 bg-rose-500 text-white rounded-xl font-semibold hover:bg-rose-600 shadow-lg shadow-rose-500/30 transition-all">
              <i v-if="saving" class="fa-solid fa-circle-notch fa-spin me-1"></i>
              {{ saving ? 'กำลังลบ...' : 'ลบข้อมูลทันที' }}
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
  name: 'StaffIndex',
  data() {
    return {
      staffList: [],
      pagination: { total: 0, per_page: 10, current_page: 1, last_page: 1 },
      totalActive: 0,
      totalInactive: 0,
      search: '',
      perPage: 10,
      loading: false,
      saving: false,
      reordering: false,
      searchTimer: null,
      showModal: false,
      showDeleteModal: false,
      showImportModal: false,
      importFile: null,
      importing: false,
      importErrors: {},
      importResult: null,
      editMode: false,
      editId: null,
      deleteTarget: null,
      form: { name: '', position: '', department: '', sort_order: 0, is_active: true },
      photoFile: null,
      photoPreview: null,
      currentPhotoUrl: '',
      errors: {},
      toast: { show: false, type: 'success', message: '' },
    };
  },
  computed: {
    appBase() { return window._appBase || ''; },
    visiblePages() {
      const { current_page, last_page } = this.pagination;
      const pages = [];
      const range = 2;
      for (let i = Math.max(1, current_page - range); i <= Math.min(last_page, current_page + range); i++) {
        pages.push(i);
      }
      return pages;
    },
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const res = await axios.get(`${this.appBase}/api/vue/staff`, {
          params: { search: this.search, per_page: this.perPage, page: this.pagination.current_page },
        });
        this.staffList    = res.data.data;
        this.pagination   = { total: res.data.total, per_page: res.data.per_page, current_page: res.data.current_page, last_page: res.data.last_page };
        this.totalActive  = res.data.total_active;
        this.totalInactive = res.data.total_inactive;
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
    openCreateModal() {
      this.editMode    = false;
      this.editId      = null;
      this.form        = { name: '', position: '', department: '', sort_order: 0, is_active: true };
      this.photoFile   = null;
      this.photoPreview = null;
      this.currentPhotoUrl = '';
      this.errors      = {};
      this.showModal   = true;
      document.body.classList.add('overflow-hidden');
    },
    openEditModal(staff) {
      this.editMode    = true;
      this.editId      = staff.id;
      this.form        = { name: staff.name, position: staff.position, department: staff.department || '', sort_order: staff.sort_order, is_active: !!staff.is_active };
      this.currentPhotoUrl = staff.photo_url || '';
      this.photoFile   = null;
      this.photoPreview = null;
      this.errors      = {};
      this.showModal   = true;
      document.body.classList.add('overflow-hidden');
    },
    closeModal() {
      this.showModal = false;
      document.body.classList.remove('overflow-hidden');
    },
    confirmDelete(staff) {
      this.deleteTarget = staff;
      this.showDeleteModal = true;
      document.body.classList.add('overflow-hidden');
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.deleteTarget = null;
      document.body.classList.remove('overflow-hidden');
    },
    onPhotoChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.photoFile = file;
      const reader = new FileReader();
      reader.onload = (ev) => { this.photoPreview = ev.target.result; };
      reader.readAsDataURL(file);
    },
    async save() {
      this.saving = true;
      this.errors = {};
      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('position', this.form.position);
      formData.append('department', this.form.department || '');
      formData.append('sort_order', this.form.sort_order);
      formData.append('is_active', this.form.is_active ? '1' : '0');
      if (this.photoFile) formData.append('photo', this.photoFile);

      try {
        if (this.editMode) {
          await axios.post(`${this.appBase}/api/vue/staff/${this.editId}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
          this.showToast('success', 'แก้ไขข้อมูลผู้ปฏิบัติงานสำเร็จ');
        } else {
          await axios.post(`${this.appBase}/api/vue/staff`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
          this.showToast('success', 'เพิ่มผู้ปฏิบัติงานสำเร็จ');
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
    async deleteStaff() {
      if (!this.deleteTarget) return;
      this.saving = true;
      try {
        await axios.delete(`${this.appBase}/api/vue/staff/${this.deleteTarget.id}`);
        this.showToast('success', 'ลบผู้ปฏิบัติงานสำเร็จ');
        this.closeDeleteModal();
        this.fetchData();
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาด');
      } finally {
        this.saving = false;
      }
    },
    openImportModal() {
      this.importFile   = null;
      this.importErrors = {};
      this.importResult = null;
      this.showImportModal = true;
      document.body.classList.add('overflow-hidden');
    },
    closeImportModal() {
      this.showImportModal = false;
      document.body.classList.remove('overflow-hidden');
    },
    onImportFileChange(e) {
      const file = e.target.files[0];
      if (file) { this.importFile = file; this.importErrors = {}; this.importResult = null; }
    },
    onImportDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file) { this.importFile = file; this.importErrors = {}; this.importResult = null; }
    },
    clearImportFile() {
      this.importFile = null;
      this.$refs.importFileInput.value = '';
    },
    async submitImport() {
      if (!this.importFile) return;
      this.importing = true;
      this.importErrors = {};
      this.importResult = null;
      const fd = new FormData();
      fd.append('file', this.importFile);
      try {
        const res = await axios.post(`${this.appBase}/api/vue/staff/import`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        this.importResult = res.data;
        if (res.data.imported > 0) {
          this.showToast('success', res.data.message);
          this.fetchData();
        }
        this.importFile = null;
        if (this.$refs.importFileInput) this.$refs.importFileInput.value = '';
      } catch (e) {
        if (e.response?.status === 422) {
          this.importErrors = e.response.data.errors;
        } else {
          this.showToast('error', 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
      } finally {
        this.importing = false;
      }
    },
    isFirst(staff) {
      return this.staffList.indexOf(staff) === 0;
    },
    isLast(staff) {
      return this.staffList.indexOf(staff) === this.staffList.length - 1;
    },
    async moveUp(staff) {
      const idx = this.staffList.indexOf(staff);
      if (idx <= 0) return;
      await this.swapOrder(idx, idx - 1);
    },
    async moveDown(staff) {
      const idx = this.staffList.indexOf(staff);
      if (idx < 0 || idx >= this.staffList.length - 1) return;
      await this.swapOrder(idx, idx + 1);
    },
    async swapOrder(indexA, indexB) {
      this.reordering = true;
      const a = this.staffList[indexA];
      const b = this.staffList[indexB];
      const tmpOrder = a.sort_order;
      a.sort_order = b.sort_order;
      b.sort_order = tmpOrder;
      // swap positions in the array visually
      this.staffList.splice(indexA, 1, b);
      this.staffList.splice(indexB, 1, a);
      try {
        await axios.post(`${this.appBase}/api/vue/staff/reorder`, {
          orders: [
            { id: a.id, sort_order: a.sort_order },
            { id: b.id, sort_order: b.sort_order },
          ],
        });
        this.showToast('success', 'เปลี่ยนลำดับสำเร็จ');
      } catch (e) {
        this.showToast('error', 'เกิดข้อผิดพลาดในการเปลี่ยนลำดับ');
        this.fetchData();
      } finally {
        this.reordering = false;
      }
    },
    showToast(type, message) {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },
  },
};
</script>
