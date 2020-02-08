<?php

namespace AllCommerce\DepartmentStore\Auth;

use AllCommerce\DepartmentStore\Services\AllCommerceAPIClientService;

class AccessToken extends AllCommerceAPIClientService
{
    protected $api_access_token = null;
    protected $username;

    public function __construct($token = null)
    {
        parent::__construct();
        $this->api_access_token = $this->verifyAccessToken($token);
    }

    private function verifyAccessToken($token = null)
    {

        if(!is_null($token))
        {
            $headers = [
                'Accept: application/json',
                "Authorization: Bearer $token"
            ];
            //$response = $this->post($this->api_url().'/user', [], $headers);

            // @todo - do shit here. Return null if response sucks
        }

        return $token;

    }

    public function token()
    {
        return $this->api_access_token;
    }
}
