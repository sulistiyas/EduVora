<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Services\School\TeacherService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function __construct(protected TeacherService $service) {}

    // ─────────────────────────────────────────────────────────────
    //  Helper: ambil school_id dari admin yang login
    // ─────────────────────────────────────────────────────────────
    private function schoolId(): int
    {
        $school = Auth::user()->schools()->first();
        abort_unless($school, 403, 'Anda tidak terhubung ke sekolah manapun.');
        return (int) $school->school_id;
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX — list guru
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request): View|JsonResponse
    {
        $schoolId = $this->schoolId();

        if ($request->expectsJson()) {
            $filters = [
                'search'        => $request->query('search'),
                'status'        => $request->query('status'),
                'subject'       => $request->query('subject'),
                'employee_type' => $request->query('employee_type'),
                'sort_by'       => $request->query('sort_by'),
                'sort_order'    => $request->query('sort_order'),
                'per_page'      => $request->query('per_page', 10),
            ];

            $teachers = $this->service->getAll($schoolId, $filters);
            $stats    = $this->service->getStats($schoolId);

            if ($teachers instanceof LengthAwarePaginator) {
                return response()->json([
                    'data'  => $teachers->items(),
                    'meta'  => [
                        'current_page' => $teachers->currentPage(),
                        'per_page'     => $teachers->perPage(),
                        'total'        => $teachers->total(),
                        'last_page'    => $teachers->lastPage(),
                    ],
                    'stats' => $stats,
                ]);
            }

            return response()->json(['data' => $teachers, 'meta' => null, 'stats' => $stats]);
        }

        return view('pages.schools.users.teacher.index');
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE — form tambah guru
    // ─────────────────────────────────────────────────────────────
    public function create(): View
    {
        return view('pages.schools.users.teacher.create');
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE — simpan guru baru
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->schoolId();

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone_number'          => 'nullable|string|max:20',
            'password'              => 'required|string|min:8|confirmed',
            'status'                => 'required|in:active,inactive',
            'profile'               => 'nullable|array',
            'profile.nip'           => 'nullable|string|max:30',
            'profile.full_name'     => 'nullable|string|max:255',
            'profile.nick_name'     => 'nullable|string|max:100',
            'profile.birth_date'    => 'nullable|date',
            'profile.gender'        => 'nullable|in:male,female',
            'profile.phone_number'  => 'nullable|string|max:20',
            'profile.address'       => 'nullable|string',
            'profile.city'          => 'nullable|string|max:100',
            'profile.province'      => 'nullable|string|max:100',
            'profile.postal_code'   => 'nullable|string|max:10',
            'profile.subject'       => 'nullable|string|max:150',
            'profile.employee_type' => 'nullable|in:permanent,honorary,contract',
            'profile.join_date'     => 'nullable|date',
            'profile.resign_date'   => 'nullable|date',
        ]);

        try {
            $teacher = $this->service->create($schoolId, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Guru berhasil ditambahkan.',
                'data'    => $teacher,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  SHOW — data JSON satu guru
    // ─────────────────────────────────────────────────────────────
    public function show($id): JsonResponse
    {
        try {
            $teacher = $this->service->getById($this->schoolId(), $id);
            return response()->json(['success' => true, 'data' => $teacher]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  DETAIL — halaman detail guru
    // ─────────────────────────────────────────────────────────────
    public function detail(Request $request, $id): View|JsonResponse
    {
        $schoolId = $this->schoolId();

        try {
            $teacher = $this->service->getById($schoolId, $id);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $teacher]);
            }

            return view('pages.schools.users.teacher.detail', compact('teacher'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
            }
            abort(404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  UPDATE
    // ─────────────────────────────────────────────────────────────
    public function update(Request $request, $id): JsonResponse
    {
        $schoolId = $this->schoolId();

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $id,
            'phone_number'          => 'nullable|string|max:20',
            'password'              => 'nullable|string|min:8|confirmed',
            'status'                => 'required|in:active,inactive',
            'profile'               => 'nullable|array',
            'profile.nip'           => 'nullable|string|max:30',
            'profile.full_name'     => 'nullable|string|max:255',
            'profile.nick_name'     => 'nullable|string|max:100',
            'profile.birth_date'    => 'nullable|date',
            'profile.gender'        => 'nullable|in:male,female',
            'profile.address'       => 'nullable|string',
            'profile.city'          => 'nullable|string|max:100',
            'profile.province'      => 'nullable|string|max:100',
            'profile.postal_code'   => 'nullable|string|max:10',
            'profile.subject'       => 'nullable|string|max:150',
            'profile.employee_type' => 'nullable|in:permanent,honorary,contract',
            'profile.join_date'     => 'nullable|date',
            'profile.resign_date'   => 'nullable|date',
        ]);

        try {
            $teacher = $this->service->update($schoolId, $id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Data guru berhasil diperbarui.',
                'data'    => $teacher,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  TOGGLE STATUS
    // ─────────────────────────────────────────────────────────────
    public function toggleStatus($id): JsonResponse
    {
        $user = $this->service->toggleStatus($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Guru tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status'  => $user->status,
            'message' => $user->status === 'active' ? 'Guru diaktifkan.' : 'Guru dinonaktifkan.',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    //  DESTROY
    // ─────────────────────────────────────────────────────────────
    public function destroy($id): JsonResponse
    {
        return response()->json(['success' => $this->service->delete($id)]);
    }

    // ─────────────────────────────────────────────────────────────
    //  SUBJECTS — untuk dropdown filter
    // ─────────────────────────────────────────────────────────────
    public function subjects(): JsonResponse
    {
        $subjects = $this->service->getSubjects($this->schoolId());
        return response()->json($subjects);
    }
}