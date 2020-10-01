<?php

namespace App\Http\Controllers;

use App\Services\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        return Categories::index();
    }

    public function store(Request $request)
    {
    	return Categories::store($request->name);
    }

    public function show($secret)
    {
        return Categories::show($secret);
    }

    public function update($secret, Request $request)
    {
    	return Categories::update($secret, $request->name);
    }

    public function delete($secret)
    {
    	return Categories::delete($secret);
    }
}
