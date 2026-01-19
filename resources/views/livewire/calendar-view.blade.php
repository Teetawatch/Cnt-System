<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('ปฏิทินการปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">ดูตารางงานของผู้บริหาร</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.pdf', ['date' => $selectedDate, 'staff' => $filterStaff]) }}" 
                   target="_blank"
                   class="btn-secondary text-sm">
                    <i class="fa-solid fa-file-pdf me-2"></i>
                    พิมพ์ PDF
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Date Navigation & Filters -->
    <div class="glass-card p-4 mb-6 animate-fade-in">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Date Navigation -->
            <div class="flex items-center gap-2">
                <button wire:click="previousDay" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-chevron-left text-gray-600 dark:text-gray-400"></i>
                </button>
                
                <div class="flex items-center gap-2">
                    <input type="date" 
                           wire:model.live="selectedDate" 
                           class="form-input-custom text-sm w-auto">
                </div>

                <button wire:click="nextDay" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-chevron-right text-gray-600 dark:text-gray-400"></i>
                </button>

                @if(!$this->getIsToday())
                    <button wire:click="goToToday" class="btn-primary text-sm">
                        <i class="fa-solid fa-calendar-day me-2"></i>
                        วันนี้
                    </button>
                @endif
            </div>

            <!-- Staff Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-filter me-1"></i>
                    ผู้ปฏิบัติ:
                </label>
                <select wire:model.live="filterStaff" class="form-input-custom text-sm w-56">
                    <option value="">ทั้งหมด</option>
                    @foreach($allStaff as $staff)
                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Current Date Display -->
    <div class="text-center mb-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $this->formattedDate }}
        </h3>
        <p class="text-sm text-gray-500 mt-1">
            <i class="fa-solid fa-calendar-check me-1"></i>
            {{ $totalEvents }} กิจกรรม
        </p>
    </div>

    <!-- Events by Staff -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($staffWithEvents as $staff)
            <div class="glass-card overflow-hidden animate-slide-up" wire:key="staff-{{ $staff->id }}">
                <!-- Staff Header -->
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 p-4 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center overflow-hidden">
                            @if($staff->photo)
                                <img src="{{ $staff->photo_url }}" alt="" class="w-12 h-12 object-cover">
                            @else
                                <i class="fa-solid fa-user text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-semibold text-lg">{{ $staff->name }}</h4>
                            <p class="text-primary-100 text-sm">{{ $staff->position }}</p>
                        </div>
                        <div class="ms-auto">
                            <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                                {{ $staff->calendarEvents->count() }} รายการ
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Events List -->
                <div class="p-4">
                    @forelse($staff->calendarEvents as $event)
                        <div wire:click="showEvent({{ $event->id }})" 
                             class="flex gap-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg px-2 -mx-2 transition-colors {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                            <div class="text-center min-w-[60px]">
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                </p>
                                @if($event->end_time)
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500 truncate">
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    {{ $event->location }}
                                </p>
                                @if($event->organization)
                                    <p class="text-xs text-gray-400 truncate">
                                        <i class="fa-solid fa-building me-1"></i>
                                        {{ $event->organization }}
                                    </p>
                                @endif
                            </div>
                            <span class="badge badge-{{ $event->status_color }} h-fit shrink-0">
                                {{ $event->status_label }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <i class="fa-solid fa-calendar-xmark text-3xl mb-2"></i>
                            <p>ไม่มีกิจกรรมในวันนี้</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 glass-card p-12 text-center text-gray-400">
                <i class="fa-solid fa-users-slash text-5xl mb-4"></i>
                <p class="text-xl mb-2">ไม่พบข้อมูล</p>
                <p class="text-sm">ไม่มีผู้ปฏิบัติงานที่ตรงกับเงื่อนไข</p>
            </div>
        @endforelse
    </div>

    @if($staffWithEvents->isNotEmpty() && $totalEvents === 0)
        <div class="glass-card p-8 text-center text-gray-400 mt-6 animate-fade-in">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
            <p class="text-lg">ไม่มีกิจกรรมในวันที่เลือก</p>
            <p class="text-sm mt-1">ลองเลือกวันที่อื่นหรือเปลี่ยนตัวกรอง</p>
        </div>
    @endif

    <!-- Event Detail Modal -->
    @if($showEventModal && $selectedEvent)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeEventModal"></div>

                <div class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl animate-scale-in">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-calendar-day text-xl text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $selectedEvent->title }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $selectedEvent->event_date->locale('th')->translatedFormat('j F Y') }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="closeEventModal" class="text-gray-400 hover:text-gray-500">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <!-- Event Details -->
                    <div class="space-y-4">
                        <!-- Time -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-clock text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">เวลา</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $selectedEvent->time_range }}</p>
                            </div>
                        </div>

                        <!-- Staff -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 bg-success-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center overflow-hidden">
                                @if($selectedEvent->staff->photo)
                                    <img src="{{ $selectedEvent->staff->photo_url }}" alt="" class="w-10 h-10 object-cover">
                                @else
                                    <i class="fa-solid fa-user text-success-500"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">ผู้ปฏิบัติ</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $selectedEvent->staff->name }}</p>
                                <p class="text-xs text-gray-400">{{ $selectedEvent->staff->position }}</p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="w-10 h-10 bg-warning-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-location-dot text-warning-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">สถานที่</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $selectedEvent->location }}</p>
                            </div>
                        </div>

                        <!-- Organization -->
                        @if($selectedEvent->organization)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-building text-gray-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">หน่วยงานที่เชิญ/จัด</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $selectedEvent->organization }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Description -->
                        @if($selectedEvent->description)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <p class="text-sm text-gray-500 mb-1">
                                    <i class="fa-solid fa-align-left me-1"></i>
                                    รายละเอียด
                                </p>
                                <p class="text-gray-900 dark:text-white">{{ $selectedEvent->description }}</p>
                            </div>
                        @endif

                        <!-- Status -->
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <span class="text-sm text-gray-500">สถานะ</span>
                            <span class="badge badge-{{ $selectedEvent->status_color }}">
                                {{ $selectedEvent->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Close Button -->
                    <div class="mt-6">
                        <button wire:click="closeEventModal" class="btn-secondary w-full">
                            <i class="fa-solid fa-xmark me-2"></i>
                            ปิด
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
