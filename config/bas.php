<?php

return [
    'base_url' => env('BAS_BASE_URL', 'https://api.basgate.com'),
    'client_id' => env('BAS_CLIENT_ID'),
    'client_secret' => env('BAS_CLIENT_SECRET'),
    'app_id' => env('BAS_APP_ID'),
    'merchant_key' => env('BAS_MERCHANT_KEY'),
    'iv' => env('BAS_IV', '@@@@&&&&####$$$$'), // **Keep the fixed IV as confirmed**
    'callback_uri' => env('BAS_CALLBACK_URI'),
    'environment' => env('BAS_ENVIRONMENT', 'staging'),


    'token_endpoint' => env('BAS_TOKEN_ENDPOINT', '/api/v1/auth/token'),
    'user_info_endpoint' => env('BAS_USER_INFO_ENDPOINT', '/api/v1/auth/secure/userinfo'),
    'refund_payment_endpoint' => env('BAS_REFUND_PAYMENT_ENDPOINT', '/api/v1/merchant/refund-payment/request'),
    'transaction_status_endpoint' => env('BAS_TRANSACTION_STATUS_ENDPOINT', '/api/v1/merchant/secure/transaction/status'),
    'transaction_initiate_endpoint' => env('BAS_TRANSACTION_INITIATE_ENDPOINT', '/api/v1/merchant/secure/transaction/initiate'),
    'notifications_endpoint' => env('BAS_NOTIFICATIONS_ENDPOINT', '/api/v1/merchant/secure/notifications/send-to-customer'),



];
