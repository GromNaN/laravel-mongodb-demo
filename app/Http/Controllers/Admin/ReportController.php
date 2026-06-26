<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::pending()
            ->orderBy('created', 'desc')
            ->paginate(50);

        return view('admin.reports.index', compact('reports'));
    }

    public function zap(Report $report): RedirectResponse
    {
        $report->zapped    = now();
        $report->zapped_by = Auth::user()->username;
        $report->save();

        return redirect()->route('admin.reports.index')->with('success', 'Report marked as handled.');
    }
}
