<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Fields;
use Illuminate\Database\Seeder;

class ReservedCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category                   = new Category();
        $category->name             = 'Motors';
        $category->canonical_name   = 'motors';
        $category->description      = 'test';
        $category->image            = 'motor.png';
        // $category->icon_class_id    = 1;
        $category->display_flag     = 1;
        $category->status           = 1;
        $category->sort_order       = 1;
        $category->reserved_flag    = 1;
        $category->save();

        $category                   = new Category();
        $category->name             = 'Property for Rent';
        $category->canonical_name   = 'property-for-rent';
        $category->description      = 'test';
        $category->image            = 'property-for-rent.png';
        // $category->icon_class_id    = 1;
        $category->display_flag     = 1;
        $category->status           = 1;
        $category->sort_order       = 1;
        $category->reserved_flag    = 1;
        $category->save();

        $category                   = new Category();
        $category->name             = 'Property for Sale';
        $category->canonical_name   = 'property-for-sale';
        $category->description      = 'test';
        $category->image            = 'property-for-sale.png';
        // $category->icon_class_id    = 1;
        $category->display_flag     = 1;
        $category->status           = 1;
        $category->sort_order       = 1;
        $category->reserved_flag    = 1;
        $category->save();
    }
}
