<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Controlpago
 *
 * @property $id
 * @property $usuario_id
 * @property $concepto
 * @property $frecuencia
 * @property $plazo
 * @property $cuotas
 * @property $status
 * @property $diaCobro
 * @property $fechaContrato
 * @property $mes
 * @property $montoPrestado
 * @property $interes
 * @property $primerCobro
 * @property $cuota
 * @property $totalInteres
 * @property $creditoTerminado
 * @property $total
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @property Abono[] $abonos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Controlpago extends Model
{

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = ['usuario_id', 'concepto', 'frecuencia', 'plazo', 'cuotas', 'status', 'diaCobro', 'fechaContrato', 'mes', 'montoPrestado', 'interes', 'primerCobro', 'cuota', 'totalInteres', 'creditoTerminado', 'total'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function abonos()
    // {
    //     return $this->hasMany(Abono::class, 'id', 'controlpago_id');
    // }

    public function abonos()
    {
        return $this->hasMany(Abono::class, 'controlpago_id');
    }
}
