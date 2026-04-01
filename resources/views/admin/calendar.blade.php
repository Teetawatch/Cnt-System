<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <i class="fa-solid fa-calendar-days text-white text-xl"></i>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight">
                    {{ __('ปฏิทินการปฏิบัติงาน') }}
                </h2>
                <p class="text-sm text-slate-500 font-medium">ตารางงานและกิจกรรมของผู้บริหาร</p>
            </div>
        </div>
    </x-slot>

    <div
        id="vue-calendar-view"
        data-date="{{ now()->format('Y-m-d') }}"
        data-pdf-url="{{ route('calendar.pdf') }}"
    ></div>
</x-admin-layout>
