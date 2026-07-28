<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Parent\ParentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentScoreController extends Controller
{
    public function __construct(
        protected ParentService $parentService
    ) {}

    public function index(Request $request)
    {
        $userId = Auth::id();
        $semesterId = $request->input('semester_id');

        $data = $this->parentService->getScoreSummary($userId, $semesterId);

        return view('pages.parent.scores', $data);
    }
}
