<?php

namespace App\Services;

use App\Models\Academic\Room;
use App\Repositories\RoomsRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoomsService
{
    private $roomsRepository;

    public function __construct(RoomsRepository $roomsRepository)
    {
        $this->roomsRepository = $roomsRepository;
    }

    public function getAllRooms(array $filters = []): LengthAwarePaginator|Collection
    {
        return $this->roomsRepository->getAllRooms($filters);
    }

    public function getRoomById($id): ?Room
    {
        return $this->roomsRepository->getRoomById($id);
    }

    public function createRoom(array $data): Room
    {
        return $this->roomsRepository->createRoom($data);
    }

    public function updateRoom($id, array $data): ?Room
    {
        return $this->roomsRepository->updateRoom($id, $data);
    }

    public function toggleStatus($id): ?Room
    {
        return $this->roomsRepository->toggleStatus($id);
    }

    public function deleteRoom($id): bool
    {
        return $this->roomsRepository->deleteRoom($id);
    }
}