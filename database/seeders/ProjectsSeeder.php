<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('projects')->delete();

        DB::table('projects')->insert(array(
            0 => array(
                'id' => Str::uuid()->toString(),
                'name' => 'First Project',
                'created_at' => now(),
                'updated_at' => now()
            ),

            1 => array(
                'id' => Str::uuid()->toString(),
                'name' => 'Second Project',
                'created_at' => now(),
                'updated_at' => now()
            ),
            2 => array(
                'id' => Str::uuid()->toString(),
                'name' => 'Third Project',
                'created_at' => now(),
                'updated_at' => now()
            )
        ));
    }
}
