<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 
class ParentStudentSeeder extends Seeder
{
    public function run(): void
    {
        // Brent (id=6) es padre de Pedro (id=3) y Jimena (id=4)
        DB::table('parent_student')->insert([
            ['parent_id' => 6, 'student_id' => 3],
            ['parent_id' => 6, 'student_id' => 4],
        ]);
    }
}
 