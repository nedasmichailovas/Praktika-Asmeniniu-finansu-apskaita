<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $user = auth()->user();

        $transactions = $user->transactions()
            ->with('category')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy(function ($t) {
                return $t->date->format('Y-m-d');
            });

        $daysInMonth  = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfWeek = (new \DateTime("$year-$month-01"))->format('N');

        return view('calendar.index', compact(
            'transactions', 'month', 'year', 'daysInMonth', 'firstDayOfWeek'
        ));
    }
}