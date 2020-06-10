<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\App;

use Ixudra\Curl\Facades\Curl;
use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;

class Installer extends SalesChannel
{
    protected $installer_url = '/installer';
    protected $shop_url;
    protected $nonce;

    public function __construct($shop_url)
    {
        parent::__construct();

        $this->shop_url = $shop_url;
    }

    public function installer_url()
    {
        return $this->ac_shopify_url().$this->installer_url;
    }

    public function getNonce()
    {
        $results = false;

        $payload = [
            'shop_url' => $this->shop_url
        ];

        $url = $this->installer_url().'/nonce';

        $response = Curl::to($url)
            ->withData($payload)
            ->asJson(true)
            ->post();

        if($response && array_key_exists('nonce', $response))
        {
            $this->nonce = $response['nonce'];
            $results = $response['nonce'];
        }

        return $results;
    }

    public function install($data = [])
    {
        $results = false;        

        $url = $this->installer_url().'/confirm-request';

        $response = Curl::to($url)
            ->withData($data)
            ->asJson(true)
            ->post();

        if($response && array_key_exists('stats', $response))
        {
            $results = $response['stats'];
        }

        return $results;
    }
}
