<?php

namespace App\Http\Controllers;

use App\Models\Bidbond;
use App\Models\Company;
use App\Models\Setting;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    use ApiResponser;

    public function index()
    {
        return self::__success(Company::all());
    }

    public function store(Request $request)
    {
        info("company_store",$request->all());
        $validator = Validator::make($request->all(), [
            'crp' => 'bail|required',
            'name' => 'bail|required',
            'postal_address' => 'required',
            'postal_code_id' => 'bail|required|exists:postal_codes,id',
            'type' => 'required',
            'agent_id' => 'required_if:type,agent',
            'id' => 'bail|required_if:type,user|unique:companies,id',
        ]);;

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        $data = $validator;

        $data['limit'] = Cache::rememberForever(Setting::company_limit, function () {
            return Setting::option(Setting::company_limit)->first()->value;
        });;

        $data['balance'] = $data['limit'];

        if ($request->type == 'agent') {

            $company = Company::where('name', $request->name)->first();

            if ($company) {
                $data['id'] = $company->id;
            } else {
                $data['id'] = $this->getUniqueId();
                Company::create(Arr::except($data, ['agent_id']));
            }

        } else {
            Company::create(Arr::except($data, ['agent_id']));
        }

        //attach company to agent
        if ($request->agent_id) {
            $agent = DB::table('agent_companies')->where('agent_id', $request->agent_id)->where('company_id', $data['id'])->first();
            if (!$agent) {
                DB::table('agent_companies')->insert([
                    'agent_id' => $request->agent_id,
                    'company_id' => $data['id'],
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }
        }

    return response()->json(["message" => "Company created successfully", "code" => 201]);
    }

    public function show($company_id)
    {
        $company = Company::select('id as company_unique_id', 'name', 'balance', 'limit','type')->where('id', $company_id)->firstorFail();
        return response()->json($company);

    }

    public function update(Request $request)
    {
        $data = $this->validate($request, [
            'company_id' => 'bail|required|exists:companies,id',
            'crp' => 'bail|required',
            'name' => 'bail|required',
            'postal_address' => 'required',
            'postal_code_id' => 'bail|required|exists:postal_codes,id',
            'agent_id' => 'bail|sometimes|exists:agent_companies,agent_id'
        ]);

        if (!Arr::has($data, 'agent_id')) {
            $company = Company::whereId($data['company_id'])->type('user')->first();
        }else{

            $exists = DB::table('agent_companies')->where('agent_id', $data['agent_id'])->where('company_id', $data['company_id'])->exists();

            if (!$exists) {
                return self::__error('No such company found for agent', 400);
            }

            $company = Company::whereId($request->company_id)->type('agent')->first();
        }

        $company->update(Arr::except($data, ['company_id', 'agent_id']));

        return response()->json(["company" => $company, "code" => 200]);
    }

    public function destroy(Request $request)
    {
        $data = $this->validate($request, [
            'company_id' => 'bail|required|exists:companies,id',
            'agent_id' => 'bail|required|exists:agent_companies,agent_id'
        ]);

        $agent_company = DB::table('agent_companies')->where('agent_id', $data['agent_id'])->where('company_id', $data['company_id']);

        if (!$agent_company->exists()) {
            return self::__error('No such company found for agent', 400);
        }

        $company = Company::whereId($data['company_id'])->type('agent')->first();

        $bidbond = Bidbond::where('company_id', $data['company_id'])->exists();

        if ($bidbond) {
            return self::__error('Cannot delete a company with a Bid bond', 400);
        }

        $agent_company->delete();
        $company->delete();

        return response()->json(["message" => "Company deleted successfully", "code" => 200]);
    }

    public function updateLimit(Request $request)
    {
        $bank_limit = Cache::get(Setting::bank_limit, function () {
            return Setting::option(Setting::bank_limit)->value;
        });

        $validator = Validator::make($request->all(), [
            'company_id' => 'bail|required|exists:companies,id',
            'limit' => 'bail|required|numeric|lte:' . $bank_limit
        ]);

        if ($validator->fails()) {
            return self::__error($validator->errors()->all(), 422);
        }

        $company = Company::whereId($request->company_id)->first();

        if ($company->limit > $request->limit) {

            $diff = $company->limit - $request->limit;

            $company->balance = $company->balance - $diff;

        } else {
            $diff = $request->limit - $company->limit;

            $company->balance = $company->balance + $diff;
        }

        $company->update(['limit' => $request->limit, 'balance' => $company->balance]);

        $company->refresh();

        return response()->json(["company" => $company, "code" => 200]);
    }

    public function agentCompanyList()
    {
        $companies = Company::select('companies.id as company_unique_id', 'companies.crp', 'companies.name', 'postal_address',
            'postal_code_id', 'postal_codes.code as postal_code', 'postal_codes.county as county')
            ->type('agent')
            ->join('postal_codes', 'postal_codes.id', '=', 'companies.postal_code_id')
            ->orderby('companies.created_at', 'desc')
            ->paginate();

        return response()->json($companies);
    }

    public function agentCompanies($agent_id)
    {
        $companies = Company::select('companies.id as company_unique_id', 'companies.crp', 'companies.name', 'postal_address',
            'postal_code_id', 'postal_codes.code as postal_code', 'postal_codes.county as county')
            ->type('agent')
            ->join('agent_companies', 'agent_companies.company_id', '=', 'companies.id')
            ->join('postal_codes', 'postal_codes.id', '=', 'companies.postal_code_id')
            ->where('agent_companies.agent_id', $agent_id)
            ->orderby('companies.created_at', 'desc')->paginate();

        return response()->json($companies);
    }

    public function agentCompanyOptions($agent_id)
    {
        $companies = Company::select('companies.id as company_unique_id', 'companies.name')
            ->type('agent')
            ->join('agent_companies', 'agent_companies.company_id', '=', 'companies.id')
            ->where('agent_companies.agent_id', $agent_id)->get();

        return response()->json($companies);
    }

    protected function getUniqueId()
    {
        do {
            $code = uniqid();
        } while (Company::where('id', $code)->count() > 0);
        return $code;
    }
}
