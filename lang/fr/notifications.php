<?php

return [
    'orders' => [
        'confirmation' => [
            'title' => 'Commande :reference reçue',
            'body' => 'La cuisine a reçu votre commande. Nous vous préviendrons à chaque changement de statut.',
        ],
        'status' => [
            'title' => 'Commande :reference : :status',
            'body' => 'Le statut de votre commande est passé à :status.',
        ],
        'new_for_staff' => [
            'title' => 'Nouvelle commande :reference',
            'body' => 'Une nouvelle commande vient d\'arriver et attend d\'être traitée.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'title' => 'Réservation :code confirmée',
            'body' => 'Votre table est réservée. Présentez ce code à l\'accueil.',
        ],
        'status' => [
            'title' => 'Réservation :code : :status',
            'body' => 'Le statut de votre réservation est passé à :status.',
        ],
        'new_for_staff' => [
            'title' => 'Nouvelle réservation :code',
            'body' => 'Une nouvelle réservation de table vient d\'être enregistrée.',
        ],
    ],
    'inventory' => [
        'low_stock' => [
            'title' => 'Stock bas : :ingredient',
            'body' => 'Cet ingrédient a atteint ou dépassé son seuil de réapprovisionnement.',
        ],
    ],
    'people' => [
        'credentials' => [
            'title' => 'Votre compte est prêt',
            'body' => 'Bienvenue dans l\'équipe. Consultez votre e-mail pour vos identifiants.',
        ],
        'new_hire' => [
            'title' => 'Nouveau membre de l\'équipe : :name',
            'body' => ':name a rejoint l\'équipe en tant que :position.',
        ],
    ],
];
