<?php

namespace AllCommerce\DepartmentStore\Services;

class LibraryService
{
    public function __construct()
    {

    }

    public function retrieve($feature = '', $params = [])
    {
        switch($feature)
        {
            case 'merchant':
                $merchant_obj = $this->basicLoadObj($feature);
                $results = $merchant_obj->refreshProfileData();
                break;

            case 'installer':
            case 'shop':
                $results = $this->loadObjectWithSingleParam($feature, $params['shop']);
                break;

            case 'lead':
                $results = $this->loadObjectWithTwoParams($feature, $params['payload'], $params['lead_uuid']);
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

        return $results;
    }

    public function loadObjectWithSingleParam($name, $param)
    {
        try
        {
            $port_model_name = config('dept-store.class_maps.'.$name);

            $results = new $port_model_name($param);
        }
        catch(\Exception $e)
        {
            $results = false;
        }

        return $results;
    }

    public function loadObjectWithTwoParams($name, $param1, $param2)
    {
        try
        {
            $port_model_name = config('dept-store.class_maps.'.$name);

            $results = new $port_model_name($param1, $param2);
        }
        catch(\Exception $e)
        {
            $results = false;
        }

        return $results;
    }
}
