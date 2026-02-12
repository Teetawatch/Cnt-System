<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30 transform transition-transform hover:scale-110 hover:rotate-3 duration-300">
                    <i class="fa-solid fa-gauge-high text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight">
                        {{ __('หน้าหลัก') }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">ภาพรวมและปฏิทินกิจกรรรม</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-3 px-4 py-2 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <span class="text-slate-600 dark:text-slate-300 font-medium">{{ now()->locale('th')->translatedFormat('l, j F Y') }}</span>
            </div>
        </div>
    </x-slot>

    @php
        $todayEvents = \App\Models\CalendarEvent::forDate(today())->count();
        $weekEvents = \App\Models\CalendarEvent::dateRange(today()->startOfWeek(), today()->endOfWeek())->count();
        $monthEvents = \App\Models\CalendarEvent::dateRange(today()->startOfMonth(), today()->endOfMonth())->count();
        $staffCount = \App\Models\Staff::active()->count();
    @endphp

    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-700 p-8 text-white shadow-2xl shadow-indigo-500/20 mb-8 animate-fade-in-up">
        <!-- Decorative shapes -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-60 h-60 rounded-full bg-black/10 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h3 class="text-3xl font-bold mb-2 flex items-center gap-3">
                    <span class="inline-block animate-wave origin-bottom-right">👋</span>
                    @auth
                        สวัสดีคุณ {{ Auth::user()->name }}
                    @else
                        ยินดีต้อนรับสู่ระบบ
                    @endauth
                </h3>
                <p class="text-indigo-100 text-lg max-w-2xl leading-relaxed">
                    @auth
                        @if(Auth::user()->isAdmin())
                            คุณเข้าสู่ระบบในฐานะ <span class="font-bold text-white bg-white/20 px-2 py-0.5 rounded-lg">ผู้ดูแลระบบ</span> จัดการข้อมูลและติดตามสถานะการดำเนินงานได้ที่นี่
                        @else
                            ติดตามตารางงานและกิจกรรมที่สำคัญของคุณได้ตลอดเวลา
                        @endif
                    @else
                        ดูตารางงานของผู้บริหารและกิจกรรมต่างๆ ได้ที่นี่
                    @endauth
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('calendar.index') }}" class="group inline-flex items-center px-5 py-3 bg-white text-indigo-600 rounded-xl font-bold shadow-lg transition-all hover:bg-indigo-50 hover:shadow-xl hover:scale-105 active:scale-95">
                    <i class="fa-solid fa-calendar-days me-2 transition-transform group-hover:rotate-12"></i>
                    ดูปฏิทิน
                </a>
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ url('/admin') }}" class="inline-flex items-center px-5 py-3 bg-indigo-800/50 text-white border border-indigo-400/30 rounded-xl font-medium backdrop-blur-sm hover:bg-indigo-800/70 transition-all hover:scale-105 active:scale-95">
                            <i class="fa-solid fa-shield-halved me-2"></i>
                            จัดการระบบ
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-3 bg-indigo-800/50 text-white border border-indigo-400/30 rounded-xl font-medium backdrop-blur-sm hover:bg-indigo-800/70 transition-all hover:scale-105 active:scale-95">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        เข้าสู่ระบบ
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Events -->
        <div class="glass-card p-6 relative overflow-hidden group hover:border-indigo-200 transition-all duration-300">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-solid fa-calendar-day text-6xl text-indigo-600"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-calendar-day text-xl"></i>
                    </div>
                    <span class="badge badge-primary shadow-sm">วันนี้</span>
                </div>
                <h4 class="text-3xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-indigo-600 transition-colors">{{ $todayEvents }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">กิจกรรมวันนี้</p>
            </div>
        </div>

        <!-- This Week -->
        <div class="glass-card p-6 relative overflow-hidden group hover:border-emerald-200 transition-all duration-300">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-solid fa-calendar-week text-6xl text-emerald-600"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-calendar-week text-xl"></i>
                    </div>
                    <span class="badge badge-success shadow-sm">สัปดาห์นี้</span>
                </div>
                <h4 class="text-3xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-emerald-600 transition-colors">{{ $weekEvents }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">กิจกรรมสัปดาห์นี้</p>
            </div>
        </div>

        <!-- This Month -->
        <div class="glass-card p-6 relative overflow-hidden group hover:border-rose-200 transition-all duration-300">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-regular fa-calendar text-6xl text-rose-600"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 shadow-sm border border-rose-100 group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300">
                        <i class="fa-regular fa-calendar text-xl"></i>
                    </div>
                    <span class="badge badge-danger shadow-sm">เดือนนี้</span>
                </div>
                <h4 class="text-3xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-rose-600 transition-colors">{{ $monthEvents }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">กิจกรรมเดือนนี้</p>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="glass-card p-6 relative overflow-hidden group hover:border-amber-200 transition-all duration-300">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i class="fa-solid fa-users text-6xl text-amber-500"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 shadow-sm border border-amber-100 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                    <span class="badge badge-warning shadow-sm">บุคลากร</span>
                </div>
                <h4 class="text-3xl font-bold text-slate-800 dark:text-white mb-1 group-hover:text-amber-500 transition-colors">{{ $staffCount }}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">ผู้ปฏิบัติทั้งหมด</p>
            </div>
        </div>
    </div>

    <!-- Today's Schedule & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Today's Events -->
        <div class="lg:col-span-2 glass-card p-0 overflow-hidden flex flex-col h-full animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-white/50 backdrop-blur-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <div class="w-2 h-6 bg-indigo-500 rounded-full"></div>
                    กิจกรรมวันนี้
                </h3>
                <a href="{{ route('calendar.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 group">
                    ดูทั้งหมด <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
            
            <div class="p-6 flex-grow bg-slate-50/30">
                <div class="space-y-4">
                    @forelse(\App\Models\CalendarEvent::with('staff')->forDate(today())->orderByTime()->get() as $event)
                        <div class="group relative bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-300">
                            <!-- Left accent bar -->
                            <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full bg-{{ $event->status_color }}-500"></div>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pl-3">
                                <div class="flex sm:flex-col items-baseline sm:items-center gap-1 sm:gap-0 min-w-[70px] text-center sm:border-r sm:border-slate-100 sm:pr-4">
                                    <span class="text-2xl font-bold text-slate-700 tracking-tight">{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}</span>
                                    <span class="text-xs font-semibold text-slate-400 uppercase">เวลา</span>
                                </div>
                                
                                <div class="flex-grow">
                                    <h5 class="font-bold text-slate-800 text-lg group-hover:text-indigo-600 transition-colors">{{ $event->title }}</h5>
                                    
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-slate-500">
                                        <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
                                            <i class="fa-solid fa-user text-indigo-500 text-xs"></i>
                                            <span class="font-medium">{{ $event->staff->name }}</span>
                                        </div>
                                        @if($event->location)
                                            <div class="flex items-center gap-1.5 bg-slate-50 px-2 py-1 rounded-md border border-slate-100">
                                                <i class="fa-solid fa-location-dot text-rose-500 text-xs"></i>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="sm:self-center pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-50 mt-2 sm:mt-0 flex justify-between sm:block w-full sm:w-auto">
                                    <span class="badge badge-{{ $event->status_color }} shadow-sm">
                                        {{ $event->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-slate-400 bg-white/40 rounded-2xl border-2 border-dashed border-slate-200">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-solid fa-calendar-check text-3xl text-slate-300"></i>
                            </div>
                            <p class="font-medium text-lg">ไม่มีกิจกรรมนัดหมายวันนี้</p>
                            <p class="text-sm">คุณสามารถพักผ่อนหรือจัดการงานอื่นๆ ได้</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions & Info -->
        <div class="space-y-6">
            <!-- Actions Card -->
            <div class="glass-card p-6 animate-fade-in-up" style="animation-delay: 200ms;">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    เมนูด่วน
                </h3>
                
                <div class="space-y-3">
                    <a href="{{ route('calendar.index') }}" class="flex items-center gap-4 p-3 rounded-xl bg-white border border-slate-100 hover:border-indigo-200 hover:shadow-md hover:bg-indigo-50/50 transition-all duration-300 group">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="font-bold text-slate-700 group-hover:text-indigo-700">ดูปฏิทิน</p>
                            <p class="text-xs text-slate-500">ตารางงานทั้งหมด</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 group-hover:text-indigo-400 group-hover:translate-x-1 transition-all"></i>
                    </a>



                    <a href="{{ route('calendar.pdf', ['date' => now()->format('Y-m-d')]) }}" target="_blank" class="flex items-center gap-4 p-3 rounded-xl bg-white border border-slate-100 hover:border-rose-200 hover:shadow-md hover:bg-rose-50/50 transition-all duration-300 group">
                        <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-file-pdf"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="font-bold text-slate-700 group-hover:text-rose-700">พิมพ์รายงาน</p>
                            <p class="text-xs text-slate-500">ส่งออกเป็น PDF</p>
                        </div>
                        <i class="fa-solid fa-arrow-up-right-from-square text-slate-300 group-hover:text-rose-400 group-hover:translate-x-1 transition-all"></i>
                    </a>
                </div>
            </div>

            <!-- Profile Info or Other Widget -->
            <div class="glass-card p-6 bg-gradient-to-br from-slate-800 to-slate-900 text-white animate-fade-in-up" style="animation-delay: 300ms;">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-xl font-bold backdrop-blur-sm">
                        {{ now()->format('d') }}
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider">เดือนปัจจุบัน</p>
                        <h4 class="text-xl font-bold font-heading">{{ now()->locale('th')->translatedFormat('F Y') }}</h4>
                    </div>
                </div>
                <div class="h-1 w-full bg-white/10 rounded-full overflow-hidden mb-2">
                    <div class="h-full bg-gradient-to-r from-indigo-400 to-violet-400 w-[{{ (now()->day / now()->daysInMonth) * 100 }}%]"></div>
                </div>
                <p class="text-xs text-slate-400 flex justify-between">
                    <span>เริ่มต้นเดือน</span>
                    <span>สิ้นเดือน</span>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
