<?php
 
namespace Database\Seeders;
 
use App\Models\Period;
use Illuminate\Database\Seeder;
 
class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        Period::insert([
            [
                'name'       => 'Semestre 1',
                'year'       => 2026,
                'start_date' => '2026-01-01',
                'end_date'   => '2026-06-30',
            ],
            [
                'name'       => 'Semestre 2',
                'year'       => 2026,
                'start_date' => '2026-07-01',
                'end_date'   => '2026-12-31',
            ],
        ]);
    }
}

