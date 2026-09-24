<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class challenges {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Problèmes',
            'title' => 'Problèmes mathématiques',
            'subtitle' => 'Chercher avant de savoir.',
            'summary' => 'Un espace pour formuler des problèmes, proposer des conjectures, comparer des stratégies et publier des solutions argumentées.',
            'cardsheading' => 'À explorer',
        ];
    }
}
