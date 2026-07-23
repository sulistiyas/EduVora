<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service) {}

    public function index(): View
    {
        $stats = $this->service->getStats();

        return view('pages.dash.index', $stats);
    }
}
