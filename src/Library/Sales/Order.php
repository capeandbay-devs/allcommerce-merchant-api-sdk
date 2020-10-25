<?php

namespace AllCommerce\DepartmentStore\Library\Sales;

use AllCommerce\DepartmentStore\Library\Feature;

class Order extends Feature
{
    protected $url = '/orders';

    protected $uuid, $lead_uuid;
    protected $shop, $merchant, $client, $access_token, $shop_type;
    protected $lead, $shippingAddress, $billingAddress;

    protected $allcommerce_transactions = [];
    protected $allcommerce_customer;

    protected $shopify_draft_order, $shopify_customer, $shopify_order;
    protected $shopify_transactions = [];


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

    public function orders_url()
    {
        return $this->allcommerce_client->api_url().$this->url;
    }

    public function setAccessToken($token) : void
    {
        $this->access_token = $token;
    }

    public function setLeadId($uuid)
    {
        $this->lead_uuid = $uuid;
    }

    private function setLeadObj($lead)
    {
        $this->lead = new Lead([
            'uuid' => $lead['id'],
            'first_name' => $lead['first_name'],
            'last_name' => $lead['last_name'],
            'email' => $lead['email'],
            'phone' => $lead['phone'],
            'ip' => $lead['ip_address'],
        ]);
    }

    public function setShippingAddress($addy)
    {
        $this->shippingAddress = $addy;
    }

    public function setBillingAddress($addy)
    {
        $this->billingAddress = $addy;
    }

    public function getLead()
    {
        return $this->lead;
    }

    public function getOrderId()
    {
        return $this->uuid;
    }

    public function getShopifyDraftOrder()
    {
        return $this->shopify_draft_order;
    }

    public function getShopType()
    {
        return $this->shop_type;
    }

    public function get()
    {
        $results = false;

        if(!is_null($this->access_token))
        {
            if(!is_null($this->uuid))
            {
                //@todo - get the order record using the order uuid
            }
            elseif(!is_null($this->lead_uuid))
            {
                //\get the order record using the lead_uuid
                if(!is_null($this->access_token))
                {
                    $payload = [
                        'leadUuid' => $this->lead_uuid,
                    ];

                    $url = $this->orders_url();
                    $order = $this->allcommerce_client->post($url, $payload, $this->putTogetherHeaders());

                    if($results = $this->evaluateOrderResponse($order))
                    {
                        $this->uuid = $results['order_uuid'];
                        $results = true;
                    }
                }

                return $results;
            }
        }

        return $results;
    }

    private function evaluateOrderResponse($order_response)
    {
        $results = false;
        if((!is_null($order_response)) && is_array($order_response))
        {
            if ($order_response['success'] == true)
            {
                $this->setLeadObj($order_response['order']['lead']);
                $this->setShippingAddress($order_response['order']['shipping_address']);
                $this->setBillingAddress($order_response['order']['billing_address']);
                $this->shop = $order_response['order']['shop'];
                $this->shopify_draft_order = $order_response['order']['shopify_draft_order'];
                $this->shopify_customer = $order_response['order']['shopify_customer'];
                $this->shop_type = $order_response['order']['shop_type'];

                $results = ['order_uuid' => $order_response['order']['record']['id']];
            }
            else
            {
                $results = $order_response['reason'];
            }
        }

        return $results;
    }

    private function putTogetherHeaders()
    {
        $results = [];

        if(!is_null($this->access_token))
        {
            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                "x-allcommerce-token: {$this->access_token}",
            ];

            if(!is_null($this->shop))
            {
                if(is_string($this->shop))
                {
                    $headers[] = "x-ac-shop-uuid: {$this->shop}";
                }
                else
                {
                    $headers[] = "x-ac-shop-uuid: {$this->shop['id']}";
                }
            }

            $results = $headers;
        }

        return $results;
    }

    public function processCreditPaymentAuth($details)
    {
        $results = false;

        if(!is_null($this->access_token))
        {
            $url = $this->orders_url().'/payments/credit/authorize';
            $results = $this->allcommerce_client->post($url, $details, $this->putTogetherHeaders());
        }

        return $results;
    }

    public function processCreditPaymentCapture($details)
    {
        $results = false;

        if(!is_null($this->access_token))
        {
            $url = $this->orders_url().'/payments/credit/capture';
            $results = $this->allcommerce_client->post($url, $details, $this->putTogetherHeaders());
        }

        return $results;
    }
}
