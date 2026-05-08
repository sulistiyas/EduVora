<?php

namespace App\Repositories;

use App\Models\Academic\Room;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RoomsRepository
{
    // ─── Private Helper ────────────────────────────────────────────────────────

    private function getAuthSchoolId(): int
    {
        $schoolId = Auth::user()->schools->first()->school_id ?? null;

        if (!$schoolId) {
            throw new \Exception('Admin tidak terkait dengan sekolah manapun.');
        }

        return $schoolId;
    }

    // ─── Read ──────────────────────────────────────────────────────────────────

    public function getAllRooms(array $filters = []): LengthAwarePaginator|Collection
    {
        $schoolId = $this->getAuthSchoolId();

        $query = Room::query()
            ->select([
                'school_id',
                'room_id',
                'room_name',
                'code',
                'type',
                'floor',
                'building',
                'capacity',
                'facility',
                'status',
            ])
            ->with([
                'school' => function ($q) {
                    $q->select([
                        'school_id',
                        'school_name',
                        'school_type',
                        'status',
                    ]);
                }
            ])
            ->whereHas('school', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });

        // 🔍 Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('room_name', 'ILIKE', "%{$search}%")
                  ->orWhereHas('school', function ($q2) use ($search) {
                      $q2->where('school_name', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // 🎯 Filter status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // 🎯 Filter per school
        if (!empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        // 🎯 Filter per type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // 🎯 Filter per floor
        if (!empty($filters['floor'])) {
            $query->where('floor', $filters['floor']);
        }

        // 🎯 Filter per building
        if (!empty($filters['building'])) {
            $query->where('building', $filters['building']);
        }

        // 🔽 Sorting
        $allowedSort = ['room_name', 'status', 'code', 'type', 'floor', 'building', 'capacity', 'facilitiy', 'created_at'];
        $sortBy      = in_array($filters['sort_by'] ?? '', $allowedSort)
            ? $filters['sort_by']
            : 'created_at';
        $sortOrder   = $filters['sort_order'] ?? 'desc';

        $query->orderBy($sortBy, $sortOrder);

        // 📄 Pagination
        $perPage = $filters['per_page'] ?? 10;

        if ($perPage === 'all') {
            return $query->get();
        }

        return $query->paginate((int) $perPage)->withQueryString();
    }

    public function getRoomById($id): ?Room
    {
        return Room::with([
            'school' => function ($q) {
                $q->select([
                    'school_id',
                    'school_name',
                    'school_type',
                    'status',
                ]);
            }
        ])->find($id);
    }

    // ─── Write ─────────────────────────────────────────────────────────────────

    public function createRoom(array $data): Room
    {
        $data['school_id'] = $this->getAuthSchoolId();
        return Room::create($data);
    }

    public function updateRoom($id, array $data): ?Room
    {
        $room = Room::whereHas('school', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);

        if (!$room) return null;

        $room->update($data);
        return $room;
    }

    // ─── Toggle Status ─────────────────────────────────────────────────────────

    public function toggleStatus($id): ?Room
    {
        $room = Room::with('school')->find($id);

        if (!$room) return null;

        // Ownership check — pastikan room ini milik sekolah yang login
        if ($room->school->school_id !== $this->getAuthSchoolId()) {
            throw new \Exception('Akses ditolak.');
        }

        $statusFlow = [
            'available'   => 'maintenance',
            'maintenance' => 'inactive',
            'inactive'    => 'available',
        ];

        $room->status = $statusFlow[$room->status] ?? 'available';
        $room->save();

        return $room;
    }

    // ─── Delete ────────────────────────────────────────────────────────────────

    public function deleteRoom($id): bool
    {
        $room = Room::whereHas('school', function ($q) {
            $q->where('school_id', $this->getAuthSchoolId());
        })->find($id);

        if (!$room) return false;

        $room->delete();
        return true;
    }
}