<?php
namespace local_uckk\local\public_pages\math;

use local_uckk\local\atlas\math_curriculum_registry;

defined('MOODLE_INTERNAL') || die();

final class programs {
    public static function definition(): array {
        $sections = [];
        foreach (math_curriculum_registry::pathways() as $pathway) {
            $sections[] = [
                'title' => $pathway['title'],
                'body' => $pathway['description'],
                'metadata' => [
                    ['label' => 'ID canonique', 'value' => $pathway['pathway_id']],
                    ['label' => 'Cours', 'value' => (string)count($pathway['course_ids'] ?? [])],
                ],
            ];
        }

        return site::base_definition() + [
            'eyebrow' => 'Parcours',
            'title' => 'Huit parcours autour d’un même noyau conceptuel',
            'subtitle' => 'De e, π et i vers Euler, l’information, la calculabilité et les mathématiques expérimentales.',
            'summary' => 'Les parcours sont dérivés des concepts portés par les documents d’ancrage : intelligibilité mathématique; continuité et exponentielle; cyclicité et π; nombres complexes et phase; synthèse d’Euler; information et calculabilité; normalité et expérimentation sur π; proportion et auto-similarité comme axe exploratoire. Les identifiants math.path.* restent la taxonomie canonique.',
            'sections' => $sections,
            'cardsheading' => 'Explorer',
            'cards' => [
                ['title' => 'Tous les cours', 'body' => 'Voir le catalogue de cours actuellement visible.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Ouvrir le catalogue'],
                ['title' => 'Index des cours', 'body' => 'Parcourir directement l’index complet des espaces de cours.', 'url' => '/course/index.php', 'actionlabel' => 'Voir l’index'],
            ],
        ];
    }
}
