<?php

namespace AllCommerce\DepartmentStore\Library\Inventory;

use AllCommerce\DepartmentStore\Library\Feature;

class ProductVariants extends Feature
{
    protected $url = '/inventory/products/variants';

    protected $uuid, $product_uuid;
    protected $product;
    protected $details;
    protected $shop, $merchant, $client;
    protected $created, $last_updated;

    public function __construct(array $data = [])
    {
        parent::__construct();

        if(!empty($data))
        {
            foreach ($data as $col => $val)
            {
                switch($col)
                {
                    case 'uuid':
                        $this->$col = $val;
                        break;

                    default:
                        $this->$col = null;
                }

            }
        }
    }
}
