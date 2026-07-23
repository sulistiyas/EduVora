<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(protected AuditLogService $service) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $filters = [
                'search'     => $request->query('search'),
                'table_name' => $request->query('table_name'),
                'user_id'    => $request->query('user_id'),
                'date_from'  => $request->query('date_from'),
                'date_to'    => $request->query('date_to'),
                'sort_by'    => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page'   => $request->query('per_page', 15),
            ];

            $logs = $this->service->getAll($filters);

            return response()->json([
                'data' => $logs->items(),
                'meta' => [
                    'current_page' => $logs->currentPage(),
                    'per_page'     => $logs->perPage(),
                    'total'        => $logs->total(),
                    'last_page'    => $logs->lastPage(),
                ],
            ]);
        }

        $tables = $this->service->getDistinctTables();

        return view('pages.audit-logs.index', compact('tables'));
    }
}
