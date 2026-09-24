<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class assemblies {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Séminaires',
            'title' => 'Séminaires mathématiques',
            'subtitle' => 'Présenter une idée, discuter une preuve, corriger une erreur.',
            'summary' => 'Des rencontres consacrées à une idée, une preuve, un problème ouvert ou une méthode de calcul, avec une place explicite pour les objections et corrections.',
            'cardsheading' => 'À explorer',
        ];
    }
}
