<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 leading-tight tracking-tight">
                        {{ __('จัดการผู้ปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-slate-500 font-medium">เพิ่ม แก้ไข ลบ ข้อมูลบุคลากรในหน่วยงาน</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div id="vue-staff-index"></div>
</x-admin-layout>
