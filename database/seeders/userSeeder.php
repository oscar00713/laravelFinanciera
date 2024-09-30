<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class userSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //crear usuario admin
        User::create([
            'name' => 'Admin',
            'apellido' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'role_id' => 1,
            'cedula' => '2781210880009D',
            'direccion' => 'calle 123',
            'municipio' => 'ciudad',
            'sexo' => '0',
        ]);
    }
}
