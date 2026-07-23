<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Services\School\StudentService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(protected StudentService $service) {}

    // ─────────────────────────────────────────────────────────────
    //  Helper: ambil school_id dari admin yang login
    // ─────────────────────────────────────────────────────────────
    private function schoolId(): int
    {
        // Asumsi: admin hanya terhubung ke satu sekolah
        $school = Auth::user()->schools()->first();
        abort_unless($school, 403, 'Anda tidak terhubung ke sekolah manapun.');

        return (int) $school->school_id;
    }

    // ─────────────────────────────────────────────────────────────
    //  INDEX — list siswa
    // ─────────────────────────────────────────────────────────────
    public function index(Request $request): View|JsonResponse
    {
        $schoolId = $this->schoolId();

        if ($request->expectsJson()) {
            $filters = [
                'search' => $request->query('search'),
                'status' => $request->query('status'),
                'class_group' => $request->query('class_group'),
                'sort_by' => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page' => $request->query('per_page', 10),
            ];

            $students = $this->service->getAll($schoolId, $filters);
            $stats = $this->service->getStats($schoolId);

            if ($students instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $students->items(),
                    'meta' => [
                        'current_page' => $students->currentPage(),
                        'per_page' => $students->perPage(),
                        'total' => $students->total(),
                        'last_page' => $students->lastPage(),
                    ],
                    'stats' => $stats,
                ]);
            }

            return response()->json(['data' => $students, 'meta' => null, 'stats' => $stats]);
        }

        return view('pages.schools.users.student.index');
    }

    // ─────────────────────────────────────────────────────────────
    //  CREATE — form tambah siswa
    // ─────────────────────────────────────────────────────────────
    public function create(): View
    {
        return view('pages.schools.users.student.create');
    }

    // ─────────────────────────────────────────────────────────────
    //  STORE — simpan siswa baru
    // ─────────────────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->schoolId();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'profile' => 'nullable|array',
            'profile.nis' => 'nullable|string|max:20',
            'profile.full_name' => 'nullable|string|max:255',
            'profile.nick_name' => 'nullable|string|max:100',
            'profile.email' => 'nullable|email|max:255',
            'profile.birth_date' => 'nullable|date',
            'profile.gender' => 'nullable|in:male,female',
            'profile.phone_number' => 'nullable|string|max:20',
            'profile.address' => 'nullable|string',
            'profile.city' => 'nullable|string|max:100',
            'profile.province' => 'nullable|string|max:100',
            'profile.postal_code' => 'nullable|string|max:10',
            'profile.grade_id' => 'nullable|integer',
            'profile.class_group' => 'nullable|string|max:50',
            'profile.enrollment_date' => 'nullable|date',
            'profile.graduation_date' => 'nullable|date',
        ]);

        try {
            $student = $this->service->create($schoolId, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan.',
                'data' => $student,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: '.$e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  SHOW — data JSON satu siswa
    // ─────────────────────────────────────────────────────────────
    public function show($id): JsonResponse
    {
        try {
            $student = $this->service->getById($this->schoolId(), $id);

            return response()->json(['success' => true, 'data' => $student]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  DETAIL — halaman detail siswa
    // ─────────────────────────────────────────────────────────────
    public function detail(Request $request, $id): View|JsonResponse
    {
        $schoolId = $this->schoolId();

        try {
            $student = $this->service->getById($schoolId, $id);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $student]);
            }

            return view('pages.schools.users.student.detail', compact('student'));

        } catch (ModelNotFoundException) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'profile' => 'nullable|array',
            'profile.nis' => 'nullable|string|max:20',
            'profile.full_name' => 'nullable|string|max:255',
            'profile.nick_name' => 'nullable|string|max:100',
            'profile.birth_date' => 'nullable|date',
            'profile.gender' => 'nullable|in:male,female',
            'profile.address' => 'nullable|string',
            'profile.city' => 'nullable|string|max:100',
            'profile.province' => 'nullable|string|max:100',
            'profile.postal_code' => 'nullable|string|max:10',
            'profile.class_group' => 'nullable|string|max:50',
            'profile.enrollment_date' => 'nullable|date',
            'profile.graduation_date' => 'nullable|date',
        ]);

        try {
            $student = $this->service->update($schoolId, $id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui.',
                'data' => $student,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: '.$e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  TOGGLE STATUS
    // ─────────────────────────────────────────────────────────────
    public function toggleStatus($id): JsonResponse
    {
        $user = $this->service->toggleStatus($id);

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Siswa tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $user->status,
            'message' => $user->status === 'active' ? 'Siswa diaktifkan.' : 'Siswa dinonaktifkan.',
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
    //  CLASS GROUPS — untuk dropdown filter
    // ─────────────────────────────────────────────────────────────
    public function classGroups(): JsonResponse
    {
        $groups = $this->service->getClassGroups($this->schoolId());

        return response()->json($groups);
    }
}
