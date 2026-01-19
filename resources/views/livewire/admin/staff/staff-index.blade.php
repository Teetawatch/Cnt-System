<div>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-users text-primary-600 dark:text-primary-400"></i>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('จัดการผู้ปฏิบัติงาน') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">เพิ่ม แก้ไข ลบ ข้อมูลผู้อำนวยการและรองผู้อำนวยการ</p>
            </div>
        </div>
    </x-slot>

    <!-- Action Button (inside Livewire scope) -->
    <div class="mb-6 flex justify-end">
        <button wire:click="openCreateModal" class="btn-primary">
            <i class="fa-solid fa-plus me-2"></i>
            เพิ่มผู้ปฏิบัติ
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
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600 dark:text-gray-400">
                        ทั้งหมด <span class="font-semibold text-gray-900 dark:text-white">{{ $staffList->total() }}</span> รายการ
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="ค้นหา..." 
                               class="form-input-custom text-sm w-64 pl-10">
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ลำดับ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ชื่อ-ตำแหน่ง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">หน่วยงาน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($staffList as $staff)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" wire:key="staff-{{ $staff->id }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $staff->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($staff->photo)
                                            <img src="{{ $staff->photo_url }}" alt="" class="w-10 h-10 object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-primary-600 dark:text-primary-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $staff->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $staff->position }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $staff->department ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($staff->is_active)
                                    <span class="badge badge-success">ใช้งาน</span>
                                @else
                                    <span class="badge badge-danger">ไม่ใช้งาน</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button wire:click="openEditModal({{ $staff->id }})" class="text-primary-600 hover:text-primary-700 me-3">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $staff->id }})" class="text-danger-500 hover:text-danger-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-plus text-4xl mb-3"></i>
                                <p>ยังไม่มีข้อมูลผู้ปฏิบัติงาน</p>
                                <button wire:click="openCreateModal" class="btn-primary mt-4">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    เพิ่มผู้ปฏิบัติคนแรก
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($staffList->hasPages())
            <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $staffList->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="$wire.closeModal()">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeModal"></div>

                <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl animate-scale-in">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fa-solid fa-{{ $editMode ? 'pen-to-square' : 'user-plus' }} me-2 text-primary-500"></i>
                            {{ $editMode ? 'แก้ไขผู้ปฏิบัติงาน' : 'เพิ่มผู้ปฏิบัติงาน' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="save">
                        <div class="space-y-4">
                            <!-- Photo Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">รูปภาพ</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($photo)
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover">
                                        @elseif($currentPhoto)
                                            <img src="{{ asset($currentPhoto) }}" class="w-20 h-20 object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-3xl text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="btn-secondary cursor-pointer text-sm">
                                            <i class="fa-solid fa-upload me-2"></i>
                                            อัปโหลดรูป
                                            <input type="file" wire:model="photo" accept="image/*" class="hidden">
                                        </label>
                                        <p class="text-xs text-gray-500 mt-1">JPG, PNG ขนาดไม่เกิน 2MB</p>
                                    </div>
                                </div>
                                @error('photo') <p class="text-danger-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ-นามสกุล <span class="text-danger-500">*</span></label>
                                <input type="text" wire:model="name" class="form-input-custom" placeholder="กรอกชื่อ-นามสกุล">
                                @error('name') <p class="text-danger-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Position -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ตำแหน่ง <span class="text-danger-500">*</span></label>
                                <input type="text" wire:model="position" class="form-input-custom" placeholder="เช่น ผู้อำนวยการ, รองผู้อำนวยการ">
                                @error('position') <p class="text-danger-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Department -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หน่วยงาน</label>
                                <input type="text" wire:model="department" class="form-input-custom" placeholder="เช่น ฝ่ายวิชาการ, ฝ่ายบริหาร">
                                @error('department') <p class="text-danger-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลำดับการแสดง</label>
                                    <input type="number" wire:model="sort_order" class="form-input-custom" min="0">
                                    @error('sort_order') <p class="text-danger-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">ใช้งาน</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="closeModal" class="btn-secondary">
                                ยกเลิก
                            </button>
                            <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="fa-solid fa-check me-2"></i>
                                    {{ $editMode ? 'บันทึกการแก้ไข' : 'เพิ่มผู้ปฏิบัติ' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <i class="fa-solid fa-spinner fa-spin me-2"></i>
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
                        คุณต้องการลบ <span class="font-semibold text-gray-900 dark:text-white">{{ $deleteName }}</span> หรือไม่?<br>
                        <span class="text-sm text-danger-500">การกระทำนี้ไม่สามารถยกเลิกได้</span>
                    </p>

                    <div class="flex justify-center gap-3">
                        <button type="button" wire:click="closeDeleteModal" class="btn-secondary">
                            ยกเลิก
                        </button>
                        <button type="button" wire:click="delete" class="btn-danger" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="delete">
                                <i class="fa-solid fa-trash me-2"></i>
                                ลบข้อมูล
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
