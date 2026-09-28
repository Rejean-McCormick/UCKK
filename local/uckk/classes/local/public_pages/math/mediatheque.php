<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class mediatheque {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Bibliothèque',
            'title' => 'Ressources mathématiques',
            'subtitle' => 'Documents, médias et références publiques.',
            'summary' => 'La bibliothèque mathématique est un fonds autonome. Elle présente ses propres documents, médias, exemples et références; des bridges explicites peuvent y rendre visibles certaines ressources d’autres médiathèques sans les dupliquer.',
            'has_mediatheque_explorer' => true,
            'mediatheque_explorer_id' => 'local-uckk-mediatheque-explorer',
            'mediatheque_initial_state' => [],
        ];
    }
}
