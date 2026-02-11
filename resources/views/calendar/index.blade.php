<x-app-layout>
    @php
        // Get date from URL parameter or use today
        $selectedDate = request('date') ? \Carbon\Carbon::parse(request('date')) : today();
        $selectedStaffId = request('staff_id');
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('ปฏิทินการปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">ดูตารางงานของผู้อำนวยการและรองผู้อำนวยการ</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a id="pdfLink" 
                   href="{{ route('calendar.pdf', ['date' => $selectedDate->format('Y-m-d'), 'staff' => $selectedStaffId]) }}" 
                   target="_blank" 
                   class="btn-secondary text-sm">
                    <i class="fa-solid fa-file-pdf me-2"></i>
                    พิมพ์ PDF
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->    
    <div class="glass-card p-4 mb-6 animate-fade-in">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">วันที่:</label>
                <input type="date" 
                       id="dateFilter" 
                       value="{{ $selectedDate->format('Y-m-d') }}" 
                       class="form-input-custom text-sm"
                       onchange="updateFilters()">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600 dark:text-gray-400">ผู้ปฏิบัติ:</label>
                <select id="staffFilter" class="form-input-custom text-sm w-56" onchange="updateFilters()">
                    <option value="">ทั้งหมด</option>
                    @foreach(\App\Models\Staff::active()->ordered()->get() as $staff)
                        <option value="{{ $staff->id }}" {{ $selectedStaffId == $staff->id ? 'selected' : '' }}>{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(!$selectedDate->isToday())
                <button onclick="goToToday()" class="btn-primary text-sm">
                    <i class="fa-solid fa-calendar-day me-2"></i>
                    วันนี้
                </button>
            @endif
        </div>
    </div>

    <!-- Selected Date Header -->
    <div class="text-center mb-6">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ $selectedDate->locale('th')->translatedFormat('l') }}
        </h3>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            {{ $selectedDate->locale('th')->translatedFormat('j F') }} พ.ศ. {{ $selectedDate->year + 543 }}
        </p>
    </div>

    <!-- Events Container -->
    <div id="eventsContainer">
        @php
            $staffList = \App\Models\Staff::active()
                ->ordered()
                ->when($selectedStaffId, fn($q) => $q->where('id', $selectedStaffId))
                ->with(['calendarEvents' => function($query) use ($selectedDate) {
                    $query->forDate($selectedDate)->orderByTime();
                }])
                ->get();
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($staffList as $staff)
                <div class="glass-card overflow-hidden animate-slide-up">
                    <!-- Staff Header -->
                    <div class="p-5 text-white relative overflow-hidden" style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); border-bottom: 1px solid rgba(255,255,255,0.1);">
                        <!-- Decorative Glows -->
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-24 h-24 bg-blue-400/20 rounded-full blur-2xl"></div>

                        <div class="relative flex items-center gap-4">
                            <div class="relative">
                                <div class="absolute -inset-1 bg-white/20 rounded-full blur-sm"></div>
                                <div class="relative w-14 h-14 bg-white/20 rounded-full flex items-center justify-center border-2 border-white/50 shadow-lg overflow-hidden">
                                    @if($staff->photo)
                                        <img src="{{ $staff->photo_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user text-2xl"></i>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-xl text-white tracking-tight drop-shadow-md leading-tight">
                                    {{ $staff->name }}
                                </h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="bg-white/15 backdrop-blur-sm border border-white/20 px-2.5 py-0.5 rounded-lg text-[10px] font-semibold text-white uppercase tracking-wider shadow-sm">
                                        {{ $staff->position }}
                                    </span>
                                </div>
                                @if($staff->description)
                                    <div class="mt-2.5 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg p-2 flex items-start gap-2 group-hover:bg-white/15 transition-colors">
                                        <i class="fa-solid fa-circle-info text-[10px] mt-0.5 text-blue-200"></i>
                                        <p class="text-white text-xs font-medium leading-tight">{{ $staff->description }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-auto flex flex-col items-end">
                                <div class="bg-black/20 backdrop-blur-md border border-white/10 px-3 py-1.5 rounded-2xl text-center shadow-lg min-w-[70px]">
                                    <span class="block text-lg font-bold leading-none">{{ $staff->calendarEvents->count() }}</span>
                                    <span class="text-[9px] uppercase tracking-tighter opacity-80 font-semibold">รายการ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events List -->
                    <div class="p-4">
                        @forelse($staff->calendarEvents as $event)
                            <div class="flex gap-4 py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                <div class="text-center min-w-[60px]">
                                    <p class="text-sm font-semibold" style="color: #2563eb;">
                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                    </p>
                                    @if($event->end_time)
                                        <p class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-500">
                                        <i class="fa-solid fa-location-dot me-1"></i>
                                        {{ $event->location }}
                                    </p>
                                    @if($event->organization)
                                        <p class="text-sm text-gray-400">
                                            <i class="fa-solid fa-building me-1"></i>
                                            {{ $event->organization }}
                                        </p>
                                    @endif
                                    @if($event->description)
                                        <p class="text-sm text-gray-500 mt-1">
                                            <i class="fa-solid fa-align-left me-1"></i>
                                            {{ $event->description }}
                                        </p>
                                    @endif
                                </div>
                                <span class="badge badge-{{ $event->status_color }} h-fit">
                                    {{ $event->status_label }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-2"></i>
                                <p>ไม่มีกิจกรรมในวันที่เลือก</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        @if($staffList->isEmpty())
            <div class="glass-card p-12 text-center text-gray-400">
                <i class="fa-solid fa-users-slash text-5xl mb-4"></i>
                <p class="text-xl mb-2">ยังไม่มีข้อมูลผู้ปฏิบัติงาน</p>
                <p class="text-sm">กรุณาติดต่อผู้ดูแลระบบเพื่อเพิ่มข้อมูล</p>
            </div>
        @endif
    </div>

    <script>
        function updateFilters() {
            const date = document.getElementById('dateFilter').value;
            const staffId = document.getElementById('staffFilter').value;
            
            // Build URL with parameters
            let url = window.location.pathname + '?date=' + date;
            if (staffId) {
                url += '&staff_id=' + staffId;
            }
            
            // Redirect to reload page with new parameters
            window.location.href = url;
        }
        
        function goToToday() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateFilter').value = today;
            updateFilters();
        }
    </script>
</x-app-layout>
