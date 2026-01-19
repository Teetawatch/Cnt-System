<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\Staff;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarPdfController extends Controller
{
    public function generate(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $staffId = $request->get('staff');
        
        $dateObject = Carbon::parse($date);
        
        // Get staff with events
        $staffWithEvents = Staff::active()
            ->ordered()
            ->when($staffId, function($query) use ($staffId) {
                $query->where('id', $staffId);
            })
            ->with(['calendarEvents' => function($query) use ($dateObject) {
                $query->forDate($dateObject)->orderByTime();
            }])
            ->get();

        // Format Thai date
        $thaiDate = $dateObject->locale('th')->translatedFormat('l ที่ j F ') . 'พ.ศ. ' . ($dateObject->year + 543);

        $pdf = Pdf::loadView('pdf.calendar', [
            'staffWithEvents' => $staffWithEvents,
            'date' => $dateObject,
            'thaiDate' => $thaiDate,
            'filterStaff' => $staffId ? Staff::find($staffId) : null,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'calendar-' . $dateObject->format('Y-m-d') . '.pdf';
        
        return $pdf->stream($filename);
    }
}
