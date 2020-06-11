<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\Shop;

use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;
use Ixudra\Curl\Facades\Curl;

class Storefront extends SalesChannel
{
    protected $storefront_url = '/shop';

    protected $installed, $shop_url, $ac_merchant;
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

                $this->installed = ($response['status']['installed'] == 1);
                $this->date_installed = $response['status']['created_at'];
                $this->last_updated = $response['status']['updated_at'];
                $this->ac_merchant = $response['allcommerce_merchant'];
                $results = $this;
            }
        }

        return $results;
    }
}
