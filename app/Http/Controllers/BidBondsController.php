<?php

namespace App\Http\Controllers;


use App\Services\BidBonds;
use App\Models\Bidbond;
use App\Services\GatewayService;
use App\Traits\ApiResponser;
use App\Models\Tender;
use App\Traits\LimitTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;



class BidBondsController extends Controller
{
    use ApiResponser,LimitTrait;

    public $gatewayService;

    public function __construct(GatewayService $gatewayService)
    {
        $this->gatewayService = $gatewayService;
    }

    public function index(Request $request)
    {
        return BidBonds::index($request);
    }
    public function allBids()
    {
        return Bidbond::where('agent_id','=',null)->get();
    }

    public function store(Request $request)
    {
      return BidBonds::store($request->all());
    }

    public function show($secret)
    {
        return BidBonds::show($secret);
    }

    public function edit($id)
    {
        return Bidbonds::edit($id);
    }

    public function getBidBond($secret, Request $request)
    {
     return BidBonds::getBidBond($secret, $request->all());
    }
    public function getByTender(Request $request)
    {
     return Bidbond::tender($request->all())->first();
    }

    public function applyBid(Request $request){
        $bidbond = Bidbond::secret($request->secret)->firstOrFail();
        $bidbond->paid = 1;
        $bidbond->reference = $request->reference;
        $bidbond->deal_date = Carbon::now();
        $bidbond->save();
        if ($bidbond->agent_id) {
            $this->incrementAgentBidUsage($bidbond);

        } else {
            $this->incrementCompanyBidUsage($bidbond);
            $this->incrementBidTotal($bidbond);
        }
        return response()->json($bidbond);
    }
    public function getBidBonds(Request $request)
    {
        return BidBonds::getBidBonds($request->all());
    }

    public function update(Request $request, $id)
    {
        $data = $this->validate($request, [
            'counter_party_id' => 'bail|numeric|required|exists:counter_parties,id',
            'tender_no' => 'required',
            'purpose' => 'required',
            'addressee' => 'required',
            'amount' => 'bail|required|numeric',
            'currency' => 'required',
            'period' => 'required',
            'effective_date' => 'bail|required|date|date_format:Y-m-d',
            'template_secret' => 'bail|required|exists:bid_bond_templates,secret',
            'charge' => 'required|numeric',
        ]);

        $bidbond = Bidbond::findOrFail($id);
        $data['expiry_date'] = Carbon::createFromFormat('Y-m-d', $data['effective_date'])->addDays($data['period']);

        if ($bidbond->charge < $data['charge']) {
            $data['paid'] = 0;
        }
        $bidbond->update($data);
        return self::__success($bidbond);
    }

    public function updateDate(Request $request)
    {
      return BidBonds::updateDate($request->all());
    }

    public function getTenderInfo(Request $request)
    {
        $tender = Bidbond::where('tender_no', $request->input('tender_number'))->with('counterparty')->first();
        return self::__success($tender);
    }

    public function obtain(Request $request)
    {
        $bidbonds = Bidbond::select('companies.name as company_name','bidbonds.id as bidbond_id', 'bidbonds.secret as bidbond_secret',
            'bidbonds.paid as bidbond_paid', 'bidbonds.addressee as tender_addressee',
            'bidbonds.effective_date as tender_effective_date', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount', 'bidbonds.currency',
            'bidbonds.purpose as tender_purpose', 'counter_parties.name as counter_party_name',
            'counter_parties.postal_address as counter_party_postal_address', 'bidbonds.company_id as company_id',
            'bidbonds.template_secret as template_secret')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->join('companies', 'companies.id', '=', 'bidbonds.company_id')
            ->whereIn('bidbonds.company_id', $request->company_unique_id)
            ->paginate();
        return response()->json($bidbonds);
    }

    public function getByUser(Request $request)
    {
        $user_ids = collect($request->user_ids)->toArray();

        $bids = Bidbond::select('companies.name as company_name','bidbonds.id as bidbond_id', 'bidbonds.secret as bidbond_secret', 'bidbonds.paid as bidbond_paid',
            'bidbonds.addressee as tender_addressee', 'bidbonds.effective_date as tender_effective_date', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount', 'bidbonds.currency', 'bidbonds.purpose as tender_purpose',
            'counter_parties.name as counter_party_name', 'counter_parties.postal_address as counter_party_postal_address',
            'bidbonds.company_id as company_id', 'bidbonds.template_secret as template_secret')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->join('companies', 'companies.id', '=', 'bidbonds.company_id')
            ->whereIn('bidbonds.created_by', $user_ids)->paginate();

        return response()->json($bids);
    }

    public function getByAgent(Request $request)
    {
        $bids = Bidbond::select('companies.name as company_name', 'bidbonds.secret as bidbond_secret', 'bidbonds.paid as bidbond_paid',
            'bidbonds.addressee as tender_addressee', 'bidbonds.effective_date as tender_effective_date', 'bidbonds.period as tender_period',
            'bidbonds.tender_no as tender_number', 'bidbonds.amount as tender_amount', 'bidbonds.currency', 'bidbonds.purpose as tender_purpose',
            'counter_parties.name as counter_party_name', 'counter_parties.postal_address as counter_party_postal_address',
            'bidbonds.company_id as company_id', 'bidbonds.template_secret as template_secret')
            ->join('counter_parties', 'counter_parties.id', '=', 'bidbonds.counter_party_id')
            ->join('companies', 'companies.id', '=', 'bidbonds.company_id')
            ->where('bidbonds.agent_id', $request->agent_id)->paginate();

        return response()->json($bids);
    }

    public function countBids(Request $request)
    {
        $comp = collect($request->all())->pluck('company_unique_id');

        $bidbonds = Bidbond::whereIn('company_id', $comp)->count();

        return $bidbonds;
    }
}
