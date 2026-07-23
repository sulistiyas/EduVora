<?php

namespace App\Repositories\SuperAdmin;

use App\Models\Core\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogRepository
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = AuditLog::with('user:id,name,email');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('action', 'ILIKE', "%{$search}%")
                    ->orWhere('table_name', 'ILIKE', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'ILIKE', "%{$search}%"));
            });
        }

        if (!empty($filters['table_name'])) {
            $query->where('table_name', $filters['table_name']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) ($filters['per_page'] ?? 15);
        return $query->paginate($perPage)->withQueryString();
    }

    public function getDistinctTables(): array
    {
        return AuditLog::distinct()
            ->whereNotNull('table_name')
            ->pluck('table_name')
            ->toArray();
    }
}
