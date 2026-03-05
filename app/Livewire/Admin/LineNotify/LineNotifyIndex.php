<?php

namespace App\Livewire\Admin\LineNotify;

use App\Models\LineNotificationLog;
use App\Models\LineNotificationSetting;
use App\Services\LineNotifyService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LineNotifyIndex extends Component
{
    use WithPagination;

    // Settings
    public $line_notify_token = '';
    public $is_enabled = false;
    public $schedule_enabled = false;
    public $schedule_time = '07:00';
    public $message_template = '';

    // Manual Send
    public $send_date = '';

    // UI States
    public $showSettingsModal = false;
    public $showTokenInput = false;
    public $activeTab = 'send'; // 'send' or 'logs'

    public function mount()
    {
        $settings = LineNotificationSetting::instance();
        $this->line_notify_token = $settings->line_notify_token ?? '';
        $this->is_enabled = $settings->is_enabled;
        $this->schedule_enabled = $settings->schedule_enabled;
        $this->schedule_time = $settings->schedule_time ? Carbon::parse($settings->schedule_time)->format('H:i') : '07:00';
        $this->message_template = $settings->message_template ?? LineNotificationSetting::defaultTemplate();
        $this->send_date = now()->format('Y-m-d');
    }

    /**
     * Save settings
     */
    public function saveSettings()
    {
        $this->validate([
            'line_notify_token' => 'nullable|string',
            'schedule_time' => 'required',
        ]);

        $settings = LineNotificationSetting::instance();
        $settings->update([
            'line_notify_token' => $this->line_notify_token ?: null,
            'is_enabled' => $this->is_enabled,
            'schedule_enabled' => $this->schedule_enabled,
            'schedule_time' => $this->schedule_time,
            'message_template' => $this->message_template ?: LineNotificationSetting::defaultTemplate(),
        ]);

        session()->flash('success', 'บันทึกการตั้งค่าสำเร็จ');
        $this->showSettingsModal = false;
    }

    /**
     * Toggle LINE notifications on/off
     */
    public function toggleEnabled()
    {
        $this->is_enabled = !$this->is_enabled;
        $settings = LineNotificationSetting::instance();
        $settings->update(['is_enabled' => $this->is_enabled]);

        session()->flash('success', $this->is_enabled ? 'เปิดการแจ้งเตือน LINE แล้ว' : 'ปิดการแจ้งเตือน LINE แล้ว');
    }

    /**
     * Toggle schedule on/off
     */
    public function toggleSchedule()
    {
        $this->schedule_enabled = !$this->schedule_enabled;
        $settings = LineNotificationSetting::instance();
        $settings->update(['schedule_enabled' => $this->schedule_enabled]);

        session()->flash('success', $this->schedule_enabled ? 'เปิดการส่งอัตโนมัติแล้ว' : 'ปิดการส่งอัตโนมัติแล้ว');
    }

    /**
     * Test the token
     */
    public function testToken()
    {
        if (empty($this->line_notify_token)) {
            session()->flash('error', 'กรุณากรอก LINE Notify Token ก่อน');
            return;
        }

        $service = new LineNotifyService();
        $result = $service->testToken($this->line_notify_token);

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    /**
     * Manually send notification for selected date
     */
    public function sendNow()
    {
        if (empty($this->send_date)) {
            session()->flash('error', 'กรุณาเลือกวันที่ก่อนส่ง');
            return;
        }

        $service = new LineNotifyService();
        $result = $service->sendForDate($this->send_date, 'manual', auth()->id());

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    /**
     * Open settings modal
     */
    public function openSettings()
    {
        $this->showSettingsModal = true;
    }

    /**
     * Close settings modal
     */
    public function closeSettings()
    {
        $this->showSettingsModal = false;
    }

    /**
     * Reset message template to default
     */
    public function resetTemplate()
    {
        $this->message_template = LineNotificationSetting::defaultTemplate();
    }

    /**
     * Switch tab
     */
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    /**
     * Delete a log entry
     */
    public function deleteLog($id)
    {
        LineNotificationLog::findOrFail($id)->delete();
        session()->flash('success', 'ลบ Log สำเร็จ');
    }

    public function render()
    {
        $settings = LineNotificationSetting::instance();

        $logs = LineNotificationLog::orderBy('created_at', 'desc')
            ->paginate(10);

        $todayEvents = \App\Models\CalendarEvent::forDate(Carbon::parse($this->send_date))
            ->with('staff')
            ->orderByTime()
            ->get();

        return view('livewire.admin.line-notify.line-notify-index', [
            'settings' => $settings,
            'logs' => $logs,
            'todayEvents' => $todayEvents,
        ])->layout('layouts.admin');
    }
}
