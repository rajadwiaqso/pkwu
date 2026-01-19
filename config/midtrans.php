<?php 

return [
    'server_key' => env('MIDTRANS_SERVER_KEY', 'your-server-key'),
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'your-client-key'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', 'your-merchant-id'),
    'midtrans_url' => env('MIDTRANS_URL', 'https://api.midtrans.com/v2/'),

];