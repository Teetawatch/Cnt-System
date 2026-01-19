<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-shield-halved text-primary-600 dark:text-primary-400"></i>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('แดชบอร์ดผู้ดูแลระบบ') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">จัดการข้อมูลระบบปฏิทินการปฏิบัติงาน</p>
            </div>
        </div>
    </x-slot>

    @php
        $staffCount = \App\Models\Staff::count();
        $eventCount = \App\Models\CalendarEvent::count();
        $todayEvents = \App\Models\CalendarEvent::forDate(today())->count();
        $userCount = \App\Models\User::count();
    @endphp

    <!-- Admin Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-6 animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-users text-xl text-primary-600 dark:text-primary-400"></i>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $staffCount }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">ผู้ปฏิบัติงาน</p>
        </div>

        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-success-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-xl text-success-500 dark:text-green-400"></i>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $eventCount }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">กิจกรรมทั้งหมด</p>
        </div>

        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-warning-50 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-calendar-day text-xl text-warning-500 dark:text-yellow-400"></i>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayEvents }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">กิจกรรมวันนี้</p>
        </div>

        <div class="glass-card p-6 animate-slide-up" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-user-shield text-xl text-gray-600 dark:text-gray-400"></i>
                </div>
            </div>
            <h4 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $userCount }}</h4>
            <p class="text-sm text-gray-600 dark:text-gray-400">ผู้ใช้งานระบบ</p>
        </div>
    </div>

    <!-- Admin Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Staff Management -->
        <div class="glass-card p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fa-solid fa-users me-2 text-primary-500"></i>
                    จัดการผู้ปฏิบัติงาน
                </h3>
                <a href="{{ route('admin.staff.index') }}" class="btn-primary text-sm">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="space-y-3">
                @foreach(\App\Models\Staff::active()->ordered()->take(5)->get() as $staff)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                            @if($staff->photo)
                                <img src="{{ $staff->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <i class="fa-solid fa-user text-primary-600 dark:text-primary-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $staff->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $staff->position }}</p>
                        </div>
                        @if($staff->is_active)
                            <span class="badge badge-success">ใช้งาน</span>
                        @endif
                    </div>
                @endforeach

                @if(\App\Models\Staff::count() == 0)
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-user-plus text-3xl mb-2"></i>
                        <p>ยังไม่มีข้อมูลผู้ปฏิบัติงาน</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Event Management -->
        <div class="glass-card p-6 animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fa-solid fa-calendar-plus me-2 text-success-500"></i>
                    กิจกรรมล่าสุด
                </h3>
                <a href="{{ route('admin.events.index') }}" class="btn-primary text-sm">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="space-y-3">
                @foreach(\App\Models\CalendarEvent::with('staff')->orderByTime()->take(5)->get() as $event)
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-10 h-10 bg-{{ $event->status_color }}-50 dark:bg-{{ $event->status_color }}-900/30 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-calendar text-{{ $event->status_color }}-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $event->title }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $event->event_date->format('d/m/Y') }} • {{ $event->time_range }}
                            </p>
                        </div>
                        <span class="badge badge-{{ $event->status_color }}">{{ $event->status_label }}</span>
                    </div>
                @endforeach

                @if(\App\Models\CalendarEvent::count() == 0)
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-calendar-plus text-3xl mb-2"></i>
                        <p>ยังไม่มีกิจกรรม</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
