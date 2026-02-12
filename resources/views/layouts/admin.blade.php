<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>ปฏิทินการปฏิบัติงาน</title>

        <!-- Google Fonts: Kanit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #e2e8f0; /* slate-200 */
                border-radius: 9999px;
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-600 bg-slate-50 relative selection:bg-indigo-100 selection:text-indigo-900" x-data="{ sidebarOpen: false }">
        
        <!-- Background Pattern -->
        <div class="fixed inset-0 z-[-1] h-full w-full bg-slate-50 bg-[linear-gradient(to_right,#f1f5f9_1px,transparent_1px),linear-gradient(to_bottom,#f1f5f9_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>

        <div class="flex min-h-screen">
            <!-- Sidebar for Desktop -->
            <aside class="hidden lg:flex lg:flex-shrink-0 lg:w-72 flex-col bg-white border-r border-slate-200 shadow-[20px_0_30px_rgba(0,0,0,0.02)] fixed inset-y-0 z-50">
                <!-- Branding -->
                <div class="h-20 flex items-center px-8 border-b border-slate-100 bg-white">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-600/20 flex items-center justify-center transition-transform duration-500 group-hover:scale-110">
                            <i class="fa-solid fa-calendar-check text-white text-base"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-lg tracking-tight text-slate-800 leading-none">Admin Panel</span>
                            <span class="text-[10px] text-slate-400 font-bold tracking-widest uppercase mt-1">CntSystem</span>
                        </div>
                    </a>
                </div>

                <div class="flex-grow flex flex-col pt-4 overflow-y-auto custom-scrollbar bg-white">
                    <nav class="flex flex-col px-3 space-y-1">
                        <!-- Navigation Groups -->
                        <div class="px-4 py-3 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em]">Navigation</div>
                        
                        <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group hover:bg-rose-50 text-slate-500 hover:text-rose-600 border border-slate-50 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-white transition-colors">
                                <i class="fa-solid fa-arrow-left text-xs"></i>
                            </div>
                            <span class="text-sm font-medium">หน้าหลัก (ผู้ใช้)</span>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white shadow-sm' : 'bg-transparent' }}">
                                <i class="fa-solid fa-chart-line text-sm"></i>
                            </div>
                            <span class="text-sm {{ request()->routeIs('admin.dashboard') ? 'font-bold' : 'font-medium' }}">{{ __('แดชบอร์ด') }}</span>
                        </a>

                        <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Management</div>
                        
                        <a href="{{ route('staff.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('staff.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('staff.*') ? 'bg-white shadow-sm' : 'bg-transparent' }}">
                                <i class="fa-solid fa-user-gear text-sm"></i>
                            </div>
                            <span class="text-sm {{ request()->routeIs('staff.*') ? 'font-bold' : 'font-medium' }}">{{ __('จัดการผู้ปฏิบัติ') }}</span>
                        </a>

                        <a href="{{ route('calendar.manage') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('calendar.manage') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('calendar.manage') ? 'bg-white shadow-sm' : 'bg-transparent' }}">
                                <i class="fa-solid fa-calendar-plus text-sm"></i>
                            </div>
                            <span class="text-sm {{ request()->routeIs('calendar.manage') ? 'font-bold' : 'font-medium' }}">{{ __('จัดการกิจกรรม') }}</span>
                        </a>

                        <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Personal</div>

                        <a href="{{ route('calendar.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('calendar.index') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('calendar.index') ? 'bg-white shadow-sm' : 'bg-transparent' }}">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                            </div>
                            <span class="text-sm {{ request()->routeIs('calendar.index') ? 'font-bold' : 'font-medium' }}">{{ __('ดูปฏิทินรวม') }}</span>
                        </a>
                    </nav>

                </div>

                <!-- User Profile Pin at Bottom -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/30">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border border-indigo-200 shadow-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold tracking-tighter">Admin User</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-all">
                                <i class="fa-solid fa-right-from-bracket"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 lg:hidden bg-slate-900/50 backdrop-blur-sm" @click="sidebarOpen = false"></div>

            <!-- Mobile Sidebar Content -->
            <aside x-show="sidebarOpen" 
                   x-transition:enter="transition ease-in-out duration-300 transform" 
                   x-transition:enter-start="-translate-x-full" 
                   x-transition:enter-end="translate-x-0" 
                   x-transition:leave="transition ease-in-out duration-300 transform" 
                   x-transition:leave-start="translate-x-0" 
                   x-transition:leave-end="-translate-x-full" 
                   class="fixed inset-y-0 left-0 w-72 glass-sidebar z-50 lg:hidden flex flex-col shadow-2xl">
                
                <div class="h-20 flex items-center justify-between px-6 border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-tr from-indigo-500 to-violet-600 rounded-lg flex items-center justify-center text-white">
                            <i class="fa-solid fa-calendar-check text-sm"></i>
                        </div>
                        <span class="font-bold text-lg text-white">Admin Panel</span>
                    </div>
                    <button @click="sidebarOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto pt-4 bg-white custom-scrollbar">
                    <nav class="flex flex-col px-3 space-y-1">
                        <div class="px-4 py-2 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em]">Navigation</div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center"></i>
                            <span class="text-sm font-medium">{{ __('แดชบอร์ด') }}</span>
                        </a>
                        
                        <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Management</div>
                        <a href="{{ route('staff.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('staff.*') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i class="fa-solid fa-user-gear w-5 text-center"></i>
                            <span class="text-sm font-medium">{{ __('จัดการผู้ปฏิบัติ') }}</span>
                        </a>
                        <a href="{{ route('calendar.manage') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('calendar.manage') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i class="fa-solid fa-calendar-plus w-5 text-center"></i>
                            <span class="text-sm font-medium">{{ __('จัดการกิจกรรม') }}</span>
                        </a>
                        
                        <div class="px-4 py-4 text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] mt-2">Personal</div>
                        <a href="{{ route('calendar.index') }}" 
                           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('calendar.index') ? 'bg-indigo-50 text-indigo-700 shadow-sm' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                            <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                            <span class="text-sm font-medium">{{ __('ดูปฏิทินรวม') }}</span>
                        </a>
                    </nav>
                </div>

                <!-- Mobile Profile Info -->
                <div class="p-6 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold shadow-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="font-bold text-white text-sm truncate leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tighter">Administrator</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex flex-col flex-1 lg:pl-72 w-full min-h-screen">
                <!-- Mobile Topbar -->
                <header class="fixed top-0 w-full lg:hidden bg-white/80 backdrop-blur-md border-b border-slate-200 z-30 px-4 h-16 flex items-center justify-between shadow-sm">
                    <button @click="sidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white text-xs">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center overflow-hidden">
                        <span class="text-xs font-bold text-slate-500">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </header>

                <!-- Page Header (Shared between layouts but integrated into sidebar layout better) -->
                @if (isset($header))
                    <div class="mt-16 lg:mt-0 pt-8 px-6 lg:px-10 pb-4">
                        <div class="max-w-[1600px] mx-auto flex flex-col md:flex-row md:items-center justify-between gap-4">
                            {{ $header }}
                        </div>
                    </div>
                @endif

                <!-- Content Body -->
                <main class="flex-grow px-6 lg:px-10 py-6">
                    <div class="max-w-[1600px] mx-auto animate-fade-in-up">
                        {{ $slot }}
                    </div>
                </main>

                <!-- Admin Footer -->
                <footer class="px-6 lg:px-10 py-6 border-t border-slate-200 bg-white/50 backdrop-blur-sm mt-8">
                    <div class="max-w-[1600px] mx-auto text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-4 text-slate-400 text-xs font-medium">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        <div class="flex items-center gap-4">
                            <a href="#" class="hover:text-indigo-600">Privacy Policy</a>
                            <a href="#" class="hover:text-indigo-600">Terms of Service</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        @php
            $appUrl = config('app.url');
            $parsedPath = parse_url($appUrl, PHP_URL_PATH);
            $subfolder = $parsedPath ? rtrim($parsedPath, '/') : '';
        @endphp

        <!-- Scripts & Livewire Support -->
        <script>
            window.livewireScriptConfig = {
                "csrf": "{{ csrf_token() }}",
                "uri": "{{ $subfolder }}/livewire/update",
                "progressBar": true,
                "nonce": ""
            };
        </script>
        <script src="{{ asset('vendor/livewire/livewire.min.js') }}" 
                data-csrf="{{ csrf_token() }}" 
                data-update-uri="{{ $subfolder }}/livewire/update" 
                data-navigate-once="true"
                onload="if(window.Livewire && !Livewire.started){ Livewire.start(); }">
        </script>

        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
            @endif

            @if(session('error'))
                Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
            @endif
        </script>
    </body>
</html>
