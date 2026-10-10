<?php

return [
    'brand' => [
        'name' => 'ACCÈS EVENT',
        'company' => 'BWAN TECHNOLOGIES',
        'tagline' => "Le hub de solutions pour l'événementiel en Afrique",
        'promise' => 'Billetterie, contrôle d\'accès, accréditation, paiements et sécurité dans une seule technologie.',
    ],

    'colors' => [
        'primary' => '#fc7016',
        'secondary' => '#043451',
        'auxiliary' => '#043451',
        'accent' => '#f4e3dd',
    ],

    'contact' => [
        'notification_email' => env('ACCES_EVENT_CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        'phone' => env('ACCES_EVENT_PHONE'),
        'address' => env('ACCES_EVENT_ADDRESS'),
    ],

    // Familles de domaines (filtres de la page Solutions, suggestions sur la fiche).
    // Un domaine dont le slug n'est listé nulle part reste affiché, sans famille.
    // Les noms des familles se modifient dans l'administration (Textes et images).
    'solution_groups' => [
        'vendre' => [
            'slugs' => ['billetterie-360', 'paiements-digitaux', 'marketing-crm'],
        ],
        'controler' => [
            'slugs' => ['controle-acces', 'accreditation', 'securite-surete-event', 'pass-parking'],
        ],
        'organiser' => [
            'slugs' => ['gestion-benevoles', 'signaletique-evenementielle', 'accessoiriste-evenement'],
        ],
        'developper' => [
            'slugs' => ['baobaa', 'tourisme'],
        ],
    ],

    // Formats d'événements de l'accueil : domaines recommandés pour chacun (par slug).
    // Les noms et textes des formats se modifient dans l'administration (Textes et images).
    'event_types' => [
        'concerts' => [
            'slugs' => ['billetterie-360', 'controle-acces', 'paiements-digitaux', 'securite-surete-event'],
        ],
        'salons' => [
            'slugs' => ['accreditation', 'baobaa', 'billetterie-360', 'signaletique-evenementielle'],
        ],
        'sport' => [
            'slugs' => ['controle-acces', 'pass-parking', 'securite-surete-event', 'gestion-benevoles'],
        ],
        'institutions' => [
            'slugs' => ['accreditation', 'controle-acces', 'accessoiriste-evenement', 'tourisme'],
        ],
    ],

    'maintenance' => [
        'rebuild_token' => env('ACCES_EVENT_REBUILD_TOKEN'),
        'npm_binary' => env('ACCES_EVENT_NPM_BINARY', '/opt/homebrew/bin/npm'),
    ],
];
