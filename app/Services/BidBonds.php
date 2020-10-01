<?php

namespace App\Services;


use App\Models\AgentLimit;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\BidBondPrice;
use App\Models\Bidbond;
use App\Traits\ApiResponser;
use Carbon\Carbon;

class BidBonds
{
    use ApiResponser;

    public static function index(Request $request)
    {
        $bidbonds = Bidbond::select('bidbonds.id', 'companies.name as company_name', 'bidbonds.secret as bidbond_secret',
            'bidbonds.paid as bidbond_paid', 'bidbonds.addressee as tender_addressee',
            'bidbonds.effective_date as tender_effective_date', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount',
            'bidbonds.purpose as tender_purpose', 'counter_parties.name as counter_party_name',
            'counter_parties.postal_address as counter_party_postal_address', 'bidbonds.company_id as company_id',
            'bidbonds.template_secret as template_secret')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->join('companies', 'companies.id', '=', 'bidbonds.company_id')
            ->paginate($request->per_page ?? 15);

        return response()->json($bidbonds);
    }


    public static function store(Array $data)
    {

//        if ($validator->fails()) {
//            info('falied');
//            return self::__error($validator->errors()->all(), 422);
//        }
        if (!Arr::exists($data, 'agent_id')) {
            $data['agent_id'] = null;
        }

        if (self::_limitExceeded($data['amount'], $data['agent_id'], $data['company'])) {
            return self::__error('Limit exceeded!, please use a lesser amount', 422);
        }
        if (Arr::has($data, 'reference')) {
          //  $bid_bond = Bidbond::where('company_id', $data['company'])->where('tender_no', $data['tender_number'])->first();
            $bid_bond = Bidbond::where('tender_no', $data['tender_number'])->first();

            if ($bid_bond->paid == 1) {
                return self::__success($bid_bond);
            }

            $bid_bond->update([
                'tender_no' => $data['tender_number'],
                'purpose' => $data['tender_purpose'],
                'addressee' => $data['addressee'],
                'effective_date' => $data['start_date'],
                'expiry_date' => Carbon::createFromFormat('Y-m-d', $data['start_date'])->addDays($data['period']),
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'period' => $data['period'],
                'company_id' => $data['company'],
                'counter_party_id' => $data['counter_party'],
                'charge' => $data['charge'],
                'template_secret' => $data['template'],
                'created_by' => $data['created_by'],
                'agent_id' => $data['agent_id']
            ]);

        } else {
            do {
                $unique_id = uniqid();
            } while (Bidbond::secret($unique_id)->count() > 0);

            $bid_bond = Bidbond::create([
                'tender_no' => $data['tender_number'],
                'purpose' => $data['tender_purpose'],
                'addressee' => $data['addressee'],
                'effective_date' => $data['start_date'],
                'expiry_date' => Carbon::createFromFormat('Y-m-d', $data['start_date'])->addDays($data['period']),
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'period' => $data['period'],
                'company_id' => $data['company'],
                'counter_party_id' => $data['counter_party'],
                'charge' => $data['charge'],
                'template_secret' => $data['template'],
                'created_by' => $data['created_by'],
                'agent_id' => $data['agent_id'],
                'secret' => $unique_id
            ]);

        }

        return self::__success($bid_bond);
    }


    protected static function _limitExceeded($amount, $agent_id, $company_id)
    {
        $bid_total_amount = Cache::rememberForever(Setting::bidbond_total, function () {
            return Setting::option(Setting::bidbond_total)->first()->value;
        });

        $current_limit = Cache::rememberForever(Setting::bank_limit, function () {
            return Setting::option(Setting::bank_limit)->first()->value;
        });

        $company_limit = Cache::rememberForever(Setting::company_limit, function () {
            return Setting::option(Setting::company_limit)->first()->value;
        });

        $balance = bcsub($current_limit, $bid_total_amount);

        if ($balance < $amount || $company_limit < $amount) {
            return true;
        }

        if ($agent_id) {
            $company_limit = AgentLimit::agent($agent_id)->first()->balance;
        } else {
            $company_limit = Company::where('id', $company_id)->first()->balance;
        }

        if ($balance < $amount || $company_limit < $amount) {
            return true;
        }


        return false;
    }

    protected static function _validator(Array $data)
    {
        return Validator::make($data, [
            'company' => 'required',
            'counter_party' => 'bail|numeric|required|exists:counter_parties,id',
            'tender_number' => 'required',
            'tender_purpose' => 'required',
            'amount' => 'bail|required|numeric',
            'currency' => 'required',
            'period' => 'required',
            'start_date' => 'bail|required|date|date_format:Y-m-d',
            'template' => 'bail|required|exists:bid_bond_templates,secret',
            'created_by' => 'bail|required|numeric',
            'charge' => 'required|numeric',
            'agent_id' => 'sometimes'
        ]);

    }
    public static function show($secret)
    {
        //
        $query = Bidbond::secret($secret);

        if (!$query->exists()) {
            return self::__error('Not Found', 404);
        }

        $bid = $query->first();

        if (!$bid->paid) {
            return self::__error('Payment processing ongoing please wait a few seconds and refresh to view generated Bid bond', 422);
        }

        $bid_bond = Bidbond::secret($secret)->select(
            'companies.name as company_name',
            'companies.postal_address as company_postal_address',
            'bidbonds.created_by as user_id',
            'bidbonds.secret as bidbond_secret',
            'bidbonds.reference as bidbond_reference',
            'bidbonds.paid as bidbond_paid',
            'bidbonds.addressee as tender_addressee',
            'bidbonds.effective_date',
            'bidbonds.expiry_date',
            'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number',
            'bidbonds.amount as tender_amount',
            'bidbonds.currency',
            'bidbonds.purpose as tender_purpose',
            'counter_parties.name as counter_party_name',
            'counter_parties.postal_address as counter_party_postal_address',
            'postal_codes.code as counter_party_postal_code',
            'postal_codes.county as counter_party_county',
            'bidbonds.company_id as company_id',
            'bidbonds.template_secret as template_secret')
            ->join('companies', 'companies.id', '=', 'bidbonds.company_id')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->join('postal_codes', 'postal_codes.id', '=', 'counter_parties.postal_code_id')
            ->first();

        $company = Company::find($bid_bond->company_id);

        $amountInWord = ucwords(self::number_words($bid_bond->tender_amount));

        return view('generated.bidbond_templates.' . $bid_bond->template_secret)->with([
            'todays_date' => Carbon::now()->format('jS, F Y'),
            'counter_party_name' => $bid_bond->counter_party_name,
            'counter_party_postal_address' => $bid_bond->counter_party_postal_address,
            'counter_party_postal_code' => $bid_bond->counter_party_postal_code,
            'counter_party_county' => $bid_bond->counter_party_county,
            'tender_addressee' => $bid_bond->tender_addressee,
            'tender_effective_date' => $bid_bond->effective_date->format('jS, F Y'),
            'tender_expiry_date' => $bid_bond->expiry_date->format('jS, F Y'),
            'tender_number' => $bid_bond->tender_number,
            'tender_amount_number' => number_format($bid_bond->tender_amount),
            'tender_amount_words' => $amountInWord,
            'currency' => $bid_bond->currency,
            'tender_purpose' => $bid_bond->tender_purpose,
            'company_county' => $company->postal_codes->county,
            'company_postal_code' => $company->postal_codes->code,
            'company_name' => $bid_bond->company_name,
            'company_id' => $bid_bond->company_id,
            'company_postal_address' => $bid_bond->company_postal_address,
            'bidbond_reference' => $bid_bond->bidbond_reference,
            'bidbond_secret' => $bid_bond->bidbond_secret,
        ]);
    }

    public static function getBidBond($secret)
    {
        info('secret++++++++');
        info($secret);
        $query = Bidbond::secret($secret);

        if (!$query->exists()) {
            return self::__error('Not Found', 404);
        }
        
        $bidbond = $query->select('bidbonds.id', 'bidbonds.secret as bidbond_secret', 'bidbonds.addressee as tender_addressee',
            'bidbonds.effective_date as tender_effective_date', 'bidbonds.expiry_date', 'bidbonds.agent_id', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount', 'bidbonds.currency',
            'bidbonds.purpose as tender_purpose', 'counter_parties.name as counter_party_name',
            'counter_parties.postal_address as counter_party_postal_address', 'bidbonds.company_id as company_id',
            'bidbonds.template_secret as template_secret', 'bidbonds.created_by as userid')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->first();
       return self::__success($bidbond);
    }

    public static function edit($id)
    {
        Bidbond::findOrFail($id);

        $bidbond = Bidbond::select('bidbonds.id', 'bidbonds.secret as bidbond_secret', 'bidbonds.addressee',
            'bidbonds.effective_date', 'bidbonds.expiry_date', 'bidbonds.period', 'bidbonds.company_id', 'bidbonds.reference',
            'bidbonds.tender_no', 'bidbonds.amount as amount', 'bidbonds.currency', 'bidbonds.purpose', 'companies.postal_address as company_postal_address',
            'bidbonds.counter_party_id', 'companies.name as company', 'bidbonds.template_secret as template_secret')
            ->join('counter_parties', 'counter_parties.id', 'bidbonds.counter_party_id')
            ->join('companies', 'companies.id', 'bidbonds.company_id')
            ->join('postal_codes', 'postal_codes.id', 'companies.postal_code_id')
            ->where('bidbonds.id', $id)
            ->first();

        return self::__success($bidbond);
    }

    public static function getBidBonds($data)
    {
        $bidbonds = Bidbond::select('bidbonds.secret as bidbond_secret', 'bidbonds.addressee as tender_addressee', 'bidbonds.effective_date as tender_effective_date', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount', 'bidbonds.currency', 'bidbonds.purpose as tender_purpose',
            'counter_parties.name as counter_party_name', 'counter_parties.postal_address as counter_party_postal_address',
            'bidbonds.company_id as company_id', 'bidbonds.template_secret as template_secret', 'bidbonds.created_by as userid')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->whereIn('bidbonds.secret', $data['secrets'])
            ->orderby('bidbonds.id', 'desc')
            ->get();

        return self::__success($bidbonds);
    }

    public static function updateDate($data)
    {
        $bid_bond = Bidbond::secret($data['secret'])->firstOrFail();

        $bid_bond->update([
            'effective_date' => $data['start_date'],
            'expiry_date' => Carbon::createFromFormat('Y-m-d', $data['start_date'])->addDays($bid_bond->period),
        ]);

        return self::__success($bid_bond);
    }

    private static function number_words($num)
    {
        $format = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);

        return $format->format($num);
    }
}
