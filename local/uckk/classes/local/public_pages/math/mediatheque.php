<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class mediatheque {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Bibliothèque',
            'title' => 'Ressources mathématiques',
            'subtitle' => 'Documents, médias et références publiques.',
            'summary' => 'La bibliothèque présente les documents, médias, exemples et références rendus publics par les services Moodle existants.',
            'has_mediatheque_explorer' => true,
            'mediatheque_explorer_id' => 'local-uckk-mediatheque-explorer',
            'mediatheque_initial_state' => [],
        ];
    }
}
