<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class mediatheque {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Bibliothèque',
            'title' => 'Documents d’ancrage et références mathématiques',
            'subtitle' => 'Le curriculum et la bibliothèque partagent la même carte conceptuelle.',
            'summary' => 'La bibliothèque mathématique est organisée autour des documents qui fondent le curriculum : le papier sur e, π, i et la note de recherche moderne sur univers mathématique, information, complexité et normalité de π. Les références explicitement citées sont cataloguées séparément; aucun lien externe non vérifié n’est inventé. Les médias et les cours sont reliés par leurs concept_refs.',
            'has_mediatheque_explorer' => true,
            'mediatheque_explorer_id' => 'local-uckk-mediatheque-explorer',
            'mediatheque_initial_state' => [],
        ];
    }
}
