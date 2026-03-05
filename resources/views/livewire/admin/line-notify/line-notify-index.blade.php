<div class="space-y-6" x-data="{ activeTab: @entangle('activeTab') }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fa-brands fa-line text-white text-lg"></i>
                </div>
                แจ้งเตือนผ่าน LINE
            </h1>
            <p class="text-slate-500 text-sm mt-1">ส่งตารางปฏิบัติงานผ่าน LINE Notify</p>
        </div>

        <button wire:click="openSettings"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all duration-300 text-sm font-medium shadow-sm">
            <i class="fa-solid fa-gear"></i>
            ตั้งค่า
        </button>
    </div>

    {{-- Status Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Connection Status --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">สถานะการเชื่อมต่อ</span>
                <div class="w-8 h-8 rounded-lg {{ $settings->line_notify_token ? 'bg-emerald-50' : 'bg-slate-100' }} flex items-center justify-center">
                    <i class="fa-solid {{ $settings->line_notify_token ? 'fa-link text-emerald-500' : 'fa-link-slash text-slate-400' }} text-sm"></i>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full {{ $settings->line_notify_token ? 'bg-emerald-400 animate-pulse' : 'bg-slate-300' }}"></div>
                <span class="font-semibold text-sm {{ $settings->line_notify_token ? 'text-emerald-600' : 'text-slate-500' }}">
                    {{ $settings->line_notify_token ? 'เชื่อมต่อแล้ว' : 'ยังไม่ได้เชื่อมต่อ' }}
                </span>
            </div>
        </div>

        {{-- Notification Toggle --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">การแจ้งเตือน</span>
                <button wire:click="toggleEnabled"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none {{ $is_enabled ? 'bg-emerald-500' : 'bg-slate-300' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $is_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full {{ $is_enabled ? 'bg-emerald-400' : 'bg-slate-300' }}"></div>
                <span class="font-semibold text-sm {{ $is_enabled ? 'text-emerald-600' : 'text-slate-500' }}">
                    {{ $is_enabled ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
                </span>
            </div>
        </div>

        {{-- Schedule Status --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">ส่งอัตโนมัติ</span>
                <button wire:click="toggleSchedule"
                        class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors duration-300 focus:outline-none {{ $schedule_enabled ? 'bg-indigo-500' : 'bg-slate-300' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300 {{ $schedule_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>
            <div class="flex items-center gap-2">
                <i class="fa-regular fa-clock text-xs {{ $schedule_enabled ? 'text-indigo-500' : 'text-slate-400' }}"></i>
                <span class="font-semibold text-sm {{ $schedule_enabled ? 'text-indigo-600' : 'text-slate-500' }}">
                    {{ $schedule_enabled ? 'ส่งทุกวัน เวลา ' . $schedule_time . ' น.' : 'ปิดอยู่' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <button wire:click="switchTab('send')"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 {{ $activeTab === 'send' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            <i class="fa-solid fa-paper-plane mr-1.5"></i>
            ส่งข้อความ
        </button>
        <button wire:click="switchTab('logs')"
                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 {{ $activeTab === 'logs' ? 'bg-white text-indigo-700 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
            <i class="fa-solid fa-clock-rotate-left mr-1.5"></i>
            ประวัติการส่ง
        </button>
    </div>

    {{-- Send Tab --}}
    @if($activeTab === 'send')
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Send Control Panel --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-teal-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-emerald-500"></i>
                        ส่งข้อความด้วยตนเอง
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">เลือกวันที่แล้วกดส่งเพื่อแจ้งเตือนผ่าน LINE</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Date Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fa-regular fa-calendar mr-1 text-indigo-500"></i>
                            เลือกวันที่ส่ง
                        </label>
                        <input type="date" wire:model.live="send_date"
                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm shadow-sm">
                    </div>

                    {{-- Quick Date Buttons --}}
                    <div class="flex flex-wrap gap-2">
                        <button wire:click="$set('send_date', '{{ now()->format('Y-m-d') }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $send_date === now()->format('Y-m-d') ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            วันนี้
                        </button>
                        <button wire:click="$set('send_date', '{{ now()->addDay()->format('Y-m-d') }}')"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all {{ $send_date === now()->addDay()->format('Y-m-d') ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            พรุ่งนี้
                        </button>
                    </div>

                    {{-- Event Preview Count --}}
                    <div class="bg-slate-50 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">กิจกรรมในวันที่เลือก</span>
                            <span class="text-lg font-bold text-indigo-600">{{ $todayEvents->count() }} รายการ</span>
                        </div>
                    </div>

                    {{-- Send Button --}}
                    <button wire:click="sendNow"
                            wire:loading.attr="disabled"
                            @if(!$is_enabled) disabled @endif
                            class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                        <span wire:loading.remove wire:target="sendNow">
                            <i class="fa-brands fa-line text-lg"></i>
                            ส่งแจ้งเตือน LINE ตอนนี้
                        </span>
                        <span wire:loading wire:target="sendNow" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            กำลังส่ง...
                        </span>
                    </button>

                    @if(!$is_enabled)
                        <p class="text-xs text-amber-600 bg-amber-50 rounded-lg p-3 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            กรุณาเปิดการแจ้งเตือนก่อนส่งข้อความ
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Event Preview --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-violet-50">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-eye text-indigo-500"></i>
                        ตัวอย่างข้อมูลที่จะส่ง
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ \Carbon\Carbon::parse($send_date)->locale('th')->translatedFormat('l ที่ j F') }} พ.ศ. {{ \Carbon\Carbon::parse($send_date)->year + 543 }}
                    </p>
                </div>

                <div class="p-6">
                    @if($todayEvents->isEmpty())
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <i class="fa-regular fa-calendar-xmark text-2xl text-slate-400"></i>
                            </div>
                            <p class="text-slate-500 font-medium">ไม่มีกิจกรรมในวันที่เลือก</p>
                            <p class="text-slate-400 text-sm mt-1">ข้อความที่ส่งจะแจ้งว่าไม่มีกิจกรรม</p>
                        </div>
                    @else
                        <div class="space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
                            @foreach($todayEvents->groupBy('staff_id') as $staffId => $events)
                                @php $staff = $events->first()->staff; @endphp
                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        @if($staff && $staff->photo_url)
                                            <img src="{{ $staff->photo_url }}" alt="{{ $staff->name }}"
                                                 class="w-9 h-9 rounded-lg object-cover border-2 border-white shadow-sm">
                                        @else
                                            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                                                <i class="fa-solid fa-user text-indigo-500 text-sm"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-bold text-slate-800 text-sm">{{ $staff->name ?? 'ไม่ระบุ' }}</p>
                                            @if($staff && $staff->position)
                                                <p class="text-xs text-slate-500">{{ $staff->position }}</p>
                                            @endif
                                        </div>
                                        <span class="ml-auto text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">
                                            {{ $events->count() }} งาน
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($events as $event)
                                            <div class="bg-white rounded-lg p-3 border border-slate-100 text-sm">
                                                <div class="flex items-center gap-2 text-slate-800 font-medium">
                                                    <i class="fa-regular fa-clock text-xs text-indigo-400"></i>
                                                    {{ $event->time_range }}
                                                </div>
                                                <p class="text-slate-600 mt-1">
                                                    <i class="fa-solid fa-thumbtack text-xs text-amber-400 mr-1"></i>
                                                    {{ $event->title }}
                                                </p>
                                                <p class="text-slate-500 text-xs mt-0.5">
                                                    <i class="fa-solid fa-location-dot text-xs text-rose-400 mr-1"></i>
                                                    {{ $event->location }}
                                                </p>
                                                @if($event->organization)
                                                    <p class="text-slate-500 text-xs mt-0.5">
                                                        <i class="fa-regular fa-building text-xs text-blue-400 mr-1"></i>
                                                        {{ $event->organization }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Logs Tab --}}
    @if($activeTab === 'logs')
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i>
                ประวัติการส่งแจ้งเตือน
            </h3>
        </div>

        @if($logs->isEmpty())
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-clock text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-500 font-medium">ยังไม่มีประวัติการส่ง</p>
                <p class="text-slate-400 text-sm mt-1">เมื่อส่งแจ้งเตือน ประวัติจะแสดงที่นี่</p>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 text-left">
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">วันที่ส่ง</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">วันที่ข้อมูล</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">ประเภท</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">กิจกรรม</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">ส่งโดย</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-slate-500 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($logs as $log)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-800 font-medium">
                                    {{ $log->notification_date->locale('th')->translatedFormat('j M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                        {{ $log->send_type === 'manual' ? 'bg-blue-50 text-blue-600' : 'bg-violet-50 text-violet-600' }}">
                                        <i class="fa-solid {{ $log->send_type === 'manual' ? 'fa-hand-pointer' : 'fa-robot' }} text-[10px]"></i>
                                        {{ $log->send_type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                        {{ $log->status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                        <i class="fa-solid {{ $log->status === 'success' ? 'fa-check-circle' : 'fa-times-circle' }} text-[10px]"></i>
                                        {{ $log->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $log->events_count }} รายการ
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ $log->sender ? $log->sender->name : 'ระบบ' }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="deleteLog({{ $log->id }})"
                                            wire:confirm="ต้องการลบ Log นี้หรือไม่?"
                                            class="text-slate-400 hover:text-rose-500 transition-colors">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-100">
                @foreach($logs as $log)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-800">
                                {{ $log->notification_date->locale('th')->translatedFormat('j M Y') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-xs font-semibold
                                {{ $log->status === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                <i class="fa-solid {{ $log->status === 'success' ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $log->status_label }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-slate-500">
                            <span>
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ $log->created_at->format('H:i') }}
                            </span>
                            <span>
                                <i class="fa-solid {{ $log->send_type === 'manual' ? 'fa-hand-pointer' : 'fa-robot' }} mr-1"></i>
                                {{ $log->send_type_label }}
                            </span>
                            <span>{{ $log->events_count }} กิจกรรม</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
    @endif

    {{-- Settings Modal --}}
    @if($showSettingsModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="document.body.classList.add('overflow-hidden')" x-on:remove="document.body.classList.remove('overflow-hidden')">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="closeSettings"></div>

        {{-- Modal Content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto z-10">
            {{-- Header --}}
            <div class="sticky top-0 z-10 bg-white border-b border-slate-100 px-6 py-4 rounded-t-2xl flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-gear text-indigo-500"></i>
                    ตั้งค่า LINE Notify
                </h3>
                <button wire:click="closeSettings"
                        class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                {{-- LINE Notify Token --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        <i class="fa-solid fa-key text-amber-500 mr-1"></i>
                        LINE Notify Token
                    </label>
                    <p class="text-xs text-slate-500 mb-3">
                        สร้าง Token ได้ที่ <a href="https://notify-bot.line.me/my/" target="_blank" class="text-indigo-600 hover:underline font-medium">notify-bot.line.me</a>
                    </p>
                    <div class="relative">
                        <input type="{{ $showTokenInput ? 'text' : 'password' }}"
                               wire:model="line_notify_token"
                               placeholder="กรอก LINE Notify Token"
                               class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm pr-24 shadow-sm">
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                            <button wire:click="$toggle('showTokenInput')" type="button"
                                    class="px-2 py-1 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid {{ $showTokenInput ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                            </button>
                            <button wire:click="testToken" type="button"
                                    wire:loading.attr="disabled"
                                    wire:target="testToken"
                                    class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-semibold hover:bg-indigo-100 transition-colors">
                                <span wire:loading.remove wire:target="testToken">ทดสอบ</span>
                                <span wire:loading wire:target="testToken">
                                    <i class="fa-solid fa-spinner animate-spin"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Schedule Time --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">
                        <i class="fa-regular fa-clock text-indigo-500 mr-1"></i>
                        เวลาส่งอัตโนมัติ
                    </label>
                    <p class="text-xs text-slate-500 mb-3">ระบบจะส่งข้อมูลกิจกรรมของวันนั้นๆ ตามเวลาที่กำหนด</p>
                    <input type="time" wire:model="schedule_time"
                           class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm shadow-sm">
                </div>

                {{-- Message Template --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-bold text-slate-700">
                            <i class="fa-solid fa-message text-emerald-500 mr-1"></i>
                            รูปแบบข้อความ
                        </label>
                        <button wire:click="resetTemplate" type="button"
                                class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                            <i class="fa-solid fa-rotate-right mr-1"></i>
                            รีเซ็ต
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 mb-3">
                        ตัวแปรที่ใช้ได้: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{date}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{events}</code>
                        <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600">{total}</code>
                    </p>
                    <textarea wire:model="message_template" rows="6"
                              class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500/20 text-sm font-mono shadow-sm resize-none"></textarea>
                </div>

                {{-- Cron Job Info --}}
                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                    <p class="text-sm font-bold text-amber-800 mb-2">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        การตั้งค่า Cron Job (สำหรับส่งอัตโนมัติ)
                    </p>
                    <p class="text-xs text-amber-700 mb-2">เพิ่มคำสั่งนี้ใน Cron Job ของ Server:</p>
                    <code class="block bg-white rounded-lg p-3 text-xs text-slate-700 border border-amber-200 font-mono break-all">
                        * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
                    </code>
                </div>
            </div>

            {{-- Footer --}}
            <div class="sticky bottom-0 bg-white border-t border-slate-100 px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3">
                <button wire:click="closeSettings"
                        class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    ยกเลิก
                </button>
                <button wire:click="saveSettings"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all">
                    <i class="fa-solid fa-check mr-1.5"></i>
                    บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
