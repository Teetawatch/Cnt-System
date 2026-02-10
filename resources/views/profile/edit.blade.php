<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-user-gear text-white text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 dark:text-white leading-tight tracking-tight">
                    {{ __('จัดการบัญชีผู้ใช้') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">แก้ไขข้อมูลส่วนตัวและรหัสผ่าน</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="glass-card p-4 sm:p-8 animate-fade-in-up">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="glass-card p-4 sm:p-8 animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="glass-card p-4 sm:p-8 animate-fade-in-up" style="animation-delay: 200ms;">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
