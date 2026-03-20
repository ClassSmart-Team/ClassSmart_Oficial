<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
 
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,        // 1. Roles primero — users depende de esto
            UserSeeder::class,        // 2. Usuarios — grupos depende de esto
            PeriodSeeder::class,      // 3. Periodos — grupos depende de esto
            GroupSeeder::class,       // 4. Grupos — unidades y student_groups dependen de esto
            StudentGroupSeeder::class,// 5. Alumnos en grupos
            ParentStudentSeeder::class,// 6. Padres vinculados a hijos
            UnitSeeder::class,        // 7. Unidades — tareas dependen de esto
        ]);
    }
}
