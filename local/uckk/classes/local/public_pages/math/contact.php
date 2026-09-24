<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class contact {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Contact',
            'title' => 'Écrire à l’Univers-Cité des mathématiques',
            'subtitle' => 'Questions, propositions de cours et ressources.',
            'summary' => 'Questions sur un parcours, proposition de ressource, projet de cours ou collaboration : utilisez les coordonnées publiées par l’institution.',
            'sections' => [
                ['title' => 'Écrire clairement', 'body' => 'Indiquez le sujet, le domaine mathématique concerné et, lorsque c’est utile, le cours ou la ressource visée.'],
            ],
        ];
    }
}
