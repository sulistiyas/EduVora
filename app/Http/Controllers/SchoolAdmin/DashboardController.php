<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Services\SchoolAdmin\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service) {}

    public function index(): View
    {
        $user = Auth::user();
        $schoolId = getAuthSchoolId();

        $stats = $this->service->getDashboardData($schoolId, $user->id);

        return view('pages.dash.school_admin', $stats);
    }
}
