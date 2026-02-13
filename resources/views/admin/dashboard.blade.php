<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Overview Dashboard') }}
                </h2>
                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ \Carbon\Carbon::now()->locale('th')->isoFormat('LL') }}</span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('calendar.manage') }}" class="btn-primary gap-2 shadow-indigo-500/20">
                    <i class="fa-solid fa-plus"></i>
                    <span>สร้างกิจกรรมใหม่</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $staffCount = \App\Models\Staff::count();
        $eventCount = \App\Models\CalendarEvent::count();
        $todayEvents = \App\Models\CalendarEvent::forDate(today())->count();
        $userCount = \App\Models\User::count();
        
        $hours = now()->hour;
        $greeting = '';
        if ($hours < 12) {
            $greeting = 'สวัสดีตอนเช้า';
        } else if ($hours < 18) {
            $greeting = 'สวัสดีตอนบ่าย';
        } else {
            $greeting = 'สวัสดีตอนเย็น';
        }
    @endphp

    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-600 to-violet-600 p-8 mb-8 text-white shadow-xl animate-fade-in-up">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-medium mb-3 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    ระบบพร้อมใช้งาน
                </div>
                <h1 class="text-3xl font-bold mb-2 text-white">{{ $greeting }}, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-indigo-100 max-w-xl">ยินดีต้อนรับสู่ระบบบริหารจัดการปฏิทินงาน ติดตามข้อมูลและจัดการกิจกรรมต่างๆ ได้อย่างง่ายดายในที่เดียว</p>
            </div>
            <!-- Simple Weather/Time Widget Decoration -->
            <div class="hidden md:block text-right">
                <div class="text-4xl font-bold tracking-tight">{{ now()->format('H:i') }}</div>
                <div class="text-indigo-200 text-sm">เวลาปัจจุบัน</div>
            </div>
        </div>
    </div>

    <!-- Admin Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Staff Card -->
        <div class="group glass-card p-6 relative overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">ผู้ปฏิบัติงานทั้งหมด</p>
                        <h4 class="text-3xl font-bold text-slate-800">{{ $staffCount }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-gradient-primary rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-users text-lg"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-emerald-600 font-medium bg-emerald-50 w-fit px-2 py-1 rounded-lg">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>บุคลากรทรงคุณค่า</span>
                </div>
            </div>
        </div>

        <!-- Events Card -->
        <div class="group glass-card p-6 relative overflow-hidden animate-fade-in-up" style="animation-delay: 0.2s">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">กิจกรรมทั้งหมด</p>
                        <h4 class="text-3xl font-bold text-slate-800">{{ $eventCount }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-gradient-success rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-calendar-check text-lg"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-indigo-600 font-medium bg-indigo-50 w-fit px-2 py-1 rounded-lg">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>รวมทุกประเภท</span>
                </div>
            </div>
        </div>

        <!-- Today Events Card -->
        <div class="group glass-card p-6 relative overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-amber-400/20 to-orange-500/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">กิจกรรมวันนี้</p>
                        <h4 class="text-3xl font-bold text-slate-800">{{ $todayEvents }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-gradient-warning rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-calendar-day text-lg"></i>
                    </div>
                </div>
                @if($todayEvents > 0)
                    <div class="flex items-center gap-2 text-xs text-amber-600 font-medium bg-amber-50 w-fit px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-bell animate-swing"></i>
                        <span>มีภารกิจต้องทำ</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium bg-slate-100 w-fit px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-mug-hot"></i>
                        <span>ว่างเว้นภารกิจ</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Users Card -->
        <div class="group glass-card p-6 relative overflow-hidden animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-rose-400/20 to-red-500/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500 mb-1">ผู้ใช้งานระบบ</p>
                        <h4 class="text-3xl font-bold text-slate-800">{{ $userCount }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-gradient-danger rounded-xl flex items-center justify-center text-white shadow-lg shadow-rose-500/30 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-user-shield text-lg"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium bg-slate-100 w-fit px-2 py-1 rounded-lg">
                    <i class="fa-solid fa-server"></i>
                    <span>Manage all users</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Upcoming Events (Timeline) -->
        <div class="lg:col-span-2 glass-card p-6 animate-fade-in-up" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">กิจกรรมเร็วๆ นี้</h3>
                        <p class="text-xs text-slate-400">กำหนดการปฏิบัติงาน</p>
                    </div>
                </div>
                <a href="{{ route('calendar.manage') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                    ดูทั้งหมด <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="relative pl-4 border-l-2 border-slate-100 space-y-8 py-2">
                @forelse(\App\Models\CalendarEvent::with('staff')->whereDate('event_date', '>=', today())->orderBy('event_date')->orderBy('start_time')->take(5)->get() as $event)
                    <div class="relative">
                        <!-- Timeline Dot -->
                        <div class="absolute -left-[21px] top-1 w-3 h-3 rounded-full border-2 border-white 
                            {{ $event->status_color == 'success' ? 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.1)]' : 
                               ($event->status_color == 'warning' ? 'bg-amber-500 shadow-[0_0_0_4px_rgba(245,158,11,0.1)]' : 
                               'bg-indigo-500 shadow-[0_0_0_4px_rgba(99,102,241,0.1)]') }}">
                        </div>
                        
                        <div class="bg-slate-50 hover:bg-white border boundary-transparent hover:border-slate-200 p-4 rounded-2xl transition-all duration-300 hover:shadow-md cursor-default group">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="badge badge-{{ $event->status_color }}">{{ $event->status_label }}</span>
                                        <span class="text-xs text-slate-400 font-medium">
                                            <i class="fa-regular fa-clock mr-1"></i>{{ $event->event_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $event->title }}</h4>
                                    <p class="text-sm text-slate-500 mt-1 line-clamp-1">{{ $event->description ?? 'ไม่มีรายละเอียดเพิ่มเติม' }}</p>
                                </div>
                                <div class="flex items-center gap-3 border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-4 min-w-[140px]">
                                    @if($event->staff && $event->staff->photo)
                                        <img src="{{ $event->staff->photo_url }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-white" alt="">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold ring-2 ring-white">
                                            {{ substr($event->staff->name ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700 truncate max-w-[100px]">{{ $event->staff->name ?? 'ไม่ระบุ' }}</span>
                                        <span class="text-[10px] text-slate-400">ผู้รับผิดชอบ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-xmark text-slate-300 text-2xl"></i>
                        </div>
                        <p class="text-slate-500 font-medium">ยังไม่มีกิจกรรมเร็วๆ นี้</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Column: Quick Actions & Staff -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.6s">
                <h3 class="text-lg font-bold text-slate-800 mb-4">เมนูด่วน</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('calendar.manage') }}" class="group flex flex-col items-center justify-center p-4 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-plus-circle text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold">สร้างกิจกรรม</span>
                    </a>
                    <a href="{{ route('staff.index') }}" class="group flex flex-col items-center justify-center p-4 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-user-plus text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold">เพิ่มบุคลากร</span>
                    </a>
                    <a href="{{ route('calendar.index') }}" class="group flex flex-col items-center justify-center p-4 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-calendar-days text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold">ปฏิทินรวม</span>
                    </a>
                     <a href="#" class="group flex flex-col items-center justify-center p-4 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all duration-300">
                        <i class="fa-solid fa-file-pdf text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold">รายงาน</span>
                    </a>
                </div>
            </div>

            <!-- New Staff -->
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 0.7s">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">บุคลากรใหม่</h3>
                    <a href="{{ route('staff.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">ดูทั้งหมด</a>
                </div>
                <div class="space-y-4">
                    @forelse(\App\Models\Staff::active()->ordered()->take(4)->get() as $staff)
                        <div class="flex items-center gap-3 group cursor-pointer hover:bg-slate-50 p-2 -mx-2 rounded-lg transition-colors">
                            <div class="relative">
                                @if($staff->photo)
                                    <img src="{{ $staff->photo_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm group-hover:border-indigo-100 transition-colors" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-sm font-bold border-2 border-white shadow-sm">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex-grow min-w-0">
                                <h5 class="text-sm font-bold text-slate-800 truncate group-hover:text-indigo-600 transition-colors">{{ $staff->name }}</h5>
                                <p class="text-xs text-slate-500 truncate">{{ $staff->position }}</p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-slate-300 group-hover:text-indigo-400 transition-colors"></i>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">
                            ไม่พบข้อมูลบุคลากร
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
