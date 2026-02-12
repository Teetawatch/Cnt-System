<x-guest-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-16 h-16 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl shadow-xl shadow-indigo-500/20 items-center justify-center mb-4 transform transition-transform hover:rotate-6">
                <i class="fa-solid fa-calendar-check text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold tracking-tight text-white italic">ยินดีต้อนรับ</h2>
            <p class="text-slate-400 font-medium tracking-wide text-sm uppercase">ระบุข้อมูลเพื่อเข้าสู่ระบบ</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2 group">
                <label for="email" class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1 transition-colors group-focus-within:text-indigo-400">
                    อีเมลผู้ใช้งาน
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-400">
                        <i class="fa-solid fa-envelope text-slate-500"></i>
                    </div>
                    <input id="email" 
                           class="block w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-600 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 focus:bg-white/10 transition-all outline-none" 
                           type="email" 
                           name="email" 
                           :value="old('email')" 
                           required 
                           autofocus 
                           autocomplete="username" 
                           placeholder="yourname@example.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400 text-xs" />
            </div>

            <!-- Password -->
            <div class="space-y-2 group">
                <div class="flex items-center justify-between px-1">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-widest transition-colors group-focus-within:text-indigo-400">
                        รหัสผ่าน
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-xs font-medium text-slate-500 hover:text-indigo-400 transition-colors" href="{{ route('password.request') }}">
                            ลืมรหัสผ่าน?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-400">
                        <i class="fa-solid fa-lock text-slate-500"></i>
                    </div>
                    <input id="password" 
                           class="block w-full pl-12 pr-12 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-600 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 focus:bg-white/10 transition-all outline-none"
                           type="password"
                           name="password"
                           required 
                           autocomplete="current-password" 
                           placeholder="••••••••••••" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400 text-xs" />
            </div>

            <!-- Remember Me & Actions -->
            <div class="flex items-center justify-between px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <div class="relative">
                        <input id="remember_me" type="checkbox" name="remember" class="sr-only">
                        <div class="w-10 h-6 bg-white/10 rounded-full shadow-inner transition-colors group-hover:bg-white/20"></div>
                        <div class="dot absolute left-1 top-1 bg-slate-400 w-4 h-4 rounded-full transition transform group-hover:bg-indigo-400"></div>
                    </div>
                    <span class="ml-3 text-sm text-slate-500 font-medium group-hover:text-slate-400 transition-colors">จำฉันไว้</span>
                </label>
            </div>

            <style>
                input:checked ~ .dot {
                    transform: translateX(100%);
                    background-color: #6366f1 !important;
                }
                input:checked ~ div {
                    background-color: rgba(99, 102, 241, 0.2) !important;
                }
            </style>

            <button type="submit" class="relative w-full group overflow-hidden bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-600/20 transition-all hover:scale-[1.02] active:scale-[0.98] hover:shadow-indigo-600/40">
                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                <span class="flex items-center justify-center gap-2">
                    เข้าสู่ระบบ
                    <i class="fa-solid fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                </span>
            </button>
        </form>

        <!-- Social/Bottom Info -->
        <div class="pt-6 border-t border-white/5 text-center">
            <p class="text-slate-500 text-xs font-medium">
                พบปัญหาในการเข้าใช้งาน? <a href="#" class="text-indigo-400 hover:text-indigo-300">ติดต่อฝ่ายไอที</a>
            </p>
        </div>
    </div>
</x-guest-layout>
