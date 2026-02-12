<nav x-data="{ open: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-700/60 sticky top-0 z-40 transition-all duration-300">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl shadow-lg shadow-indigo-500/20 flex items-center justify-center transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                        </div>
                        <span class="hidden sm:block font-bold text-xl bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-violet-600 dark:from-indigo-400 dark:to-violet-400 font-heading tracking-tight">
                            CntSystem
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="nav-link-modern {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                        <i class="fa-solid fa-home me-2 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                        {{ __('หน้าหลัก') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')" class="nav-link-modern {{ request()->routeIs('calendar.*') ? 'nav-link-active' : '' }}">
                            <i class="fa-solid fa-calendar-days me-2 {{ request()->routeIs('calendar.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                            {{ __('ปฏิทิน') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('staff.index')" :active="request()->routeIs('staff.*')" class="nav-link-modern {{ request()->routeIs('staff.*') ? 'nav-link-active' : '' }}">
                                <i class="fa-solid fa-users me-2 {{ request()->routeIs('staff.*') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                                {{ __('จัดการผู้ปฏิบัติ') }}
                            </x-nav-link>

                            <x-nav-link :href="route('calendar.manage')" :active="request()->routeIs('calendar.manage')" class="nav-link-modern {{ request()->routeIs('calendar.manage') ? 'nav-link-active' : '' }}">
                                <i class="fa-solid fa-calendar-plus me-2 {{ request()->routeIs('calendar.manage') ? 'text-indigo-600' : 'text-slate-400' }}"></i>
                                {{ __('จัดการกิจกรรม') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown (for logged in users) -->
            @auth
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <!-- Role Badge -->
                    @if(Auth::user()->isAdmin())
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold me-3 border border-indigo-200 shadow-sm flex items-center gap-1">
                            <i class="fa-solid fa-shield-halved"></i> Admin
                        </span>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-slate-200 dark:border-slate-700 text-sm leading-4 font-medium rounded-xl text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 hover:text-indigo-600 hover:border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition ease-in-out duration-200 shadow-sm gap-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-lg flex items-center justify-center text-white text-xs shadow-md">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 group-hover:rotate-180"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700">
                                <p class="text-xs text-slate-500 uppercase font-semibold">บัญชีของฉัน</p>
                            </div>
                            
                            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-slate-50 group">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                        <i class="fa-solid fa-user-pen"></i>
                                    </div>
                                    <span>{{ __('โปรไฟล์') }}</span>
                                </div>
                            </x-dropdown-link>

                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                <div class="px-4 py-2">
                                    <p class="text-xs text-slate-500 uppercase font-semibold">ผู้ดูแลระบบ</p>
                                </div>
                                
                                <x-dropdown-link :href="route('admin.dashboard')" class="hover:bg-slate-50 group">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </div>
                                        <span>{{ __('แดชบอร์ดผู้ดูแล') }}</span>
                                    </div>
                                </x-dropdown-link>


                                <x-dropdown-link :href="route('staff.index')" class="hover:bg-slate-50 group">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                            <i class="fa-solid fa-users"></i>
                                        </div>
                                        <span>{{ __('จัดการผู้ปฏิบัติ') }}</span>
                                    </div>
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('calendar.manage')" class="hover:bg-slate-50 group">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                            <i class="fa-solid fa-calendar-plus"></i>
                                        </div>
                                        <span>{{ __('จัดการกิจกรรม') }}</span>
                                    </div>
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')" class="text-rose-600 hover:bg-rose-50 group"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center group-hover:bg-rose-100 group-hover:text-rose-600 transition-colors">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                        </div>
                                        <span>{{ __('ออกจากระบบ') }}</span>
                                    </div>
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Login Button for Guests -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('login') }}" class="btn-primary flex items-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        เข้าสู่ระบบ
                    </a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 focus:outline-none transition duration-150 ease-in-out">
                    <i x-show="!open" class="fa-solid fa-bars text-xl"></i>
                    <i x-show="open" x-cloak class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-xl">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="border-l-4 border-transparent hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '' }}">
                <i class="fa-solid fa-home me-2"></i>
                {{ __('หน้าหลัก') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')" class="border-l-4 border-transparent hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('calendar.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '' }}">
                    <i class="fa-solid fa-calendar-days me-2"></i>
                    {{ __('ปฏิทิน') }}
                </x-responsive-nav-link>

                @if(Auth::user()->isAdmin())
                    <div class="py-2 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">เมนูผู้ดูแล</div>
                    
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->is('admin')" class="border-l-4 border-transparent hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->is('admin') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '' }}">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        {{ __('แดชบอร์ดผู้ดูแล') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('staff.index')" :active="request()->routeIs('staff.*')" class="border-l-4 border-transparent hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('staff.*') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '' }}">
                        <i class="fa-solid fa-users me-2"></i>
                        {{ __('จัดการผู้ปฏิบัติ') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('calendar.manage')" :active="request()->routeIs('calendar.manage')" class="border-l-4 border-transparent hover:border-indigo-500 hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('calendar.manage') ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '' }}">
                        <i class="fa-solid fa-calendar-plus me-2"></i>
                        {{ __('จัดการกิจกรรม') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
            @auth
                <div class="px-4 flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg me-3">
                        <span class="text-white font-bold text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="font-bold text-base text-slate-800 dark:text-slate-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-slate-500">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>

                <div class="mt-4 space-y-1 px-2">
                    <x-responsive-nav-link :href="route('profile.edit')" class="rounded-lg hover:bg-white hover:shadow-sm">
                        <i class="fa-solid fa-user-pen me-2 text-indigo-500"></i>
                        {{ __('โปรไฟล์') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')" class="rounded-lg hover:bg-rose-50 text-rose-600 hover:text-rose-700"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>
                            {{ __('ออกจากระบบ') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-3">
                    <a href="{{ route('login') }}" class="btn-primary w-full text-center shadow-lg">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        เข้าสู่ระบบ
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
