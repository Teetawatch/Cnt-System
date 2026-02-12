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
                background-color: #4f46e5;
                background-image: 
                    radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 0% 100%, hsla(339,49%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 50% 100%, hsla(225,39%,30%,1) 0, transparent 50%), 
                    radial-gradient(at 100% 100%, hsla(253,16%,7%,1) 0, transparent 50%);
            }

            .glass-morphism {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
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
            <div class="absolute top-0 -left-4 w-72 h-72 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

            <div class="w-full max-w-6xl flex flex-col lg:flex-row items-center justify-between gap-12 z-10">
                <!-- Left Content: Branding items -->
                <div class="flex-1 text-center lg:text-left text-white space-y-6">
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/10 border border-white/20 backdrop-blur-sm tracking-wide text-xs font-semibold text-indigo-200 uppercase animate-fade-in">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                        </span>
                        Enterprise Workflow Systems
                    </div>
                    
                    <h1 class="text-4xl lg:text-6xl font-bold leading-tight group">
                        จัดตารางงาน <br/>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-300 to-violet-300">ให้สมบูรณ์แบบ</span>
                    </h1>
                    
                    <p class="text-slate-400 text-lg max-w-md mx-auto lg:mx-0 font-light">
                        สัมผัสประสบการณ์การจัดการปฏิทินรูปแบบใหม่ <br class="hidden lg:block"/> 
                        รวดเร็ว แม่นยำ และทรงพลังในทุกคลิก
                    </p>

                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6 pt-4">
                        <div class="flex -space-x-3">
                            @for($i=1; $i<=4; $i++)
                                <div class="w-10 h-10 rounded-full border-2 border-slate-900 bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-400">
                                    {{ chr(64 + $i) }}
                                </div>
                            @endfor
                        </div>
                        <div class="text-sm text-slate-500 font-medium">
                            เข้าร่วมกับผู้ดูแลระบบกว่า <span class="text-indigo-400">100+</span> ท่าน
                        </div>
                    </div>
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
