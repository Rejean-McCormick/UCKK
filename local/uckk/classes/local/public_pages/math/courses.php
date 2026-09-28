<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class courses {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Cours',
            'title' => 'Explorer les cours',
            'subtitle' => '64 cours reliés à une carte explicite de concepts et de documents d’ancrage.',
            'summary' => 'Le curriculum canonique relie chaque cours à des concepts précis — continuité, π, phase, Euler, information, calculabilité, normalité, complexité ou auto-similarité — tandis que Moodle matérialise les espaces de cours disponibles.',
            'cardsheading' => 'Cours publics',
        ];
    }
}
