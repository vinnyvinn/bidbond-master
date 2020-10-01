<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CounterParty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'physical_address', 'postal_address', 'postal_code_id', 'category_secret'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return HasMany
     */
    public function bidbonds(){
        return $this->hasMany(Bidbond::class);
    }

    public function postal_codes(){
        return $this->belongsTo(PostalCode::class,'postal_code_id','id');
    }
}
