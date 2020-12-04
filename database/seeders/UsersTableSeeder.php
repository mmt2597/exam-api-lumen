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
        // User::factory(User::class, 100)->create();
        for($i = 0; $i <= 100; $i++) {
        	User::insert([
        		'username' => 'admin' . $i,
        		'email' => 'admin' . $i . '@gmail.com',
        		'firstname' => 'admin' . $i,
        		'lastname' =>  'admin' . $i,
        		'password' => app('hash')->make('admin1234')
        	]);
        }
    }
}
