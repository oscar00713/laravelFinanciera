<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Observers\AbonoObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Class Abono
 *
 * @property $id
 * @property $usuario_id
 * @property $controlpago_id
 * @property $numAbono
 * @property $fechaProximoAbono
 * @property $montoAbono
 * @property $estado
 * @property $interesAbono
 * @property $total
 * @property $created_at
 * @property $updated_at
 *
 * @property Controlpago $controlpago
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Abono extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['usuario_id', 'controlpago_id', 'numAbono', 'fechaProximoAbono', 'montoAbono', 'estado', 'interesAbono', 'total'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function controlpago()
    {
        return $this->belongsTo(Controlpago::class, 'controlpago_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
