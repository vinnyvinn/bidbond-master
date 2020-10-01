<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;


class GatewayService
{

    use ConsumesExternalService;

    public $baseUri;

    public $secret;


    public function __construct()
    {
        $this->baseUri = env('GATEWAY_URL');
        $this->secret = env('GATEWAY_SECRET');
    }

    /**
     * Get company cost from the gateway
     * @param $data
     * @return string
     */
    public static function init(){
        return new self();
    }
    public function restoreAgentBidBalance($data)
    {
    return $this->performRequest('POST', "agent/restore-limit", $data);
    }

    public function increaseAgentBidUsage($data)
    {
    return $this->performRequest('POST', "agent/usage", $data);
    }

    public function getConversionRate($data)
    {
    return $this->performRequest('POST', "conversion-rate", $data);
    }
    public function obtainCompany($company)
    {
    return $this->performRequest('POST', "companies/by-unique-id",['company'=>$company]);
    }


}
