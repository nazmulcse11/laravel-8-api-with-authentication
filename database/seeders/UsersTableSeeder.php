<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['name'=>'Nazmul Hoque','email'=>'nazmul@gmail.com','password'=>'nazmul'],
            ['name'=>'Anamul Hoque','email'=>'anamul@gmail.com','password'=>'anamul'],
            ['name'=>'Sohel Hoque','email'=>'sohel@gmail.com','password'=>'sohel'],
        ];
        User::insert($users);
    }
}
