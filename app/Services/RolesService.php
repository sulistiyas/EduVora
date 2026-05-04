<?php

namespace App\Services;
use App\Repositories\RolesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RolesService
{
    protected $rolesRepository;

    public function __construct(RolesRepository $rolesRepository)
    {
        $this->rolesRepository = $rolesRepository;
    }

    public function getAllRoles($filters = []): LengthAwarePaginator|Collection
    {
        return $this->rolesRepository->getAllRoles($filters);
    }

    public function getRoleById($id)
    {
        return $this->rolesRepository->getRoleById($id);
    }

    public function createRole($data)
    {
        return $this->rolesRepository->createRole($data);
    }

    public function updateRole($id, $data)
    {
        return $this->rolesRepository->updateRole($id, $data);
    }

    public function toggleStatus($id)
    {
        return $this->rolesRepository->toggleStatus($id);
    }

    public function deleteRole($id)
    {
        return $this->rolesRepository->deleteRole($id);
    }

}