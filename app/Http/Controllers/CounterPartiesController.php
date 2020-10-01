<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CounterParties;;

class CounterPartiesController extends Controller
{
    public function index()
    {
        return CounterParties::index();
    }

    public function count()
    {
        return CounterParties::count();
    }

    public function store(Request $request)
    {
    	return CounterParties::store($request->all());
    }

    public function createcounterdetails()
    {
        return CounterParties::create();
    }

    public function show($id)
    {
    	return CounterParties::show($id);
    }

    public function update($id, Request $request)
    {
    	return CounterParties::update($id, $request->all());
    }

    public function delete($id)
    {
    	return CounterParties::delete($id);
    }
}
