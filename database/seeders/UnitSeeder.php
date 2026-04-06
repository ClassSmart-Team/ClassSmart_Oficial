<?php
 
namespace Database\Seeders;
 
use App\Models\Unit;
use Illuminate\Database\Seeder;
 
class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Unit::insert([
            // Unidades de Topicos de Calidad de Software (grupo 1)
            [
                'group_id'   => 1,
                'name'       => 'Unidad 1',
                'start_date' => '2026-01-01',
                'end_date'   => '2026-02-28',
            ],
            [
                'group_id'   => 1,
                'name'       => 'Unidad 2',
                'start_date' => '2026-03-01',
                'end_date'   => '2026-04-30',
            ],
            [
                'group_id'   => 1,
                'name'       => 'Unidad 3',
                'start_date' => '2026-05-01',
                'end_date'   => '2026-06-30',
            ],
            // Unidades de Base de Datos (grupo 2)
            [
                'group_id'   => 2,
                'name'       => 'Unidad 1',
                'start_date' => '2026-01-01',
                'end_date'   => '2026-02-28',
            ],
            [
                'group_id'   => 2,
                'name'       => 'Unidad 2',
                'start_date' => '2026-03-01',
                'end_date'   => '2026-04-30',
            ],
            [
                'group_id'   => 2,
                'name'       => 'Unidad 3',
                'start_date' => '2026-05-01',
                'end_date'   => '2026-06-30',
            ],
        ]);
    }
}