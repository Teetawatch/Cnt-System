<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-gauge-high text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('หน้าหลัก') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">ยินดีต้อนรับสู่ระบบปฏิทินการปฏิบัติงาน</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ now()->locale('th')->translatedFormat('l, j F Y') }}</span>
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
    <div class="glass-card p-6 mb-6 animate-fade-in">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                    <i class="fa-solid fa-hand-wave text-yellow-500 me-2"></i>
                    @auth
                        สวัสดีคุณ {{ Auth::user()->name }}!
                    @else
                        ยินดีต้อนรับสู่ระบบปฏิทินงาน
                    @endauth
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    @auth
                        @if(Auth::user()->isAdmin())
                            คุณเข้าสู่ระบบในฐานะ <span class="text-primary-600 font-medium">ผู้ดูแลระบบ</span> สามารถจัดการข้อมูลทั้งหมดได้
                        @else
                            คุณสามารถดูปฏิทินการปฏิบัติงานและพิมพ์รายงานได้
                        @endif
                    @else
                        ดูตารางงานของผู้บริหารและกิจกรรมต่างๆ ได้ที่นี่
                    @endauth
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('calendar.index') }}" class="btn-primary">
                    <i class="fa-solid fa-calendar-days me-2"></i>
                    ดูปฏิทิน
                </a>
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn-secondary">
                            <i class="fa-solid fa-shield-halved me-2"></i>
                            จัดการระบบ
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-secondary">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        เข้าสู่ระบบ
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('error'))
        <div class="glass-card p-4 mb-6 border-l-4 border-danger-500 bg-danger-50 dark:bg-danger-900/20 animate-fade-in">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation text-danger-500 me-3"></i>
                <span class="text-danger-700 dark:text-danger-300">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="glass-card p-4 mb-6 border-l-4 border-success-500 bg-success-50 dark:bg-green-900/20 animate-fade-in">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-success-500 me-3"></i>
                <span class="text-green-700 dark:text-green-300">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Today's Events -->
        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-day text-xl text-primary-600 dark:text-primary-400"></i>
                </div>
                <span class="badge badge-primary">วันนี้</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayEvents }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">กิจกรรมวันนี้</p>
        </div>

        <!-- This Week -->
        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-success-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-week text-xl text-success-500 dark:text-green-400"></i>
                </div>
                <span class="badge badge-success">สัปดาห์นี้</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $weekEvents }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">กิจกรรมสัปดาห์นี้</p>
        </div>

        <!-- Total Staff -->
        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-warning-50 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-users text-xl text-warning-500 dark:text-yellow-400"></i>
                </div>
                <span class="badge badge-warning">ผู้ปฏิบัติ</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $staffCount }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">ผู้ปฏิบัติงานทั้งหมด</p>
        </div>

        <!-- This Month -->
        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-danger-50 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-regular fa-calendar text-xl text-danger-500 dark:text-red-400"></i>
                </div>
                <span class="badge badge-danger">เดือนนี้</span>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $monthEvents }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">กิจกรรมเดือนนี้</p>
        </div>
    </div>

    <!-- Today's Schedule & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Events -->
        <div class="lg:col-span-2 glass-card p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fa-solid fa-calendar-day me-2 text-primary-500"></i>
                    กิจกรรมวันนี้
                </h3>
                <a href="{{ route('calendar.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                    ดูทั้งหมด <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse(\App\Models\CalendarEvent::with('staff')->forDate(today())->orderByTime()->get() as $event)
                    <div class="flex gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="text-center min-w-[60px]">
                            <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                            </p>
                            <p class="text-xs text-gray-400">น.</p>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</p>
                            <div class="flex flex-wrap gap-3 mt-1 text-sm text-gray-500">
                                <span>
                                    <i class="fa-solid fa-user me-1"></i>
                                    {{ $event->staff->name }}
                                </span>
                                <span>
                                    <i class="fa-solid fa-location-dot me-1"></i>
                                    {{ $event->location }}
                                </span>
                            </div>
                        </div>
                        <span class="badge badge-{{ $event->status_color }} h-fit">
                            {{ $event->status_label }}
                        </span>
                    </div>
                @empty
                    <div class="flex items-center justify-center py-12 text-gray-400">
                        <div class="text-center">
                            <i class="fa-solid fa-calendar-check text-4xl mb-3"></i>
                            <p>ไม่มีกิจกรรมในวันนี้</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-card p-6 animate-fade-in">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fa-solid fa-bolt me-2 text-warning-500"></i>
                เมนูลัด
            </h3>
            
            <div class="space-y-3">
                <a href="{{ route('calendar.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">ดูปฏิทิน</p>
                        <p class="text-xs text-gray-500">ดูตารางงานทั้งหมด</p>
                    </div>
                </a>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-10 h-10 bg-success-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-user-plus text-success-500 dark:text-green-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">จัดการผู้ปฏิบัติ</p>
                                <p class="text-xs text-gray-500">เพิ่ม/แก้ไขข้อมูล</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <div class="w-10 h-10 bg-warning-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-calendar-plus text-warning-500 dark:text-yellow-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">จัดการกิจกรรม</p>
                                <p class="text-xs text-gray-500">เพิ่ม/แก้ไขกิจกรรม</p>
                            </div>
                        </a>
                    @endif
                @endauth

                <!-- PDF Export accessible to everyone -->
                <a href="{{ route('calendar.pdf', ['date' => now()->format('Y-m-d')]) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-10 h-10 bg-danger-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-file-pdf text-danger-500 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">พิมพ์รายงาน</p>
                        <p class="text-xs text-gray-500">ส่งออกเป็น PDF</p>
                    </div>
                </a>

                @auth
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-gear text-gray-600 dark:text-gray-300"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">ตั้งค่าโปรไฟล์</p>
                            <p class="text-xs text-gray-500">แก้ไขข้อมูลส่วนตัว</p>
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-right-to-bracket text-primary-500 dark:text-primary-400"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">เข้าสู่ระบบ</p>
                            <p class="text-xs text-gray-500">สำหรับเจ้าหน้าที่</p>
                        </div>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>
