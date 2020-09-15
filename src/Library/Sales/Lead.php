<?php

namespace AllCommerce\DepartmentStore\Library\Leads;

use AllCommerce\DepartmentStore\Library\Feature;

class Lead extends Feature
{
    protected $url = '/leads';

    protected $uuid, $first_name, $last_name, $email, $phone;
    protected $shipping_address, $billing_address;
    protected $attributes;
    protected $order;
    protected $shop, $merchant, $client;
    protected $ip, $utm;
    protected $created, $last_updated;
    protected $products;

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
                    case 'first_name':
                    case 'last_name':
                    case 'email':
                    case 'phone':
                    case 'ip':
                        $this->$col = $val;
                        break;

                    default:
                        $this->$col = null;
                }

            }
        }
    }

    public function leads_url()
    {
        return $this->allcommerce_client->api_url().$this->url;
    }

    public function createOrUpdateLead($payload, $lead_uuid = null)
    {
        $results = false;

        $payload = [
            'attributes' => $payload
        ];

        if(!is_null($lead_uuid))
        {
            $payload['lead_uuid'] = $lead_uuid;
        }
        // Ping AllCommerce Merchants or false
        $lead = $this->allcommerce_client->post($this->leads_url(), $payload);

        // Get a Success Response or false
        if((!is_null($lead)) && is_array($lead))
        {
            if($lead['success'] == true)
            {
                // Populate the protected variables above or skip.
                $this->uuid = $lead['lead']['id'];
                $this->first_name = $lead['lead']['first_name'];
                $this->last_name = $lead['lead']['last_name'];
                $this->email = $lead['lead']['email'];
                $this->phone = $lead['lead']['phone'];

                // @todo - create billing and shipping objects and pass the response data to it
                $this->shipping_address = null;
                $this->billing_address = null;

                // @todo - create lead_attributes and order objects and pass the response data to it
                $this->attributes = null;
                $this->order = null;

                $this->ip = $lead['lead']['ip'];
                $this->utm = $lead['lead']['utm'];
                $this->created_at = $lead['lead']['created_at'];
                $this->last_updated = $lead['lead']['last_updated'];

                // create Product object and pass the response data into it
                $this->products = collect([]);
                if(count($lead['products']) > 0)
                {
                    $produce = [];
                    $product_model_name = config('dept-store.class_maps.product');
                    foreach ($lead['products'] as $product)
                    {
                        $product['uuid'] = $product['product'];
                        unset($product['product']);

                        $produce[] = new $product_model_name($payload);
                    }

                    $this->products = collect($produce);
                }

                // @todo - restore oauth authentication before activating these
                $this->shop = null;
                $this->client = null;
                $this->merchant = null;

                // @todo - make get functions for all of them

                return $this;
            }
        }

        return $results;
    }
}
