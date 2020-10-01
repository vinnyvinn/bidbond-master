<?php
$router->get('/', function ()  {
    $data = array_flip(\App\Services\BidBondTemplates::create());
    return view('generated.bidbond_templates.5eea7020cfc13')->with($data);
});
