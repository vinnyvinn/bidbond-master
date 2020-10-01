<?php

namespace App\Traits;


use App\Models\AgentLimit;
use App\Models\Bidbond;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

trait LimitTrait
{
    public function incrementBidTotal(Bidbond $bidbond)
    {
        $setting = Setting::option(Setting::bidbond_total)->first();

        $amount = $this->getBidAmountInKenyaShillings($bidbond);

        $setting->update(['value' => bcadd($setting->value, $amount)]);

        if (Cache::has(Setting::bidbond_total)) {
            Cache::increment(Setting::bidbond_total, $amount);
            return;
        }

        Cache::rememberForever(Setting::bidbond_total, function () {
            return Setting::option(Setting::bidbond_total)->first()->value;
        });
    }

    public function incrementAgentBidUsage(Bidbond $bidbond): void
    {
        $agent_limit = AgentLimit::agent($bidbond->agent_id)->first();

        $amount = $this->getBidAmountInKenyaShillings($bidbond);

        $agent_limit->update(['balance' => bcsub($agent_limit->balance, $amount)]);

        $this->gatewayService->increaseAgentBidUsage(['agent_id' => $bidbond->agent_id, 'amount' => $bidbond->amount]);
    }

    public function incrementCompanyBidUsage(Bidbond $bidbond): void
    {
        $company_limit = Company::where('id', $bidbond->company_id)->first();

        $amount = $this->getBidAmountInKenyaShillings($bidbond);

        $company_limit->update(['balance' => bcsub($company_limit->balance, $amount)]);
    }

     function getBidAmountInKenyaShillings(Bidbond $bidbond): int
    {
        $bidbond->currency = strtoupper($bidbond->currency);

        if ($bidbond->currency == "KES") {
            return $bidbond->amount;
        };

        $conversion = Cache::remember("KES_" . $bidbond->currency, 1800, function () use($bidbond) {
            return json_decode($this->gatewayService->getConversionRate(['from' => $bidbond->currency, 'to' => "KES"]))->rate;
        });

        return round($bidbond->amount * $conversion);
    }

}
