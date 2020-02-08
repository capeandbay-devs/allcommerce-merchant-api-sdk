<?php

namespace AllCommerce\DepartmentStore\Services;

class LibraryService
{
    public function __construct()
    {

    }

    public function retrieve($feature = '')
    {
        $results = false;

        switch($feature)
        {
            case 'merchant':
                $merchant_obj = $this->basicLoadObj($feature);
                $results = $merchant_obj->refreshProfileData();

                break;

            default:
                $results = $this->basicLoadObj($feature);
        }

        return $results;
    }

    public function basicLoadObj($name)
    {
        try
        {
            $port_model_name = config('dept-store.class_maps.'.$name);

            $results = new $port_model_name();
        }
        catch(\Exception $e)
        {
            $results = false;
        }

        return new $results;
    }
}
