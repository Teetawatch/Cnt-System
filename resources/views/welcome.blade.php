<!DOCTYPE html>
<html lang="th" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="ระบบปฏิทินการปฏิบัติงานผู้บริหาร">

    <title>ปฏิทินการปฏิบัติงานผู้บริหาร</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }

        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #f3f4f6;
            color: #1e293b;
        }

        /* Stunning Background Mesh */
        .premium-bg {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(245, 158, 11, 0.15) 0px, transparent 50%);
            background-attachment: fixed;
        }

        .mesh-decoration {
            position: absolute;
            background: linear-gradient(120deg, #d97706, #3b82f6);
            filter: blur(80px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 50%;
        }

        /* Glassmorphism Pro - Max Skill */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }

        /* Premium Text Gradients */
        .text-gradient-gold {
            background: linear-gradient(135deg, #b45309 0%, #d97706 50%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-blue {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal-up {
            animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Shine Effect */
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        .shine-effect::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: skewX(-25deg);
            transition: 0.5s;
        }
        .shine-effect:hover::after {
            animation: shine 0.75s;
        }
        @keyframes shine {
            100% { left: 125%; }
        }

        /* Active State Ring */
        .active-ring {
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #d97706;
        }
    </style>
</head>

<body class="premium-bg min-h-screen relative overflow-x-hidden selection:bg-amber-100 selection:text-amber-900 flex flex-col">
    
    <!-- Background Accents -->
    <div class="mesh-decoration top-0 left-0 w-[500px] h-[500px] -translate-x-1/3 -translate-y-1/3"></div>
    <div class="mesh-decoration bottom-0 right-0 w-[600px] h-[600px] translate-x-1/3 translate-y-1/3 !bg-blue-500"></div>

    @php
        $selectedDate = request('date') ? \Carbon\Carbon::parse(request('date')) : now();
        $selectedStaffId = request('staff_id');

        $thaiDate = $selectedDate->locale('th')->translatedFormat('l');
        $thaiDay = $selectedDate->day;
        $thaiMonth = $selectedDate->locale('th')->translatedFormat('F');
        $thaiYear = $selectedDate->year + 543;

        $staffQuery = \App\Models\Staff::active()->ordered()->with([
            'calendarEvents' => function ($query) use ($selectedDate) {
                $query->forDate($selectedDate)->orderByTime();
            }
        ]);

        if ($selectedStaffId) {
            $staffQuery->where('id', $selectedStaffId);
        }

        $staffList = $staffQuery->get();
        $allStaff = \App\Models\Staff::active()->ordered()->get();
        $totalEvents = \App\Models\CalendarEvent::forDate($selectedDate)->count();
    @endphp

    <!-- Navbar -->
    <nav class="fixed top-0 w-full z-50 px-4 py-4 md:px-6">
        <div class="max-w-7xl mx-auto">
            <div class="glass-panel rounded-2xl px-6 py-3 flex items-center justify-between">
                <!-- Branding -->
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="w-10 h-10 md:w-11 md:h-11 rounded-xl flex items-center justify-center shadow-lg text-white">
                         <!-- If you have a logo image, use it here, otherwise use default icon -->
                         <img src="{{ asset('images/logo.png') }}" class="w-8 h-8 object-contain" alt="Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                         <i class="fa-solid fa-calendar-check text-xl hidden"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-slate-800 font-bold text-lg leading-tight tracking-tight">ปฏิทินผู้บริหาร</h1>
                        <p class="text-slate-500 text-xs font-medium">Executive Calendar System</p>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2 md:gap-3">
                    <a href="{{ route('calendar.pdf', ['date' => $selectedDate->format('Y-m-d')]) }}"
                        target="_blank"
                        class="hidden md:flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 hover:bg-white text-slate-600 hover:text-red-500 text-sm font-medium transition-all duration-300 border border-transparent hover:border-red-100 hover:shadow-lg hover:shadow-red-500/10 shine-effect">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span>PDF</span>
                    </a>

                    @auth
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl transition-all duration-300 shadow-xl shadow-slate-900/20 shine-effect">
                            <i class="fa-solid fa-gear text-amber-400"></i>
                            <span class="hidden sm:inline">จัดการข้อมูล</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-medium rounded-xl transition-all duration-300 shadow-xl shadow-slate-900/20 shine-effect">
                            <i class="fa-solid fa-arrow-right-to-bracket text-amber-400"></i>
                            <span class="hidden sm:inline">เข้าสู่ระบบ</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-12 flex-grow w-full">

        <!-- Hero Section: Date & Filters -->
        <section class="grid lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Date Display & Clock -->
            <div class="lg:col-span-5 flex flex-col items-center lg:items-start text-center lg:text-left space-y-6 reveal-up">
                <!-- Date Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-50 border border-amber-100 text-amber-700 text-sm font-medium animate-float">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    {{ $selectedDate->isToday() ? 'วันนี้' : 'วันที่เลือก' }}
                </div>

                <!-- Big Date -->
                <div>
                    <h2 class="text-slate-500 text-xl md:text-2xl font-light">วัน{{ $thaiDate }}ที่</h2>
                    <div class="flex items-baseline gap-2 lg:block">
                        <h1 class="text-8xl md:text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-br from-slate-900 via-slate-700 to-slate-900 leading-none tracking-tighter" style="font-family: 'Outfit', sans-serif;">
                            {{ $thaiDay }}
                        </h1>
                        <p class="text-3xl md:text-4xl text-amber-600 font-medium lg:-mt-2">{{ $thaiMonth }} {{ $thaiYear }}</p>
                    </div>
                </div>

                <!-- Realtime Clock Card -->
                <div class="w-full max-w-xs glass-card rounded-2xl p-5 border-l-4 border-amber-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold">เวลาปัจจุบัน</p>
                        <div class="flex items-baseline gap-1">
                            <span id="realTimeClock" class="text-3xl font-bold text-slate-800 tabular-nums"></span>
                            <span class="text-sm text-slate-400">น.</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center">
                        <i class="fa-regular fa-clock text-xl text-amber-500"></i>
                    </div>
                </div>
            </div>

            <!-- Right: Filters & Quick Select -->
            <div class="lg:col-span-7 space-y-6 reveal-up delay-100">
                
                <!-- Filter Card -->
                <div class="glass-panel rounded-3xl p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                    
                    <form id="filterForm" method="GET" action="{{ route('home') }}" class="relative z-10 space-y-6">
                        <div class="grid md:grid-cols-2 gap-5 z-20">
                            <!-- Date Input -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">
                                    <i class="fa-regular fa-calendar-days text-amber-500 mr-1.5"></i> วันที่ต้องการ
                                </label>
                                <div class="relative transition-all duration-300 group-hover:-translate-y-1">
                                    <input type="date" name="date" 
                                        value="{{ $selectedDate->format('Y-m-d') }}"
                                        class="w-full pl-4 pr-4 py-3.5 rounded-xl bg-white border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-sm transition-all"
                                        onchange="document.getElementById('filterForm').submit()">
                                </div>
                            </div>

                            <!-- Staff Select - improved visual -->
                            <div class="group">
                                <label class="block text-sm font-semibold text-slate-700 mb-2 ml-1">
                                    <i class="fa-regular fa-user text-blue-500 mr-1.5"></i> เลือกผู้บริหาร
                                </label>
                                <div class="relative transition-all duration-300 group-hover:-translate-y-1">
                                    <select name="staff_id" 
                                        class="w-full pl-4 pr-10 py-3.5 rounded-xl bg-white border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-sm transition-all appearance-none cursor-pointer"
                                        onchange="document.getElementById('filterForm').submit()">
                                        <option value="">แสดงทั้งหมด</option>
                                        @foreach($allStaff as $person)
                                            <option value="{{ $person->id }}" {{ $selectedStaffId == $person->id ? 'selected' : '' }}>
                                                {{ $person->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reset & Stats -->
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-100">
                            <div class="flex gap-3">
                                <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-calendar-day mr-1"></i> วันนี้
                                </a>
                                @if(request('date') || request('staff_id'))
                                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium transition-colors">
                                        <i class="fa-solid fa-xmark mr-1"></i> ล้างค่า
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Mini Stat -->
                            <div class="flex items-center gap-4 text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                    {{ $staffList->count() }} ท่าน
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    {{ $totalEvents }} ภารกิจ
                                </span>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Executive Avatars Bar -->
                <div class="glass-panel rounded-2xl p-4 flex flex-wrap gap-3 items-center justify-center md:justify-start">
                    @foreach($allStaff as $person)
                        @php $hasEvent = $person->calendarEvents()->forDate($selectedDate)->exists(); @endphp
                        <a href="{{ route('home', ['date' => $selectedDate->format('Y-m-d'), 'staff_id' => $person->id]) }}" 
                           class="relative group transition-transform hover:scale-110 duration-300 {{ $selectedStaffId == $person->id ? 'scale-110' : 'opacity-80 hover:opacity-100' }}"
                           title="{{ $person->name }}">
                            <div class="w-12 h-12 rounded-full border-2 {{ $selectedStaffId == $person->id ? 'border-amber-500 shadow-lg shadow-amber-500/20' : 'border-white' }} overflow-hidden">
                                <img src="{{ $person->photo_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover">
                            </div>
                            <!-- Status Dot -->
                            @if($hasEvent)
                            <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-amber-500 border-2 border-white rounded-full"></div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Dynamic Announcement Slider (Modernized) -->
        <section class="reveal-up delay-200">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-slate-900 to-slate-800 shadow-2xl text-white">
                <!-- Decorative Blobs -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20 -mr-16 -mt-16 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500 rounded-full mix-blend-overlay filter blur-3xl opacity-20 -ml-16 -mb-16"></div>

                <div class="relative p-6 md:p-10 flex flex-col md:flex-row items-center gap-8">
                    <!-- Icon Box -->
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-inner">
                            <i class="fa-solid fa-bullhorn text-2xl text-amber-400"></i>
                        </div>
                    </div>
                    
                    <!-- Content Slider -->
                    <div class="flex-1 w-full overflow-hidden" id="slider-container">
                        @php
                            $announcements = [
                                ['title' => 'ยินดีต้อนรับสู่ระบบปฏิทินผู้บริหาร', 'desc' => 'ติดตามภารกิจ ตารางงาน และกิจกรรมสำคัญได้แบบ Real-time', 'icon' => 'fa-door-open'],
                                ['title' => 'ระบบรายงานรูปแบบใหม่', 'desc' => 'สามารถพิมพ์รายงาน PDF สรุปประจำวันได้ทันทีจากเมนูด้านบน', 'icon' => 'fa-print'],
                                ['title' => 'การแสดงผลรองรับมือถือ', 'desc' => 'ตรวจสอบตารางงานได้ทุกที่ ทุกเวลา ผ่านสมาร์ทโฟนและแท็บเล็ต', 'icon' => 'fa-mobile-screen'],
                            ];
                        @endphp
                        
                        <div class="relative h-24 md:h-16">
                             @foreach($announcements as $index => $ann)
                                <div class="announcement-slide absolute top-0 left-0 w-full transition-all duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8' }}" data-index="{{ $index }}">
                                    <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-300 mb-1">
                                        {{ $ann['title'] }}
                                    </h3>
                                    <p class="text-slate-400 font-light">{{ $ann['desc'] }}</p>
                                </div>
                             @endforeach
                        </div>
                    </div>
                    
                    <!-- Dots -->
                    <div class="flex gap-2">
                        @foreach($announcements as $index => $ann)
                            <button onclick="switchSlide({{ $index }})" class="dot-indicator w-2 h-2 rounded-full transition-all {{ $index === 0 ? 'bg-amber-400 w-6' : 'bg-white/20 hover:bg-white/40' }}" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content Grid: Staff & Events -->
        <section class="max-w-none">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-6 lg:gap-8">
                @forelse($staffList as $staff)
                    <div class="glass-card rounded-3xl p-0 overflow-hidden group reveal-up" style="animation-delay: {{ $loop->index * 100 }}ms">
                        
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-slate-50 to-white border-b border-slate-100 p-6 md:p-8 relative">
                            <!-- Background Pattern -->
                             <div class="absolute top-0 right-0 p-6 opacity-5 md:opacity-10 transition-opacity group-hover:opacity-20 pointer-events-none">
                                <i class="fa-solid fa-user-tie text-9xl text-slate-800"></i>
                             </div>
                            
                            <div class="relative flex items-center gap-6 z-10">
                                <!-- Avatar -->
                                <div class="relative flex-shrink-0">
                                    <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-2xl border-4 border-white group-hover:border-amber-400 transition-colors duration-300">
                                        @if($staff->photo)
                                            <img src="{{ $staff->photo_url }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-400">
                                                <i class="fa-solid fa-user text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-lg border-2 border-white">
                                        {{ $staff->calendarEvents->count() }}
                                    </div>
                                </div>

                                <!-- Info -->
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-800 group-hover:text-amber-600 transition-colors">{{ $staff->name }}</h3>
                                    <p class="text-amber-600 font-medium text-sm md:text-base mt-0.5">{{ $staff->position }}</p>
                                    @if($staff->department)
                                        <div class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-md bg-slate-100 text-slate-500 text-xs font-medium">
                                            <i class="fa-solid fa-building text-slate-400"></i>
                                            {{ $staff->department }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Body: Events -->
                        <div class="p-6 md:p-8 bg-white/40">
                            @if($staff->calendarEvents->count() > 0)
                                <div class="relative space-y-0">
                                    <!-- Vertical Line -->
                                    <div class="absolute left-3.5 top-2 bottom-6 w-0.5 bg-slate-200/70 rounded-full"></div>

                                    @foreach($staff->calendarEvents as $event)
                                        <div class="relative pl-10 pb-6 last:pb-0 group/event">
                                            <!-- Timestamp Bullet -->
                                            <div class="absolute left-0 top-1.5 w-7 h-7 rounded-full bg-white border-2 border-amber-500 flex items-center justify-center z-10 shadow-sm group-hover/event:scale-110 transition-transform">
                                                <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div>
                                            </div>

                                            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 hover:border-amber-200 hover:shadow-md transition-all duration-300 group-hover/event:translate-x-1">
                                                <div class="flex flex-wrap items-center justify-between gap-y-1 mb-2">
                                                    <span class="inline-flex items-center gap-1.5 text-sm font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">
                                                        <i class="fa-regular fa-clock text-xs"></i>
                                                        {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} 
                                                        @if($event->end_time) - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }} @endif
                                                    </span>
                                                    @if($event->location)
                                                        <span class="text-xs text-slate-500 flex items-center gap-1 max-w-[150px] truncate">
                                                            <i class="fa-solid fa-location-dot text-slate-400"></i> 
                                                            {{ $event->location }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                <h4 class="text-slate-800 font-semibold leading-relaxed">{{ $event->title }}</h4>
                                                
                                                @if($event->organization)
                                                    <p class="mt-2 text-sm text-slate-500 flex items-start gap-2">
                                                        <i class="fa-regular fa-building mt-0.5 text-slate-400"></i>
                                                        <span class="line-clamp-1">{{ $event->organization }}</span>
                                                    </p>
                                                @endif

                                                @if($event->description)
                                                    <div class="mt-3 pt-3 border-t border-slate-100">
                                                        <p class="text-sm text-amber-700 bg-amber-50 px-3 py-2 rounded-lg flex items-start gap-2">
                                                            <i class="fa-solid fa-note-sticky mt-1 text-amber-500 text-xs"></i>
                                                            <span class="font-medium">หมายเหตุ: {{ $event->description }}</span>
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 text-center opacity-60">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                                        <i class="fa-solid fa-calendar-minus text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">ไม่มีภารกิจในวันนี้</p>
                                    <p class="text-xs text-slate-400 mt-1">ว่างเว้นจากการปฏิบัติราชการ</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="inline-block p-8 rounded-full bg-white shadow-xl mb-6">
                            <i class="fa-solid fa-user-slash text-4xl text-slate-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700">ไม่พบข้อมูล</h3>
                        <p class="text-slate-500 mt-2">กรุณาเลือกเงื่อนไขการค้นหาใหม่อีกครั้ง</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>

    <!-- Refined Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" class="w-12 h-12 object-contain grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-500" alt="Logo">
                    <div class="text-center md:text-left">
                        <p class="text-slate-900 font-bold">ระบบปฏิทินการปฏิบัติงานผู้บริหาร</p>
                        <p class="text-slate-500 text-sm">© {{ date('Y') }} สงวนลิขสิทธิ์</p>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right hidden md:block">
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Last Update</p>
                        <p class="text-sm text-slate-600 font-mono">{{ now()->format('d M Y H:i:s') }}</p>
                    </div>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                        <i class="fa-solid fa-arrow-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Logic Scripts -->
    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('realTimeClock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Slider Logic
        let slideIndex = 0;
        const slides = document.querySelectorAll('.announcement-slide');
        const dots = document.querySelectorAll('.dot-indicator');
        
        function switchSlide(n) {
            slideIndex = n;
            if (slideIndex >= slides.length) slideIndex = 0;
            if (slideIndex < 0) slideIndex = slides.length - 1;

            // Reset styles
            slides.forEach(s => {
                s.classList.remove('opacity-100', 'translate-y-0');
                s.classList.add('opacity-0', 'translate-y-8');
            });
            dots.forEach(d => {
                d.classList.remove('bg-amber-400', 'w-6');
                d.classList.add('bg-white/20', 'w-2');
            });

            // Active style
            slides[slideIndex].classList.remove('opacity-0', 'translate-y-8');
            slides[slideIndex].classList.add('opacity-100', 'translate-y-0');
            
            dots[slideIndex].classList.remove('bg-white/20', 'w-2');
            dots[slideIndex].classList.add('bg-amber-400', 'w-6');
        }

        // Auto play
        setInterval(() => {
            switchSlide(slideIndex + 1);
        }, 5000);
    </script>
</body>
</html>