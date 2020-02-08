<?php

namespace AllCommerce\DepartmentStore;

use AllCommerce\DepartmentStore\Services\LibraryService;
use AllCommerce\DepartmentStore\Auth\AccessToken;

class DepartmentStoreFactory
{
    /**
     * The Access Token to use.
     *
     * @var mixed
     */
    protected $access_token;

    /**
     * Create a new DepartmentStoreFactory instance.
     *
     * @param mixed  $token
     */
    public function   __construct($token = null)
    {
        $this->access_token = $token;
    }

    /**
     * Create an instance of DepartmentStore.
     *
     * @return DepartmentStore
     */
    public function create()
    {
        $access_token = $this->getAccessToken();
        $walmart = new DepartmentStore(new LibraryService(), $access_token);

        return $walmart;
    }

    /**
     * Get an instance of the AccessToken.
     *
     * @return AccessToken
     */
    protected function getAccessToken()
    {
        return new AccessToken($this->access_token);
    }
}
