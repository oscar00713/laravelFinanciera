<?php

namespace Database\Seeders;

use App\Models\CantidadBillete;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class cantidadBilletesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CantidadBillete::create([
            'billetes10' => 0,
            'billetes20' => 0,
            'billetes50' => 0,
            'billetes100' => 0,
            'billetes200' => 0,
            'billetes500' => 0,
            'billetes1000' => 0,
            'monedas1' => 0,
            'monedas5' => 0,
            'dolares1' => 0,
            'dolares5' => 0,
            'dolares10' => 0,
            'dolares20' => 0,
            'dolares50' => 0,
            'dolares100' => 0,
        ]);
    }
}
