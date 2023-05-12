<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    // Get all users
    public function listPage()
    {
        $users = User::all();
        return response()->json($users);
    }
}
