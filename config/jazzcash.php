<?php

return [
    'environment' => env('JAZZCASH_ENV', 'sandbox'), // sandbox | production

    'merchant_id'    => env('JAZZCASH_MERCHANT_ID'),
    'password'       => env('JAZZCASH_PASSWORD'),
    'integrity_salt' => env('JAZZCASH_INTEGRITY_SALT'), // this is the "Hashkey"

    'sandbox_url'    => env('JAZZCASH_SANDBOX_URL', 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'),
    'production_url' => env('JAZZCASH_PRODUCTION_URL', 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/'),

    'return_url' => env('JAZZCASH_RETURN_URL'), // e.g. https://closyyy.com/jazzcash/return

    // How long a JazzCash payment session stays valid before it expires
    'txn_expiry_minutes' => env('JAZZCASH_TXN_EXPIRY_MINUTES', 60),
];