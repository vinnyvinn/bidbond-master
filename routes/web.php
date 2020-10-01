<?php
use Illuminate\Support\Facades\Artisan;

$router->get('categories', 'CategoriesController@index');
$router->post('categories', 'CategoriesController@store');
$router->get('categories/{secret}', 'CategoriesController@show');
$router->put('categories/{secret}', 'CategoriesController@update');
$router->delete('categories/{secret}', 'CategoriesController@delete');

$router->get('counter-parties', 'CounterPartiesController@index');
$router->post('counter-parties', 'CounterPartiesController@store');
$router->get('counter-parties/count', 'CounterPartiesController@count');
$router->get('counter-parties/{id}', 'CounterPartiesController@show');
$router->put('counter-parties/{id}', 'CounterPartiesController@update');
$router->post('counter-parties/{id}', 'CounterPartiesController@delete');
$router->get('createcounterdetails', 'CounterPartiesController@createcounterdetails');


$router->get('bid-bond-templates', 'BidBondTemplatesController@index');
$router->post('bid-bond-templates', 'BidBondTemplatesController@store');
$router->post('bid-bond-templates/preview', 'BidBondTemplatesController@preview');
$router->get('bid-bond-templates/create', 'BidBondTemplatesController@create');
$router->get('bid-bond-templates/{secret}', 'BidBondTemplatesController@show');
$router->get('bid-bond-templates/{secret}/edit', 'BidBondTemplatesController@edit');
$router->put('bid-bond-templates/{secret}', 'BidBondTemplatesController@update');
$router->post('bid-bond-templates/{secret}', 'BidBondTemplatesController@destroy');

$router->get('bid-bonds', 'BidBondsController@index');
$router->get('all-bid-bonds', 'BidBondsController@allBids');
$router->post('bid-bonds', 'BidBondsController@store');
$router->post('bid-bonds/secret', 'BidBondsController@getBidBonds');
$router->post('apply-bidbond', 'BidBondsController@applyBid');
$router->get('bid-bonds/id/{id}', 'BidBondsController@edit');
$router->put('bid-bonds/{id}', 'BidBondsController@update');
$router->put('bid-bonds/{id}', 'BidBondsController@update');
$router->get('bid-bonds/{secret}', 'BidBondsController@show');
$router->get('get-bond/{secret}', 'BidBondsController@getBidBond');
$router->post('get-bond-by-tender', 'BidBondsController@getByTender');
$router->post('obtainUserBidBonds', 'BidBondsController@obtain');
$router->post('user/bid-bonds', 'BidBondsController@getByUser');
$router->post('agent/bid-bonds', 'BidBondsController@getByAgent');
$router->put('update-bidbonds', 'BidBondsController@updateDate');

$router->get('companies', 'CompanyController@index');
$router->post('companies', 'CompanyController@store');
$router->put('companies', 'CompanyController@update');
$router->delete('companies', 'CompanyController@destroy');
$router->get('agent/companies', 'CompanyController@agentCompanyList');
$router->get('{agent_id}/companies', 'CompanyController@agentCompanies');
$router->get('{agent_id}/companies/options', 'CompanyController@agentCompanyOptions');
$router->get('companies/{company_id}', 'CompanyController@show');
$router->post('company/limits', 'CompanyController@updateLimit');

$router->post('agent/limits', 'AgentController@store');
$router->put('agent/limits', 'AgentController@updateLimit');

$router->get('settings', 'SettingsController@index');
$router->post('settings', 'SettingsController@update');

$router->post('count-bidbonds', 'BidBondsController@countBids');
$router->post('tender-info', 'BidBondsController@getTenderInfo');

$router->get('migrate', function () {
    Artisan::call('migrate:fresh --seed');
    return response()->json(["message" => "bidbond migrate fresh success"]);
});
