<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Отчёты: заработок за период, по комнатам, доп. услуги, экспорт Excel. */
class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request): Response
    {
        $from = $request->has('from') ? Carbon::parse($request->input('from')) : now()->startOfMonth();
        $to = $request->has('to') ? Carbon::parse($request->input('to')) : now();
        $roomId = $request->input('room_id') ? (int) $request->input('room_id') : null;

        $totalEarnings = $this->reportService->earningsForPeriod($from, $to, $roomId);
        $byRoom = $this->reportService->earningsByRoom($from, $to);
        $extraEarnings = $this->reportService->earningsExtraServices($from, $to);

        return Inertia::render('Panel/Reports/Index', [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'room_id' => $roomId,
            'totalEarnings' => $totalEarnings,
            'byRoom' => $byRoom,
            'extraEarnings' => $extraEarnings,
        ]);
    }

    /** Скачать Excel — placeholder (файл не создаётся). */
    public function export(Request $request): RedirectResponse
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()));
        $to = Carbon::parse($request->input('to', now()));
        $this->reportService->exportToExcel($from, $to);

        return redirect()->back()->with('info', __('report.export_placeholder'));
    }
}
