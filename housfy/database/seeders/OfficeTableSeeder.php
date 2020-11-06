<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Office;

class OfficeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //public function run()
        //
        //
        
		Office::factory()->count(50)->create();
    }
}
