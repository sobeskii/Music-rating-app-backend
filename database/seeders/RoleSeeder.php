<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['role'=>'user']);
        Role::create(['role'=>'admin']);
        Role::create(['role'=>'banned']);
    }
}
