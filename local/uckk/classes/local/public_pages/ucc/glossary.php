<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public glossary page for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class glossary {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'editorial',
            'eyebrow' => 'Second axe',
            'title' => 'Glossaire des mots et expressions',
            'subtitle' => 'Suivre le sens des mots à travers les auteurs, les œuvres, les traductions et les époques.',
            'summary' => 'Le glossaire n’est pas un dictionnaire figé. Il sert de carte relationnelle : un même terme peut porter plusieurs sens selon le contexte, changer de traduction, être disputé ou devenir le point de rencontre de traditions différentes.',
            'sections' => [
                [
                    'title' => 'Un mot n’a pas toujours un seul sens',
                    'body' => 'Les définitions sont situées. Lorsqu’un terme change d’usage entre deux auteurs ou deux époques, le glossaire doit permettre de conserver cette différence au lieu de forcer une définition unique.',
                ],
                [
                    'title' => 'Les mots relient le corpus',
                    'body' => 'Chaque entrée peut renvoyer à des personnes, œuvres, sources, cours, débats et notions voisines. Le glossaire devient ainsi un second chemin pour naviguer dans la même encyclopédie.',
                ],
                [
                    'title' => 'Traduction, nuance et anachronisme',
                    'body' => 'Un terme ancien ne doit pas être automatiquement interprété avec son sens contemporain. Lorsque la traduction ou le contexte modifie la compréhension, cette nuance doit être visible.',
                ],
                [
                    'title' => 'Du corpus chrétien vers un vocabulaire plus vaste',
                    'body' => 'Le glossaire actuel peut contenir de nombreux termes provenant des traditions chrétiennes et catholiques. Son architecture est toutefois destinée à accueillir des concepts issus d’autres corpus à mesure que l’encyclopédie s’élargit.',
                ],
            ],
            'cardsheading' => 'Relier les mots',
            'cards' => [
                ['title' => 'Penseurs', 'body' => 'Retrouver les personnes qui emploient, définissent ou transforment un concept.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Explorer les penseurs', 'type' => 'people'],
                ['title' => 'Cours', 'body' => 'Voir les espaces pédagogiques actuellement reliés aux thèmes et concepts du corpus.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Explorer les cours', 'type' => 'courses'],
                ['title' => 'Médiathèque', 'body' => 'Consulter des œuvres, documents et références qui donnent un contexte aux mots.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Voir les sources', 'type' => 'media'],
            ],
            'notices' => [
                [
                    'title' => 'Définitions révisables',
                    'body' => 'Une définition peut être corrigée ou nuancée lorsqu’une objection argumentée montre qu’elle simplifie trop un auteur, une époque, une traduction ou une tradition.',
                    'type' => 'light',
                ],
            ],
        ];
    }
}
