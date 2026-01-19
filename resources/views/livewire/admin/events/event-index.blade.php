<div>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-success-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-calendar-plus text-success-500 dark:text-green-400"></i>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('จัดการกิจกรรม') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">เพิ่ม แก้ไข ลบ รายการปฏิทินการปฏิบัติงาน</p>
            </div>
        </div>
    </x-slot>

    <!-- Action Button (inside Livewire scope) -->
    <div class="mb-6 flex justify-end">
        <button wire:click="openCreateModal" class="btn-primary">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มกิจกรรม
        </button>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="glass-card p-4 mb-6 border-l-4 border-success-500 bg-success-50 dark:bg-green-900/20 animate-fade-in">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-success-500 me-3"></i>
                <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="glass-card animate-fade-in">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600 dark:text-gray-400">
                        ทั้งหมด <span class="font-semibold text-gray-900 dark:text-white">{{ $events->total() }}</span> รายการ
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="date" 
                           wire:model.live="filterDate" 
                           class="form-input-custom text-sm">
                    <select wire:model.live="filterStaff" class="form-input-custom text-sm w-48">
                        <option value="">ผู้ปฏิบัติทั้งหมด</option>
                        @foreach($staffList as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterStatus" class="form-input-custom text-sm w-36">
                        <option value="">สถานะทั้งหมด</option>
                        <option value="pending">รอยืนยัน</option>
                        <option value="confirmed">ยืนยันแล้ว</option>
                        <option value="cancelled">ยกเลิก</option>
                    </select>
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="ค้นหา..." 
                               class="form-input-custom text-sm w-48 pl-10">
                    </div>
                    @if($search || $filterDate || $filterStaff || $filterStatus)
                        <button wire:click="clearFilters" class="btn-secondary text-sm">
                            <i class="fa-solid fa-xmark me-1"></i>
                            ล้าง
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">วันที่/เวลา</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">รายการงาน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ผู้ปฏิบัติ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สถานที่</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" wire:key="event-{{ $event->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->event_date->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $event->time_range }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <p class="font-medium text-gray-900 dark:text-white truncate">{{ $event->title }}</p>
                                    @if($event->organization)
                                        <p class="text-sm text-gray-500 truncate">{{ $event->organization }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-user text-sm text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $event->staff->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $event->location }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="badge badge-{{ $event->status_color }}">{{ $event->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button wire:click="openEditModal({{ $event->id }})" class="text-primary-600 hover:text-primary-700 me-3">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $event->id }})" class="text-danger-500 hover:text-danger-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-calendar-plus text-4xl mb-3"></i>
                                <p>ยังไม่มีกิจกรรม</p>
                                <button wire:click="openCreateModal" class="btn-primary mt-4">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    เพิ่มกิจกรรมแรก
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ showStaffDropdown: false }">
            <div class="flex items-start sm:items-center justify-center min-h-screen p-2 sm:p-4">
                <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

                <div class="relative w-full max-w-2xl mx-auto my-2 sm:my-8 bg-white dark:bg-gray-800 shadow-2xl rounded-xl sm:rounded-2xl animate-scale-in max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col">
                    <!-- Header - Compact -->
                    <div class="flex items-center justify-between p-4 sm:p-5 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-green-500 to-emerald-600 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-{{ $editMode ? 'pen-to-square' : 'calendar-plus' }} text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $editMode ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรมใหม่' }}
                                </h3>
                                <p class="text-xs text-white/70 hidden sm:block">กรอกข้อมูลกิจกรรมที่ต้องการ</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark text-white"></i>
                        </button>
                    </div>

                    <!-- Form Body - Scrollable -->
                    <form wire:submit="save" class="flex-1 overflow-y-auto">
                        <div class="p-4 sm:p-5 space-y-4">
                            
                            <!-- Staff Selection - Compact Horizontal Cards -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fa-solid fa-user-tie me-1.5 text-primary-500"></i>
                                    ผู้ปฏิบัติงาน <span class="text-danger-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                    @foreach($staffList as $staff)
                                        <label class="cursor-pointer" wire:key="staff-select-{{ $staff->id }}">
                                            <input type="radio" wire:model.live="staff_id" value="{{ $staff->id }}" class="sr-only">
                                            <div class="relative flex flex-col items-center p-2 sm:p-3 rounded-xl border-2 transition-all duration-200 text-center"
                                                 style="{{ $staff_id == $staff->id 
                                                    ? 'border-color: #3b82f6; background-color: #eff6ff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);' 
                                                    : 'border-color: #e5e7eb; background-color: transparent;' }}">
                                                <!-- Photo - Small -->
                                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden mb-1.5 flex-shrink-0"
                                                     style="{{ $staff_id == $staff->id 
                                                        ? 'border: 3px solid #3b82f6;' 
                                                        : 'border: 2px solid #e5e7eb;' }}">
                                                    @if($staff->photo_url)
                                                        <img src="{{ $staff->photo_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-600 dark:to-gray-700 flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-gray-400 text-sm"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Name -->
                                                <p class="text-xs sm:text-sm font-medium truncate w-full"
                                                   style="{{ $staff_id == $staff->id ? 'color: #1d4ed8;' : 'color: #111827;' }}">
                                                    {{ Str::limit($staff->name, 12) }}
                                                </p>
                                                <!-- Check badge -->
                                                @if($staff_id == $staff->id)
                                                    <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center" 
                                                          style="background-color: #3b82f6; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.4);">
                                                        <i class="fa-solid fa-check text-white text-xs"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('staff_id') <p class="text-xs mt-1.5" style="color: #dc2626;"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Date & Time Row - Compact -->
                            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 sm:p-4 border border-amber-200 dark:border-amber-800">
                                <div class="grid grid-cols-3 gap-2 sm:gap-3">
                                    <div>
                                        <label class="block text-xs text-amber-700 dark:text-amber-400 mb-1 font-medium">
                                            <i class="fa-solid fa-calendar me-1"></i>วันที่ *
                                        </label>
                                        <input type="date" wire:model="event_date" class="form-input-custom text-sm py-2">
                                        @error('event_date') <p class="text-danger-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-amber-700 dark:text-amber-400 mb-1 font-medium">
                                            <i class="fa-solid fa-clock me-1"></i>เริ่ม *
                                        </label>
                                        <input type="time" wire:model="start_time" class="form-input-custom text-sm py-2">
                                        @error('start_time') <p class="text-danger-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs text-amber-700 dark:text-amber-400 mb-1 font-medium">
                                            <i class="fa-solid fa-flag-checkered me-1"></i>สิ้นสุด
                                        </label>
                                        <input type="time" wire:model="end_time" class="form-input-custom text-sm py-2">
                                    </div>
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    <i class="fa-solid fa-pen me-1.5 text-blue-500"></i>
                                    รายการงาน <span class="text-danger-500">*</span>
                                </label>
                                <input type="text" wire:model="title" class="form-input-custom" placeholder="เช่น ประชุมคณะกรรมการ, ตรวจราชการ">
                                @error('title') <p class="text-danger-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Location & Organization - 2 columns -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        <i class="fa-solid fa-location-dot me-1.5 text-red-500"></i>
                                        สถานที่ <span class="text-danger-500">*</span>
                                    </label>
                                    <input type="text" wire:model="location" class="form-input-custom" placeholder="ห้องประชุม, โรงเรียน">
                                    @error('location') <p class="text-danger-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                        <i class="fa-solid fa-building me-1.5 text-purple-500"></i>
                                        หน่วยงาน
                                    </label>
                                    <input type="text" wire:model="organization" class="form-input-custom" placeholder="สพม., เทศบาล">
                                </div>
                            </div>

                            <!-- Description - Optional, collapsible on mobile -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    <i class="fa-solid fa-align-left me-1.5 text-gray-500"></i>
                                    รายละเอียด <span class="text-xs font-normal text-gray-400">(ไม่บังคับ)</span>
                                </label>
                                <textarea wire:model="description" rows="2" class="form-input-custom text-sm" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                            </div>

                            <!-- Status - Inline buttons -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fa-solid fa-circle-check me-1.5 text-green-500"></i>
                                    สถานะ
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <label class="cursor-pointer flex-1 sm:flex-none">
                                        <input type="radio" wire:model="status" value="confirmed" class="sr-only peer">
                                        <div class="px-3 py-2 rounded-lg border-2 font-medium text-xs sm:text-sm transition-all text-center
                                            peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700
                                            dark:peer-checked:bg-green-900/30 dark:peer-checked:text-green-400
                                            border-gray-200 dark:border-gray-700 hover:border-green-300">
                                            <i class="fa-solid fa-circle-check me-1"></i>ยืนยัน
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1 sm:flex-none">
                                        <input type="radio" wire:model="status" value="pending" class="sr-only peer">
                                        <div class="px-3 py-2 rounded-lg border-2 font-medium text-xs sm:text-sm transition-all text-center
                                            peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700
                                            dark:peer-checked:bg-amber-900/30 dark:peer-checked:text-amber-400
                                            border-gray-200 dark:border-gray-700 hover:border-amber-300">
                                            <i class="fa-solid fa-clock me-1"></i>รอยืนยัน
                                        </div>
                                    </label>
                                    <label class="cursor-pointer flex-1 sm:flex-none">
                                        <input type="radio" wire:model="status" value="cancelled" class="sr-only peer">
                                        <div class="px-3 py-2 rounded-lg border-2 font-medium text-xs sm:text-sm transition-all text-center
                                            peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700
                                            dark:peer-checked:bg-red-900/30 dark:peer-checked:text-red-400
                                            border-gray-200 dark:border-gray-700 hover:border-red-300">
                                            <i class="fa-solid fa-xmark me-1"></i>ยกเลิก
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions - Sticky -->
                        <div class="flex items-center justify-end gap-2 sm:gap-3 p-4 sm:p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex-shrink-0">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-sm">
                                ยกเลิก
                            </button>
                            <button type="submit" class="px-5 py-2 sm:px-6 sm:py-2.5 rounded-lg bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold shadow-lg shadow-green-500/25 hover:shadow-green-500/40 transition-all text-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fa-solid fa-check me-1.5"></i>
                                    {{ $editMode ? 'บันทึก' : 'เพิ่มกิจกรรม' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="fa-solid fa-spinner fa-spin me-1.5"></i>
                                    กำลังบันทึก...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeDeleteModal"></div>

                <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-center align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl animate-scale-in">
                    <div class="w-16 h-16 bg-danger-50 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-trash text-2xl text-danger-500"></i>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        ยืนยันการลบ
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        คุณต้องการลบกิจกรรม <span class="font-semibold text-gray-900 dark:text-white">{{ $deleteTitle }}</span> หรือไม่?<br>
                        <span class="text-sm text-danger-500">การกระทำนี้ไม่สามารถยกเลิกได้</span>
                    </p>

                    <div class="flex justify-center gap-3">
                        <button type="button" wire:click="closeDeleteModal" class="btn-secondary">
                            ยกเลิก
                        </button>
                        <button type="button" wire:click="delete" class="btn-danger" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="delete">
                                <i class="fa-solid fa-trash me-2"></i>
                                ลบกิจกรรม
                            </span>
                            <span wire:loading wire:target="delete">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i>
                                กำลังลบ...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
