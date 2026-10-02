<?php
namespace local_uckk\local\public_pages\math;

use local_uckk\local\atlas\math_university_projection;

defined('MOODLE_INTERNAL') || die();

final class courses {
    public static function definition(): array {
        $projection = math_university_projection::get();
        $stats = $projection['statistics'];
        return site::base_definition() + [
            'eyebrow' => 'Cours',
            'title' => 'Explorer les cours projetés depuis MathKristal',
            'subtitle' => sprintf(
                '%d étapes/cours reliés à %d référents uniques du Kristal, avec une spécification sémantique reconstruisible pour chaque page.',
                (int)$stats['projected_courses'],
                (int)$stats['projected_unique_referents']
            ),
            'summary' => 'Chaque cours conserve son URN MathKristal, les assertions pertinentes, les prérequis pédagogiques, les preuves et les références de provenance. UCKK sélectionne et ordonne cette matière; SemantiK Architect peut ensuite la réaliser dans toute langue/profil RELEASED, sans confier la sélection du contenu à un modèle génératif.',
            'cardsheading' => 'Cours publics',
        ];
    }
}
