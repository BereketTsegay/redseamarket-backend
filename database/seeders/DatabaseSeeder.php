<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Prophecy\Call\Call;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(UserSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(CitySeeder::class);
        $this->call(ReservedCategorySeeder::class);
        $this->call(IconSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(CurrencySeeder::class);
    }
}
