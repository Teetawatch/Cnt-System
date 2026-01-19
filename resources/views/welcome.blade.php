<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ปฏิทินการปฏิบัติงาน</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --gold: #d97706;
            --gold-light: #f59e0b;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
        }
        
        .gradient-mesh {
            background: 
                radial-gradient(at 40% 20%, rgba(251, 191, 36, 0.25) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(59, 130, 246, 0.2) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(251, 191, 36, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 50%, rgba(147, 197, 253, 0.2) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(254, 215, 170, 0.3) 0px, transparent 50%),
                linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .shine {
            position: relative;
            overflow: hidden;
        }
        
        .shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                transparent 0%,
                rgba(255, 255, 255, 0.4) 50%,
                transparent 100%
            );
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }

        /* Light Theme Text Colors */
        .light-text-primary { color: #1f2937; }
        .light-text-secondary { color: #4b5563; }
        .light-text-muted { color: #6b7280; }
    </style>
</head>
<body class="font-sans antialiased">
    @php
        // รับค่า filter จาก query parameters
        $selectedDate = request('date') ? \Carbon\Carbon::parse(request('date')) : now();
        $selectedStaffId = request('staff_id');
        
        $thaiDate = $selectedDate->locale('th')->translatedFormat('l');
        $thaiDay = $selectedDate->day;
        $thaiMonth = $selectedDate->locale('th')->translatedFormat('F');
        $thaiYear = $selectedDate->year + 543;
        
        // Query staffList พร้อมกรองตาม selectedStaffId ถ้ามี
        $staffQuery = \App\Models\Staff::active()->ordered()->with(['calendarEvents' => function($query) use ($selectedDate) {
            $query->forDate($selectedDate)->orderByTime();
        }]);
        
        if ($selectedStaffId) {
            $staffQuery->where('id', $selectedStaffId);
        }
        
        $staffList = $staffQuery->get();
        
        // ดึงรายชื่อ staff ทั้งหมดสำหรับ dropdown
        $allStaff = \App\Models\Staff::active()->ordered()->get();
        
        $totalEvents = \App\Models\CalendarEvent::forDate($selectedDate)->count();
    @endphp

    <!-- Main Container with Gradient Background -->
    <div class="min-h-screen gradient-mesh">
        
        <!-- Floating Header -->
        <header class="fixed top-0 left-0 right-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="glass-effect rounded-2xl px-6 py-3 flex items-center justify-between">
                    <!-- Logo & Title -->
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl flex items-center justify-center p-1.5 shadow-lg">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-slate-800 font-semibold text-lg">ปฏิทินการปฏิบัติงาน</h1>
                            <p class="text-slate-500 text-xs">ผู้อำนวยการและรองผู้อำนวยการ</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('calendar.pdf', ['date' => $selectedDate->format('Y-m-d')]) }}" 
                           target="_blank"
                           class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm transition-all duration-300">
                            <i class="fa-solid fa-file-pdf text-red-500"></i>
                            <span>พิมพ์ PDF</span>
                        </a>
                        @auth
                            <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-slate-900 font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/25">
                                <i class="fa-solid fa-cog"></i>
                                <span class="hidden sm:inline">จัดการ</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm transition-all duration-300">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>เข้าสู่ระบบ</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="pt-32 pb-16 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto text-center">
                <!-- Decorative Badge -->
                <div class="inline-flex items-center gap-2 px-5 py-2 rounded-full glass-effect text-amber-600 mb-8 animate-float">
                    <i class="fa-solid fa-calendar-star"></i>
                    <span class="text-xl font-medium">
                        @if($selectedDate->isToday())
                            ตารางงานวันนี้
                        @else
                            ตารางงานวันที่ {{ $selectedDate->locale('th')->translatedFormat('j F') }} {{ $selectedDate->year + 543 }}
                        @endif
                    </span>
                </div>
                
                <!-- Big Date Display -->
                <div class="mb-8">
                   <p class="text-xl text-amber-600 mt-2 font-medium">วัน{{ $thaiDate }}ที่</p>
                    <h2 class="text-8xl md:text-9xl font-bold text-gradient mb-2">{{ $thaiDay }}</h2>
                    <p class="text-3xl md:text-4xl font-light text-slate-700">{{ $thaiMonth }} {{ $thaiYear }}</p>
                    
                    <!-- Real-time Clock -->
                    <div class="mt-4 inline-flex items-center gap-3 px-6 py-3 rounded-2xl glass-effect">
                        <i class="fa-regular fa-clock text-2xl text-amber-500"></i>
                        <span id="realTimeClock" class="text-xl md:text-xl font-bold text-slate-800 tabular-nums"></span>
                        <span class="text-lg text-slate-500">น.</span>
                    </div>
                </div>

                <!-- Filter Section -->
                <form id="filterForm" method="GET" action="{{ route('home') }}" class="mb-10">
                    <div class="glass-effect rounded-2xl p-6 md:p-8 max-w-3xl mx-auto">
                        <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
                            <!-- Date Filter -->
                            <div class="w-full md:flex-1">
                                <label class="text-slate-600 text-sm font-medium mb-2 block text-left">
                                    <i class="fa-regular fa-calendar mr-2"></i>เลือกวันที่
                                </label>
                                <input 
                                    type="date" 
                                    name="date" 
                                    id="filterDate"
                                    value="{{ $selectedDate->format('Y-m-d') }}"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all duration-300"
                                    onchange="document.getElementById('filterForm').submit()"
                                >
                            </div>
                            
                            <!-- Person Filter -->
                            <div class="w-full md:flex-1">
                                <label class="text-slate-600 text-sm font-medium mb-2 block text-left">
                                    <i class="fa-regular fa-user mr-2"></i>เลือกบุคคล
                                </label>
                                <select 
                                    name="staff_id" 
                                    id="filterStaff"
                                    class="w-full px-4 py-3 rounded-xl bg-white border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all duration-300 appearance-none cursor-pointer"
                                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%23475569\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"%3e%3cpolyline points=\"6 9 12 15 18 9\"%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1rem;"
                                    onchange="document.getElementById('filterForm').submit()"
                                >
                                    <option value="">ทั้งหมด</option>
                                    @foreach($allStaff as $person)
                                        <option value="{{ $person->id }}" {{ $selectedStaffId == $person->id ? 'selected' : '' }}>
                                            {{ $person->name }} ({{ $person->position }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-3 mt-4 md:mt-6">
                                <a href="{{ route('home') }}" 
                                   class="px-5 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow-md"
                                   title="ดูวันนี้">
                                    <i class="fa-solid fa-calendar-day"></i>
                                    <span class="hidden sm:inline">วันนี้</span>
                                </a>
                                @if(request('date') || request('staff_id'))
                                    <a href="{{ route('home') }}" 
                                       class="px-5 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-medium transition-all duration-300 flex items-center gap-2"
                                       title="ล้างตัวกรอง">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span class="hidden sm:inline">ล้าง</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Active Filters Display -->
                        @if($selectedStaffId)
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-slate-500 text-sm">กำลังดู:</span>
                                    @php $filterStaff = $allStaff->firstWhere('id', $selectedStaffId); @endphp
                                    @if($filterStaff)
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-amber-100 text-amber-700 text-sm">
                                            <i class="fa-solid fa-user"></i>
                                            {{ $filterStaff->name }}
                                            <a href="{{ route('home', ['date' => $selectedDate->format('Y-m-d')]) }}" class="hover:text-amber-900 transition-colors">
                                                <i class="fa-solid fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </form>

                <!-- Stats Cards -->
                <div class="flex flex-wrap justify-center gap-6 mb-12">
                    <div class="glass-effect rounded-2xl px-8 py-5 min-w-[140px]">
                        <p class="text-4xl font-bold text-gradient">{{ $staffList->count() }}</p>
                        <p class="text-slate-500 text-sm mt-1">ผู้บริหาร</p>
                    </div>
                    <div class="glass-effect rounded-2xl px-8 py-5 min-w-[140px]">
                        <p class="text-4xl font-bold text-gradient">{{ $totalEvents }}</p>
                        <p class="text-slate-500 text-sm mt-1">กิจกรรม</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Staff & Events Section -->
        <section class="pb-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @forelse($staffList as $staff)
                        <div class="group bg-white/80 backdrop-blur-xl rounded-3xl overflow-hidden border border-slate-200 shadow-lg transition-all duration-500 card-hover" style="animation-delay: {{ $loop->index * 0.15 }}s">
                            
                            <!-- Staff Profile Header -->
                            <div class="relative p-8 pb-6">
                                <div class="absolute inset-0 bg-gradient-to-br from-amber-100/50 to-transparent"></div>
                                <div class="relative flex items-center gap-6">
                                    <!-- Avatar with Ring - BIGGER -->
                                    <div class="relative">
                                        <div class="w-28 h-28 md:w-32 md:h-32 rounded-3xl overflow-hidden ring-4 ring-amber-500/30 group-hover:ring-amber-500/50 transition-all duration-300 shadow-2xl">
                                            @if($staff->photo)
                                                <img src="{{ $staff->photo_url }}" alt="{{ $staff->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                                                    <i class="fa-solid fa-user text-4xl text-slate-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Event Count Badge -->
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg ring-4 ring-white">
                                            <span class="text-white text-lg font-bold">{{ $staff->calendarEvents->count() }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Name & Position - BIGGER -->
                                    <div class="flex-1">
                                        <h3 class="text-2xl md:text-3xl font-bold text-slate-800 group-hover:text-amber-600 transition-colors duration-300">
                                            {{ $staff->name }}
                                        </h3>
                                        <p class="text-amber-600 text-l font-medium mt-1">{{ $staff->position }}</p>
                                        @if($staff->department)
                                            <p class="text-slate-500 text-sm mt-2 flex items-center gap-2">
                                                <i class="fa-solid fa-building"></i>
                                                {{ $staff->department }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Events List -->
                            <div class="p-6 pt-2">
                                @if($staff->calendarEvents->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($staff->calendarEvents as $event)
                                            <div class="group/event relative pl-6 before:absolute before:left-0 before:top-0 before:bottom-0 before:w-1 before:bg-gradient-to-b before:from-amber-500 before:to-amber-300 before:rounded-full">
                                                <div class="bg-slate-50 rounded-2xl p-4 hover:bg-amber-50 transition-all duration-300 border border-transparent hover:border-amber-200">
                                                    <div class="flex items-start justify-between gap-3 mb-2">
                                                        <div class="flex-1">
                                                            <h4 class="font-semibold text-slate-800 group-hover/event:text-amber-600 transition-colors">
                                                                {{ $event->title }}
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                                                        <span class="flex items-center gap-1.5">
                                                            <i class="fa-regular fa-clock text-amber-500"></i>
                                                            {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                                            @if($event->end_time)
                                                                - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                                            @endif
                                                            น.
                                                        </span>
                                                        <span class="flex items-center gap-1.5">
                                                            <i class="fa-solid fa-location-dot text-amber-500"></i>
                                                            {{ $event->location }}
                                                        </span>
                                                    </div>
                                                    
                                                    @if($event->organization)
                                                        <p class="text-xs text-slate-400 mt-2 flex items-center gap-1.5">
                                                            <i class="fa-regular fa-building"></i>
                                                            {{ $event->organization }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-10">
                                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                                            <i class="fa-solid fa-calendar-xmark text-2xl text-slate-300"></i>
                                        </div>
                                        <p class="text-slate-400 text-sm">ไม่มีกิจกรรมในวันนี้</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="lg:col-span-2 text-center py-20">
                            <div class="w-24 h-24 rounded-3xl bg-slate-100 flex items-center justify-center mx-auto mb-6">
                                <i class="fa-solid fa-users-slash text-4xl text-slate-300"></i>
                            </div>
                            <h3 class="text-2xl font-semibold text-slate-700 mb-2">ยังไม่มีข้อมูลผู้บริหาร</h3>
                            <p class="text-slate-500">กรุณาเพิ่มข้อมูลผู้บริหารในระบบ</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-200 py-8 bg-white/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1.5 shadow-sm">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <p class="text-slate-800 font-medium">ระบบปฏิทินการปฏิบัติงาน</p>
                            <p class="text-slate-400 text-sm">© {{ date('Y') }} สงวนลิขสิทธิ์</p>
                        </div>
                    </div>
                    <div class="text-slate-400 text-sm flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i>
                        อัพเดท: {{ now()->locale('th')->translatedFormat('j M Y, H:i') }} น.
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Real-time Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const clockElement = document.getElementById('realTimeClock');
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }
        
        // Update immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
