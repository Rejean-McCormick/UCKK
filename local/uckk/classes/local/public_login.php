<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Shared public login presentation contract for the Univers-Cités.
 *
 * Authentication remains entirely owned by Moodle. This class only provides
 * the public identity shown around Moodle's standard login form and a stable
 * "Explorer sans connexion" exit back to the active public Univers-Cité.
 *
 * @package local_uckk
 * @copyright 2026 Univers-Cité King Klown
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

final class public_login {
    /**
     * Return the login-shell definition for the active public site.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        $site = public_site_context::current();

        $definitions = [
            public_site_context::SITE_UCKK => [
                'theme' => public_site_context::THEME_UCKK,
                'eyebrow' => 'Univers-Cité King Klown',
                'title' => 'Lire le Grand Jeu social',
                'summary' => 'UCKK est une univers-cité émergente issue du mouvement kOA. Elle accompagne des joueurs lucides capables de lire les systèmes, d’agir avec intégrité et de transformer les règles.',
                'points' => [
                    ['title' => 'Comprendre.', 'body' => 'Lire les règles, récits, institutions et pouvoirs qui structurent le monde social.'],
                    ['title' => 'Agir.', 'body' => 'Apprendre par l’enquête, la preuve, les assemblées, les défis et la mémoire.'],
                ],
            ],
            public_site_context::SITE_UCC => [
                'theme' => public_site_context::THEME_UCC,
                'eyebrow' => 'Univers-Cité chrétienne',
                'title' => 'Explorer les traditions, les œuvres et les idées',
                'summary' => 'L’UCC ouvre un espace public de lecture et d’étude consacré aux corpus chrétiens, à leurs institutions, à leurs œuvres et à leurs dialogues avec la philosophie, les sciences et la société.',
                'points' => [
                    ['title' => 'Lire.', 'body' => 'Parcourir les sources, les penseurs, les traditions et leurs contextes.'],
                    ['title' => 'Relier.', 'body' => 'Explorer les Voies, les cours, la médiathèque et les repères documentaires sans compte.'],
                ],
            ],
            public_site_context::SITE_MATH => [
                'theme' => public_site_context::THEME_MATH,
                'eyebrow' => 'Univers-Cité des mathématiques',
                'title' => 'Comprendre. Démontrer. Modéliser.',
                'summary' => 'L’Univers-Cité des mathématiques organise des parcours publics autour des structures, des preuves, des modèles, de la calculabilité et de l’expérimentation mathématique.',
                'points' => [
                    ['title' => 'Comprendre.', 'body' => 'Explorer les concepts, les structures et les modèles qui rendent les phénomènes intelligibles.'],
                    ['title' => 'Démontrer.', 'body' => 'Parcourir les parcours et les cours publics avant toute connexion au campus.'],
                ],
            ],
        ];

        $definition = $definitions[$site] ?? $definitions[public_site_context::SITE_UCKK];
        $definition['site'] = $site;
        $definition['explorelabel'] = 'Explorer sans connexion';
        $definition['exploreurl'] = (new moodle_url('/local/uckk/index.php', [
            'theme' => $definition['theme'],
        ]))->out(false);

        return $definition;
    }
}
