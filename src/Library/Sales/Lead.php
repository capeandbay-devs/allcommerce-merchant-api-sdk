<?php

namespace AllCommerce\DepartmentStore\Library\Sales;

use AllCommerce\DepartmentStore\Library\Feature;

class Lead extends Feature
{
    protected $url = '/leads';

    protected $uuid, $first_name, $last_name, $email, $phone, $optin;
    protected $shipping_address, $shipping_uuid, $billing_address, $billing_uuid;
    protected $attributes;
    protected $order;
    protected $checkout_type, $checkout_id;
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

    public function createOrUpdateLead($argies, $lead_uuid = null)
    {
        $results = false;

        $payload = [
            'attributes' => $argies
        ];

        if(!is_null($lead_uuid))
        {
            $payload['lead_uuid'] = $lead_uuid;
        }

        if(array_key_exists('shipping_uuid', $argies))
        {
            $payload['shipping_uuid'] = $argies['shipping_uuid'];
            unset($payload['attributes']['shipping_uuid']);
        }

        if(array_key_exists('billing_uuid', $argies))
        {
            $payload['billing_uuid'] = $argies['billing_uuid'];
            unset($payload['attributes']['billing_uuid']);
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

                // create billing and shipping objects and pass the response data to it
                if(!empty($lead['shipping_address']))
                {
                    $this->shipping_uuid = $lead['shipping_address']['id'];
                    $this->shipping_address = null;
                }

                if(!empty($lead['billing_address']))
                {
                    $this->billing_uuid = $lead['billing_address']['id'];
                    $this->billing_address = null;
                }

                if(!empty($lead['attributes']))
                {
                    $this->attributes = [];
                    foreach($lead['attributes'] as $attr)
                    {
                        $this->attributes[$attr['name']] = $attr;
                    }
                }

                // @todo - create order object and pass the response data to it
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

    public function getLeadAttributes($name = null)
    {
        $results = false;

        if(!is_null($name))
        {
            if(array_key_exists($name, $this->attributes))
            {
                $results = $this->attributes[$name];
            }
        }
        else
        {
            $results = $this->attributes;
        }

        return $results;
    }

    public function getLeadId()
    {
        return $this->uuid;
    }

    public function getBillingId()
    {
        return $this->billing_uuid;
    }

    public function getShippingId()
    {
        return $this->shipping_uuid;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function getFullName()
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->getPhone();
    }

    public function getProducts()
    {
        return $this->products;
    }


    public function setEmail($email) : void
    {
        $this->email = $email;
    }

    public function setOptin($flag) : void
    {
        $this->optin = $flag;
    }

    public function setShopUuid($uuid) : void
    {
        $this->shop = $uuid;
    }

    public function setCheckout($type, $id) : void
    {
        $this->checkout_type = $type;
        $this->checkout_id = $id;
    }

    public function commit($mode = 'email')
    {
        $results = false;

        if(is_null($this->uuid))
        {
            switch($mode)
            {
                case 'email':
                    if($response = $this->createWithEmail())
                    {
                        if(is_array($response))
                        {
                            $this->uuid = $response['lead_uuid'];
                            $results = $this;
                        }
                    }
                    break;

                case 'shipping':
                    $results = $this;
                    break;

                case 'billing':
                    $results = $this;
                    break;
            }
        }
        else
        {
            // @todo - run the updates.
            $results = $this;
        }

        return $results;
    }

    private function createWithEmail()
    {
        $results = false;

        $payload = [
            'email' => $this->email,
            'checkoutType' => $this->checkout_type,
            'checkoutId' => $this->checkout_id,
            'shopUuid' => $this->shop,
            'emailList' => $this->optin,
        ];

        $url = $this->leads_url().'/email';
        $lead = $this->allcommerce_client->post($url, $payload);

        if((!is_null($lead)) && is_array($lead))
        {
            if($lead['success'] == true)
            {
                $results = ['lead_uuid' => $lead['lead_uuid']];
            }
            else
            {
                $results = $lead['reason'];
            }
        }

        return $results;
    }
}
