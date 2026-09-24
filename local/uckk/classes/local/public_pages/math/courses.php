<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class courses {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Cours',
            'title' => 'Explorer les cours',
            'subtitle' => 'Chercher, filtrer et ouvrir les espaces disponibles.',
            'summary' => 'Le catalogue présente les cours rendus visibles dans Moodle et permet de chercher par notion, domaine ou mot-clé.',
            'cardsheading' => 'Cours publics',
        ];
    }
}
