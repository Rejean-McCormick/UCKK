<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Public home page definition for Univers-Cité chrétienne.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class home {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'display',
            'eyebrow' => 'Encyclopédie relationnelle de la pensée',
            'title' => 'Univers-Cité chrétienne',
            'subtitle' => 'Les humains, les œuvres, les idées et les mots qui traversent l’histoire.',
            'summary' => 'Univers-Cité chrétienne construit progressivement un corpus encyclopédique reliant penseurs, auteurs, œuvres, concepts et expressions. Le projet commence par un riche héritage chrétien — notamment catholique — puis a vocation à s’élargir aux intellectuels marquants de l’histoire de l’humanité.',

            'quicklinks' => [
                ['label' => 'Plans et lectures', 'description' => '110 plans de séminaire : passages, objectifs, activités, évaluations et limites documentaires.', 'url' => '/local/uckk/ucc_study.php'],
                ['label' => 'Références par cours', 'description' => 'Consulter les sources externes sélectionnées et retrouver leurs cours.', 'url' => '/local/uckk/ucc_study.php?view=references'],
                ['label' => 'Penseurs', 'description' => 'Entrer dans le savoir par les personnes qui ont formulé, transmis, commenté ou contesté des idées.', 'url' => '/local/uckk/thinkers.php'],
                ['label' => 'Glossaire', 'description' => 'Suivre les mots, expressions et concepts à travers les époques, les auteurs et les traditions.', 'url' => '/local/uckk/glossary.php'],
                ['label' => 'Corpus chrétien', 'description' => 'Explorer le point de départ historique du projet : textes, œuvres, institutions, débats et penseurs chrétiens.', 'url' => '/local/uckk/christian.php'],
                ['label' => 'Méthode éditoriale', 'description' => 'Comprendre la provenance de la sélection par IA, la révision des arguments, les ajouts déclarés et les limites du corpus.', 'url' => '/local/uckk/method.php'],
                ['label' => 'Transparence', 'description' => 'Distinguer clairement l’initiative indépendante de toute reconnaissance ecclésiale officielle.', 'url' => '/local/uckk/transparency.php'],
                ['label' => 'Médiathèque', 'description' => 'Parcourir les documents, médias, œuvres, références et traces reliés au corpus.', 'url' => '/local/uckk/mediatheque.php'],
                ['label' => 'Le Dieu cosmique', 'description' => 'Ouvrage-charnière de Jacques Languirand et Jean Proulx : une carte comparative pour parcourir plusieurs réflexions sur le divin, la nature, la raison et le cosmos.', 'url' => '/local/uckk/programs.php'],
            ],

            'sections' => [
                [
                    'title' => 'Lire les sources, comparer, argumenter',
                    'body' => 'Les 11 Voies disposent de 110 plans de séminaire. Chaque plan propose une question, des passages à lire, cinq séances, une production et un barème. Un socle conseillé relie Écriture, théologie, histoire, vie spirituelle et méthode critique. Les plans restent soumis à relecture ; les espaces Moodle ouverts sont indiqués séparément.',
                    'items' => ['Œuvres jusqu’en 1926 inclusivement ; exceptions : Teilhard de Chardin, Fratelli tutti et Le Dieu cosmique.', 'Distinguer source biblique, définition conciliaire, théologien, témoignage historique et interprétation éditoriale.', 'Les langues, éditions, passages et droits encore à vérifier sont signalés.'],
                ],
                [
                    'type' => 'method',
                    'eyebrow' => 'Provenance éditoriale',
                    'title' => 'Une sélection générale par IA, deux ajouts déclarés',
                    'body' => 'La sélection générale des auteurs, des œuvres, des sujets et des axes d’étude a été effectuée par l’intelligence artificielle. Deux ajouts relèvent d’un choix explicite du concepteur : Pierre Teilhard de Chardin et Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx.',
                    'items' => [
                        'La provenance par IA est déclarée sans prétendre que l’IA serait parfaitement neutre ou objective.',
                        'Teilhard et Le Dieu cosmique sont identifiés comme des ajouts explicites du concepteur, distincts de la sélection générale.',
                        'Le Dieu cosmique sert notamment de pont vers des philosophes et des conceptions non chrétiennes du divin afin d’éviter le cloisonnement doctrinal et de favoriser le dialogue.',
                    ],
                ],
                [
                    'type' => 'positioning',
                    'eyebrow' => 'Vue principale',
                    'title' => 'Le savoir passe par les humains',
                    'body' => 'Tout savoir transmis jusqu’à nous a été formulé, conservé, traduit, commenté, enseigné, contesté ou transformé par des personnes et des collectifs. Dans ce corpus, Univers-Cité prend ces trajectoires humaines comme vue principale, sans en faire une règle universelle pour toutes les Univers-Cités.',
                    'items' => [
                        'Relier un penseur à ses œuvres, ses sources, son époque, ses influences et sa réception.',
                        'Juxtaposer des perspectives plutôt que réduire l’histoire à une seule interprétation.',
                        'Conserver aussi les idées devenues discutables ou archaïques lorsqu’elles demeurent importantes pour comprendre une époque.',
                    ],
                ],
                [
                    'type' => 'architecture',
                    'eyebrow' => 'Index transversal',
                    'title' => 'Les mots relient les trajectoires',
                    'body' => 'Un mot change de sens selon les siècles, les auteurs et les traditions. Le glossaire n’est pas un second corpus ni un axe parallèle : il inverse la navigation afin de relier les personnes, les œuvres, les sources et les assertions par les concepts et expressions qu’ils mobilisent.',
                    'items' => [
                        'Entrer par un mot ou une expression plutôt que par une biographie.',
                        'Comparer les définitions, usages, traductions et déplacements de sens.',
                        'Relier chaque concept aux auteurs et aux œuvres qui l’ont porté ou transformé.',
                    ],
                ],
                [
                    'type' => 'method',
                    'eyebrow' => 'Lire sans réduire',
                    'title' => 'Plusieurs niveaux de lecture peuvent coexister',
                    'body' => 'Une œuvre ancienne peut être lue au premier degré, replacée dans son contexte historique, étudiée pour son influence, interrogée symboliquement ou confrontée aux connaissances contemporaines. Ces lectures ne sont pas confondues : elles sont distinguées et mises en relation.',
                    'items' => [
                        'Lecture littérale ou argumentative.',
                        'Lecture historique et contextualisée.',
                        'Lecture symbolique, philosophique ou théologique lorsque le texte s’y prête.',
                        'Étude critique d’idées dépassées pour leur valeur documentaire et historique.',
                    ],
                ],
                [
                    'type' => 'orientation',
                    'eyebrow' => 'Ouvrage-charnière',
                    'title' => 'Le Dieu cosmique comme continuum de navigation',
                    'body' => 'Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx, est référencé explicitement comme ouvrage-charnière. UCC l’utilise moins comme interprétation à adopter que comme carte de passage entre plusieurs manières de penser le divin : raison et émerveillement, immanence et transcendance, nature et création, science et métaphysique, expérience religieuse et évolution.',
                    'items' => [
                        'L’ouvrage est nommé et cité comme source d’orientation, sans être présenté comme doctrine UCC; son intégration au corpus est un choix explicitement déclaré du concepteur.',
                        'Une Voie dédiée permet de suivre les auteurs, concepts et textes historiques auxquels cette cartographie conduit.',
                        'La table des matières exacte n’est pas reconstruite sans source vérifiable; le parcours UCC est une architecture éditoriale distincte.',
                    ],
                ],
                [
                    'type' => 'horizon',
                    'eyebrow' => 'Horizon',
                    'title' => 'Un corpus appelé à dépasser son point de départ',
                    'body' => 'Le corpus chrétien constitue aujourd’hui une base importante du projet. Il n’en fixe pas la frontière ultime. L’ambition est d’ajouter progressivement d’autres auteurs, traditions et œuvres afin de construire une encyclopédie relationnelle de la pensée humaine.',
                ],
            ],

            'cardsheading' => 'Une vue principale, plusieurs chemins',
            'cards' => [
                [
                    'title' => 'Penseurs et auteurs',
                    'body' => 'La vue principale du corpus : suivre les humains et collectifs qui ont produit, transmis ou transformé les idées.',
                    'url' => '/local/uckk/thinkers.php',
                    'actionlabel' => 'Explorer les penseurs',
                    'type' => 'people',
                ],
                [
                    'title' => 'Glossaire',
                    'body' => 'Un index transversal dérivé : partir des mots et concepts pour retrouver les personnes, œuvres, sources et contextes qui les portent.',
                    'url' => '/local/uckk/glossary.php',
                    'actionlabel' => 'Explorer les mots',
                    'type' => 'glossary',
                ],
                [
                    'title' => 'Corpus chrétien',
                    'body' => 'Le point de départ actuel : héritages chrétiens et catholiques, arts, institutions, débats, œuvres et traditions intellectuelles.',
                    'url' => '/local/uckk/christian.php',
                    'actionlabel' => 'Comprendre le corpus',
                    'type' => 'corpus',
                ],
                [
                    'title' => 'Méthode éditoriale',
                    'body' => 'L’IA a effectué la sélection générale du corpus et intervient aussi dans les synthèses et les demandes de correction; ses propres erreurs restent révisables et doivent pouvoir être contestées par des arguments.',
                    'url' => '/local/uckk/method.php',
                    'actionlabel' => 'Lire la méthode',
                    'type' => 'method',
                ],
                [
                    'title' => 'Transparence',
                    'body' => 'Statut indépendant, relation éventuelle avec des autorités ecclésiales et distinction entre corpus catholique et reconnaissance officielle.',
                    'url' => '/local/uckk/transparency.php',
                    'actionlabel' => 'Voir le statut',
                    'type' => 'integrity',
                ],
                [
                    'title' => 'Médiathèque',
                    'body' => 'Documents, médias, références et œuvres qui donnent au corpus ses sources et ses points d’appui, dont Le Dieu cosmique comme ouvrage-charnière explicitement référencé.',
                    'url' => '/local/uckk/mediatheque.php',
                    'actionlabel' => 'Ouvrir la Médiathèque',
                    'type' => 'media',
                ],
            ],

            'notices' => [
                [
                    'title' => 'Initiative chrétienne indépendante',
                    'body' => 'Univers-Cité chrétienne ne se présente pas comme une institution officielle de l’Église catholique. Les corpus catholiques sont étudiés comme des traditions historiques, intellectuelles, spirituelles, artistiques et institutionnelles documentées.',
                    'type' => 'institutional',
                ],
                [
                    'title' => 'Corpus révisable',
                    'body' => 'Le contenu peut comporter des simplifications, erreurs ou coquilles. Les objections argumentées peuvent conduire à une réévaluation, une nuance, une correction ou au maintien du passage concerné.',
                    'type' => 'light',
                ],
            ],

            'metadataheading' => 'Repères du projet',
            'metadata' => [
                ['label' => 'Nature', 'value' => 'Encyclopédie relationnelle en construction'],
                ['label' => 'Vue principale', 'value' => 'Personnes, auteurs, penseurs et collectifs intellectuels'],
                ['label' => 'Index transversal', 'value' => 'Glossaire dérivé des concepts reliés aux personnes, œuvres, sources et assertions'],
                ['label' => 'Point de départ', 'value' => 'Héritages chrétiens, dont un important corpus catholique'],
                ['label' => 'Provenance éditoriale', 'value' => 'Sélection générale par IA; ajouts déclarés : Pierre Teilhard de Chardin et Le Dieu cosmique'],
                ['label' => 'Horizon', 'value' => 'Relier progressivement les intellectuels marquants de l’histoire humaine'],
            ],

            'cta' => [
                'title' => 'Entrer par les personnes, traverser par les concepts',
                'body' => 'La navigation principale part des personnes; le glossaire permet ensuite de retraverser le même corpus par les mots et concepts qui relient leurs œuvres, leurs sources et leurs positions.',
                'url' => '/local/uckk/thinkers.php',
                'label' => 'Entrer par les penseurs',
            ],
        ];
    }
}
