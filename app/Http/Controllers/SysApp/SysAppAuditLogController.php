<?php

namespace App\Http\Controllers\SysApp;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\SysAppAuditLog;
use Illuminate\Http\Request;

class SysAppAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $logsQuery = SysAppAuditLog::query()
            ->with(['admin', 'hotel'])
            ->latest('id');

        if ($request->filled('action')) {
            $logsQuery->where('action', $request->string('action')->toString());
        }

        if ($request->filled('hotel_id')) {
            $logsQuery->where('hotel_id', (int) $request->hotel_id);
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->q);

            $logsQuery->where(function ($query) use ($q) {
                $query->where('description', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        $logs = $logsQuery
            ->paginate(50)
            ->withQueryString();

        $actions = SysAppAuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $hotels = Hotel::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('sysapp.audit-logs.index', compact('logs', 'actions', 'hotels'));
    }
}