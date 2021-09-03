<?php

namespace Database\Seeders;

use App\Common\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user                       = new User();
        $user->name                 = 'Admin';
        $user->email                = 'admin@gmail.com';
        $user->phone                = 1234567891;
        $user->nationality_id       = 0;
        $user->password             = Hash::make('admin');
        $user->type                 = UserType::ADMIN;
        $user->status               = 1;
        $user->email_verified_flag  = 1;
        $user->save();
    }
}
