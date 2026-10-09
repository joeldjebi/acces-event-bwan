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
    'solution_groups' => [
        'vendre' => [
            'label' => 'Vendre & encaisser',
            'slugs' => ['billetterie-360', 'paiements-digitaux', 'marketing-crm'],
        ],
        'controler' => [
            'label' => 'Contrôler & sécuriser',
            'slugs' => ['controle-acces', 'accreditation', 'securite-surete-event', 'pass-parking'],
        ],
        'organiser' => [
            'label' => 'Organiser le terrain',
            'slugs' => ['gestion-benevoles', 'signaletique-evenementielle', 'accessoiriste-evenement'],
        ],
        'developper' => [
            'label' => 'Développer',
            'slugs' => ['baobaa', 'tourisme'],
        ],
    ],

    // Formats d'événements de l'accueil : domaines recommandés pour chacun (par slug).
    'event_types' => [
        'concerts' => [
            'label' => 'Concerts & festivals',
            'text' => "Grandes jauges, plusieurs portes d'entrée et une forte affluence aux mêmes heures.",
            'slugs' => ['billetterie-360', 'controle-acces', 'paiements-digitaux', 'securite-surete-event'],
        ],
        'salons' => [
            'label' => 'Salons & expositions',
            'text' => "Exposants, visiteurs professionnels et rendez-vous d'affaires sur plusieurs jours.",
            'slugs' => ['accreditation', 'baobaa', 'billetterie-360', 'signaletique-evenementielle'],
        ],
        'sport' => [
            'label' => 'Sport & stades',
            'text' => 'Tribunes, zones à accès restreint, flux de véhicules et équipes nombreuses.',
            'slugs' => ['controle-acces', 'pass-parking', 'securite-surete-event', 'gestion-benevoles'],
        ],
        'institutions' => [
            'label' => 'Conférences & institutions',
            'text' => 'Invités, délégations, presse et protocole à accueillir dans les règles.',
            'slugs' => ['accreditation', 'controle-acces', 'accessoiriste-evenement', 'tourisme'],
        ],
    ],

    'maintenance' => [
        'rebuild_token' => env('ACCES_EVENT_REBUILD_TOKEN'),
        'npm_binary' => env('ACCES_EVENT_NPM_BINARY', '/opt/homebrew/bin/npm'),
    ],
];
