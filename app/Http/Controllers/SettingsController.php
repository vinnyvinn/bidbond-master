<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{

    use ApiResponser;

    public function index()
    {
        return Setting::all()->flatMap(function ($setting) {
            return [
                $setting['option'] => $setting->value
            ];
        });
    }

    public function update(Request $request)
    {
        foreach ($request->all() as $key => $value) {

            $set = Setting::option($key)->firstOrFail();

            $set->update(['value' => $value]);

            if (Cache::has($set->option)) {
                $option = Cache::get($set->option);
                if ($option !== $value) {
                    Cache::forget($set->option);
                    Cache::rememberForever($set->option, function () use ($value) {
                        return $value;
                    });
                }
            }else{
                Cache::rememberForever($set->option, function () use ($value) {
                    return $value;
                });
            }
        }
        return 'updated';
    }

}
