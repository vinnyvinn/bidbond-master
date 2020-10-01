<?php


namespace App\Models;


use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    const TYPES = [
        'user' => 'user',
        'agent' => 'agent'
    ];

    public function scopeType($builder, $type): void
    {
        $builder->where('type', $type);
    }

    public function bidbonds()
    {
        return $this->belongsTo(Bidbond::class);
    }

    public function postal_codes()
    {
        return $this->belongsTo(PostalCode::class,'postal_code_id','id');
    }



}
