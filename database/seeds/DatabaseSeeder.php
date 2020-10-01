<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('PostalCodesTableSeeder');
        $this->call('CategoriesTableSeeder');
        $this->call('CounterPartiesTableSeeder');
        $this->call('SettingsTableSeeder');
        $this->call('BidBondTemplatesTableSeeder');
    }
}
