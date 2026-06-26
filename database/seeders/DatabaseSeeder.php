<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GroupSeeder::class,
            ForumConfigSeeder::class,
            AdminUserSeeder::class,
            CategorySeeder::class,
            TestDataSeeder::class,
        ]);
    }
}
