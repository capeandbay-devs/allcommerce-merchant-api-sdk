<?php

return [
    'deets' => [
        'merchant_uuid' => env('AC_MERCHANT_ID', ''),
        'oauth_token' => env('AC_ACCESS_TOKEN', ''),
        'refresh_token' => env('AC_REFRESH_TOKEN', ''),
    ],
    // You can rename this disk here. Default: root
    'root_disk_name' => 'root',
    'class_maps' => [
        'merchant' => \AllCommerce\DepartmentStore\Library\Account\Merchant::class
    ]
];
