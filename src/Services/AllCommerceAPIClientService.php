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

    public function get($endpoint, $headers = [])
    {
        $results = false;

        $url = $endpoint;

        if(empty($headers))
        {
            $headers = [
                'Accept: application/json',
                //"Authorization: Bearer ".config('dept-store.deets.oauth_token')
            ];
        }

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

    /**
     * @param $endpoint
     * @param $args
     * @param $headers
     * @return Curl;
     */
    private function preparePostPut($endpoint, $args = [], $headers = [])
    {
        $url = $endpoint;

        if(!empty($args))
        {
            if(!empty($headers))
            {
                $results = Curl::to($url)
                    ->withHeaders($headers)
                    ->withData($args)
                    ->asJson(true)
                    ;
            }
            else
            {
                $results = Curl::to($url)
                    ->withHeader('Accept: application/json')
                    ->withData($args)
                    ->asJson(true)
                    ;
            }
        }
        elseif(!empty($headers))
        {
            $results = Curl::to($url)
                ->withHeaders($headers)
                ->asJson(true)
                ;
        }
        else
        {
            $results = Curl::to($url)
                ->withHeader('Accept: application/json')
                ->asJson(true)
                ;
        }

        return $results;
    }

    public function post($endpoint, $args = [], $headers = [])
    {
        $results = false;

        $response = $this->preparePostPut($endpoint, $args, $headers)->post();

        if($response)
        {
            Log::info('AllCommerce Response from '.$endpoint, $response);
            $results = $response;
        }
        else
        {
            Log::info('AllCommerce Null Response from '.$endpoint);
        }

        return $results;
    }

    public function put($endpoint, $args = [], $headers = [])
    {
        $results = false;

        $response = $this->preparePostPut($endpoint, $args, $headers)->put();

        if($response)
        {
            Log::info('AllCommerce Response from '.$endpoint, $response);
            $results = $response;
        }
        else
        {
            Log::info('AllCommerce Null Response from '.$endpoint);
        }

        return $results;
    }
}
