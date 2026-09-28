<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Shared public-site contract for Univers-Cité chrétienne.
 *
 * UCC remains the stable technical identifier. The public identity is
 * "Univers-Cité chrétienne"; references to Catholicism are reserved for
 * historically, intellectually or institutionally Catholic material.
 *
 * @package local_uckk
 * @copyright 2026 Univers-Cité chrétienne
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class site {
    /** @return array<int, array<string, mixed>> */
    public static function navigation(): array {
        return [
            ['key' => 'home', 'label' => 'Accueil', 'url' => '/local/uckk/index.php'],
            ['key' => 'thinkers', 'label' => 'Penseurs', 'url' => '/local/uckk/thinkers.php'],
            ['key' => 'glossary', 'label' => 'Glossaire', 'url' => '/local/uckk/glossary.php'],
            ['key' => 'christian', 'label' => 'Corpus chrétien', 'url' => '/local/uckk/christian.php'],
            ['key' => 'method', 'label' => 'Méthode', 'url' => '/local/uckk/method.php'],
            ['key' => 'transparency', 'label' => 'Transparence', 'url' => '/local/uckk/transparency.php'],
            ['key' => 'mediatheque', 'label' => 'Médiathèque', 'url' => '/local/uckk/mediatheque.php'],
            ['key' => 'contact', 'label' => 'Contact', 'url' => '/local/uckk/contact.php'],
        ];
    }

    public static function page_title(string $slug): string {
        $titles = [
            'home' => 'Univers-Cité chrétienne',
            'about' => 'À propos — Univers-Cité chrétienne',
            'thinkers' => 'Penseurs — Univers-Cité chrétienne',
            'glossary' => 'Glossaire — Univers-Cité chrétienne',
            'christian' => 'Corpus chrétien — Univers-Cité chrétienne',
            'method' => 'Méthode éditoriale — Univers-Cité chrétienne',
            'transparency' => 'Transparence — Univers-Cité chrétienne',
            'programs' => 'Voies UCC',
            'courses' => 'Cours UCC',
            'challenges' => 'Défis UCC',
            'assemblies' => 'Assemblées UCC',
            'integrity' => 'Intégrité UCC',
            'archives' => 'Registraire UCC',
            'mediatheque' => 'Médiathèque chrétienne — Univers-Cité chrétienne',
            'news' => 'Actualités UCC',
            'contact' => 'Contact — Univers-Cité chrétienne',
        ];

        return $titles[$slug] ?? $titles['home'];
    }

    public static function heading(): string {
        return 'Univers-Cité chrétienne';
    }

    /** @return array<string, mixed> */
    public static function base_definition(): array {
        return [
            'layout' => 'wide',
            'navigationlayout' => 'singleline',
            'typography' => 'institutional',
            'visualstyle' => 'ucc-institutional-library',
            'fontstrategy' => 'serif-editorial-sans-interface',
            'boundarynotice' => 'Univers-Cité chrétienne est une initiative chrétienne indépendante et une encyclopédie relationnelle en construction. Les mentions catholiques désignent un corpus, une tradition, des œuvres ou des institutions précises; elles ne valent pas reconnaissance officielle par l’Église catholique.',
            'navigation' => self::navigation(),
            'notices' => [],
            'metadata' => [],
            'cta' => [],
        ];
    }
}
