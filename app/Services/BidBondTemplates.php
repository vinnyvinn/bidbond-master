<?php

namespace App\Services;

use App\Models\BidBondTemplate;
use App\Models\Company;
use App\Models\CounterParty;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class BidBondTemplates
{
    use ApiResponser;


    public static function create()
    {
        return [
            'BidBond Reference' => 'bidbond_reference',
            'Company Name' => 'company_name',
            'Company Postal Address' => 'company_postal_address',
            'Company Postal Code' => 'company_postal_code',
            'Company County' => 'company_county',
            'Counter Party Postal Code' => 'counter_party_postal_code',
            'Counter Party County' => 'counter_party_county',
            'Currency' => 'currency',
            'Counter Party Name' => 'counter_party_name',
            'Counter Party Postal Address' => 'counter_party_postal_address',
            'Tender Addressee' => 'tender_addressee',
            'Tender Effective Date' => 'tender_effective_date',
            'Tender Expiry Date' => 'tender_expiry_date',
            'Tender Number' => 'tender_number',
            'Tender Amount in Number' => 'tender_amount_number',
            'Tender Amount in Words' => 'tender_amount_words',
            'Tender Purpose' => 'tender_purpose',
            'Todays Date' => 'todays_date',
        ];
    }

    public static function preview(Request $request)
    {

        $counterparty = CounterParty::findOrFail($request->counter_party_id);
        $company = Company::with('postal_codes')->findOrFail($request->company_id);

        $validator = Validator::make($request->all(), [
            'company' => 'required',
            'counter_party_id' => 'bail|numeric|required|exists:counter_parties,id',
            'tender_no' => 'required',
            'purpose' => 'required',
            'amount' => 'bail|required|numeric',
            'currency' => 'required',
            'period' => 'bail|required',
            'effective_date' => 'bail|required|date|date_format:Y-m-d',
            'expiry_date' => 'bail|required|date|date_format:Y-m-d',
            'template_secret' => 'bail|required|exists:bid_bond_templates,secret',
            'company_id' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }
        return view('preview.bidbond_templates.' . $request->template_secret)->with([
            'todays_date' => Carbon::now()->format('jS, F Y'),
            'counter_party_name' => $counterparty->name,
            'counter_party_postal_address' => $counterparty->postal_address,
            'tender_addressee' => $request->addressee,
            'tender_effective_date' => Carbon::parse($request->effective_date)->format('jS, F Y'),
            'tender_expiry_date' => Carbon::parse($request->expiry_date)->format('jS, F Y'),
            'currency' => $request->currency,
            'tender_number' => $request->tender_no,
            'tender_amount_number' => number_format($request->amount),
            'tender_amount_words' => ucwords(self::number_words($request->amount)),
            'tender_purpose' => $request->purpose,
            'company_name' => $request->company,
            'company_county' => $company->postal_codes->county,
            'company_postal_code' => $company->postal_codes->code,
            'company_postal_address' => $request->company_postal_address,
            'bidbond_reference' => $request->reference,
            'counter_party_postal_code' => $counterparty->postal_codes->code,
            'counter_party_county' => $counterparty->postal_codes->county
        ]);
    }

    public static function store(Array $data)
    {
        $validator = self::validator($data);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        $template = self::_store($data['name']);

        self::generateTemplate($data['content'], $template->secret);

        return self::__success($template);
    }

    public static function update($secret, Array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'bail|required|unique:bid_bond_templates,secret,' . $secret,
            'content' => 'required'
        ]);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        $template = BidBondTemplate::secret($secret)->firstOrFail();

        $template->name= $data["name"];
        $template->save();

        self::generateTemplate($data['content'], $template->secret);

        return self::__success($template);
    }

    protected static function _store($name)
    {
        return BidBondTemplate::create(['name' => $name]);
    }

    protected static function validator($data)
    {
        return Validator::make($data, [
            'name' => 'bail|required|unique:bid_bond_templates',
            'content' => 'required'
        ]);
    }

    protected static function generateTemplate($content, $name)
    {
        str_replace("@extends('default.bidbond')", "", $content);
        str_replace("@section('content')", "", $content);
        str_replace("@endsection", "", $content);
        $bidbond_content = "@extends('default.bidbond') @section('content')" . $content . "@endsection";
        $preview_content = "@extends('default.preview') @section('content')" . $content . "@endsection";
        $preview_html = new Crawler($preview_content);
        $bidbond_file_path = resource_path() . '/views/generated/bidbond_templates/' . $name . '.blade.php';
        $preview_file_path = resource_path() . '/views/preview/bidbond_templates/' . $name . '.blade.php';

        File::put($bidbond_file_path, $bidbond_content);
        File::put($preview_file_path, $preview_html->html());
    }

    public static function show($secret)
    {
        return self::_previewTemplate(BidBondTemplate::secret($secret)->firstOrFail());
    }

    public static function edit($secret)
    {
        $template = BidBondTemplate::secret($secret)->firstOrFail();
        $content = File::get(resource_path() . '/views/generated/bidbond_templates/' . $secret . '.blade.php');
        $content = str_replace("@extends('default.bidbond')", "", $content);
        $content = str_replace("@section('content')", "", $content);
        $content = str_replace("@endsection", "", $content);
        $response = [
            'template' => [
                'name' => $template->name,
                'content' => $content
            ],
            'variables' => self::create()
        ];

        return self::__success($response, 200);

    }

    protected static function _generateTemplate($template)
    {
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('generated.bidbond_templates.' . $template->secret);

        return $pdf->stream();
    }

    protected static function _previewTemplate($template)
    {
     
        $data = array_flip(self::create());
        return view('preview.bidbond_templates.' . $template->secret)->with($data);
    }

    public static function destroy($secret)
    {
        $bidbondTemplate = BidBondTemplate::secret($secret)->firstOrFail();

        $bidbondTemplate->delete();

        return self::__success('Deleted', 201);
    }

    private static function number_words($num)
    {
        $format = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        return $format->format($num);
    }
}
