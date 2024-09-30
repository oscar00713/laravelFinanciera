<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CantidadBillete
 *
 * @property $id
 * @property $billetes10
 * @property $billetes20
 * @property $billetes50
 * @property $billetes100
 * @property $billetes200
 * @property $billetes500
 * @property $billetes1000
 * @property $monedas1
 * @property $monedas5
 * @property $dolares1
 * @property $dolares5
 * @property $dolares10
 * @property $dolares20
 * @property $dolares50
 * @property $dolares100
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CantidadBillete extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['billetes10', 'billetes20', 'billetes50', 'billetes100', 'billetes200', 'billetes500', 'billetes1000', 'monedas1', 'monedas5', 'dolares1', 'dolares5', 'dolares10', 'dolares20', 'dolares50', 'dolares100'];


}
