<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

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

        ]);

        //User::factory(10)->create();

        /*User::factory(5)->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
    }
}
