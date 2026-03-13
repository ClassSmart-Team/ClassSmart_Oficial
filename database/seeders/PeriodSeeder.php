<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        Period::insert([
            [
                "name" => "Semestre 1",
                "year" => 2023,
                "start_date" => "2023-01-10",
                "end_date" => "2023-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2023,
                "start_date" => "2023-07-10",
                "end_date" => "2023-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2024,
                "start_date" => "2024-01-10",
                "end_date" => "2024-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2024,
                "start_date" => "2024-07-10",
                "end_date" => "2024-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2025,
                "start_date" => "2025-01-10",
                "end_date" => "2025-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2025,
                "start_date" => "2025-07-10",
                "end_date" => "2025-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2026,
                "start_date" => "2026-01-10",
                "end_date" => "2026-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2026,
                "start_date" => "2026-07-10",
                "end_date" => "2026-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2027,
                "start_date" => "2027-01-10",
                "end_date" => "2027-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2027,
                "start_date" => "2027-07-10",
                "end_date" => "2027-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2028,
                "start_date" => "2028-01-10",
                "end_date" => "2028-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2028,
                "start_date" => "2028-07-10",
                "end_date" => "2028-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2029,
                "start_date" => "2029-01-10",
                "end_date" => "2029-06-30"
            ],
            [
                "name" => "Semestre 2",
                "year" => 2029,
                "start_date" => "2029-07-10",
                "end_date" => "2029-12-20"
            ],
            [
                "name" => "Semestre 1",
                "year" => 2030,
                "start_date" => "2030-01-10",
                "end_date" => "2030-06-30"
            ],
        ]);
    }
}
