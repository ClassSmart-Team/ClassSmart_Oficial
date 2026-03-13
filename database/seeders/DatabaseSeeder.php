<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PeriodSeeder::class,
            GroupSeeder::class,

        ]);

        //User::factory(10)->create();

        /*User::factory(5)->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
    }
}
