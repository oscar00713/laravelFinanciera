<?php

namespace App\Http\Controllers\historial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function historial($id)
    {
        return response()->json(['message' => 'Hola']);
    }
}
