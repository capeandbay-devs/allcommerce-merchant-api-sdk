<?php

namespace AllCommerce\DepartmentStore\Library\Account;

use AllCommerce\DepartmentStore\Library\Feature;

class Merchant extends Feature
{
    protected $url = '/merchant';
    protected $uuid, $name, $joinDate, $activeMerchant, $permissions;

    public function __construct($data = [])
    {
        parent::__construct();

        $this->uuid = config('dept-store.deets.merchant_uuid');

        if(!empty($data))
        {
            $this->name = $data['name'];
            $this->joinDate = $data['joinDate'];
            $this->activeMerchant = $data['active'];
            $this->permissions = $data['permissions'];
        }
    }

    public function merchant_url()
    {
        return $this->allcommerce_client->api_url().$this->url;
    }

    public function refreshProfileData()
    {
        $results = [];

        $merchant = $this->allcommerce_client->get($this->merchant_url());

        if($merchant && array_key_exists('success', $merchant))
        {
            $results = $merchant['merchant'];
        }

        return new $this($results);
    }

    public function permissions()
    {
        return $this->permissions;
    }
}
