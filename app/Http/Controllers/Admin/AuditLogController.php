<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Журнал аудита (только для админа). */
class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AuditLog::with('user')
            ->orderByDesc('created_at')
            ->paginate((int) $request->get('per_page', 20));

        return Inertia::render('Panel/AuditLog/Index', [
            'logs' => AuditLogResource::collection($logs->getCollection()),
            'meta' => ['current_page' => $logs->currentPage(), 'last_page' => $logs->lastPage(), 'total' => $logs->total()],
        ]);
    }
}
