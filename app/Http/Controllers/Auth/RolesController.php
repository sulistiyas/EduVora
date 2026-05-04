<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RolesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class RolesController extends Controller
{
    protected $rolesService;

    public function __construct(RolesService $rolesService)
    {
        $this->rolesService = $rolesService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {

            $filters = [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'sort_by' => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page' => $request->query('per_page', 10),
            ];

            $roles = $this->rolesService->getAllRoles($filters);

            if ($roles instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $roles->items(),
                    'meta' => [
                        'current_page' => $roles->currentPage(),
                        'per_page' => $roles->perPage(),
                        'total' => $roles->total(),
                        'last_page' => $roles->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $roles,
                'meta' => null,
            ]);
        }

        return view('pages.roles.index');
    }

    public function create(){
        
    }

    public function show($id)
    {
        return response()->json($this->rolesService->getRoleById($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->rolesService->createRole($request->all()));
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->rolesService->updateRole($id, $request->all()));
    }

    public function toggleStatus($id): JsonResponse
    {
        $role = $this->rolesService->toggleStatus($id);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $role->status,   // 'active' | 'inactive'
            'message' => $role->status === 'active' ? 'Role diaktifkan.' : 'Role dinonaktifkan.',
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->rolesService->deleteRole($id)]);
    }

}
