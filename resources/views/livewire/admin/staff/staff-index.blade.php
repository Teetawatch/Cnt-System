<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight">
                        {{ __('จัดการผู้ปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">เพิ่ม แก้ไข ลบ ข้อมูลบุคลากรในหน่วยงาน</p>
                </div>
            </div>
            
            <button wire:click="openCreateModal" class="btn-primary group">
                <i class="fa-solid fa-plus me-2 transition-transform group-hover:rotate-90"></i>
                เพิ่มผู้ปฏิบัติใหม่
            </button>
        </div>
    </x-slot>

    <!-- Content Container -->
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">บุคลากรทั้งหมด</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $staffList->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-colors"></div>
            </div>
            
            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">ใช้งานปกติ</p>
                    <h3 class="text-3xl font-bold text-emerald-600">{{ \App\Models\Staff::where('is_active', true)->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
            </div>

            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">ปิดการใช้งาน</p>
                    <h3 class="text-3xl font-bold text-slate-400">{{ \App\Models\Staff::where('is_active', false)->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
            </div>
        </div>

        <!-- Main Table Card -->
        <div class="glass-card animate-fade-in-up">
            <!-- Table Header & Search -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-700/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="ค้นหาชื่อ, ตำแหน่ง..." 
                               class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-full sm:w-72 transition-all">
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <select wire:model.live="perPage" class="bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer">
                        <option value="10">10 รายการ</option>
                        <option value="25">25 รายการ</option>
                        <option value="50">50 รายการ</option>
                        <option value="100">100 รายการ</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100 text-left">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ลำดับ</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ผู้ปฏิบัติงาน</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ตำแหน่ง/หน่วยงาน</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">สถานะ</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($staffList as $staff)
                            <tr class="hover:bg-slate-50/80 transition-colors group" wire:key="staff-{{ $staff->id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                                    <span class="bg-slate-100 text-slate-600 py-1 px-2 rounded-lg text-xs">
                                        {{ $staff->sort_order }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden shadow-sm border border-slate-100 group-hover:shadow-md transition-all">
                                                @if($staff->photo)
                                                    <img src="{{ $staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                                        <i class="fa-solid fa-user text-slate-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($staff->is_active)
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                                            @else
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-slate-400 border-2 border-white rounded-full"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-800 dark:text-white text-base group-hover:text-indigo-600 transition-colors">{{ $staff->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $staff->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                                            <span class="text-sm font-medium text-slate-700">{{ $staff->position }}</span>
                                        </div>
                                        @if($staff->department)
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                            <span class="text-sm text-slate-500">{{ $staff->department }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($staff->is_active)
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            ใช้งานปกติ
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            ระงับการใช้งาน
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button wire:click="openEditModal({{ $staff->id }})" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm hover:shadow-md" title="แก้ไข">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $staff->id }})" class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all shadow-sm hover:shadow-md" title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-users-slash text-3xl text-slate-300"></i>
                                        </div>
                                        <p class="text-lg font-medium text-slate-900 mb-1">ไม่พบข้อมูลผู้ปฏิบัติงาน</p>
                                        <p class="text-slate-500 mb-6">ลองเปลี่ยนคำค้นหา หรือเพิ่มผู้ปฏิบัติงานใหม่</p>
                                        <button wire:click="openCreateModal" class="btn-primary">
                                            <i class="fa-solid fa-plus me-2"></i>
                                            เพิ่มผู้ปฏิบัติงาน
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($staffList->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $staffList->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal (Glassmorphism) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="$wire.closeModal()">
            <!-- Backdrop with blur -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="relative inline-block w-full max-w-lg p-0 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-slate-800 shadow-2xl rounded-2xl animate-scale-in border border-slate-100 dark:border-slate-700">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-violet-600 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fa-solid fa-{{ $editMode ? 'user-pen' : 'user-plus' }}"></i>
                            {{ $editMode ? 'แก้ไขข้อมูลผู้ปฏิบัติงาน' : 'เพิ่มผู้ปฏิบัติงานใหม่' }}
                        </h3>
                        <button wire:click="closeModal" class="text-white/70 hover:text-white transition-colors bg-white/10 hover:bg-white/20 rounded-lg w-8 h-8 flex items-center justify-center">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="p-6">
                        <form wire:submit="save">
                            <div class="space-y-5">
                                <!-- Photo Upload Section -->
                                <div class="flex flex-col items-center justify-center mb-6">
                                    <div class="relative group cursor-pointer" onclick="document.getElementById('photo-upload').click()">
                                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center group-hover:border-indigo-100 transition-all">
                                            @if($photo)
                                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                            @elseif($currentPhoto)
                                                <img src="{{ asset($currentPhoto) }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fa-solid fa-camera text-3xl text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                                            @endif
                                        </div>
                                        <div class="absolute bottom-0 right-0 bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-md border-2 border-white group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </div>
                                    </div>
                                    <input id="photo-upload" type="file" wire:model="photo" accept="image/*" class="hidden">
                                    <p class="text-xs text-slate-500 mt-2">คลิกเพื่ออัปโหลดรูปภาพ (สูงสุด 2MB)</p>
                                    @error('photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Inputs -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ชื่อ-นามสกุล <span class="text-rose-500">*</span></label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                <i class="fa-solid fa-user text-sm"></i>
                                            </span>
                                            <input type="text" wire:model="name" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5 transition-all" placeholder="ระบุชื่อ-นามสกุล">
                                        </div>
                                        @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">ตำแหน่ง <span class="text-rose-500">*</span></label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                    <i class="fa-solid fa-id-badge text-sm"></i>
                                                </span>
                                                <input type="text" wire:model="position" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5 transition-all" placeholder="ระบุตำแหน่ง">
                                            </div>
                                            @error('position') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">หน่วยงาน</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                    <i class="fa-solid fa-building text-sm"></i>
                                                </span>
                                                <input type="text" wire:model="department" class="pl-10 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm py-2.5 transition-all" placeholder="ระบุหน่วยงาน">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 items-center bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">ลำดับการแสดงผล</label>
                                            <input type="number" wire:model="sort_order" class="w-full rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm" min="0">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">สถานะการใช้งาน</label>
                                            <label class="inline-flex relative items-center cursor-pointer">
                                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                                <span class="ml-3 text-sm font-medium text-slate-600">{{ $is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white text-slate-700 border border-slate-200 rounded-xl font-medium hover:bg-slate-50 hover:border-slate-300 transition-all">
                                    ยกเลิก
                                </button>
                                <button type="submit" class="btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="save">
                                        <i class="fa-solid fa-save me-2"></i>
                                        บันทึกข้อมูล
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
        </div>
    @endif

    <!-- Delete Modal (Glassmorphism) -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:close-modal.window="$wire.closeDeleteModal()">
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" wire:click="closeDeleteModal"></div>

            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="relative inline-block w-full max-w-sm p-6 overflow-hidden text-center align-middle transition-all transform bg-white shadow-2xl rounded-2xl animate-scale-in">
                    <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-sm border-4 border-white">
                        <i class="fa-solid fa-trash-can text-3xl"></i>
                    </div>
                    
                    <h3 class="text-xl font-bold text-slate-800 mb-2">
                        ยืนยันการลบ?
                    </h3>
                    <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                        คุณต้องการลบข้อมูลของ <span class="font-bold text-slate-800">"{{ $deleteName }}"</span> ใช่หรือไม่?<br>
                        การกระทำนี้ไม่สามารถเรียกคืนได้
                    </p>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="closeDeleteModal" class="px-4 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50 transition-all">
                            ยกเลิก
                        </button>
                        <button type="button" wire:click="delete" class="px-4 py-2.5 bg-rose-500 text-white rounded-xl font-semibold hover:bg-rose-600 shadow-lg shadow-rose-500/30 transition-all">
                            <span wire:loading.remove wire:target="delete">ลบข้อมูลทันที</span>
                            <span wire:loading wire:target="delete"><i class="fa-solid fa-circle-notch fa-spin"></i> กำลังลบ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
