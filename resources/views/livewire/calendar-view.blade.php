<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-calendar-days text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight">
                        {{ __('ปฏิทินการปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">ตารางงานและกิจกรรมของผู้บริหาร</p>
                </div>
            </div>
            
            <a href="{{ route('calendar.pdf', ['date' => $selectedDate, 'staff' => $filterStaff]) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-semibold text-slate-700 shadow-sm hover:bg-slate-50 hover:shadow-md transition-all group">
                <i class="fa-solid fa-file-pdf me-2 text-rose-500 group-hover:scale-110 transition-transform"></i>
                พิมพ์ PDF
            </a>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="space-y-6">
        <!-- Date Navigation & Filters -->
        <div class="glass-card p-2 sm:p-4 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Date Navigation -->
                <div class="flex items-center justify-center lg:justify-start gap-3 bg-slate-50/50 p-2 rounded-xl border border-slate-100/50">
                    <button wire:click="previousDay" class="w-10 h-10 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    
                    <div class="relative group">
                        <i class="fa-solid fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-indigo-500 pointer-events-none"></i>
                        <input type="date" 
                               wire:model.live="selectedDate" 
                               class="pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm group-hover:border-indigo-200">
                    </div>

                    <button wire:click="nextDay" class="w-10 h-10 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 flex items-center justify-center transition-all shadow-sm">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    @if(!$this->getIsToday())
                        <button wire:click="goToToday" class="px-4 py-2 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-sm font-bold hover:bg-indigo-100 transition-all">
                            วันนี้
                        </button>
                    @endif
                </div>

                <!-- Staff Filter -->
                <div class="flex items-center justify-center lg:justify-end gap-3">
                    <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                        <span class="text-sm font-bold text-slate-500">
                            <i class="fa-solid fa-filter me-1.5 text-indigo-500"></i>
                            กรองตามบุคคล:
                        </span>
                        <select wire:model.live="filterStaff" class="border-0 bg-transparent text-sm font-semibold text-slate-700 focus:ring-0 cursor-pointer py-1 pr-8">
                            <option value="">ทั้งหมด</option>
                            @foreach($allStaff as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Date Display -->
        <div class="text-center relative py-4">
            <div class="absolute inset-0 flex items-center justify-center z-0 opacity-10">
                <span class="text-8xl font-bold text-slate-200 select-none">{{ \Carbon\Carbon::parse($selectedDate)->format('d') }}</span>
            </div>
            <div class="relative z-10">
                <h3 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400">
                    {{ $this->formattedDate }}
                </h3>
                <div class="inline-flex items-center gap-2 mt-2 px-4 py-1.5 bg-white/60 dark:bg-slate-800/60 backdrop-blur rounded-full border border-slate-200 dark:border-slate-700 shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">{{ $totalEvents }} กิจกรรม</span>
                </div>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($staffWithEvents as $staff)
                <div class="glass-card overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col h-full animate-fade-in-up" style="animation-delay: {{ $loop->index * 100 }}ms">
                    <!-- Staff Header -->
                    <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 p-4 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-500/5 to-violet-500/5 rounded-full -mr-8 -mt-8 transition-transform group-hover:scale-150 duration-500"></div>
                        
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-full p-1 bg-white shadow-sm border border-slate-100">
                                    <div class="w-full h-full rounded-full overflow-hidden bg-slate-100">
                                        @if($staff->photo)
                                            <img src="{{ $staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fa-solid fa-user text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($staff->calendarEvents->count() > 0)
                                    <div class="absolute -bottom-1 -right-1 bg-indigo-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                        {{ $staff->calendarEvents->count() }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-slate-800 dark:text-white leading-tight group-hover:text-indigo-600 transition-colors">{{ $staff->name }}</h4>
                                <p class="text-slate-500 text-sm font-medium">{{ $staff->position }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Events List -->
                    <div class="p-4 space-y-3 flex-1 bg-white/40 dark:bg-slate-800/40">
                        @forelse($staff->calendarEvents as $event)
                            <div wire:click="showEvent({{ $event->id }})" 
                                 class="relative bg-white dark:bg-slate-700 rounded-xl p-3 border border-slate-100 dark:border-slate-600 shadow-sm hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-500/50 transition-all cursor-pointer group/event overflow-hidden">
                                
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-{{ $event->status_color }}-500"></div>
                                
                                <div class="flex gap-3 pl-2">
                                    <!-- Time -->
                                    <div class="flex flex-col items-center justify-center min-w-[3.5rem] px-2 py-1 bg-slate-50 dark:bg-slate-600 rounded-lg border border-slate-100 dark:border-slate-500">
                                        <span class="text-sm font-bold text-slate-700 dark:text-white">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</span>
                                        @if($event->end_time)
                                            <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="flex-1 min-w-0 py-0.5">
                                        <h5 class="font-bold text-slate-800 dark:text-white text-sm truncate group-hover/event:text-indigo-600 transition-colors">{{ $event->title }}</h5>
                                        <div class="flex items-center gap-3 mt-1.5">
                                            <span class="text-xs text-slate-500 flex items-center gap-1 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                                                <i class="fa-solid fa-location-dot text-rose-400"></i>
                                                {{ Str::limit($event->location, 15) }}
                                            </span>
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-{{ $event->status_color }}-50 text-{{ $event->status_color }}-600 border border-{{ $event->status_color }}-100">
                                                {{ $event->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center py-8 text-slate-400">
                                <i class="fa-regular fa-calendar text-2xl mb-2 opacity-50"></i>
                                <p class="text-sm">ไม่มีกิจกรรมวันนี้</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="lg:col-span-3">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-6 animate-pulse">
                            <i class="fa-solid fa-users-slash text-4xl text-slate-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">ไม่พบข้อมูลผู้ปฏิบัติงาน</h3>
                        <p class="text-slate-500">ลองปรับเปลี่ยนตัวกรองค้นหาดูใหม่อีกครั้ง</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if($staffWithEvents->isNotEmpty() && $totalEvents === 0)
            <div class="glass-card p-12 text-center text-slate-400 animate-fade-in-up">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-calendar-xmark text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-white">ไม่มีกิจกรรมในวันนี้</h3>
                <p class="text-sm mt-1">วันที่ {{ $this->formattedDate }} ไม่มีรายการกิจกรรม</p>
            </div>
        @endif
    </div>

    <!-- Event Detail Modal -->
    @if($showEventModal && $selectedEvent)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <!-- Backdrop -->
            <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" wire:click="closeEventModal"></div>

            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative w-full max-w-lg bg-white dark:bg-slate-800 shadow-2xl rounded-3xl animate-scale-in overflow-hidden border border-slate-100 dark:border-slate-700">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-br from-indigo-500 to-violet-600 p-6 text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full -ml-10 -mb-10 blur-xl"></div>
                        
                        <div class="relative z-10">
                            <h3 class="text-xl font-bold leading-tight mb-1">{{ $selectedEvent->title }}</h3>
                            <div class="flex items-center gap-2 text-indigo-100 text-sm">
                                <i class="fa-regular fa-calendar"></i>
                                {{ $selectedEvent->event_date->locale('th')->translatedFormat('lที่ j F Y') }}
                            </div>
                        </div>

                        <button wire:click="closeEventModal" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center text-white transition-colors backdrop-blur-sm">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-5">
                        <!-- Key Info Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 dark:bg-slate-700/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-600">
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">เวลา</div>
                                <div class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                    {{ $selectedEvent->time_range }}
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-700/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-600">
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">สถานะ</div>
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-bold bg-{{ $selectedEvent->status_color }}-50 text-{{ $selectedEvent->status_color }}-600 border border-{{ $selectedEvent->status_color }}-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-{{ $selectedEvent->status_color }}-500"></span>
                                        {{ $selectedEvent->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Info -->
                        <div class="flex items-center gap-4 p-4 bg-white border-2 border-slate-50 rounded-2xl shadow-sm">
                            <div class="w-12 h-12 rounded-full p-0.5 bg-gradient-to-br from-indigo-500 to-violet-500">
                                <div class="w-full h-full rounded-full overflow-hidden bg-white">
                                    @if($selectedEvent->staff->photo)
                                        <img src="{{ $selectedEvent->staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-400 uppercase">ผู้ปฏิบัติงาน</div>
                                <h4 class="font-bold text-slate-800 dark:text-white">{{ $selectedEvent->staff->name }}</h4>
                                <p class="text-xs text-slate-500 font-medium">{{ $selectedEvent->staff->position }}</p>
                            </div>
                        </div>

                        <!-- Details List -->
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400">สถานที่</div>
                                    <div class="font-medium text-slate-700 dark:text-slate-200">{{ $selectedEvent->location }}</div>
                                </div>
                            </div>

                            @if($selectedEvent->organization)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400">หน่วยงาน</div>
                                    <div class="font-medium text-slate-700 dark:text-slate-200">{{ $selectedEvent->organization }}</div>
                                </div>
                            </div>
                            @endif

                            @if($selectedEvent->description)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-align-left"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-400">รายละเอียดเพิ่มเติม</div>
                                    <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 p-3 rounded-xl mt-1 border border-slate-100">
                                        {{ $selectedEvent->description }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                        <button wire:click="closeEventModal" class="px-6 py-2 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all">
                            ปิดหน้าต่าง
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
