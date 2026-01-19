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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800">
            <!-- Logo Section -->
            <div class="mb-6 text-center">
                <a href="/" class="flex flex-col items-center gap-3">
                    <div class="w-20 h-20 bg-white rounded-2xl shadow-xl flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-4xl text-primary-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white">ปฏิทินการปฏิบัติงาน</h1>
                    <p class="text-primary-200 text-sm">ระบบจัดการตารางงานผู้บริหาร</p>
                </a>
            </div>

            <!-- Form Card -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-2xl animate-fade-in">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-primary-200 text-sm">
                <p>&copy; {{ date('Y') }} ระบบปฏิทินการปฏิบัติงาน</p>
            </div>
        </div>
    </body>
</html>

