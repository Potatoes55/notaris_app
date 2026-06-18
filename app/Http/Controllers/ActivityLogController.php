<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $activities = ActivityLog::with('causer')->latest()->paginate(20);

        return view('auth.activity-log', compact('activities'));
    }

    public function print()
    {
        $activities = ActivityLog::with('causer')->latest()->limit(100)->get();

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'tempDir' => storage_path('app/mpdf/temp'), // Atur direktori sementara untuk mPDF
        ]);
        $html = view('auth.activity-log-pdf', compact('activities'))->render();
        $pdf->WriteHTML($html);

        return $pdf->Output('activity_log_'.date('Ymd_His').'.pdf', 'I');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActivityLog $activityLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActivityLog $activityLog)
    {
        //
    }
}
