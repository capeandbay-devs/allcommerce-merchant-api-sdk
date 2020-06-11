<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\Shop;

use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;
use Ixudra\Curl\Facades\Curl;

class Storefront extends SalesChannel
{
    protected $storefront_url = '/shop';

    protected $shop_url;
    protected $shop_data = [];

    public function __construct($shop_url)
    {
        parent::__construct();

        $this->shop_url = $shop_url;
    }

    public function storefront_url()
    {
        return $this->ac_shopify_url().$this->storefront_url;
    }

    public function init($data = [])
    {
        $results = false;

        $response = Curl::to($this->storefront_url())
            ->withData($data)
            ->asJson(true)
            ->post();

        if($response && array_key_exists('success', $response))
        {
            if($response['success'])
            {
                $this->shop_data = $response['shop'];
            }
        }

        return $results;
    }
}
