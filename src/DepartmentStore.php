<?php

namespace AllCommerce\DepartmentStore;

use AllCommerce\DepartmentStore\Auth\AccessToken;
use AllCommerce\DepartmentStore\DepartmentStoreFactory;
use AllCommerce\DepartmentStore\Services\LibraryService;

class DepartmentStore
{
    protected $access_token;
    protected $library;

    public function __construct(LibraryService $lib, AccessToken $token)
    {
        $this->library = $lib;
        $this->access_token = $token;
    }

    public function get($feature = '')
    {
        $results = false;

        $asset = $this->library->retrieve($feature);

        if($asset)
        {
            $results = $asset;
        }

        return $results;
    }

    /**
     * Create a new DepartmentStore instance.
     *
     * @param mixed $token
     * @return static
     */
    public static function create($token = null)
    {
        return static::make($token)->create();
    }

    /**
     * Create a DepartmentStore factory instance.
     *
     * @param  mixed  $token
     * @return DepartmentStoreFactory
     */
    public static function make($token = null)
    {
        return new DepartmentStoreFactory($token);
    }
}
