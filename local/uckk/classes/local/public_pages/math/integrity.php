<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class integrity {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Méthode',
            'title' => 'Rigueur, preuve et reproductibilité',
            'subtitle' => 'Distinguer hypothèse, calcul, preuve et interprétation.',
            'summary' => 'Une pratique mathématique rigoureuse distingue les hypothèses, les transformations autorisées, les calculs vérifiables, les tests statistiques et l’interprétation du résultat. Dans ce curriculum, l’efficacité ou l’élégance d’une structure mathématique ne suffit jamais à établir une thèse ontologique.',
            'cardsheading' => 'Repères',
        ];
    }
}
