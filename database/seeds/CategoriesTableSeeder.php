<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::firstOrCreate(['name' => 'Counties', 'secret' => '5ef4d25597081']);
        Category::firstOrCreate(['name' => 'Ministries', 'secret' => '5ef4d255979fb']);
        Category::firstOrCreate(['name' => 'Parastatals', 'secret' => '5ef4d255983c0']);
        Category::firstOrCreate(['name' => 'Private Company', 'secret' => '5ef4d25598e69']);
        Category::firstOrCreate(['name' => 'NGO', 'secret' => '5ef4d25599688']);
        Category::firstOrCreate(['name' => 'Universities', 'secret' => '5ef4d2559a0a2']);
        Category::firstOrCreate(['name' => 'Banks', 'secret' => '5ef4d2559ad1e']);
    }
}
