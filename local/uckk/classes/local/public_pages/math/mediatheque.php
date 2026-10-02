<?php
namespace local_uckk\local\public_pages\math;

use local_uckk\local\atlas\math_university_projection;

defined('MOODLE_INTERNAL') || die();

final class mediatheque {
    public static function definition(): array {
        $projection = math_university_projection::get();
        return site::base_definition() + [
            'eyebrow' => 'Bibliothèque',
            'title' => 'MathKristal, sources et références mathématiques',
            'subtitle' => 'La médiathèque référence le corpus épinglé et sa provenance sans devenir propriétaire du contenu mathématique.',
            'summary' => sprintf(
                'MathKristal %s (%s) est le corpus de connaissance de référence de l’Université des mathématiques. Les pages et les cours conservent leurs URN, assertions et source_refs. Les anciens documents d’ancrage restent disponibles comme fonds historique; la nouvelle projection s’appuie sur le catalogue et la provenance du Kristal.',
                $projection['generated_from']['release'],
                $projection['generated_from']['state_id']
            ),
            'has_mediatheque_explorer' => true,
            'mediatheque_explorer_id' => 'local-uckk-mediatheque-explorer',
            'mediatheque_initial_state' => [],
        ];
    }
}
