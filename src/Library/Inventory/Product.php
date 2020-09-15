<?php

namespace AllCommerce\DepartmentStore\Library\Inventory;

use AllCommerce\DepartmentStore\Library\Feature;

class Product extends Feature
{
    protected $url = '/inventory/products';

    protected $uuid;
    protected $details;
    protected $shop, $merchant, $client;
    protected $created, $last_updated;
    protected $availableVariants, $selectedVariants;

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

                    case 'variant':
                        $payload = [
                            'uuid' => $val
                        ];

                        $pv_model_name = config('dept-store.class_maps.product_variants');
                        if(array_key_exists('qty', $data))
                        {
                            for($x = 0; $x < $data['qty']; $x++)
                            {
                                $this->selectedVariants[] = new $pv_model_name($payload);
                            }
                        }
                        else
                        {
                            $this->selectedVariants = [new $pv_model_name($payload)];
                        }
                        break;

                }

            }
        }
    }
}
