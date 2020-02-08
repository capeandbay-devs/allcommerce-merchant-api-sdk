<?php

namespace AllCommerce\DepartmentStore\Facades;

use Illuminate\Support\Facades\Facade;
use AllCommerce\DepartmentStore\DepartmentStore as Reference;

class DepartmentStore extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Reference::class;
    }
}
