<?php

namespace App\Services\SuperAdmin;

use App\Repositories\SuperAdmin\AuditLogRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditLogService
{
    public function __construct(protected AuditLogRepository $repo) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repo->getAll($filters);
    }

    public function getDistinctTables(): array
    {
        return $this->repo->getDistinctTables();
    }
}
