<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = array(
            [
                'name' => 'Usuario',
                'created_at' => Carbon::now()
            ],
            [
                'name' => 'Administrador',
                'created_at' => Carbon::now()
            ]
        );

        //almacenar data
        DB::table('roles')->insert($data);
    }
}
