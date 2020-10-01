<?php

namespace App\Jobs;

use App\Models\Bidbond;
use App\Models\Company;
use App\Models\Setting;
use App\Services\GatewayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ExpireBidbond extends Job
{

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @param GatewayService $gatewayService
     * @return void
     */
    public function handle(GatewayService $gatewayService)
    {
        $bidbonds = Bidbond::paid()->due()->active()->select('id', 'amount', 'agent_id', 'company_id')->get();

        $total_bid_to_recover = $bidbonds->sum('amount');

        info("total_bid_to_recover: $total_bid_to_recover");

        //get unique company ids
        $company_ids = $bidbonds->whereNull('agent_id')->pluck('company_id')->unique();

        //sum of bidbonds per company
        $company_sum = [];

        $company_ids->each(function ($company_id) use ($bidbonds, &$company_sum) {
            $company_sum[] = [
                "company_id" => $company_id,
                "amount" => $bidbonds->where('company_id', $company_id)->sum('amount')
            ];
        });

        info("company_sum", $company_sum);

        $agent_ids = $bidbonds->whereNotNull('agent_id')->pluck('agent_id')->unique();

        info("agent_ids", $agent_ids->values()->all());

        $agent_sum = [];

        $agent_ids->each(function ($agent_id) use ($bidbonds, &$agent_sum) {
            $agent_sum[] = [
                "agent_id" => $agent_id,
                "amount" => $bidbonds->where('agent_id', $agent_id)->sum('amount')
            ];
        });

        info("agent_sum", $agent_sum);

        $ids = $bidbonds->pluck('id')->all();

        Bidbond::whereIn('id', $ids)->update(['expired_at' => Carbon::now()]);

        info("expiring bidbond ids", $ids);

        //restore agent limit
        $gatewayService->restoreAgentBidBalance($agent_sum);

        $this->restoreCompanyBidBalance($company_sum);

        $setting = Setting::option(Setting::bidbond_total)->first();

        $total_bid_amount = bcsub($setting->value, $total_bid_to_recover);

        $setting->update(['value' => $total_bid_amount]);


        $this->updateBidAmountCache($total_bid_amount);
    }

    private function restoreCompanyBidBalance($company_sum)
    {
        $company_sum = collect($company_sum);
        $company_array = $company_sum->pluck('company_id')->all();

        $companies = Company::whereIn('id',$company_array)->get();

        $company_sum->each(function ($item) use ($companies){
            $company = $companies->where('id', $item['company_id'])->first();
            if (!$company) return;
            $company->update([ 'balance' => bcadd($company->balance, $item["amount"]) ]);
        });
    }


    private function updateBidAmountCache(string $total_bid_amount): void
    {
        if (Cache::has(Setting::bidbond_total)) {
            Cache::decrement(Setting::bidbond_total, $total_bid_amount);
            return;
        }

        Cache::rememberForever(Setting::bidbond_total, function () use ($total_bid_amount) {
            return $total_bid_amount;
        });
    }
}
