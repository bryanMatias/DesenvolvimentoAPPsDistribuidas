<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function GetAllUsers()
    {
        $users = User::all();
        return $users;
    }
}
