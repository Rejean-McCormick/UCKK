<?php
namespace local_uckk\local\public_pages\math;

use local_uckk\local\atlas\math_university_projection;

defined('MOODLE_INTERNAL') || die();

final class programs {
    public static function definition(): array {
        $projection = math_university_projection::get();
        $stats = $projection['statistics'];
        $sections = [];
        foreach (math_university_projection::pathways() as $pathway) {
            $sections[] = [
                'title' => $pathway['title'],
                'body' => $pathway['description'],
                'metadata' => [
                    ['label' => 'ID canonique', 'value' => $pathway['pathway_id']],
                    ['label' => 'Voie Kristal', 'value' => $pathway['kristal_path_id']],
                    ['label' => 'Étapes / cours projetés', 'value' => (string)count($pathway['course_ids'] ?? [])],
                ],
            ];
        }

        return site::base_definition() + [
            'eyebrow' => 'Voies projetées depuis MathKristal',
            'title' => sprintf('%d voies d’apprentissage dérivées du Kristal', (int)$stats['projected_pathways']),
            'subtitle' => 'Le Kristal porte la structure mathématique; UCKK en projette les parcours pédagogiques sans devenir l’autorité épistémique du corpus.',
            'summary' => sprintf(
                'Cette projection est reconstruisible à partir de MathKristal %s (%s). Elle expose %d étapes/cours et %d spécifications de pages. Les dépendances pédagogiques proviennent du Kristal; la topologie universitaire est une projection UCKK et ne modifie jamais les dépendances logiques du corpus.',
                $projection['generated_from']['release'],
                $projection['generated_from']['state_id'],
                (int)$stats['projected_courses'],
                (int)$stats['projected_page_specs']
            ),
            'sections' => $sections,
            'cardsheading' => 'Explorer',
            'cards' => [
                ['title' => 'Cours projetés', 'body' => 'Explorer les étapes/cours dérivés des voies pédagogiques du MathKristal.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Ouvrir les cours'],
                ['title' => 'Bibliothèque / Kristal', 'body' => 'Retrouver le corpus épinglé, ses sources et la provenance qui alimente les pages.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Ouvrir la bibliothèque'],
            ],
        ];
    }
}
