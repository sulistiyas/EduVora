<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Parent\ParentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentAttendanceController extends Controller
{
    public function __construct(
        protected ParentService $parentService
    ) {}

    public function index(Request $request)
    {
        $userId = Auth::id();
        $filters = $request->only(['semester_id', 'status', 'month']);

        $data = $this->parentService->getAttendanceHistory($userId, $filters);

        return view('pages.parent.attendance', $data);
    }
}
