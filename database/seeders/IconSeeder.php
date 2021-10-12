<?php

namespace Database\Seeders;

use App\Common\Status;
use App\Models\IconClass;
use Illuminate\Database\Seeder;

class IconSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $icon               = new IconClass();
        $icon->name         = 'fa fa-facebook';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 1;
        $icon->save();

        $icon               = new IconClass();
        $icon->name         = 'fa fa-twitter';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 2;
        $icon->save();

        $icon               = new IconClass();
        $icon->name         = 'fa fa-instagram';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 3;
        $icon->save();

        $icon               = new IconClass();
        $icon->name         = 'fa fa-linkedin';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 4;
        $icon->save();

        $icon               = new IconClass();
        $icon->name         = 'fa fa-whatsapp';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 4;
        $icon->save();

        $icon               = new IconClass();
        $icon->name         = 'fas fa-envelope';
        $icon->status       = Status::ACTIVE;
        $icon->sort_order   = 4;
        $icon->save();
    }
}
