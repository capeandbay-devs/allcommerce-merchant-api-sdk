<?php

namespace AllCommerce\DepartmentStore\Services;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;

class AllCommerceAPIClientService
{
    protected $root_url = 'https://ac-merchants.capeandbay.com';
    protected $api_url = '/api';

    public function __construct()
    {
        $this->root_url = env('AC_MERCHANT_API_URL','https://ac-merchants.capeandbay.com');
    }

    public function api_url()
    {
        return $this->root_url.$this->api_url;
    }

    public function get($endpoint)
    {
        $results = false;

        $headers = ['Accept: application/json',"Authorization: Bearer ".config('dept-store.deets.oauth_token')];

        $url = $endpoint;
        $response = Curl::to($url)
            ->withHeaders($headers)
            ->asJson(true)
            ->get();

        if($response)
        {
            Log::info('AllCommerce Response from '.$url, $response);
            $results = $response;
        }
        else
        {
            Log::info('AllCommerce Null Response from '.$url);
        }

        return $results;
    }

    public function post($endpoint, $args = [], $headers = [])
    {
        $results = false;

        $url = $endpoint;

        if(!empty($args))
        {
            if(!empty($headers))
            {
                $response = Curl::to($url)
                    ->withHeaders($headers)
                    ->withData($args)
                    ->asJson(true)
                    ->post();
            }
            else
            {
                $response = Curl::to($url)
                    ->withHeader('Accept: application/json')
                    ->withData($args)
                    ->asJson(true)
                    ->post();
            }
        }
        elseif(!empty($headers))
        {
            $response = Curl::to($url)
                ->withHeaders($headers)
                ->asJson(true)
                ->post();
        }
        else
        {
            $response = Curl::to($url)
                ->withHeader('Accept: application/json')
                ->asJson(true)
                ->post();
        }

        if($response)
        {
            Log::info('AllCommerce Response from '.$url, $response);
            $results = $response;
        }
        else
        {
            Log::info('AllCommerce Null Response from '.$url);
        }

        return $results;
    }
}
