<?php

return [

    'locales' => ['en', 'ar', 'fr'],

    'rtl_locales' => ['ar'],

    'currency' => [
        'code' => env('FLAVOR_CURRENCY', 'SYP'),
        'symbol' => env('FLAVOR_CURRENCY_SYMBOL', 'SYP'),
        'symbol_ar' => env('FLAVOR_CURRENCY_SYMBOL_AR', 'ل.س'),
    ],

    'brand' => [
        'name' => env('APP_NAME', 'Flavor'),
        'tagline' => env('FLAVOR_TAGLINE', 'Kitchen, floor and books in one place'),
        'phone' => env('FLAVOR_PHONE', '+963 11 000 0000'),
        'email' => env('FLAVOR_EMAIL', 'hello@flavor.test'),
        'address' => env('FLAVOR_ADDRESS', 'Damascus, Syria'),
    ],

    'inventory' => [
        'low_stock_threshold' => (int) env('FLAVOR_LOW_STOCK_THRESHOLD', 20),
        'critical_stock_threshold' => (int) env('FLAVOR_CRITICAL_STOCK_THRESHOLD', 5),
    ],

    'orders' => [
        'delivery_buffer_minutes' => (int) env('FLAVOR_DELIVERY_BUFFER', 15),
        'max_item_quantity' => 100,
        'cart_session_prefix' => 'flavor.cart',
    ],

    'reservations' => [
        'ban_days' => (int) env('FLAVOR_BAN_DAYS', 7),
        'cancellation_limit' => (int) env('FLAVOR_CANCELLATION_LIMIT', 5),
        'min_duration_minutes' => 60,
        'max_duration_minutes' => 300,
        'opening_hour' => (int) env('FLAVOR_OPENING_HOUR', 10),
        'closing_hour' => (int) env('FLAVOR_CLOSING_HOUR', 24),
    ],

    'media' => [
        'disk' => env('FLAVOR_MEDIA_DISK', 'public'),
        'meals_path' => 'meals',
        'max_upload_kb' => 4096,
    ],

    'cache' => [
        'enabled' => (bool) env('FLAVOR_CACHE_ENABLED', true),
        'prefix' => 'flavor',
        'profiles' => [
            'menu' => 10,
            'catalog' => 60,
            'dashboard' => 5,
            'statistics' => 5,
            'reports' => 15,
            'lookups' => 720,
        ],
    ],

    'queues' => [
        'mail' => env('FLAVOR_QUEUE_MAIL', 'mail'),
        'reports' => env('FLAVOR_QUEUE_REPORTS', 'reports'),
        'maintenance' => env('FLAVOR_QUEUE_MAINTENANCE', 'maintenance'),
    ],

    'pagination' => [
        'default' => 15,
        'menu' => 12,
    ],

];
