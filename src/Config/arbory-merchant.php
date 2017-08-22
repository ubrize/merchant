<?php

return [
    // Add in each gateway here and necessary named routes
    'gateways' => [
        'firstdata' => [
            'errorTransactionRoute'  => 'firstdata.error',
            'successTransactionRoute'  => 'firstdata.success',
        ]
    ]
];