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
                ],
            ];
        }

        return site::base_definition() + [
            'eyebrow' => 'Parcours',
            'title' => 'Cartographier les mathématiques',
            'subtitle' => 'Des domaines reliés plutôt qu’une simple liste de matières.',
            'summary' => 'Les parcours organisent les domaines autour de grandes familles de questions plutôt que d’une simple succession de matières. Les identifiants math.path.* constituent désormais la taxonomie canonique de l’Univers-Cité des mathématiques.',
            'sections' => $sections,
            'cardsheading' => 'Explorer',
            'cards' => [
                ['title' => 'Tous les cours', 'body' => 'Voir le catalogue de cours actuellement visible.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Ouvrir le catalogue'],
                ['title' => 'Index des cours', 'body' => 'Parcourir directement l’index complet des espaces de cours.', 'url' => '/course/index.php', 'actionlabel' => 'Voir l’index'],
            ],
        ];
    }
}
