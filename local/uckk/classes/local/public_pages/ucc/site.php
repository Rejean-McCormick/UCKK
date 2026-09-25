<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Shared public-site contract for Univers-Cité Catho.
 *
 * This class contains presentation and editorial identity metadata only.
 * Live courses, media, permissions and user state remain owned by Moodle
 * and the plugins that already manage them.
 *
 * @package local_uckk
 * @copyright 2026 Univers-Cité Catho
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class site {
    /** @return array<int, array<string, mixed>> */
    public static function navigation(): array {
        return [
            ['key' => 'home', 'label' => 'Accueil', 'url' => '/local/uckk/index.php'],
            ['key' => 'about', 'label' => 'À propos', 'url' => '/local/uckk/about.php'],
            ['key' => 'programs', 'label' => 'Voies', 'url' => '/local/uckk/programs.php'],
            ['key' => 'courses', 'label' => 'Cours', 'url' => '/local/uckk/courses.php'],
            ['key' => 'challenges', 'label' => 'Défis', 'url' => '/local/uckk/challenges.php'],
            ['key' => 'assemblies', 'label' => 'Assemblées', 'url' => '/local/uckk/assemblies.php'],
            ['key' => 'integrity', 'label' => 'Intégrité', 'url' => '/local/uckk/integrity.php'],
            ['key' => 'mediatheque', 'label' => 'Médiathèque', 'url' => '/local/uckk/mediatheque.php'],
            ['key' => 'news', 'label' => 'Actualités', 'url' => '/local/uckk/news.php'],
            ['key' => 'contact', 'label' => 'Contact', 'url' => '/local/uckk/contact.php'],
        ];
    }

    public static function page_title(string $slug): string {
        $titles = [
            'home' => 'Univers-Cité Catho',
            'about' => 'À propos — Univers-Cité Catho',
            'programs' => 'Voies UCC',
            'courses' => 'Cours UCC',
            'challenges' => 'Défis UCC',
            'assemblies' => 'Assemblées UCC',
            'integrity' => 'Intégrité UCC',
            'archives' => 'Registraire UCC',
            'mediatheque' => 'Médiathèque UCC',
            'news' => 'Actualités UCC',
            'contact' => 'Contact — Univers-Cité Catho',
        ];

        return $titles[$slug] ?? $titles['home'];
    }

    public static function heading(): string {
        return 'Univers-Cité Catho';
    }

    /** @return array<string, mixed> */
    public static function base_definition(): array {
        return [
            'layout' => 'wide',
            'navigationlayout' => 'singleline',
            'typography' => 'institutional',
            'visualstyle' => 'ucc-institutional-library',
            'fontstrategy' => 'serif-editorial-sans-interface',
            'boundarynotice' => 'L’UCC est une bibliothèque publique vivante et un univers pédagogique consacré à l’exploration structurée du corpus catholique.',
            'navigation' => self::navigation(),
            'notices' => [],
            'metadata' => [],
            'cta' => [],
        ];
    }
}
