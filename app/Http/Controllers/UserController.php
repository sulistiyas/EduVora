<?php

namespace App\Http\Controllers;

use App\Models\Core\Role;
use App\Models\Core\SchoolProfiles;
use App\Models\Core\User;
use App\Services\UserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function roles():JsonResponse{
        $roles = Role::select('roles.role_id', 'roles.role_name')
        ->orderBy('role_name','asc')
        ->get();

        return response()->json($roles);
    }

    public function schools():JsonResponse{
        $schools = SchoolProfiles::select('school_profiles.school_id', 'school_profiles.school_name')
        ->orderBy('school_name','asc')
        ->get();

        return response()->json($schools);
    }
    
    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {

            $filters = [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'role' => $request->query('role'),
                'sort_by' => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page' => $request->query('per_page', 10),
            ];

            $users = $this->userService->getAllUsers($filters);

            $baseQuery = User::query();
            $status = [
                'active' => (clone $baseQuery)->where('status', 'active')->count(),
                'inactive' => (clone $baseQuery)->where('status', 'inactive')->count(),
                'unverified' => (clone $baseQuery)->whereNull('email_verified_at')->count(),
            ];
            // ✅ Handle paginator vs collection
            if ($users instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $users->items(),
                    'meta' => [
                        'current_page' => $users->currentPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                        'last_page' => $users->lastPage(),
                    ],
                    'status'=> $status
                ]);
            }

            return response()->json([
                'data' => $users,
                'meta' => null,
                'status'=> $status
            ]);
        }

        return view('pages.users.index');
    }

    public function create(){
        return view('pages.users.create');
    }

    public function detail(Request $request, $id): JsonResponse|View
    {
        try{
            $user = $this->userService->getUserById($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $user,
            ]);
        }
        return view('pages.users.detail', compact('user'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                ], 404);
            }
            abort(404);
        }
    }
    

    public function show($id)
    {
        return response()->json($this->userService->getUserById($id));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone_number'          => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|exists:roles,role_id',
            'school'                => 'required|exists:school_profiles,school_id',
            'status'                => 'required|in:active,inactive',
            'profile'               => 'nullable|array',
            // Teacher profile rules
            'profile.nip'               => 'nullable|string|max:50',
            'profile.nik'               => 'nullable|string|max:20',
            'profile.full_name'         => 'nullable|string|max:255',
            'profile.birth_place'       => 'nullable|string|max:100',
            'profile.birth_date'        => 'nullable|date',
            'profile.gender'            => 'nullable|in:male,female',
            'profile.religion'          => 'nullable|string|max:50',
            'profile.address'           => 'nullable|string',
            'profile.phone'             => 'nullable|string|max:20',
            'profile.email'             => 'nullable|email|max:255',
            'profile.employment_status' => 'nullable|string|max:50',
            'profile.position'          => 'nullable|string|max:100',
            'profile.grade_level'       => 'nullable|string|max:20',
            'profile.education_level'   => 'nullable|string|max:10',
            'profile.major'             => 'nullable|string|max:100',
            'profile.certification'     => 'nullable|string|max:100',
            'profile.npwp'              => 'nullable|string|max:20',
            'profile.join_date'         => 'nullable|date',
            // Student profile rules
            'profile.nis'               => 'nullable|string|max:20',
            'profile.nick_name'         => 'nullable|string|max:100',
            'profile.phone_number'      => 'nullable|string|max:20',
            'profile.city'              => 'nullable|string|max:100',
            'profile.province'          => 'nullable|string|max:100',
            'profile.postal_code'       => 'nullable|string|max:10',
            'profile.grade_id'          => 'nullable|integer',
            'profile.class_group'       => 'nullable|string|max:50',
            'profile.enrollment_date'   => 'nullable|date',
            'profile.graduation_date'   => 'nullable|date',
        ]);
    
        try {
            $user = $this->userService->createUser($validated);
    
            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan.',
                'data'    => $user,
            ], 201);
    
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $id,
            'phone_number'          => 'nullable|string|max:20',
            'password'              => 'nullable|string|min:8|confirmed',
            'role'                  => 'nullable|exists:roles,role_id',
            'school'                => 'nullable|exists:school_profiles,school_id',
            'profile'               => 'nullable|array',
            // Teacher
            'profile.nip'               => 'nullable|string|max:50',
            'profile.nik'               => 'nullable|string|max:20',
            'profile.full_name'         => 'nullable|string|max:255',
            'profile.birth_place'       => 'nullable|string|max:100',
            'profile.birth_date'        => 'nullable|date',
            'profile.gender'            => 'nullable|string|max:20',
            'profile.religion'          => 'nullable|string|max:50',
            'profile.address'           => 'nullable|string',
            'profile.employment_status' => 'nullable|string|max:50',
            'profile.position'          => 'nullable|string|max:100',
            'profile.education_level'   => 'nullable|string|max:10',
            'profile.major'             => 'nullable|string|max:100',
            'profile.certification'     => 'nullable|string|max:100',
            'profile.join_date'         => 'nullable|date',
            // Student
            'profile.nis'               => 'nullable|string|max:20',
            'profile.class_group'       => 'nullable|string|max:50',
            'profile.enrollment_date'   => 'nullable|date',
            'profile.city'              => 'nullable|string|max:100',
            'profile.province'          => 'nullable|string|max:100',
        ]);

        try {
            $user = $this->userService->updateUser($id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diperbarui.',
                'data'    => $user,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus($id): JsonResponse
    {
        $users = $this->userService->toggleStatus($id);

        if (!$users) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $users->status,   // 'active' | 'inactive'
            'message' => $users->status === 'active' ? 'User diaktifkan.' : 'User dinonaktifkan.',
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->userService->deleteUser($id)]);
    }
}
