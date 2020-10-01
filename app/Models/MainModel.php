<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Classes\ModelSecret;

class MainModel extends Model
{
	use SoftDeletes, ModelSecret;

    protected $hidden = [
    	'created_at', 'updated_at', 'deleted_at'
    ];

    public static function secret($secret)
    {
    	return self::where('secret', '=', $secret);
    }
}
