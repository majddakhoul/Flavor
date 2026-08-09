<?php

return [
    'user_type' => [
        'Manager' => 'Gérant',
        'Employee' => 'Employé',
        'Customer' => 'Client',
    ],
    'employee_position' => [
        'Manager' => 'Gérant',
        'Chef' => 'Chef',
        'Waiter' => 'Serveur',
        'Security' => 'Sécurité',
        'Delivery' => 'Livreur',
    ],
    'order_status' => [
        'Pending' => 'En attente',
        'Confirmed' => 'Confirmée',
        'Completed' => 'Terminée',
        'Cancelled' => 'Annulée',
    ],
    'order_type' => [
        'Delivery' => 'Livraison',
        'Reservation' => 'Sur place',
        'Takeaway' => 'À emporter',
    ],
    'reservation_status' => [
        'Pending' => 'En attente',
        'Confirmed' => 'Confirmée',
        'Cancelled' => 'Annulée',
        'Completed' => 'Terminée',
    ],
    'reservation_type' => [
        'Locally' => 'Sur place',
        'Application' => 'En ligne',
    ],
    'table_location' => [
        'Indoor' => 'Intérieur',
        'Outdoor' => 'Terrasse',
        'VIP' => 'VIP',
        'Roof' => 'Rooftop',
    ],
    'meal_availability' => [
        'available' => 'Disponible',
        'unavailable' => 'Indisponible',
    ],
    'gender' => [
        'Male' => 'Homme',
        'Female' => 'Femme',
    ],
    'cart_item_type' => [
        'meal' => 'Plat',
        'offer' => 'Offre',
    ],
    'allergy' => [
        'Cows Milk Allergy' => 'Lait de vache',
        'Egg Allergy' => 'Œuf',
        'Peanut Allergy' => 'Arachide',
        'Tree Nut Allergy' => 'Fruits à coque',
        'Fish Allergy' => 'Poisson',
        'Shellfish Allergy' => 'Crustacés',
        'Wheat Allergy' => 'Blé',
        'Soy Allergy' => 'Soja',
        'Seed Allergies' => 'Graines',
        'Red Meat Allergy' => 'Viande rouge',
        'Fruit Allergies' => 'Fruits',
        'Vegetable Allergies' => 'Légumes',
        'Spice Allergies' => 'Épices',
    ],
];
