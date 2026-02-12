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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Livewire Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-600 bg-slate-50 relative">
        <!-- Background Pattern -->
        <div class="fixed inset-0 z-[-1] h-full w-full bg-slate-50 bg-[linear-gradient(to_right,#f1f5f9_1px,transparent_1px),linear-gradient(to_bottom,#f1f5f9_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_110%)]"></div>
        
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white/70 backdrop-blur-md border-b border-slate-200/60 sticky top-16 z-30 transition-all duration-300">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-grow py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 animate-fade-in-up">
                    {{ $slot }}
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white/50 backdrop-blur-sm border-t border-slate-100 py-6 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-400 text-sm">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </footer>
        </div>

        @php
            // Extract subfolder dynamically from APP_URL for Livewire update URI
            $appUrl = config('app.url');
            $parsedPath = parse_url($appUrl, PHP_URL_PATH);
            $subfolder = $parsedPath ? rtrim($parsedPath, '/') : '';
        @endphp

        <!-- Livewire Scripts (Published static assets for subfolder support) -->
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

        <!-- Global SweetAlert2 Toast -->
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif
        </script>
    </body>
</html>

