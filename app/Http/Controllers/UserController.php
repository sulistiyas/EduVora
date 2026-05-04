<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        // return response()->json($this->userService->getAllUsers());

        return view('pages.users.index', ['users' => $this->userService->getAllUsers()]);
    }

    public function create(){
        
    }

    public function show($id)
    {
        return response()->json($this->userService->getUserById($id));
    }

    public function store(Request $request)
    {
        return response()->json($this->userService->createUser($request->all()));
    }

    public function update(Request $request, $id)
    {
        return response()->json($this->userService->updateUser($id, $request->all()));
    }

    public function destroy($id)
    {
        return response()->json(['success' => $this->userService->deleteUser($id)]);
    }
}
