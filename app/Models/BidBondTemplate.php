<?php

namespace App\Models;

class BidBondTemplate extends MainModel
{
    protected $fillable = [
        'name'
    ];

    public function bidbonds(){
        return $this->hasMany(Bidbond::class);
    }
}
