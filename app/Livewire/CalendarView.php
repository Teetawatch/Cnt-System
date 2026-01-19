<?php

namespace App\Livewire;

use App\Models\CalendarEvent;
use App\Models\Staff;
use Carbon\Carbon;
use Livewire\Component;

class CalendarView extends Component
{
    public $selectedDate;
    public $filterStaff = '';
    public $showEventModal = false;
    public $selectedEvent = null;

    protected $queryString = ['filterStaff'];

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function goToToday()
    {
        $this->selectedDate = today()->format('Y-m-d');
    }

    public function previousDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subDay()->format('Y-m-d');
    }

    public function nextDay()
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addDay()->format('Y-m-d');
    }

    public function setDate($date)
    {
        $this->selectedDate = $date;
    }

    public function showEvent($eventId)
    {
        $this->selectedEvent = CalendarEvent::with('staff')->find($eventId);
        $this->showEventModal = true;
    }

    public function closeEventModal()
    {
        $this->showEventModal = false;
        $this->selectedEvent = null;
    }

    public function getFormattedDateProperty()
    {
        return Carbon::parse($this->selectedDate)->locale('th')->translatedFormat('l ที่ j F พ.ศ. ') . 
               (Carbon::parse($this->selectedDate)->year + 543);
    }

    public function getIsToday()
    {
        return Carbon::parse($this->selectedDate)->isToday();
    }

    public function render()
    {
        $date = Carbon::parse($this->selectedDate);
        
        // Get staff with their events for the selected date
        $staffWithEvents = Staff::active()
            ->ordered()
            ->when($this->filterStaff, function($query) {
                $query->where('id', $this->filterStaff);
            })
            ->with(['calendarEvents' => function($query) use ($date) {
                $query->forDate($date)->orderByTime();
            }])
            ->get();

        $allStaff = Staff::active()->ordered()->get();
        
        $totalEvents = CalendarEvent::forDate($date)
            ->when($this->filterStaff, function($query) {
                $query->where('staff_id', $this->filterStaff);
            })
            ->count();

        return view('livewire.calendar-view', [
            'staffWithEvents' => $staffWithEvents,
            'allStaff' => $allStaff,
            'totalEvents' => $totalEvents,
            'dateObject' => $date,
        ])->layout('layouts.app');
    }
}
