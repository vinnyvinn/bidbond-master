<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $fillable = [
        'name', 'code', 'constituency', 'county', 'country'
    ];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
