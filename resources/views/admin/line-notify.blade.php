<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i class="fa-brands fa-line text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('แจ้งเตือนผ่าน LINE') }}
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">ส่งตารางปฏิบัติงานผ่าน LINE Messaging API</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div
        id="vue-line-notify"
        data-settings="{{ json_encode([
            'channel_access_token' => $settings->channel_access_token,
            'is_enabled'           => (bool) $settings->is_enabled,
            'send_mode'            => $settings->send_mode ?? 'broadcast',
            'destination_id'       => $settings->destination_id,
            'destination_name'     => $settings->destination_name,
            'schedule_enabled'     => (bool) $settings->schedule_enabled,
            'schedule_time'        => $settings->schedule_time ? \Carbon\Carbon::parse($settings->schedule_time)->format('H:i') : '07:00',
            'message_template'     => $settings->message_template ?? '',
        ]) }}"
    ></div>
</x-admin-layout>
