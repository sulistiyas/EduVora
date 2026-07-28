<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Parent\ParentService;
use Illuminate\Support\Facades\Auth;

class ParentFeeController extends Controller
{
    public function __construct(
        protected ParentService $parentService
    ) {}

    public function index()
    {
        $userId = Auth::id();
        $data = $this->parentService->getFeeInvoices($userId);

        return view('pages.parent.fees', $data);
    }
}
