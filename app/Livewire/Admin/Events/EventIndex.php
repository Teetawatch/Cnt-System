<?php

namespace App\Livewire\Admin\Events;

use App\Models\CalendarEvent;
use App\Models\Staff;
use Livewire\Component;
use Livewire\WithPagination;

class EventIndex extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterDate = '';
    public $filterStaff = '';
    public $filterStatus = '';

    // Modal states
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;

    // Form data
    public $eventId;
    public $staff_id = '';
    public $event_date = '';
    public $end_date = '';
    public $start_time = '';
    public $end_time = '';
    public $title = '';
    public $description = '';
    public $location = '';
    public $organization = '';
    public $status = 'confirmed';

    // Delete confirmation
    public $deleteId;
    public $deleteTitle;

    protected $queryString = ['search', 'filterDate', 'filterStaff', 'filterStatus'];

    public function mount()
    {
        // Set filterDate to today by default if not provided in URL
        if (empty($this->filterDate)) {
            $this->filterDate = now()->format('Y-m-d');
        }
    }

    protected function rules()
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'event_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:event_date',
            'start_time' => 'required',
            'end_time' => 'nullable',
            'title' => 'required|max:255',
            'description' => 'nullable',
            'location' => 'required|max:255',
            'organization' => 'nullable|max:255',
            'status' => 'required|in:pending,confirmed,cancelled',
        ];
    }

    protected $messages = [
        'staff_id.required' => 'กรุณาเลือกผู้ปฏิบัติงาน',
        'staff_id.exists' => 'ไม่พบผู้ปฏิบัติงานที่เลือก',
        'event_date.required' => 'กรุณาระบุวันที่เริ่มต้น',
        'event_date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
        'end_date.date' => 'รูปแบบวันที่ไม่ถูกต้อง',
        'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่ก่อนวันที่เริ่มต้น',
        'start_time.required' => 'กรุณาระบุเวลาเริ่ม',
        'title.required' => 'กรุณากรอกรายการงาน',
        'title.max' => 'รายการงานต้องไม่เกิน 255 ตัวอักษร',
        'location.required' => 'กรุณากรอกสถานที่',
        'location.max' => 'สถานที่ต้องไม่เกิน 255 ตัวอักษร',
        'organization.max' => 'หน่วยงานต้องไม่เกิน 255 ตัวอักษร',
        'status.required' => 'กรุณาเลือกสถานะ',
        'status.in' => 'สถานะไม่ถูกต้อง',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDate()
    {
        $this->resetPage();
    }

    public function updatingFilterStaff()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterDate', 'filterStaff', 'filterStatus']);
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->event_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $event = CalendarEvent::findOrFail($id);

        $this->resetForm();
        $this->editMode = true;
        $this->eventId = $event->id;
        $this->staff_id = $event->staff_id;
        $this->event_date = $event->event_date->format('Y-m-d');
        $this->end_date = $event->event_date->format('Y-m-d');
        $this->start_time = $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('H:i') : '';
        $this->end_time = $event->end_time ? \Carbon\Carbon::parse($event->end_time)->format('H:i') : '';
        $this->title = $event->title;
        $this->description = $event->description;
        $this->location = $event->location;
        $this->organization = $event->organization;
        $this->status = $event->status;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editMode) {
            $data = [
                'staff_id' => $this->staff_id,
                'event_date' => $this->event_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time ?: null,
                'title' => $this->title,
                'description' => $this->description,
                'location' => $this->location,
                'organization' => $this->organization,
                'status' => $this->status,
            ];
            $event = CalendarEvent::findOrFail($this->eventId);
            $event->update($data);
            session()->flash('success', 'แก้ไขกิจกรรมสำเร็จ');
        } else {
            $startDate = \Carbon\Carbon::parse($this->event_date);
            $endDate = $this->end_date ? \Carbon\Carbon::parse($this->end_date) : $startDate;
            
            $days = $startDate->diffInDays($endDate) + 1;
            
            for ($i = 0; $i < $days; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                CalendarEvent::create([
                    'staff_id' => $this->staff_id,
                    'event_date' => $currentDate->format('Y-m-d'),
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time ?: null,
                    'title' => $this->title,
                    'description' => $this->description,
                    'location' => $this->location,
                    'organization' => $this->organization,
                    'status' => $this->status,
                    'created_by' => auth()->id(),
                ]);
            }
            
            session()->flash('success', 'เพิ่มกิจกรรมสำเร็จ (' . $days . ' วัน)');
        }

        // Auto-filter to the saved event's date so user can see it immediately
        $this->filterDate = $this->event_date;

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $event = CalendarEvent::findOrFail($id);
        $this->deleteId = $event->id;
        $this->deleteTitle = $event->title;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $event = CalendarEvent::findOrFail($this->deleteId);
        $event->delete();

        session()->flash('success', 'ลบกิจกรรมสำเร็จ');
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteTitle = null;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteTitle = null;
    }

    private function resetForm()
    {
        $this->reset(['eventId', 'staff_id', 'event_date', 'end_date', 'start_time', 'end_time', 'title', 'description', 'location', 'organization', 'status']);
        $this->status = 'confirmed';
        $this->resetValidation();
    }

    public function render()
    {
        $events = CalendarEvent::with('staff')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%')
                      ->orWhere('organization', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDate, function ($query) {
                $query->whereDate('event_date', $this->filterDate);
            })
            ->when($this->filterStaff, function ($query) {
                $query->where('staff_id', $this->filterStaff);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->orderByTime()
            ->paginate(10);

        $staffList = Staff::active()->ordered()->get();

        return view('livewire.admin.events.event-index', [
            'events' => $events,
            'staffList' => $staffList,
        ])->layout('layouts.app');
    }
}
