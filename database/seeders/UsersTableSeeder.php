<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = bcrypt('saddam123');

        DB::table('users')->insert([
            'name' => 'saddam',
            'email' => 'saddamhk567@gmail.com',
            'password' => $password
        ]);
    }
}
