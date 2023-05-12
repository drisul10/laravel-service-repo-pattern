<?php

namespace App\Http\Controllers;

use App\Models\User;

/**
 * @OA\Tag(
 *     name="User",
 *     description="API endpoints of user"
 * )
 */
class UserController extends Controller
{
    /**
     * Get list users (pagination)
     * @OA\Get(
     *     path="/api/user/list-page",
     *     tags={"User"},
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function listPage()
    {
        $users = User::all();
        return response()->json($users);
    }
}
