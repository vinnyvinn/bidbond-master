<?php

namespace App\Http\Controllers;

use App\Models\BidBondTemplate;
use Illuminate\Http\Request;
use App\Services\BidBondTemplates;

class BidBondTemplatesController extends Controller
{

    public function index()
    {
        return response()->json(BidBondTemplate::select(['secret', 'name'])->paginate());
    }

    public function store(Request $request)
    {
        return BidBondTemplates::store($request->all());
    }

    public function show($secret)
    {

        return BidBondTemplates::show($secret);
    }

    public function create()
    {
        return BidBondTemplates::create();
    }

    public function preview(Request $request)
    {
        return BidBondTemplates::preview($request);
    }

    public function edit($secret)
    {
        return BidBondTemplates::edit($secret);
    }

    public function update($secret, Request $request)
    {
        return BidBondTemplates::update($secret, $request->all());
    }

    public function destroy($secret)
    {
        return BidBondTemplates::destroy($secret);
    }
}
