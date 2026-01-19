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
            ->with([
                'calendarEvents' => function ($query) use ($date) {
                    $query->forDate($date)->orderByTime();
                }
            ])
            ->get();

        $pdf = Pdf::loadView('calendar.pdf', [
            'staffList' => $staffList,
            'selectedDate' => $date,
        ]);

        // Set paper size to A4 landscape
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('calendar-' . $date->format('Y-m-d') . '.pdf');
    }
}
