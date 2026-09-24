<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class news {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Notes',
            'title' => 'Notes mathématiques',
            'subtitle' => 'Nouveaux cours, problèmes et ressources.',
            'summary' => 'Nouveaux cours, notes de séminaire, problèmes, ressources et changements importants du corpus public.',
            'cardsheading' => 'Notes',
        ];
    }
}
