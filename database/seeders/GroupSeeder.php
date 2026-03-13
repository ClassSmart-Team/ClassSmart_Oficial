<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        Group::insert([
            [
                "owner" => 2,
                "period_id" => 1,
                "name" => "Programacion 1",
                "description" => "Grupo de programacion basica",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 2,
                "name" => "Programacion 2",
                "description" => "Programacion intermedia",
                "active" => true
            ],
            [
                "owner" => 6,
                "period_id" => 3,
                "name" => "Base de Datos",
                "description" => "Fundamentos de bases de datos",
                "active" => true
            ],
            [
                "owner" => 7,
                "period_id" => 4,
                "name" => "Redes",
                "description" => "Introduccion a redes",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 5,
                "name" => "Estructuras de Datos",
                "description" => "Listas, pilas y colas",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 6,
                "name" => "Algoritmos",
                "description" => "Analisis de algoritmos",
                "active" => true
            ],
            [
                "owner" => 4,
                "period_id" => 7,
                "name" => "Desarrollo Web",
                "description" => "HTML, CSS y JS",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 8,
                "name" => "Laravel",
                "description" => "Framework PHP",
                "active" => true
            ],
            [
                "owner" => 5,
                "period_id" => 9,
                "name" => "Vue",
                "description" => "Framework frontend",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 10,
                "name" => "APIs",
                "description" => "Creacion de APIs REST",
                "active" => true
            ],
            [
                "owner" => 3,
                "period_id" => 11,
                "name" => "Seguridad",
                "description" => "Seguridad en aplicaciones",
                "active" => true
            ],
            [
                "owner" => 4,
                "period_id" => 12,
                "name" => "DevOps",
                "description" => "Integracion continua",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 13,
                "name" => "Docker",
                "description" => "Contenedores",
                "active" => true
            ],
            [
                "owner" => 2,
                "period_id" => 14,
                "name" => "Testing",
                "description" => "Pruebas automatizadas",
                "active" => true
            ],
            [
                "owner" => 6,
                "period_id" => 15,
                "name" => "Arquitectura",
                "description" => "Arquitectura de software",
                "active" => true
            ]
        ]);
    }
}
