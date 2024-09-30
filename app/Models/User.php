<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class User
 *
 * @property $id
 * @property $name
 * @property $email
 * @property $role_id
 * @property $telephone
 * @property $direccion
 * @property $municipio
 * @property $sexo
 * @property $fiador_id
 * @property $fiador
 * @property $email_verified_at
 * @property $password
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @property Abono[] $abonos
 * @property Controlpago[] $controlpagos
 * @property User[] $users
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = ['name', 'email', 'role_id', 'telephone', 'direccion', 'municipio', 'sexo', 'fiador_id', 'fiador'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fiadorUser()
    {
        return $this->belongsTo(User::class, 'fiador_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function abonos()
    {
        return $this->hasMany(Abono::class, 'id', 'usuario_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function controlpagos()
    {
        return $this->hasMany(Controlpago::class, 'usuario_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'fiador_id');
    }
}
