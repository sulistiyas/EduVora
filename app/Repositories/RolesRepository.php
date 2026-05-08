<?php

namespace App\Repositories;
use App\Models\Core\Role;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RolesRepository
{
    public function getAllRoles(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Role::query()
            ->select([
                'role_id',
                'role_name',
                'role_description',
                'status',
            ]);

        // 🔍 Search (hanya kalau ada input)
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);

            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(role_name) ILIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(role_description) ILIKE ?', ["%{$search}%"]);
            });
        }

        // 🎯 Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🔽 Sorting (whitelist biar aman)
        $allowedSort = ['role_name', 'status', 'created_at'];
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

    public function getRoleById($id)
    {
        return Role::find($id);
    }

    public function createRole($data)
    {
        return Role::create($data);
    }

    public function updateRole($id, $data)
    {
        $role = Role::find($id);
        if ($role) {
            $role->update($data);
            return $role;
        }
        return null;
    }

    public function toggleStatus($id): ?Role
    {
        $role = Role::find($id);
        if ($role) {
            $role->update([
                'status' => $role->status === 'active' ? 'inactive' : 'active',
            ]);
            return $role;
        }
        return null;
    }

    public function deleteRole($id)
    {
        $role = Role::find($id);
        if ($role) {
            $role->delete();
            return true;
        }
        return false;
    }
}