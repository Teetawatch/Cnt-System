<x-guest-layout>
    <div class="space-y-8 animate-fade-in-up">
        <!-- Header -->
        <div class="text-center space-y-3">
            <div class="inline-flex w-16 h-16 bg-white rounded-2xl shadow-xl shadow-slate-200/50 items-center justify-center mb-2 transform transition-transform hover:rotate-6 border border-slate-100">
                <i class="fa-solid fa-calendar-check text-2xl text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-800">ยินดีต้อนรับ</h2>
            <p class="text-slate-500 font-medium tracking-wide text-sm">ระบุข้อมูลเพื่อเข้าสู่ระบบการจัดการ</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2 group">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-indigo-600">
                    อีเมลผู้ใช้งาน
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-600">
                        <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                    </div>
                    <input id="email" 
                           class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none shadow-sm" 
                           type="email" 
                           name="email" 
                           :value="old('email')" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="name@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500 text-xs font-medium" />
            </div>

            <!-- Password -->
            <div class="space-y-2 group">
                <div class="flex items-center justify-between px-1">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest transition-colors group-focus-within:text-indigo-600">
                        รหัสผ่าน
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-bold text-indigo-500 hover:text-indigo-600 transition-colors" href="{{ route('password.request') }}">
                            ลืมรหัสผ่าน?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-600">
                        <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                    </div>
                    <input id="password" 
                           class="block w-full pl-12 pr-12 py-4 bg-white border border-slate-200 rounded-2xl text-slate-800 placeholder-slate-400 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none shadow-sm"
                           type="password"
                           name="password"
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500 text-xs font-medium" />
            </div>

            <!-- Remember Me & Actions -->
            <div class="flex items-center justify-between px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <div class="relative">
                        <input id="remember_me" type="checkbox" name="remember" class="sr-only">
                        <div class="w-10 h-6 bg-slate-100 rounded-full shadow-inner transition-colors group-hover:bg-slate-200"></div>
                        <div class="dot absolute left-1 top-1 bg-slate-300 w-4 h-4 rounded-full transition transform group-hover:bg-slate-400"></div>
                    </div>
                    <span class="ml-3 text-sm text-slate-500 font-bold group-hover:text-slate-600 transition-colors">จำฉันไว้ในระบบ</span>
                </label>
            </div>

            <style>
                input:checked ~ .dot {
                    transform: translateX(100%);
                    background-color: #ffffff !important;
                }
                input:checked ~ div {
                    background-color: #6366f1 !important;
                }
            </style>

            <button type="submit" class="relative w-full group overflow-hidden bg-indigo-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-600/20 transition-all hover:bg-indigo-700 hover:scale-[1.01] active:scale-[0.99]">
                <span class="flex items-center justify-center gap-2">
                    เข้าสู่ระบบ
                    <i class="fa-solid fa-right-to-bracket text-xs transition-transform group-hover:translate-x-1"></i>
                </span>
            </button>
        </form>

        <!-- Bottom Info -->
        <div class="pt-6 border-t border-slate-100 text-center">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                Cnt-System Enterprise
            </p>
        </div>
    </div>
</x-guest-layout>
