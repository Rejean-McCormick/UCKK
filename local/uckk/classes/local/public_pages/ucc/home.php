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
                ['label' => 'Penseurs', 'description' => 'Entrer dans le savoir par les personnes qui ont formulé, transmis, commenté ou contesté des idées.', 'url' => '/local/uckk/thinkers.php'],
                ['label' => 'Glossaire', 'description' => 'Suivre les mots, expressions et concepts à travers les époques, les auteurs et les traditions.', 'url' => '/local/uckk/glossary.php'],
                ['label' => 'Corpus chrétien', 'description' => 'Explorer le point de départ historique du projet : textes, œuvres, institutions, débats et penseurs chrétiens.', 'url' => '/local/uckk/christian.php'],
                ['label' => 'Méthode éditoriale', 'description' => 'Comprendre le rôle de l’IA, la révision des arguments, les corrections et les limites du corpus.', 'url' => '/local/uckk/method.php'],
                ['label' => 'Transparence', 'description' => 'Distinguer clairement l’initiative indépendante de toute reconnaissance ecclésiale officielle.', 'url' => '/local/uckk/transparency.php'],
                ['label' => 'Médiathèque', 'description' => 'Parcourir les documents, médias, œuvres, références et traces reliés au corpus.', 'url' => '/local/uckk/mediatheque.php'],
            ],

            'sections' => [
                [
                    'type' => 'positioning',
                    'eyebrow' => 'Premier axe',
                    'title' => 'Le savoir passe par les humains',
                    'body' => 'Tout savoir transmis jusqu’à nous a été formulé, conservé, traduit, commenté, enseigné, contesté ou transformé par des personnes. Univers-Cité prend ces trajectoires humaines comme premier fil conducteur.',
                    'items' => [
                        'Relier un penseur à ses œuvres, ses sources, son époque, ses influences et sa réception.',
                        'Juxtaposer des perspectives plutôt que réduire l’histoire à une seule interprétation.',
                        'Conserver aussi les idées devenues discutables ou archaïques lorsqu’elles demeurent importantes pour comprendre une époque.',
                    ],
                ],
                [
                    'type' => 'architecture',
                    'eyebrow' => 'Second axe',
                    'title' => 'Les mots forment une autre cartographie',
                    'body' => 'Un mot change de sens selon les siècles, les auteurs et les traditions. Le glossaire permet de suivre ces déplacements et de relier les personnes, les œuvres et les débats au vocabulaire qu’ils emploient.',
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
                    'type' => 'horizon',
                    'eyebrow' => 'Horizon',
                    'title' => 'Un corpus appelé à dépasser son point de départ',
                    'body' => 'Le corpus chrétien constitue aujourd’hui une base importante du projet. Il n’en fixe pas la frontière ultime. L’ambition est d’ajouter progressivement d’autres auteurs, traditions et œuvres afin de construire une encyclopédie relationnelle de la pensée humaine.',
                ],
            ],

            'cardsheading' => 'Deux axes, quatre repères',
            'cards' => [
                [
                    'title' => 'Penseurs et auteurs',
                    'body' => 'Le premier axe : suivre les humains qui ont produit, transmis ou transformé les idées.',
                    'url' => '/local/uckk/thinkers.php',
                    'actionlabel' => 'Explorer les penseurs',
                    'type' => 'people',
                ],
                [
                    'title' => 'Glossaire',
                    'body' => 'Le second axe : suivre les mots et expressions qui permettent de relier les œuvres et les époques.',
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
                    'body' => 'L’IA arbitre par défaut les synthèses et les demandes de correction; ses propres erreurs restent révisables et doivent pouvoir être contestées par des arguments.',
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
                    'body' => 'Documents, médias, références et œuvres qui donnent au corpus ses sources et ses points d’appui.',
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
                ['label' => 'Premier axe', 'value' => 'Penseurs, auteurs et trajectoires humaines'],
                ['label' => 'Second axe', 'value' => 'Glossaire de mots, expressions et concepts'],
                ['label' => 'Point de départ', 'value' => 'Héritages chrétiens, dont un important corpus catholique'],
                ['label' => 'Horizon', 'value' => 'Relier progressivement les intellectuels marquants de l’histoire humaine'],
            ],

            'cta' => [
                'title' => 'Commencer par les personnes ou par les mots',
                'body' => 'Les deux axes se répondent : un penseur renvoie à des concepts; un concept renvoie à des auteurs, des œuvres et des contextes.',
                'url' => '/local/uckk/thinkers.php',
                'label' => 'Entrer par les penseurs',
            ],
        ];
    }
}
