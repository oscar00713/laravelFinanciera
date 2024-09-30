<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {


        $credentials = $request->only(['email', 'password']);



        // Verificar las credenciales
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'errors' => ['El Email o el password son incorrectos']
            ], 422);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();


        // Respuesta después de autenticación
        return response()->json([
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return [
            'user' => null
        ];
    }
}
