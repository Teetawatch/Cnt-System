<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Staff;
use App\Models\CalendarEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'ผู้ดูแลระบบ',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Regular User
        $user = User::create([
            'name' => 'ผู้ใช้งานทั่วไป',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Create Staff Members
        $director = Staff::create([
            'name' => 'นายสมชาย ใจดี',
            'position' => 'ผู้อำนวยการ',
            'department' => 'สำนักงานผู้อำนวยการ',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $deputy1 = Staff::create([
            'name' => 'นางสาวสมหญิง รักเรียน',
            'position' => 'รองผู้อำนวยการฝ่ายวิชาการ',
            'department' => 'ฝ่ายวิชาการ',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $deputy2 = Staff::create([
            'name' => 'นายประเสริฐ ทำดี',
            'position' => 'รองผู้อำนวยการฝ่ายบริหาร',
            'department' => 'ฝ่ายบริหาร',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $deputy3 = Staff::create([
            'name' => 'นางมาลี สร้างสรรค์',
            'position' => 'รองผู้อำนวยการฝ่ายกิจการนักเรียน',
            'department' => 'ฝ่ายกิจการนักเรียน',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Create Sample Calendar Events
        $today = Carbon::today();

        // Today's events
        CalendarEvent::create([
            'staff_id' => $director->id,
            'created_by' => $admin->id,
            'event_date' => $today,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'title' => 'ประชุมคณะกรรมการสถานศึกษาขั้นพื้นฐาน',
            'description' => 'ประชุมหารือเรื่องนโยบายการศึกษาประจำปี',
            'location' => 'ห้องประชุมใหญ่ อาคาร 1',
            'organization' => 'คณะกรรมการสถานศึกษา',
            'status' => 'confirmed',
        ]);

        CalendarEvent::create([
            'staff_id' => $director->id,
            'created_by' => $admin->id,
            'event_date' => $today,
            'start_time' => '14:00',
            'end_time' => '16:00',
            'title' => 'ต้อนรับคณะศึกษาดูงาน',
            'location' => 'ห้องประชุม VIP',
            'organization' => 'โรงเรียนสาธิต มหาวิทยาลัยศรีนครินทร์',
            'status' => 'confirmed',
        ]);

        CalendarEvent::create([
            'staff_id' => $deputy1->id,
            'created_by' => $admin->id,
            'event_date' => $today,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'title' => 'ประชุมหัวหน้ากลุ่มสาระการเรียนรู้',
            'location' => 'ห้องประชุมฝ่ายวิชาการ',
            'organization' => 'ฝ่ายวิชาการ',
            'status' => 'confirmed',
        ]);

        // Tomorrow's events
        CalendarEvent::create([
            'staff_id' => $deputy2->id,
            'created_by' => $admin->id,
            'event_date' => $today->copy()->addDay(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'title' => 'ประชุมคณะกรรมการจัดซื้อจัดจ้าง',
            'location' => 'ห้องประชุมฝ่ายบริหาร',
            'organization' => 'ฝ่ายบริหาร',
            'status' => 'pending',
        ]);

        CalendarEvent::create([
            'staff_id' => $deputy3->id,
            'created_by' => $admin->id,
            'event_date' => $today->copy()->addDay(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'title' => 'ประชุมสภานักเรียน',
            'location' => 'ห้องประชุมนักเรียน',
            'organization' => 'สภานักเรียน',
            'status' => 'confirmed',
        ]);

        // Next week events
        CalendarEvent::create([
            'staff_id' => $director->id,
            'created_by' => $admin->id,
            'event_date' => $today->copy()->addWeek(),
            'start_time' => '08:30',
            'end_time' => '16:30',
            'title' => 'ร่วมงานวันครู',
            'description' => 'ร่วมกิจกรรมวันครูที่จังหวัด',
            'location' => 'หอประชุมจังหวัด',
            'organization' => 'สำนักงานศึกษาธิการจังหวัด',
            'status' => 'confirmed',
        ]);

        $this->command->info('✅ Seeded successfully!');
        $this->command->info('   - Admin: admin@example.com / password');
        $this->command->info('   - User: user@example.com / password');
        $this->command->info('   - Staff: ' . Staff::count() . ' records');
        $this->command->info('   - Events: ' . CalendarEvent::count() . ' records');
    }
}
