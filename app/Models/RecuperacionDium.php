<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RecuperacionDium
 *
 * @property $id
 * @property $montoCordobas
 * @property $montoDolares
 * @property $gastos
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class RecuperacionDium extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['montoCordobas', 'montoDolares', 'gastos'];


}
