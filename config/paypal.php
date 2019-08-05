<?php
return [
    'mode' => env('PAYPAL_MODE', 'sandbox'), // Solo puede ser 'sandbox' o 'live'. Si está vacío o no es válido, se usará "en vivo".
    'invoice_prefix' => env('PAYPAL_INVOICE_PREFIX', ''),
    'sandbox' => [
        'username' => env('PAYPAL_SANDBOX_API_USERNAME', ''),
        'password' => env('PAYPAL_SANDBOX_API_PASSWORD', ''),
        'secret' => env('PAYPAL_SANDBOX_API_SECRET', ''),
        'certificate' => env('PAYPAL_SANDBOX_API_CERTIFICATE', ''),
        'app_id' => env('PAYPAL_SANDBOX_APP_ID', 'APP-80W284485P519543T'), // Se utiliza para probar la API de pagos adaptativos en modo sandbox
    ],
    'live' => [
        'username'    => env('PAYPAL_LIVE_API_USERNAME', ''),
        'password'    => env('PAYPAL_LIVE_API_PASSWORD', ''),
        'secret'      => env('PAYPAL_LIVE_API_SECRET', ''),
        'certificate' => env('PAYPAL_LIVE_API_CERTIFICATE', ''),
        'app_id'      => env('PAYPAL_LIVE_APP_ID', ''), // Utilizado para API de pagos adaptativos
    ],
    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // Solo puede ser 'Sale', 'Authorization' o 'Order'
    'currency'       => env('PAYPAL_CURRENCY', 'MXN'),
    'notify_url'     => env('PAYPAL_NOTIFICATION_URL', ''), // Cambie esto en consecuencia para su aplicación.
    'locale'         => env('PAYPAL_LOCALE', 'es_ES'), // forzar el idioma de la puerta de enlace, es decir, it_IT, es_ES, en_US ... (solo para check-out rápido)
    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true), // Valide SSL al crear un cliente api.
];