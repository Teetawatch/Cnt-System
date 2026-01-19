<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-calendar-check text-white text-lg"></i>
                        </div>
                        <span class="hidden sm:block font-semibold text-gray-900 dark:text-white">ปฏิทินการปฏิบัติงาน</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fa-solid fa-home me-2"></i>
                        {{ __('หน้าหลัก') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">
                            <i class="fa-solid fa-calendar-days me-2"></i>
                            {{ __('ปฏิทิน') }}
                        </x-nav-link>

                        @if(Auth::user()->isAdmin())
                            <x-nav-link :href="route('admin.staff.index')" :active="request()->routeIs('admin.staff.*')">
                                <i class="fa-solid fa-users me-2"></i>
                                {{ __('จัดการผู้ปฏิบัติ') }}
                            </x-nav-link>

                            <x-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                                <i class="fa-solid fa-calendar-plus me-2"></i>
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
                        <span class="badge badge-primary me-3">
                            <i class="fa-solid fa-crown me-1"></i>
                            Admin
                        </span>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center me-2">
                                        <i class="fa-solid fa-user text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <span>{{ Auth::user()->name }}</span>
                                </div>

                                <div class="ms-1">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fa-solid fa-user-pen me-2 text-gray-400"></i>
                                {{ __('โปรไฟล์') }}
                            </x-dropdown-link>

                            @if(Auth::user()->isAdmin())
                                <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                
                                <x-dropdown-link :href="route('admin.staff.index')">
                                    <i class="fa-solid fa-users me-2 text-gray-400"></i>
                                    {{ __('จัดการผู้ปฏิบัติ') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('admin.events.index')">
                                    <i class="fa-solid fa-calendar-plus me-2 text-gray-400"></i>
                                    {{ __('จัดการกิจกรรม') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <i class="fa-solid fa-right-from-bracket me-2 text-gray-400"></i>
                                    {{ __('ออกจากระบบ') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            @else
                <!-- Login Button for Guests -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('login') }}" class="btn-primary text-sm">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        เข้าสู่ระบบ
                    </a>
                </div>
            @endauth

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <i x-show="!open" class="fa-solid fa-bars text-xl"></i>
                    <i x-show="open" x-cloak class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fa-solid fa-home me-2"></i>
                {{ __('หน้าหลัก') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar.*')">
                    <i class="fa-solid fa-calendar-days me-2"></i>
                    {{ __('ปฏิทิน') }}
                </x-responsive-nav-link>

                @if(Auth::user()->isAdmin())
                    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>
                    
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fa-solid fa-shield-halved me-2"></i>
                        {{ __('แดชบอร์ดผู้ดูแล') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.staff.index')" :active="request()->routeIs('admin.staff.*')">
                        <i class="fa-solid fa-users me-2"></i>
                        {{ __('จัดการผู้ปฏิบัติ') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                        <i class="fa-solid fa-calendar-plus me-2"></i>
                        {{ __('จัดการกิจกรรม') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4 flex items-center">
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center me-3">
                        <i class="fa-solid fa-user text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">
                            {{ Auth::user()->email }}
                            @if(Auth::user()->isAdmin())
                                <span class="ms-1 text-primary-600">(Admin)</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        <i class="fa-solid fa-user-pen me-2 text-gray-400"></i>
                        {{ __('โปรไฟล์') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="fa-solid fa-right-from-bracket me-2 text-gray-400"></i>
                            {{ __('ออกจากระบบ') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4 py-2">
                    <a href="{{ route('login') }}" class="btn-primary w-full text-center">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>
                        เข้าสู่ระบบ
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
