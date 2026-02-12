<!DOCTYPE html>
<html lang="th" class="scroll-smooth">
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .mesh-bg {
                background-color: #f8fafc; /* slate-50 */
                background-image: 
                    radial-gradient(at 0% 0%, hsla(220,100%,97%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(240,100%,98%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(260,100%,97%,1) 0, transparent 50%), 
                    radial-gradient(at 0% 100%, hsla(210,100%,96%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 100%, hsla(230,100%,97%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 100%, hsla(250,100%,98%,1) 0, transparent 50%);
            }

            .glass-morphism {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.8);
                box-shadow: 
                    0 4px 6px -1px rgba(0, 0, 0, 0.05),
                    0 10px 15px -3px rgba(0, 0, 0, 0.1),
                    inset 0 0 0 1px rgba(255, 255, 255, 0.5);
            }

            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite alternate;
            }
            .animation-delay-2000 { animation-delay: 2s; }
            .animation-delay-4000 { animation-delay: 4s; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-hidden">
        <div class="min-h-screen relative flex items-center justify-center p-4 mesh-bg">
            <!-- Animated Blobs -->
            <div class="absolute top-0 -left-4 w-96 h-96 bg-indigo-200/40 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-96 h-96 bg-purple-200/40 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-12 left-20 w-96 h-96 bg-rose-200/40 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

            <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-between gap-12 z-10">
                <!-- Left Content: Branding items -->
                <div class="flex-1 text-center lg:text-left text-slate-800 space-y-6">
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight group text-slate-900">
                        จัดตารางงาน <br/>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600">ให้สมบูรณ์แบบ</span>
                    </h1>
                    
                    <p class="text-slate-500 text-lg max-w-md mx-auto lg:mx-0 font-medium">
                        สัมผัสประสบการณ์การจัดการปฏิทินรูปแบบใหม่ <br class="hidden lg:block"/> 
                        รวดเร็ว แม่นยำ และทรงพลังในทุกคลิก
                    </p>

                </div>

                <!-- Right Content: Login Form -->
                <div class="w-full sm:max-w-md">
                    <div class="glass-morphism overflow-hidden rounded-[2.5rem] shadow-2xl p-8 lg:p-10 transition-all duration-500 hover:shadow-indigo-500/10 border-white/10">
                        {{ $slot }}
                    </div>
                    
                    <div class="mt-8 text-center text-slate-500 text-sm">
                        <p>&copy; {{ date('Y') }} CntSystem. Built for excellence.</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
