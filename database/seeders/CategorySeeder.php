<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' =>'Programming',
                'slug'=> 'programming',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ],
            [
                'name' =>'Digital Marketing',
                'slug'=> 'digital-marketing',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ],
            [
                'name' =>'Product Design',
                'slug'=> 'product-design',
                'created_at'=> Carbon::now(),
                'updated_at'=> Carbon::now(),
            ],
        ]);
    }
}