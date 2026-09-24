<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Shared public-site contract for the Univers-Cité des mathématiques.
 *
 * This class contains only public identity metadata shared by mathematics
 * pages. Live courses, media, permissions and user state remain owned by
 * Moodle and the plugins that already manage them.
 *
 * @package local_uckk
 */

namespace local_uckk\local\public_pages\math;

defined('MOODLE_INTERNAL') || die();

final class site {
    /** @return array<int, array<string, mixed>> */
    public static function navigation(): array {
        return [
            ['key' => 'home', 'label' => 'Accueil', 'url' => '/local/uckk/index.php'],
            ['key' => 'programs', 'label' => 'Parcours', 'url' => '/local/uckk/programs.php'],
            ['key' => 'courses', 'label' => 'Cours', 'url' => '/local/uckk/courses.php'],
            ['key' => 'mediatheque', 'label' => 'Bibliothèque', 'url' => '/local/uckk/mediatheque.php'],
            ['key' => 'about', 'label' => 'À propos', 'url' => '/local/uckk/about.php'],
            ['key' => 'contact', 'label' => 'Contact', 'url' => '/local/uckk/contact.php'],
        ];
    }

    public static function page_title(string $slug): string {
        $titles = [
            'home' => 'Univers-Cité des mathématiques',
            'about' => 'À propos — Univers-Cité des mathématiques',
            'programs' => 'Parcours mathématiques',
            'courses' => 'Cours de mathématiques',
            'challenges' => 'Problèmes mathématiques',
            'assemblies' => 'Séminaires mathématiques',
            'integrity' => 'Méthode et rigueur',
            'archives' => 'Archives mathématiques',
            'mediatheque' => 'Bibliothèque mathématique',
            'news' => 'Notes mathématiques',
            'contact' => 'Contact — Univers-Cité des mathématiques',
        ];

        return $titles[$slug] ?? $titles['home'];
    }

    public static function heading(): string {
        return 'Univers-Cité des mathématiques';
    }

    /** @return array<string, mixed> */
    public static function base_definition(): array {
        return [
            'layout' => 'wide',
            'navigationlayout' => 'singleline',
            'typography' => 'minimal',
            'visualstyle' => 'mathematical-minimalism',
            'fontstrategy' => 'system-sans-monospace-accent',
            'boundarynotice' => '',
            'navigation' => self::navigation(),
            'notices' => [],
            'metadata' => [],
            'cta' => [],
        ];
    }
}
