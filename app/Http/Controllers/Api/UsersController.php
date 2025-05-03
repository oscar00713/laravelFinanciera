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
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{
    public function index()
    {
        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::callback('name', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        // Buscar tanto en 'name' como en 'apellido'
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('apellido', 'like', "%{$value}%")
                            ->orWhere('cedula', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::exact('activo')->default(true),
            ])
            ->with('fiadorUser')  // Cargar la relación 'user'
            ->where('role_id', '!=', 1)  // Omitir usuarios con role_id = 1
            ->orderBy('id', 'desc')           // Ordenar la tabla
            ->jsonPaginate();

        // Modificar la estructura de la respuesta
        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'apellido' => $user->apellido,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'fiador_name' => $user->fiadorUser->name ?? 'Sin fiador',
                'fiador_apellido' => $user->fiadorUser->apellido ?? '',
                'telephone' => $user->telephone,
                'telefono' => $user->telefono,
                'direccion' => $user->direccion,
                'municipio' => $user->municipio,
                'sexo' => $user->sexo,
                'activo' => $user->activo,
                'cedula' => $user->cedula,
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

    public function destroy(User $user): JsonResponse
    {
        //Deshabilitar las restricciones de foreign keys
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
