<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\Student\DashboardService;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = Auth::user();

        $data = $this->dashboardService->getDashboardData($user->id);

        return view('pages.student.dash', $data);
    }
}
