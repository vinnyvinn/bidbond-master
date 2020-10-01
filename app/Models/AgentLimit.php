<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class AgentLimit extends Model
{
    protected $guarded = [];

    public function scopeAgent($query, $agent_id)
    {
        return $query->where("agent_id", $agent_id);
    }

}
