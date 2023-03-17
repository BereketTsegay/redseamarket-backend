<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Featured;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data               = new Featured();
        $data->featured     = 0;
        $data->save();
    }
}
