<?php
 
namespace Database\Seeders;
 
use App\Models\Group;
use Illuminate\Database\Seeder;
 
class GroupSeeder extends Seeder
{
    public function run(): void
    {
        // Laura (id=2) es la maestra — ella es el owner de los grupos
        Group::insert([
            [
                'owner'       => 2,
                'period_id'   => 1,
                'name'        => 'Topicos de Calidad de Software',
                'description' => 'Revision de la IEEE 830 y sus aplicaciones',
                'active'      => true,
            ],
            [
                'owner'       => 2,
                'period_id'   => 1,
                'name'        => 'Base de Datos',
                'description' => 'Diseño y administración de bases de datos',
                'active'      => true,
            ],
        ]);
    }
}
 