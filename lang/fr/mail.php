<?php

return [
    'orders' => [
        'confirmation' => [
            'subject' => 'Commande :reference reçue',
            'heading' => 'Votre commande est enregistrée',
            'intro' => 'La cuisine a reçu votre commande. Nous vous informerons du suivi.',
        ],
        'status' => [
            'subject' => 'Commande :reference : :status',
            'heading' => 'Mise à jour de commande',
            'intro' => 'Le statut de votre commande a changé.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'subject' => 'Réservation :code confirmée',
            'heading' => 'Votre table est réservée',
            'intro' => 'Présentez ce code à l\'accueil.',
        ],
        'status' => [
            'subject' => 'Réservation :code : :status',
            'heading' => 'Mise à jour de réservation',
            'intro' => 'Le statut de votre réservation a changé.',
        ],
    ],
    'people' => [
        'credentials' => [
            'subject' => 'Votre compte Flavor est prêt',
            'heading' => 'Bienvenue dans l\'équipe, :name',
            'intro' => 'Voici le mot de passe pour votre première connexion.',
            'footnote' => 'Changez ce mot de passe après la première connexion.',
        ],
    ],
    'auth' => [
        'verification' => [
            'subject' => 'Votre code de vérification',
            'heading' => 'Confirmez votre e-mail, :name',
            'intro' => 'Saisissez ce code sur la page de vérification.',
        ],
    ],
    'inventory' => [
        'low-stock' => [
            'subject' => 'Stock bas : :ingredient',
            'heading' => 'Le stock s\'épuise',
            'intro' => 'Un ingrédient est passé sous le seuil.',
        ],
    ],
];
