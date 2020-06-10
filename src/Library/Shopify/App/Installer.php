<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\App;

use Ixudra\Curl\Facades\Curl;
use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;

class Installer extends SalesChannel
{
    protected $url = '/installer';
    protected $shop_url;
    protected $nonce;

    public function __construct($shop_url)
    {
        parent::__construct();

        $this->shop_url = $shop_url;
    }

    public function installer_url()
    {
        return $this->ac_shopify_url().$this->url;
    }

    public function getNonce()
    {
        $results = false;

        $payload = [
            'shop_url' => $this->shop_url
        ];

        $response = Curl::to($this->installer_url().'/nonce')
            ->withData($payload)
            ->asJson(true)
            ->post();

        if($response && array_key_exists('nonce', $response))
        {
            $results = $response['nonce'];
        }

        return $results;
    }
}
