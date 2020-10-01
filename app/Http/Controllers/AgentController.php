<?php


namespace App\Http\Controllers;


use App\Models\AgentLimit;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'bail|required|unique:agent_limits,agent_id',
            'limit' => 'bail|required|numeric'
        ]);;

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        return response()->json(AgentLimit::create([
            'agent_id' => $request->agent_id,
            'limit' => $request->limit,
            'balance' => $request->limit
        ]), 201);
    }

    public function updateLimit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'bail|required|exists:agent_limits,agent_id',
            'limit' => 'bail|required|numeric'
        ]);;

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        $agent = AgentLimit::agent($request->agent_id)->first();

        if ($agent->limit > $request->limit) {

            $diff = $agent->limit - $request->limit;

            $agent->balance = $agent->balance - $diff;

        } else {

            $diff = $request->limit - $agent->limit;

            $agent->balance = $agent->balance + $diff;
        }

        $agent->limit = $request->limit;

        $agent->save();

        return response()->json($agent);
    }
}
