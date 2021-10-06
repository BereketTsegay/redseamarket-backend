<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $task           = new Task();
        $task->name     = 'Manage Authority';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage User';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Testimonial';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Category';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Subcategory';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Ads';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Payment';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Featured Dealer';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Reject Reason';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Icons';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Social Links';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Terms & Conditions';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Privacy Policy';
        $task->save();

        $task           = new Task();
        $task->name     = 'Manage Banners';
        $task->save();
    }
}
