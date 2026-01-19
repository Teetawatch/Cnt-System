<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function exportPdf(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : today();
        $staffId = $request->input('staff');

        $staffList = Staff::active()
            ->ordered()
            ->when($staffId, fn($q) => $q->where('id', $staffId))
            ->with(['calendarEvents' => function($query) use ($date) {
                $query->forDate($date)->orderByTime();
            }])
            ->get();

        $pdf = Pdf::loadView('calendar.pdf', [
            'staffList' => $staffList,
            'selectedDate' => $date,
        ]);

        // Set paper size a4 landscape just in case allow for side by side if needed, 
        // or portrait if preferred. Let's stick to standard portrait for reading lists.
        return $pdf->stream('calendar-' . $date->format('Y-m-d') . '.pdf');
    }
}
