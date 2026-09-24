<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class archives {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Archives',
            'title' => 'Archives mathématiques',
            'subtitle' => 'Conserver définitions, preuves, versions et corrections.',
            'summary' => 'Conserver les définitions, preuves, versions, corrections et ressources de manière à pouvoir retracer l’évolution d’un travail.',
            'cardsheading' => 'Repères',
        ];
    }
}
