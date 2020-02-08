<?php

namespace AllCommerce\DepartmentStore\Library;

use AllCommerce\DepartmentStore\Services\AllCommerceAPIClientService;

class Feature
{
    public $allcommerce_client;

    public function __construct()
    {
        $this->allcommerce_client = new AllCommerceAPIClientService();
    }

    /**
     * Returns all whatever from the AllCommerce API
     * @return array
     */
    public function get()
    {
        $results = [];

        // Leave it for a child to use, right?

        return $results;
    }

    public function setIfExists($key, $arr = [])
    {
        $results = '';

        if(array_key_exists($key, $arr))
        {
            $results = $arr[$key];
        }

        return $results;
    }
}
