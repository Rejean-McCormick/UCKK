<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class about {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'À propos',
            'title' => 'Une univers-cité structurée par des questions d’intelligibilité',
            'subtitle' => 'Comprendre les mathématiques, leurs usages et les limites de leur interprétation.',
            'summary' => 'L’Univers-Cité des mathématiques est désormais centrée sur un corpus conceptuel précis : continuité et exponentielle, cyclicité et π, nombres complexes et phase, formule d’Euler, information et calculabilité, normalité et expérimentation sur π, puis proportion et auto-similarité comme axe exploratoire.',
            'sections' => [
                ['title' => 'Structure avant catalogue', 'body' => 'Les parcours partent de relations conceptuelles explicites plutôt que d’une division scolaire en matières isolées.'],
                ['title' => 'Rigueur avant métaphysique', 'body' => 'Le programme distingue systématiquement démonstration, modèle, résultat empirique, interprétation philosophique et thèse ontologique.'],
                ['title' => 'Expérimentation mathématique', 'body' => 'Normalité, chiffres de π, tests statistiques, complexité algorithmique et robustesse aux bases servent de terrain pour apprendre à formuler des hypothèses falsifiables.'],
            ],
            'cardsheading' => 'Principes',
            'cards' => [
                ['title' => 'Concepts', 'body' => 'Chaque cours est relié à une carte conceptuelle explicite.'],
                ['title' => 'Preuves', 'body' => 'Les raisons mathématiques restent distinctes des interprétations philosophiques.'],
                ['title' => 'Tests', 'body' => 'Les thèses sur motifs et hasard sont formulées avec hypothèses nulles et contrôles de robustesse.'],
            ],
        ];
    }
}
