<?php

use Illuminate\Database\Seeder;
use DB as DataBase;

class PostalCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DataBase::unprepared(file_get_contents(database_path('seeds/sql/postal_codes.sql')));
    }
}
