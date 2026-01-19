<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-users text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ __('จัดการผู้ปฏิบัติงาน') }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">เพิ่ม แก้ไข ลบ ข้อมูลผู้อำนวยการและรองผู้อำนวยการ</p>
                </div>
            </div>
            <button class="btn-primary">
                <i class="fa-solid fa-plus me-2"></i>
                เพิ่มผู้ปฏิบัติ
            </button>
        </div>
    </x-slot>

    <div class="glass-card animate-fade-in">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600 dark:text-gray-400">
                        ทั้งหมด <span class="font-semibold text-gray-900 dark:text-white">{{ \App\Models\Staff::count() }}</span> รายการ
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" placeholder="ค้นหา..." class="form-input-custom text-sm w-64">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ลำดับ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ชื่อ-ตำแหน่ง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">หน่วยงาน</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สถานะ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse(\App\Models\Staff::ordered()->get() as $staff)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $staff->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center overflow-hidden">
                                        @if($staff->photo)
                                            <img src="{{ $staff->photo_url }}" alt="" class="w-10 h-10 object-cover">
                                        @else
                                            <i class="fa-solid fa-user text-primary-600 dark:text-primary-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $staff->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $staff->position }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $staff->department ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($staff->is_active)
                                    <span class="badge badge-success">ใช้งาน</span>
                                @else
                                    <span class="badge badge-danger">ไม่ใช้งาน</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <button class="text-primary-600 hover:text-primary-700 me-3">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button class="text-danger-500 hover:text-danger-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-user-plus text-4xl mb-3"></i>
                                <p>ยังไม่มีข้อมูลผู้ปฏิบัติงาน</p>
                                <button class="btn-primary mt-4">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    เพิ่มผู้ปฏิบัติคนแรก
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
