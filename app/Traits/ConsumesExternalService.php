<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

trait ConsumesExternalService
{
    public function performRequest($method, $requestUrl, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (isset($this->secret)) {
            $headers = [
                'Authorization' => 'Bearer ' . $this->secret,
                'Accept' => 'application/json',
            ];
        } else {
            $headers = [
                'Accept' => 'application/json',
            ];
        }

        try {
            if ($method == "get") {
                $response = $client->request($method, $requestUrl, ['query' => $formParams, 'headers' => $headers]);
            } else {
                $response = $client->request($method, $requestUrl, ['json' => $formParams, 'headers' => $headers]);
            }

            return $response->getBody()->getContents();

        } catch (ClientException $e) {
            return $e->getResponse()->getBody()->getContents();
        } catch (GuzzleException $e) {
            Log::error("GuzzleException: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error("Exception: " . $e->getMessage());
        }
    }

}
