<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Parent\ParentService;
use Illuminate\Support\Facades\Auth;

class ParentProfileController extends Controller
{
    public function __construct(
        protected ParentService $parentService
    ) {}

    public function index()
    {
        $userId = Auth::id();
        $childData = $this->parentService->getChildData($userId);

        return view('pages.parent.profile', [
            'has_student' => (bool) $childData,
            'childData' => $childData,
        ]);
    }
}
