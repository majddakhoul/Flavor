<?php

return [
    'orders' => [
        'confirmation' => [
            'title' => 'Order :reference received',
            'body' => 'The kitchen has your order. We will notify you when the status changes.',
        ],
        'status' => [
            'title' => 'Order :reference is :status',
            'body' => 'The status of your order has changed to :status.',
        ],
        'new_for_staff' => [
            'title' => 'New order :reference',
            'body' => 'A new order just came in and is waiting to be handled.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'title' => 'Reservation :code confirmed',
            'body' => 'Your table is held. Show this code at the door.',
        ],
        'status' => [
            'title' => 'Reservation :code is :status',
            'body' => 'The status of your reservation has changed to :status.',
        ],
        'new_for_staff' => [
            'title' => 'New reservation :code',
            'body' => 'A new table reservation was just booked.',
        ],
    ],
    'inventory' => [
        'low_stock' => [
            'title' => 'Low stock: :ingredient',
            'body' => 'This ingredient has dropped to or below its reorder threshold.',
        ],
    ],
    'people' => [
        'credentials' => [
            'title' => 'Your account is ready',
            'body' => 'Welcome to the team. Check your email for your sign-in details.',
        ],
        'new_hire' => [
            'title' => 'New team member: :name',
            'body' => ':name joined as :position.',
        ],
    ],
];
