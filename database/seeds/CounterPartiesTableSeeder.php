<?php

use Illuminate\Database\Seeder;

class CounterPartiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('counter_parties')->truncate();
        DB::unprepared(file_get_contents(database_path('seeds/sql/counterparties.sql')));
    }
}
