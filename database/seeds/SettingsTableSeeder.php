<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::firstorCreate(['option' => Setting::bank_limit], ['value' => 100000000]);

        Setting::firstorCreate(['option' => Setting::company_limit], ['value' => 30000000]);

        Setting::firstorCreate(['option' => Setting::mpf], ['value' => 0.2]);

        Setting::firstorCreate(['option' => Setting::other], ['value' => 0.8]);

        Setting::firstorCreate(['option' => Setting::bidbond_total], ['value' => 0]);
    }
}
