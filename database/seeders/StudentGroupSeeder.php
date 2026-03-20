<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 
class StudentGroupSeeder extends Seeder
{
    public function run(): void
    {
        // Inscribir alumnos en los grupos
        // Pedro (3), Jimena (4), Emiliano (5), Ana (7) en Topicos de Calidad de Software (grupo 1)
        // Pedro (3), Jimena (4) en Base de Datos (grupo 2)
        DB::table('student_groups')->insert([
            ['student_id' => 3, 'group_id' => 1, 'active' => true],
            ['student_id' => 4, 'group_id' => 1, 'active' => true],
            ['student_id' => 5, 'group_id' => 1, 'active' => true],
            ['student_id' => 7, 'group_id' => 1, 'active' => true],
            ['student_id' => 3, 'group_id' => 2, 'active' => true],
            ['student_id' => 4, 'group_id' => 2, 'active' => true],
        ]);
    }
}
 