<?php

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('directions')->insert([
            'id' => 1,
            'contry' => 'México',
            'administrative_area_level_1' => 'Durango',
            'administrative_area_level_2' => 'Durango',
            'route' => 'Independencia',
            'street_number' => 531,
            'postal_code' => 34000,
            'lat' => NUll,
            'lng' => NUll,
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')
        ]);
        \DB::table('users')->insert([
            'id' => 1,
            'name' => 'Juan',
            'first_name' => 'Juan Fernando',
            'last_name' => 'Lozoya Valdez',
            'birthday' => '1995-08-19',
            'gender' => 'male',
            'email' => 'jlozoya1995@gmail.com',
            'password' => Hash::make('c9b1b2a7ea'),
            'Authorization' => 'yZyHYZ6VGRMNxtz5cSWmHZLTJWyf5GXIIa1SZj3RIBZjgDULHIMJE4ojV1wC',
            'confirmed' => true,
            'role' => 'admin',
            'phone' => 6181746512,
            'birthday' => '1995-08-11',
            'direction_id' => 1,
            'created_at' => date('Y-m-d H:m:s'),
            'updated_at' => date('Y-m-d H:m:s')
        ]);
        // $this->call('UsersTableSeeder');
    }
}
