<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\Shop;

use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;
use Ixudra\Curl\Facades\Curl;

class Storefront extends SalesChannel
{
    protected $storefront_url = '/shop';

    protected $installed, $shop_url, $ac_merchant, $access_token;
    protected $shop_data = [];
    protected $session_data = [];
    protected $date_installed, $last_updated;

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
                $this->session_data = $data;
                $this->shop_data = $response['shop'];
                $this->access_token = $response['shop']['status']['access_token'];

                $this->installed = ($this->shop_data['status']['installed'] == 1);
                $this->date_installed = $this->shop_data['status']['created_at'];
                $this->last_updated = $this->shop_data['status']['updated_at'];
                $this->ac_merchant = $this->shop_data['allcommerce_merchant'];
                $results = $this;
            }
        }

        return $results;
    }

    public function isInstalled()
    {
        return $this->installed == true;
    }

    public function getMerchant()
    {
        $results = false;

        if(!empty($this->ac_merchant))
        {
            // @todo - return an AC Merchant Object
        }

        return $results;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }
}
