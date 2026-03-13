<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                "name" => "Admin",
                "lastname" => "Administrador",
                "email" => "admin@gmail.com",
                "password" => Hash::make("adminadmin"),
                "cellphone" => "11111111111",
                "active" => true,
                "role_id" => 1
            ],
            [
                "name" => "Laura Elena",
                "lastname" => "Castañeda Davila",
                "email" => "laura@gmail.com",
                "password" => Hash::make("lauralaura"),
                "cellphone" => "8711381969",
                "active" => true,
                "role_id" => 2
            ],
            [
                "name" => "Pedro Abraham",
                "lastname" => "Martell Vazquez",
                "email" => "pedro@gmail.com",
                "password" => Hash::make("pedropedro"),
                "cellphone" => "8713518648",
                "active" => true,
                "role_id" => 2
            ],
            [
                "name" => "Jimena Itiel",
                "lastname" => "Flores Lerma",
                "email" => "jimena@gmail.com",
                "password" => Hash::make("jimenajimena"),
                "cellphone" => "8711341714",
                "active" => true,
                "role_id" => 2
            ],
            [
                "name" => "Emiliano",
                "lastname" => "Pacheco",
                "email" => "emiliano@gmail.com",
                "password" => Hash::make("emilianoemiliano"),
                "cellphone" => "4421124127",
                "active" => true,
                "role_id" => 2
            ],
            [
                "name" => "Brent",
                "lastname" => "Faiyaz",
                "email" => "brent@gmail.com",
                "password" => Hash::make("brentbrent"),
                "cellphone" => "8711341715",
                "active" => true,
                "role_id" => 2
            ],
            [
                "name" => "Ana Lilia",
                "lastname" => "Hernandez Viesca",
                "email" => "ana@gmail.com",
                "password" => Hash::make("anaana"),
                "cellphone" => "8711353535",
                "active" => true,
                "role_id" => 3
            ]
        ]);
    }
}
