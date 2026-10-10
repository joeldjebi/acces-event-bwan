<?php

namespace Database\Seeders;

use App\Models\PlatformApplication;
use App\Models\SitePage;
use App\Models\SiteSetting;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate([
            'email' => 'admin@acces-event.test',
        ], [
            'name' => 'Administrateur ACCÈS EVENT',
            'password' => 'password',
            'role' => 'admin',
        ]);

        // Le premier administrateur est le propriétaire, sauf si un propriétaire existe déjà.
        if (! User::query()->where('is_owner', true)->exists()) {
            $admin->forceFill(['is_owner' => true])->save();
        }

        $this->seedSolutions();
        $this->seedApplications();
        $this->seedPages();
        SiteSetting::seedDefaults();
    }

    private function seedPages(): void
    {
        foreach ($this->pages() as $page) {
            SitePage::query()->updateOrCreate([
                'key' => $page['key'],
            ], $page);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function pages(): array
    {
        return [
            [
                'key' => 'home',
                'name' => 'Accueil',
                'eyebrow' => "Le hub de solutions pour l'événementiel en Afrique",
                'heading' => "Tout l'événement, dans une seule technologie.",
                'summary' => 'Du premier billet vendu au dernier spectateur sorti, ACCÈS EVENT sécurise, fluidifie et valorise chaque étape de votre événement.',
                'body' => "Une suite intégrée de douze solutions pensées pour les promoteurs, salons, festivals et institutions d'Afrique.",
                'cta_label' => 'Demander une démonstration',
                'cta_url' => '/contact',
                'seo_title' => "Tout l'événement dans une seule technologie",
                'seo_description' => config('accessevent.brand.promise'),
                'is_published' => true,
            ],
            [
                'key' => 'ecosystem',
                'name' => 'Écosystème',
                'eyebrow' => 'Écosystème',
                'heading' => 'Toutes nos plateformes connectées au même système central.',
                'summary' => "ACCÈS EVENT relie la billetterie, le contrôle d'accès, l'accréditation, les paiements et les opérations terrain dans une architecture unique.",
                'body' => 'Les applications partagent le même cœur technique pour garantir la cohérence des titres, des droits, des passages et des recettes.',
                'cta_label' => 'Une seule donnée, partout, en temps réel.',
                'cta_url' => null,
                'seo_title' => 'Écosystème',
                'seo_description' => 'Toutes les applications et plateformes propriétaires ACCÈS EVENT réunies dans un système central.',
                'is_published' => true,
            ],
            [
                'key' => 'about',
                'name' => 'Qui sommes-nous',
                'eyebrow' => 'Qui sommes-nous',
                'heading' => 'Une technologie événementielle pensée pour les réalités africaines.',
                'summary' => 'ACCÈS EVENT est la solution événementielle de EIGTH CORPORATE, au sein du groupe BWAN HOLDING.',
                'body' => "Notre conviction est simple : l'Afrique mérite des technologies événementielles de niveau mondial, adaptées au terrain.\n\nDe la billetterie au tourisme, en passant par le contrôle d'accès, l'accréditation, le paiement et la sécurité, ACCÈS EVENT accompagne promoteurs, salons, festivals et institutions à chaque étape.\n\nNotre ambition est de devenir le hub de référence de l'événementiel en Afrique, en plaçant la sécurité des recettes, la fluidité de l'expérience et la maîtrise des données au cœur de chaque événement.",
                'cta_label' => null,
                'cta_url' => null,
                'seo_title' => 'Qui sommes-nous',
                'seo_description' => 'ACCÈS EVENT est la solution événementielle de EIGTH CORPORATE au sein du groupe BWAN HOLDING.',
                'is_published' => true,
            ],
            [
                'key' => 'contact',
                'name' => 'Contact',
                'eyebrow' => 'Contact',
                'heading' => 'Demandez une démonstration personnalisée.',
                'summary' => "Partagez les grandes lignes de votre événement. L'équipe ACCÈS EVENT vous aidera à identifier les modules les plus adaptés.",
                'body' => 'Formulaire de contact et demande de démonstration.',
                'cta_label' => 'Envoyer la demande',
                'cta_url' => null,
                'seo_title' => 'Contact',
                'seo_description' => "Demandez une démonstration personnalisée de l'écosystème ACCÈS EVENT.",
                'is_published' => true,
            ],
        ];
    }

    private function seedSolutions(): void
    {
        foreach ($this->solutions() as $solutionData) {
            $benefits = $solutionData['benefits'];
            $tools = $solutionData['tools'];
            unset($solutionData['benefits'], $solutionData['tools']);

            $solution = Solution::query()->updateOrCreate([
                'slug' => $solutionData['slug'],
            ], $solutionData);

            $solution->benefits()->delete();
            $solution->tools()->delete();

            foreach ($benefits as $position => $benefit) {
                $solution->benefits()->create([
                    'title' => $benefit[0],
                    'description' => $benefit[1],
                    'position' => $position + 1,
                ]);
            }

            foreach ($tools as $position => $tool) {
                $solution->tools()->create([
                    'name' => $tool[0],
                    'description' => $tool[1],
                    'position' => $position + 1,
                ]);
            }
        }
    }

    private function seedApplications(): void
    {
        foreach ($this->applications() as $applicationData) {
            $solutionSlugs = $applicationData['solutions'];
            unset($applicationData['solutions']);

            $application = PlatformApplication::query()->updateOrCreate([
                'slug' => $applicationData['slug'],
            ], $applicationData);

            $solutionIds = Solution::query()
                ->whereIn('slug', $solutionSlugs)
                ->pluck('id')
                ->all();

            $application->solutions()->sync($solutionIds);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function applications(): array
    {
        return [
            [
                'name' => 'ACCÈS TICKETS',
                'slug' => 'acces-tickets',
                'role' => 'Application mobile de billetterie : billets mobiles, portefeuilles numériques et distribution.',
                'description' => 'ACCÈS TICKETS permet la vente, la diffusion et la présentation des billets numériques sur mobile.',
                'position' => 1,
                'is_published' => true,
                'solutions' => ['billetterie-360'],
            ],
            [
                'name' => 'ACCÈS BUSINESS',
                'slug' => 'acces-business',
                'role' => 'Plateforme de billets électroniques dédiée aux professionnels et revendeurs.',
                'description' => 'ACCÈS BUSINESS structure les achats en volume, la distribution professionnelle et le suivi des revendeurs.',
                'position' => 2,
                'is_published' => true,
                'solutions' => ['billetterie-360'],
            ],
            [
                'name' => 'DISTRI ACCÈS',
                'slug' => 'distri-acces',
                'role' => 'Suivi et distribution des billets physiques sur le terrain.',
                'description' => 'DISTRI ACCÈS donne une visibilité terrain sur la circulation des billets physiques et les points de distribution.',
                'position' => 3,
                'is_published' => true,
                'solutions' => ['billetterie-360'],
            ],
            [
                'name' => 'ACCÈS VENDOR',
                'slug' => 'acces-vendor',
                'role' => 'Application des ambassadeurs pour la vente de billets digitaux.',
                'description' => 'ACCÈS VENDOR équipe les ambassadeurs et points relais pour vendre des billets numériques de manière tracée.',
                'position' => 4,
                'is_published' => true,
                'solutions' => ['billetterie-360'],
            ],
            [
                'name' => 'ACCÈS EXPO',
                'slug' => 'acces-expo',
                'role' => 'Gestion des salons et expositions, web et mobile.',
                'description' => 'ACCÈS EXPO accompagne les salons, exposants, visiteurs et dispositifs de contrôle associés.',
                'position' => 5,
                'is_published' => true,
                'solutions' => ['billetterie-360', 'accreditation'],
            ],
            [
                'name' => 'ACCÈS CHECK',
                'slug' => 'acces-check',
                'role' => 'Authentification et validation des titres par les contrôleurs.',
                'description' => "ACCÈS CHECK est l'application terrain des agents de contrôle pour vérifier les billets, badges et pass.",
                'position' => 6,
                'is_published' => true,
                'solutions' => ['controle-acces', 'accreditation'],
            ],
            [
                'name' => 'BIG ACCESS',
                'slug' => 'big-access',
                'role' => "Super-application et système central de contrôle d'accès.",
                'description' => 'BIG ACCESS centralise la validation des billets, badges, pass parking et droits associés.',
                'position' => 7,
                'is_published' => true,
                'solutions' => ['controle-acces', 'accreditation', 'pass-parking', 'gestion-benevoles'],
            ],
            [
                'name' => 'MY ACCRED',
                'slug' => 'my-accred',
                'role' => "Plateforme d'accréditation et d'édition des badges.",
                'description' => "MY ACCRED gère les demandes, validations, profils, zones et badges d'accréditation.",
                'position' => 8,
                'is_published' => true,
                'solutions' => ['accreditation'],
            ],
            [
                'name' => 'PASS PARKING',
                'slug' => 'pass-parking-app',
                'role' => 'Gestion du stationnement : réservation, contrôle et flux.',
                'description' => 'PASS PARKING permet de vendre, réserver et valider les droits de stationnement.',
                'position' => 9,
                'is_published' => true,
                'solutions' => ['pass-parking'],
            ],
            [
                'name' => 'FINEO PAY, X PAYE & JEKO',
                'slug' => 'fineo-pay-x-paye-jeko',
                'role' => "Paiement et encaissement intégrés à l'écosystème.",
                'description' => 'Ces solutions couvrent le paiement mobile, la carte bancaire et les encaissements sans espèces.',
                'position' => 10,
                'is_published' => true,
                'solutions' => ['paiements-digitaux'],
            ],
            [
                'name' => 'BAOBAA',
                'slug' => 'baobaa-app',
                'role' => "Place de marché B2B de l'événementiel.",
                'description' => 'BAOBAA connecte promoteurs, prestataires, fournisseurs et partenaires institutionnels.',
                'position' => 11,
                'is_published' => true,
                'solutions' => ['baobaa'],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function solutions(): array
    {
        return [
            [
                'title' => 'Billetterie 360',
                'slug' => 'billetterie-360',
                'subtitle' => 'Vendez partout, encaissez en sécurité, maîtrisez vos recettes.',
                'short_description' => 'Billets physiques sécurisés, billets mobiles, billets électroniques et portefeuilles numériques distribués sur tous les canaux.',
                'content' => "ACCÈS EVENT couvre l'intégralité de la chaîne de billetterie : du billet papier ultra-sécurisé au billet dans le wallet du smartphone.\n\nNotre approche 360 signifie que vous touchez chaque profil de spectateur là où il se trouve, tout en gardant une vision unifiée des ventes.",
                'position' => 1,
                'is_published' => true,
                'benefits' => [
                    ['Plus de ventes', 'Présence sur tous les canaux, physiques comme digitaux.'],
                    ['Moins de fraude', 'Billets sécurisés et contrôles en temps réel.'],
                    ['Vision unifiée', 'Toutes les ventes consolidées dans un seul tableau de bord.'],
                ],
                'tools' => [
                    ['ACCÈS TICKETS', 'Application mobile de billetterie, billets mobiles et portefeuilles numériques.'],
                    ['DISTRI ACCÈS', 'Suivi et distribution des billets physiques sur le terrain.'],
                    ['ACCÈS BUSINESS', 'Plateforme de billets électroniques pour professionnels et revendeurs.'],
                ],
            ],
            [
                'title' => "Contrôle d'accès",
                'slug' => 'controle-acces',
                'subtitle' => 'Un seul cerveau pour toutes vos entrées.',
                'short_description' => 'Authentification et validation en temps réel des entrées, pilotées par BIG ACCESS.',
                'content' => "ACCÈS EVENT met à disposition une plateforme de contrôle d'accès complète : authentification, validation et suivi temps réel.\n\nLe système central gère les billets, badges et pass parking via API ou via BIG ACCESS.",
                'position' => 2,
                'is_published' => true,
                'benefits' => [
                    ['Zéro double entrée', 'Chaque titre est valide une seule fois.'],
                    ['Vue temps réel', 'Le remplissage de chaque zone est visible à tout moment.'],
                    ['Interopérabilité', 'Une API connecte les systèmes de billetterie.'],
                ],
                'tools' => [
                    ['ACCÈS CHECK', 'Application de contrôle pour les agents terrain.'],
                    ['BIG ACCESS', 'Système central de contrôle des titres.'],
                ],
            ],
            [
                'title' => 'Accréditation',
                'slug' => 'accreditation',
                'subtitle' => 'La bonne personne, au bon endroit, avec les bons droits.',
                'short_description' => 'Gestion complète des badges, droits et zones pour équipes, presse, VIP et partenaires.',
                'content' => "ACCÈS EVENT gère l'accréditation de bout en bout : demandes, validations, zones et édition des badges.\n\nChaque badge devient un titre d'accès intelligent contrôlé en temps réel par BIG ACCESS.",
                'position' => 3,
                'is_published' => true,
                'benefits' => [
                    ['Sécurité', 'Seules les personnes autorisées franchissent chaque zone.'],
                    ['Traçabilité', 'Chaque passage est journalisé.'],
                    ['Temps gagné', 'Demandes et validations centralisées.'],
                ],
                'tools' => [
                    ['MY ACCRED', 'Plateforme de gestion des demandes et badges.'],
                    ['BIG ACCESS', 'Validation des badges sur site.'],
                ],
            ],
            [
                'title' => 'Marketing & CRM',
                'slug' => 'marketing-crm',
                'subtitle' => 'Vos données de billetterie deviennent votre levier de croissance.',
                'short_description' => 'Audience, campagnes ciblées et fidélisation à partir des données de billetterie.',
                'content' => "Le module Marketing & CRM transforme chaque vente en connaissance client.\n\nLes données d'achat et de fréquentation alimentent automatiquement votre base de contacts.",
                'position' => 4,
                'is_published' => true,
                'benefits' => [
                    ['Audience qualifiée', 'Une base de contacts qui vous appartient.'],
                    ['Ventes répétées', 'Vos anciens acheteurs reviennent au bon moment.'],
                    ['Décision claire', 'Des indicateurs pour piloter les prochains événements.'],
                ],
                'tools' => [],
            ],
            [
                'title' => 'Mise en relation B2B - BAOBAA',
                'slug' => 'baobaa',
                'subtitle' => "L'écosystème événementiel africain réuni au même endroit.",
                'short_description' => "Place de marché pour connecter promoteurs, prestataires et fournisseurs de l'événementiel.",
                'content' => "BAOBAA connecte promoteurs, prestataires techniques, fournisseurs et partenaires institutionnels.\n\nLa plateforme structure les demandes de devis, appels d'offres et profils vérifiés.",
                'position' => 5,
                'is_published' => true,
                'benefits' => [
                    ['Partenaires fiables', 'Des prestataires qualifiés par métier et par zone.'],
                    ['Gain de temps', 'Une plateforme au lieu de contacts dispersés.'],
                    ['Visibilité', 'Les prestataires accèdent à un marché régional structuré.'],
                ],
                'tools' => [
                    ['BAOBAA', "Place de marché B2B de l'événementiel."],
                ],
            ],
            [
                'title' => 'Paiements digitaux',
                'slug' => 'paiements-digitaux',
                'subtitle' => 'Encaissez de toutes les façons, perdez le moins possible.',
                'short_description' => 'Paiement mobile, carte bancaire et paiement sans espèces intégrés à toute la chaîne de valeur.',
                'content' => "ACCÈS EVENT intègre le paiement mobile, la carte bancaire et le paiement sans espèces pour la billetterie et les consommations sur site.\n\nLes flux financiers sont tracés et réconciliables.",
                'position' => 6,
                'is_published' => true,
                'benefits' => [
                    ['Zéro vente perdue', 'Chaque spectateur paie avec son moyen préféré.'],
                    ['Moins d’espèces', 'Le paiement sans espèces réduit les manipulations et pertes.'],
                    ['Recettes sécurisées', 'Les encaissements sont suivis et réconciliés.'],
                ],
                'tools' => [
                    ['FINEO PAY / X PAYE / JEKO', 'Solutions de paiement intégrées.'],
                    ['Paiement sans espèces sur site', 'Paiement par bracelet, carte ou application.'],
                ],
            ],
            [
                'title' => 'Pass Parking',
                'slug' => 'pass-parking',
                'subtitle' => 'Le stationnement aussi devient un accès maîtrisé.',
                'short_description' => 'Réservation, contrôle et suivi temps réel des flux véhicules.',
                'content' => "PASS PARKING applique la logique de la billetterie au stationnement.\n\nLe parking devient une expérience fluide et une source de revenus sécurisée.",
                'position' => 7,
                'is_published' => true,
                'benefits' => [
                    ['Fluidité', "Moins d'attente aux entrées parking."],
                    ['Revenus maîtrisés', 'Le stationnement devient une recette sécurisée.'],
                    ['Lisibilité', 'Le remplissage est suivi en temps réel.'],
                ],
                'tools' => [
                    ['PASS PARKING', 'Réservation, contrôle et suivi des parkings.'],
                    ['BIG ACCESS', 'Validation des pass véhicules.'],
                ],
            ],
            [
                'title' => 'Sécurité & Sûreté Event',
                'slug' => 'securite-surete-event',
                'subtitle' => 'Protéger le public, les biens et les recettes.',
                'short_description' => 'Dispositifs humains et technologiques pour protéger public, biens et recettes.',
                'content' => "ACCÈS EVENT combine contrôle d'accès fiable, gestion des flux et coordination terrain.\n\nLa sécurité est pensée comme une chaîne opérationnelle connectée.",
                'position' => 8,
                'is_published' => true,
                'benefits' => [
                    ['Sérénité', 'Un public protégé et un événement maîtrisé.'],
                    ['Réactivité', 'Coordination temps réel entre sécurité et accès.'],
                    ['Recettes protégées', 'Moins de fraude et moins de pertes.'],
                ],
                'tools' => [],
            ],
            [
                'title' => 'Signalétique événementielle',
                'slug' => 'signaletique-evenementielle',
                'subtitle' => 'Orienter, rassurer, valoriser votre marque à chaque pas.',
                'short_description' => "Conception et déploiement d'une signalétique claire pour orienter et valoriser.",
                'content' => "La signalétique ACCÈS EVENT oriente le public et soutient la gestion des flux.\n\nElle devient aussi un support de marque et de sponsoring.",
                'position' => 9,
                'is_published' => true,
                'benefits' => [
                    ['Public orienté', 'Chacun trouve son chemin sans stress.'],
                    ['Flux maîtrisés', 'La signalétique réduit les points de tension.'],
                    ['Marque valorisée', 'Un événement cohérent et professionnel.'],
                ],
                'tools' => [],
            ],
            [
                'title' => "Accessoiriste d'événement",
                'slug' => 'accessoiriste-evenement',
                'subtitle' => "Tous les supports de l'événement, livrés et prêts à l'emploi.",
                'short_description' => 'Bracelets, lanyards, porte-badges, supports et consommables compatibles.',
                'content' => "ACCÈS EVENT fournit les accessoires indispensables à l'exploitation terrain.\n\nCes supports sont choisis pour leur compatibilité avec les solutions d'accès et de paiement.",
                'position' => 10,
                'is_published' => true,
                'benefits' => [
                    ['Compatibilité', 'Des accessoires adaptés aux solutions de paiement sans espèces et d’accès.'],
                    ['Un seul interlocuteur', 'Tout se commande au même endroit.'],
                    ['Cohérence', 'Des supports aux couleurs de votre événement.'],
                ],
                'tools' => [],
            ],
            [
                'title' => 'Gestion des bénévoles',
                'slug' => 'gestion-benevoles',
                'subtitle' => 'Des équipes mobilisées, planifiées et reconnues.',
                'short_description' => 'Recrutement, planification et suivi des équipes bénévoles et terrain.',
                'content' => "ACCÈS EVENT aide à recruter, planifier et suivre les équipes bénévoles.\n\nLes affectations, plannings et pointages sont centralisés.",
                'position' => 11,
                'is_published' => true,
                'benefits' => [
                    ['Postes couverts', 'Chaque créneau dispose des effectifs nécessaires.'],
                    ['Organisation', 'Plannings et affectations sont clairs.'],
                    ['Suivi', 'Présences et heures sont journalisées.'],
                ],
                'tools' => [
                    ['BIG ACCESS', 'Badges et accès dédiés pour les bénévoles.'],
                ],
            ],
            [
                'title' => 'Tourisme',
                'slug' => 'tourisme',
                'subtitle' => "Faire de votre événement une porte d'entrée vers la destination.",
                'short_description' => "Valorisation des destinations, hébergements, transports et expériences autour de l'événement.",
                'content' => "Le volet Tourisme transforme l'affluence événementielle en opportunité locale.\n\nIl met en valeur les expériences, le séjour et les partenaires touristiques.",
                'position' => 12,
                'is_published' => true,
                'benefits' => [
                    ['Plus de retombées', 'Les visiteurs prolongent leur séjour et leurs dépenses.'],
                    ['Destination valorisée', "L'événement rayonne au-delà de ses murs."],
                    ['Expérience complète', "Le visiteur vit l'événement et la destination."],
                ],
                'tools' => [],
            ],
        ];
    }
}
