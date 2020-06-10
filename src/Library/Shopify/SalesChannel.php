<?php

namespace AllCommerce\DepartmentStore\Library\Shopify;

use AllCommerce\DepartmentStore\Library\Feature;

class SalesChannel extends Feature
{
    protected $url = '/shopify';

    public function __construct()
    {
        parent::__construct();
    }

    public function ac_shopify_url()
    {
        return $this->allcommerce_client->api_url().$this->url;
    }
}
