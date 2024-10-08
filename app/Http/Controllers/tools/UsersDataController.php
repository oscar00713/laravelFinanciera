<?php

namespace App\Http\Controllers\tools;

use App\Models\User;
use App\Models\Controlpago;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersDataController extends Controller
{
    public function index()
    {
        $users = User::where('role_id', '!=', 1)->get();
        //cantidad de usuarios
        return response()->json($users);
    }

    public function indexControl()
    {
        $control = Controlpago::where('creditoTerminado', false)->get();
        return response()->json($control);
    }
}
