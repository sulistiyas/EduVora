<?php

namespace App\Repositories;
use App\Models\Core\SchoolProfiles;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SchoolRepository
{
    public function getAllSchools(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = SchoolProfiles::query()
            ->select([
                'school_id',
                'school_name',
                'school_type',
                'status',

                'contact_email',
                'contact_phone',
                'website',
                'kkm_default',

                'npsn',
                'nss',
                'accreditation',

                'province',
                'city',
                'district',
                'postal_code',

                'headmaster_name',
                'headmaster_nip',

                'address',

                'created_at',
                'updated_at',
            ]);

        // 🔍 Search (hanya jalan kalau ada input)
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(school_name) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(school_type) LIKE ?', ["%{$search}%"]);
            });
        }

        // 🎯 Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🔽 Sorting (whitelist biar aman)
        $allowedSort = ['school_name', 'school_type', 'status', 'created_at'];
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

    public function getSchoolById($id)
    {
        return SchoolProfiles::find($id);
    }

    public function createSchool($data)
    {
        return SchoolProfiles::create($data);
    }

    public function updateSchool($id, $data)
    {
        $schoolProfiles = SchoolProfiles::find($id);
        if ($schoolProfiles) {
            $schoolProfiles->update($data);
            return $schoolProfiles;
        }
        return null;
    }

    public function toggleStatus($id): ?SchoolProfiles
    {
        $school = SchoolProfiles::find($id);
        if ($school) {
            $school->update([
                'status' => $school->status === 'active' ? 'inactive' : 'active',
            ]);
            return $school;
        }
        return null;
    }

    public function deleteSchool($id)
    {
        $schoolProfiles = SchoolProfiles::find($id);
        if ($schoolProfiles) {
            $schoolProfiles->delete();
            return true;
        }
        return false;
    }
}