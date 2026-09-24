<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class about {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'À propos',
            'title' => 'Une univers-cité consacrée aux mathématiques',
            'subtitle' => 'Étudier les idées avant les recettes.',
            'summary' => 'L’Univers-Cité des mathématiques rassemble des parcours, des cours, des problèmes et des ressources autour d’une même exigence : comprendre ce que l’on affirme, pourquoi cela fonctionne et dans quelles limites.',
            'sections' => [
                ['title' => 'Comprendre', 'body' => 'Partir des définitions, des exemples et des contre-exemples pour construire des objets mathématiques qui restent manipulables.'],
                ['title' => 'Démontrer', 'body' => 'Rendre les raisons explicites : distinguer intuition, calcul, conjecture, preuve et conséquence.'],
            ],
            'cardsheading' => 'Principes',
            'cards' => [
                ['title' => 'Problèmes', 'body' => 'Chercher avant d’appliquer une méthode.'],
                ['title' => 'Preuves', 'body' => 'Expliquer pourquoi un résultat est vrai et sous quelles hypothèses.'],
                ['title' => 'Modèles', 'body' => 'Relier une structure mathématique à une situation et mesurer ce que l’approximation perd.'],
            ],
        ];
    }
}
