<?php 

namespace App\Repositories\Academic;

use App\Models\Academic\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SubjectsRepository 
{
    private function getAuthSchoolId(): int
    {
        $schoolId = Auth::user()->schools->first()->school_id ?? null;

        if (!$schoolId) {
            throw new \Exception('Admin tidak terkait dengan sekolah manapun.');
        }

        return $schoolId;
    }

    public function getAllSubjects(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Subject::query()
            ->select([
                'id',
                'subject_name',
                'subject_code',
                'category',
                'credits',
                'hours_per_week',
                'description',
                'status',
            ])
            ->where('school_id', $this->getAuthSchoolId());
        // 🎯 Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🔍 Search
        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $query->where(function ($sub) use ($q) {
                $sub->where('subject_name', 'like', "%{$q}%")
                    ->orWhere('subject_code', 'like', "%{$q}%")
                    ->orWhere('category',     'like', "%{$q}%");
            });
        }

        // 🔽 Sorting (whitelist biar aman)
        $allowedSort = ['subject_name', 'status', 'created_at'];
        $sortBy = in_array($filters['sort_by'] ?? '', $allowedSort)
            ? $filters['sort_by']
            : 'created_at';

        $sortOrder = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        // 📄 Pagination
        $perPage = $filters['per_page'] ?? 10;

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage)->withQueryString();

    }

    public function getSubjectById($id)
    {
        return Subject::find($id);
    }

    public function createSubject($data)
    {
        $data['school_id'] = $this->getAuthSchoolId();
        return Subject::create($data);
    }

    public function updateSubject($id, $data)
    {
        $subjects = Subject::where('id', $id)->where('school_id', $this->getAuthSchoolId() ?? null)->first();
        if ($subjects) {
            $subjects->update($data);
            return $subjects;
        }
        return null;
    }

    public function toggleStatus($id): ?Subject
    {
        $students = Subject::find($id);
        if ($students) {
            $students->update([
                'status' => $students->status === 'active' ? 'inactive' : 'active',
            ]);
            return $students;
        }
        return null;
    }

    public function deleteSubject($id)
    {
        $subjects = Subject::where('id', $id)->where('school_id', $this->getAuthSchoolId() ?? null)->first();
        if ($subjects) {
            $subjects->delete();
            return true;
        }
        return false;
    }
}