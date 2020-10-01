<?php

namespace App\Services;

use App\Models\CounterParty;
use App\Models\PostalCode;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;

class CounterParties
{
    use ApiResponser;

    public static function index()
    {
        return self::__success(CounterParty::query()->select('id','name','physical_address','postal_address','postal_code_id','category_secret')->get());
    }

    public static function count()
    {
        return self::__success(CounterParty::query()->count());
    }

    public static function create()
    {
        $categories = Category::all();
        $postalcodes = PostalCode::all();

        $data = [
            'categories' => $categories,
            'postalcodes' => $postalcodes
        ];

        return self::__success($data);
    }

    public static function show($id)
    {
        return self::__success(CounterParty::query()->findOrFail($id));
    }

    public static function store(Array $data)
    {
        $validator = self::validator($data);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        return self::__success(self::_store($data));
    }

    protected static function _store($data)
    {
        return CounterParty::create([
            'name' => $data['name'],
            'physical_address' => $data['physical_address'],
            'postal_address' => $data['postal_address'],
            'category_secret' => $data['category'],
            'postal_code_id' => $data['postal_code']
        ]);
    }

    protected static function validator($data, $secret = null)
    {
        return Validator::make($data, [
            'name' => 'bail|required|unique:counter_parties,name,' . $secret,
            'physical_address' => 'required',
            'postal_address' => 'required',
            'postal_code' => 'required',
            'category' => 'bail|required|exists:categories,secret'
        ]);
    }

    public static function update($id, Array $data)
    {
        $counterparty = CounterParty::findOrFail($id);

        $validator = self::validator($data, $counterparty->id);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        self::_update($counterparty, $data);

        return self::__success($counterparty, 201);
    }

    protected static function _update($counter_party, $data)
    {
        $counter_party->update([
            'name' => $data['name'],
            'physical_address' => $data['physical_address'],
            'postal_address' => $data['postal_address'],
            'category_secret' => $data['category'],
            'postal_code_id' => $data['postal_code']
        ]);
    }

    public static function delete($id)
    {
        $counterparty = CounterParty::findorFail($id);

        $counterparty->delete();

        return self::__success('Counterparty Deleted', 200);
    }
}
