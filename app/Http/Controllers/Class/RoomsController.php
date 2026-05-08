<?php

namespace App\Http\Controllers\Class;

use App\Http\Controllers\Controller;
use App\Services\RoomsService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomsController extends Controller
{
    protected $roomsService;

    public function __construct(RoomsService $roomsService)
    {
        $this->roomsService = $roomsService;
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {

            $filters = [
                'search'     => $request->query('search'),
                'status'     => $request->query('status'),
                'type'       => $request->query('type'),
                'floor'      => $request->query('floor'),
                'building'   => $request->query('building'),
                'sort_by'    => $request->query('sort_by'),
                'sort_order' => $request->query('sort_order'),
                'per_page'   => $request->query('per_page', 10),
            ];

            $rooms = $this->roomsService->getAllRooms($filters);

            if ($rooms instanceof LengthAwarePaginator) {
                return response()->json([
                    'data' => $rooms->items(),
                    'meta' => [
                        'current_page' => $rooms->currentPage(),
                        'per_page'     => $rooms->perPage(),
                        'total'        => $rooms->total(),
                        'last_page'    => $rooms->lastPage(),
                    ],
                ]);
            }

            return response()->json([
                'data' => $rooms,
                'meta' => null,
            ]);
        }

        return view('pages.schools.class.rooms.index');
    }

    public function show($id): JsonResponse
    {
        $room = $this->roomsService->getRoomById($id);

        if (!$room) {
            return response()->json(['message' => 'Ruangan tidak ditemukan.'], 404);
        }

        return response()->json($room);
    }

    public function store(Request $request): JsonResponse
    {
        
        $room = $this->roomsService->createRoom($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil ditambahkan.',
            'data'    => $room,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $room = $this->roomsService->updateRoom($id, $request->all());

        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Ruangan tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil diperbarui.',
            'data'    => $room,
        ]);
    }

    public function toggleStatus($id): JsonResponse
    {
        $room = $this->roomsService->toggleStatus($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.'
            ], 404);
        }

        $statusMessages = [
            'available'   => 'Ruangan sekarang tersedia.',
            'maintenance' => 'Ruangan masuk maintenance.',
            'inactive'    => 'Ruangan dinonaktifkan.',
        ];

        return response()->json([
            'success' => true,
            'status'  => $room->status,
            'message' => $statusMessages[$room->status] ?? 'Status berhasil diperbarui.',
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->roomsService->deleteRoom($id);

        if (!$deleted) {
            return response()->json(['success' => false, 'message' => 'Ruangan tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ruangan berhasil dihapus.',
        ]);
    }
}