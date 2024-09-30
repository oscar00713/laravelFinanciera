<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class usersController extends Controller
{
    public function index()
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters(['name', 'apellido', 'status'])
            ->with('fiadorUser')  // Cargar la relación 'user'
            ->orderBy('id', 'desc')           // Ordenar la tabla
            ->jsonPaginate();

        // Modificar la estructura de la respuesta
        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'fiador_name' => $user->fiadorUser->name ?? 'Sin fiador',
                'telephone' => $user->telephone,
                'direccion' => $user->direccion,
                'municipio' => $user->municipio,
                'sexo' => $user->sexo,
                // Añadir otros campos que necesites
            ];
        });

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): User
    {

        // Obtener los datos validados del request
        $validatedData = $request->validated();

        // Encriptar la contraseña
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        // Crear y devolver el usuario
        return User::create($validatedData);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user): User
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): User
    {
        $user->update($request->validated());

        return $user;
    }

    public function destroy(User $user): Response
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); //Deshabilitar las restricciones de foreign keys
        $user->delete();

        return response()->noContent();
    }
}
