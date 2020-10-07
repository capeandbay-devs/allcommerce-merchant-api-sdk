<?php

namespace AllCommerce\DepartmentStore\Library\Shopify\Shop;

use Ixudra\Curl\Facades\Curl;
use AllCommerce\DepartmentStore\Library\Feature;
use AllCommerce\DepartmentStore\Library\Shopify\SalesChannel;

class Storefront extends Feature
{
    protected $storefront_url = '/shop';

    protected $installed, $shop_url, $ac_merchant, $ac_shop, $access_token;
    protected $shop_data = [];
    protected $session_data = [];
    protected $shipping_rates = [];
    protected $date_installed, $last_updated;

    public function __construct($shop_url)
    {
        parent::__construct();

        $this->shop_url = $shop_url;
    }

    public function storefront_url()
    {
        return $this->allcommerce_client->api_url().$this->storefront_url;
    }

    public function shopify_storefront_url()
    {
        return $this->allcommerce_client->api_url().'/shopify'.$this->storefront_url;
    }

    public function shipping_rates_url()
    {
        return $this->storefront_url().'/shipping-rates';
    }

    public function init($data = [])
    {
        $results = false;

        $response = Curl::to($this->shopify_storefront_url())
            //->withHeaders($headers)
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
                $this->ac_shop = $this->shop_data['allcommerce_shop'];
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
            $results = $this->ac_merchant;
        }
        else
        {
            // @todo - curl out to get that info!
        }

        return $results;
    }

    public function getShop()
    {
        $results = false;

        if(!empty($this->ac_shop))
        {
            $results = $this->ac_shop;
        }
        else
        {
            // @todo - curl out to get that info!
        }

        return $results;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getShopData()
    {
        return $this->shop_data;
    }

    public function getShopShippingRates()
    {
        $results = false;

        if(!is_null($this->access_token))
        {
            if(!empty($this->shipping_rates))
            {
                $results = $this->shipping_rates;
            }
            else
            {
                $headers = [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    "x-allcommerce-token: {$this->access_token}",
                ];

                if(!is_null($this->ac_shop))
                {
                    $headers[] = "x-ac-shop-uuid: {$this->ac_shop}";
                }

                $response = $this->allcommerce_client->get($this->shipping_rates_url(), $headers);

                if($response && array_key_exists('success', $response))
                {
                    if($response['success'])
                    {
                        $this->shipping_rates = $response['shipping_rates'];
                        $results = $this->shipping_rates;
                    }
                }
            }
        }

        return $results;
    }

    public function setShopUuid($uuid) : void
    {
        $this->ac_shop = $uuid;
    }

    public function setAccessToken($token) : void
    {
        $this->access_token = $token;
    }
}
