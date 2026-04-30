<?php

namespace App\Repositories;
use App\Models\Core\User;

class UserRepository
{
    public function getAllUsers()
    {
        return User::with(
            [
                'roles',
                'schools',
                'student',
                'teacher'
            ])->get();
    }
    

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function createUser($data)
    {
        return User::create($data);
    }

    public function updateUser($id, $data)
    {
        $user = User::find($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return true;
        }
        return false;
    }
}