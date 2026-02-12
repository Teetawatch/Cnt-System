<div>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-calendar-plus text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight">
                        {{ __('จัดการกิจกรรม') }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">เพิ่ม แก้ไข ลบ รายการปฏิทินการปฏิบัติงาน</p>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Action Button (must be inside Livewire component scope) -->
    <div class="flex justify-end mb-6">
        <button wire:click="openCreateModal" class="btn-primary group">
            <i class="fa-solid fa-plus me-2 transition-transform group-hover:rotate-90"></i>
            เพิ่มกิจกรรมใหม่
        </button>
    </div>

    <!-- Content Container -->
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">กิจกรรมทั้งหมด</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $events->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-colors"></div>
            </div>
            
            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">เดือนนี้</p>
                    <h3 class="text-3xl font-bold text-emerald-600">
                        {{ \App\Models\CalendarEvent::whereMonth('event_date', now()->month)->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar-week"></i>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-colors"></div>
            </div>

            <div class="glass-card p-6 flex items-center justify-between relative overflow-hidden group card-hover-effect">
                <div class="relative z-10">
                    <p class="text-slate-500 font-medium mb-1">รอดำเนินการ</p>
                    <h3 class="text-3xl font-bold text-amber-500">
                        {{ \App\Models\CalendarEvent::where('status', 'pending')->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-amber-500/5 rounded-full blur-2xl group-hover:bg-amber-500/10 transition-colors"></div>
            </div>
        </div>

        <div class="glass-card animate-fade-in-up relative overflow-hidden">
            <!-- Header with Title and Search (New!) -->
            <div class="p-6 border-b border-slate-100 bg-slate-50/30 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                    <h3 class="font-bold text-slate-800">รายการงานทั้งหมด</h3>
                </div>
                <!-- Global Search -->
                <div class="relative w-full md:w-80">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="ค้นหาชื่อเรื่อง, สถานที่ หรือหน่วยงาน..." 
                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
            </div>

            <!-- Detailed Advanced Filters -->
            <div class="p-6 border-b border-slate-100 bg-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">กรองวันที่</label>
                        <input type="date" 
                               wire:model.live="filterDate" 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">เลือกผู้ปฏิบัติ</label>
                        <select wire:model.live="filterStaff" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer transition-all">
                            <option value="">ผู้ปฏิบัติงานทั้งหมด</option>
                            @foreach($staffList as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] uppercase font-bold text-slate-400 ml-1 tracking-wider">สถานะกิจกรรม</label>
                        <select wire:model.live="filterStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2 px-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer transition-all">
                            <option value="">ทุกสถานะ</option>
                            <option value="pending">🟡 รอยืนยัน</option>
                            <option value="confirmed">🟢 ยืนยันแล้ว</option>
                            <option value="cancelled">🔴 ยกเลิก</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        @if($search || $filterDate || $filterStaff || $filterStatus)
                            <button wire:click="clearFilters" class="w-full px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                ล้างตัวกรอง
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Desktop View: High-Detail Table -->
            <div class="hidden lg:block">
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
                        @forelse($events as $event)
                            <tr class="hover:bg-indigo-50/30 transition-all duration-300 group" wire:key="event-desktop-{{ $event->id }}">
                                <td class="px-6 py-5 whitespace-nowrap align-top">
                                    <div class="flex flex-col">
                                        <div class="text-base font-bold text-slate-700">{{ $event->event_date->format('d') }}</div>
                                        <div class="text-[10px] uppercase font-bold text-indigo-500 tracking-tighter">{{ $event->event_date->translatedFormat('M Y') }}</div>
                                        <div class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 rounded-md px-1.5 py-0.5 w-fit">
                                            <i class="fa-regular fa-clock"></i> {{ $event->time_range }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <div class="flex flex-col gap-1.5">
                                        <div class="flex flex-wrap items-baseline gap-x-4">
                                            <p class="font-bold text-slate-800 text-base group-hover:text-indigo-600 transition-colors leading-snug">
                                                {{ $event->title }}
                                            </p>
                                            @if($event->organization)
                                                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-100 flex items-center gap-1">
                                                    <i class="fa-solid fa-building-circle-check"></i> {{ $event->organization }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($event->description)
                                            <p class="text-xs text-slate-400 max-w-2xl leading-relaxed italic">
                                                <i class="fa-solid fa-quote-left text-[8px] opacity-30 me-1"></i>
                                                {{ $event->description }}
                                            </p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-100 transition-transform group-hover:scale-105">
                                            @if($event->staff->photo_url)
                                                <img src="{{ $event->staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-indigo-50 flex items-center justify-center text-indigo-300">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-700">{{ $event->staff->name }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-tighter">Personnel</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap align-top">
                                    <div class="flex items-center gap-1.5 text-sm font-medium text-slate-500">
                                        <div class="w-6 h-6 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center text-[10px]">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        {{ $event->location }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap align-top text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-sm
                                        @if($event->status === 'confirmed') bg-emerald-100 text-emerald-700
                                        @elseif($event->status === 'pending') bg-amber-100 text-amber-700
                                        @else bg-rose-100 text-rose-700 @endif">
                                        <span class="w-1.5 h-1.5 rounded-full me-1.5 
                                            @if($event->status === 'confirmed') bg-emerald-500
                                            @elseif($event->status === 'pending') bg-amber-500
                                            @else bg-rose-500 @endif animate-pulse"></span>
                                        {{ $event->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap align-top text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openEditModal({{ $event->id }})" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm active:scale-95" title="แก้ไข">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $event->id }})" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-rose-600 flex items-center justify-center hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all shadow-sm active:scale-95" title="ลบ">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <!-- Empty State handled below Table -->
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile & Tablet View: Card Layout -->
            <div class="lg:hidden p-4 space-y-4">
                @forelse($events as $event)
                    <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-4" wire:key="event-mobile-{{ $event->id }}">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-50 text-indigo-600 w-12 h-12 rounded-2xl flex flex-col items-center justify-center border border-indigo-100">
                                    <span class="text-lg font-bold leading-none">{{ $event->event_date->format('d') }}</span>
                                    <span class="text-[8px] uppercase font-bold">{{ $event->event_date->translatedFormat('M') }}</span>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 leading-tight">{{ $event->title }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $event->time_range }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider
                                            @if($event->status === 'confirmed') text-emerald-500
                                            @elseif($event->status === 'pending') text-amber-500
                                            @else text-rose-500 @endif">
                                            ● {{ $event->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 py-3 border-y border-slate-50">
                            <div>
                                <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-1">ผู้ปฏิบัติงาน</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden border border-white ring-1 ring-slate-100">
                                        @if($event->staff->photo_url)
                                            <img src="{{ $event->staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-[10px] text-slate-300"></i>
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold text-slate-600">{{ $event->staff->name }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-[9px] uppercase font-bold text-slate-400 tracking-wider mb-1">สถานที่</p>
                                <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                                    <i class="fa-solid fa-location-dot text-rose-400"></i>
                                    {{ Str::limit($event->location, 20) }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                            <button wire:click="openEditModal({{ $event->id }})" class="flex-1 py-2 bg-slate-50 border border-slate-100 rounded-xl text-indigo-600 font-bold text-xs hover:bg-indigo-50 transition-all">
                                <i class="fa-solid fa-pen-to-square me-1"></i> แก้ไข
                            </button>
                            <button wire:click="confirmDelete({{ $event->id }})" class="w-10 py-2 bg-slate-50 border border-slate-100 rounded-xl text-rose-500 hover:bg-rose-50 transition-all">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <!-- Empty State handled below -->
                @endforelse
            </div>

            <!-- Empty State -->
            @if($events->isEmpty())
                <div class="px-6 py-24 text-center bg-white rounded-b-2xl">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-sm ring-1 ring-slate-100">
                            <i class="fa-solid fa-calendar-xmark text-4xl text-slate-200"></i>
                        </div>
                        <p class="text-xl font-bold text-slate-800 mb-2">ไม่พบข้อมูลกิจกรรมที่คุณค้นหา</p>
                        <p class="text-slate-500 mb-8 max-w-sm mx-auto">ลองเปลี่ยนเงื่อนไขการค้นหา หรือเพิ่มกิจกรรมใหม่เข้าสู่ระบบได้ทันที</p>
                        <button wire:click="openCreateModal" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-xl shadow-indigo-600/20 transition-all transform active:scale-95 flex items-center gap-2">
                            <i class="fa-solid fa-plus"></i>
                            สร้างกิจกรรมใหม่
                        </button>
                    </div>
                </div>
            @endif

            <!-- Pagination -->
            @if($events->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/50">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal (Glassmorphism) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ showStaffDropdown: false }">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="flex items-start sm:items-center justify-center min-h-screen p-2 sm:p-4">
                <div class="relative w-full max-w-2xl mx-auto my-2 sm:my-8 bg-white dark:bg-slate-800 shadow-2xl rounded-2xl animate-scale-in max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col border border-slate-100">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-r from-indigo-500 to-violet-600 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                <i class="fa-solid fa-{{ $editMode ? 'pen-to-square' : 'calendar-plus' }} text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">
                                    {{ $editMode ? 'แก้ไขกิจกรรม' : 'เพิ่มกิจกรรมใหม่' }}
                                </h3>
                                <p class="text-xs text-white/80 hidden sm:block font-medium">กรอกรายละเอียดกิจกรรมด้านล่าง</p>
                            </div>
                        </div>
                        <button wire:click="closeModal" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors text-white">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Form Body -->
                    <form wire:submit="save" class="flex-1 overflow-y-auto">
                        <div class="p-6 space-y-6">
                            
                            <!-- Staff Selection -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    ผู้ปฏิบัติงาน <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach($staffList as $staff)
                                        <label class="cursor-pointer group relative" wire:key="staff-select-{{ $staff->id }}">
                                            <input type="radio" wire:model.live="staff_id" value="{{ $staff->id }}" class="sr-only peer">
                                            <div class="flex flex-col items-center p-3 rounded-xl border-2 transition-all duration-200 text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 border-slate-100 hover:border-indigo-200">
                                                <div class="w-12 h-12 rounded-full overflow-hidden mb-2 border-2 peer-checked:border-indigo-500 border-slate-100 transition-colors group-hover:scale-105">
                                                    @if($staff->photo_url)
                                                        <img src="{{ $staff->photo_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                                            <i class="fa-solid fa-user text-slate-400"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="text-xs sm:text-sm font-bold truncate w-full peer-checked:text-indigo-700 text-slate-700">
                                                    {{ Str::limit($staff->name, 12) }}
                                                </p>
                                                
                                                <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center text-white text-xs shadow-sm">
                                                        <i class="fa-solid fa-check"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('staff_id') <p class="text-xs mt-2 text-rose-500 font-medium"><i class="fa-solid fa-exclamation-circle me-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <div class="h-px bg-slate-100 w-full"></div>

                            <!-- Date & Time Section -->
                            <div class="bg-indigo-50/30 dark:bg-indigo-900/10 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-800/50">
                                <div class="flex items-center gap-2 mb-6">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-800 dark:text-white">กำหนดวันและเวลา</h4>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Dates -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">วันที่เริ่มต้น <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                        <i class="fa-solid fa-calendar-alt"></i>
                                                    </span>
                                                    <input type="date" wire:model.live="event_date" class="form-input-custom pl-10">
                                                </div>
                                                @error('event_date') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                            </div>
                                            
                                            @if(!$editMode)
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">วันที่สิ้นสุด</label>
                                                    <div class="relative">
                                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                            <i class="fa-solid fa-calendar-check"></i>
                                                        </span>
                                                        <input type="date" wire:model.live="end_date" class="form-input-custom pl-10" min="{{ $this->event_date }}">
                                                    </div>
                                                    @error('end_date') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                                </div>
                                            @endif
                                        </div>

                                        @if(!$editMode && $this->event_date && $this->end_date && $this->event_date != $this->end_date)
                                            <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-100 rounded-lg text-amber-700 text-xs font-medium animate-pulse">
                                                <i class="fa-solid fa-info-circle"></i>
                                                จะมีการสร้างกิจกรรมทั้งหมด {{ \Carbon\Carbon::parse($this->event_date)->diffInDays(\Carbon\Carbon::parse($this->end_date)) + 1 }} รายการ (แยกตามวัน)
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Times -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">เวลาเริ่ม <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                        <i class="fa-solid fa-clock"></i>
                                                    </span>
                                                    <input type="time" wire:model="start_time" class="form-input-custom pl-10">
                                                </div>
                                                @error('start_time') <p class="text-rose-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">เวลาสิ้นสุด</label>
                                                <div class="relative">
                                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                    </span>
                                                    <input type="time" wire:model="end_time" class="form-input-custom pl-10">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        รายการงาน <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" wire:model="title" class="form-input-custom" placeholder="เช่น ประชุมคณะกรรมการ, ตรวจราชการ">
                                    @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                            สถานที่ <span class="text-rose-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                <i class="fa-solid fa-location-dot text-sm"></i>
                                            </span>
                                            <input type="text" wire:model="location" class="form-input-custom pl-9" placeholder="ห้องประชุม, โรงเรียน">
                                        </div>
                                        @error('location') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                            หน่วยงาน
                                        </label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                                <i class="fa-solid fa-building text-sm"></i>
                                            </span>
                                            <input type="text" wire:model="organization" class="form-input-custom pl-9" placeholder="สพม., เทศบาล">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        รายละเอียดเพิ่มเติม
                                    </label>
                                    <textarea wire:model="description" rows="3" class="form-input-custom rounded-xl" placeholder="รายละเอียดอื่นๆ..."></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                        สถานะ
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="status" value="confirmed" class="sr-only peer">
                                            <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all
                                                peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700
                                                border-slate-200 text-slate-600 hover:border-emerald-200">
                                                <i class="fa-solid fa-circle-check me-1.5"></i>ยืนยันแล้ว
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="status" value="pending" class="sr-only peer">
                                            <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all
                                                peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700
                                                border-slate-200 text-slate-600 hover:border-amber-200">
                                                <i class="fa-solid fa-clock me-1.5"></i>รอยืนยัน
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model="status" value="cancelled" class="sr-only peer">
                                            <div class="px-4 py-2 rounded-xl border-2 font-bold text-sm transition-all
                                                peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700
                                                border-slate-200 text-slate-600 hover:border-rose-200">
                                                <i class="fa-solid fa-xmark me-1.5"></i>ยกเลิก
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="flex items-center justify-end gap-3 p-5 border-t border-slate-100 bg-slate-50/80 sticky bottom-0 backdrop-blur-sm z-10">
                            <button type="button" wire:click="closeModal" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-white hover:shadow-sm transition-all text-sm">
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
    @endif

    <!-- Delete Confirmation Modal -->
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
                        คุณต้องการลบกิจกรรม <span class="font-bold text-slate-800">"{{ $deleteTitle }}"</span> ใช่หรือไม่?<br>
                        การกระทำนี้ไม่สามารถเรียกคืนได้
                    </p>

                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" wire:click="closeDeleteModal" class="px-4 py-2.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50 transition-all">
                            ยกเลิก
                        </button>
                        <button type="button" wire:click="delete" class="px-4 py-2.5 bg-rose-500 text-white rounded-xl font-semibold hover:bg-rose-600 shadow-lg shadow-rose-500/30 transition-all">
                            <span wire:loading.remove wire:target="delete">ลบกิจกรรม</span>
                            <span wire:loading wire:target="delete"><i class="fa-solid fa-circle-notch fa-spin"></i> กำลังลบ...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
