<?php
namespace local_uckk\local\public_pages\math;

use local_uckk\local\atlas\math_university_projection;

defined('MOODLE_INTERNAL') || die();

final class home {
    public static function definition(): array {
        $projection = math_university_projection::get();
        $stats = $projection['statistics'];
        return site::base_definition() + [
            'eyebrow' => 'Univers-Cité des mathématiques',
            'title' => 'Une université compilée depuis un Kristal mathématique.',
            'subtitle' => 'MathKristal structure la connaissance; UCKK en projette les voies, les cours et les pages; SemantiK Architect en réalise la langue.',
            'summary' => sprintf(
                'L’Université des mathématiques consomme actuellement MathKristal %s, épinglé par %s. Le corpus contient %d référents et %d assertions. UCKK en construit une projection pédagogique reconstruisible; Moodle conserve les inscriptions, évaluations, notes et productions humaines.',
                $projection['generated_from']['release'],
                $projection['generated_from']['state_id'],
                (int)$stats['kristal_referents'],
                (int)$stats['kristal_assertions']
            ),
            'quicklinks' => [
                ['label' => 'Fondations → Euler', 'description' => 'Ensembles, réels, complexes, exponentielle complexe et formule d’Euler.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'Analyse → analyse fonctionnelle', 'description' => 'Limites, dérivation, espaces de Banach et grands théorèmes fonctionnels.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'Topologie → π₁', 'description' => 'Espaces topologiques, chemins, homotopies et groupe fondamental.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'Probabilités → Itô', 'description' => 'Espaces probabilisés, mouvement brownien, intégrale et formule d’Itô.', 'url' => '/local/uckk/programs.php'],
            ],
            'sections' => [
                ['eyebrow' => '01', 'title' => 'Kristal = autorité mathématique', 'body' => 'Référents, assertions, définitions, preuves, dépendances, domaines et provenance demeurent dans MathKristal. La copie Moodle n’est jamais un second canon.'],
                ['eyebrow' => '02', 'title' => 'UCKK = projection pédagogique', 'body' => 'Le projector transforme les voies pédagogiques du Kristal en parcours, étapes/cours et spécifications de pages. La projection peut être reconstruite à partir du même state_id.'],
                ['eyebrow' => '03', 'title' => 'SemantiK Architect = articulation', 'body' => 'Les pages transmettent des obligations sémantiques déjà sélectionnées. SemantiK Architect articule ces obligations dans la langue cible; il ne choisit ni les faits ni le curriculum.'],
                ['eyebrow' => '04', 'title' => 'Moodle = runtime pédagogique', 'body' => 'Moodle sert les artefacts, gère les usagers et conserve l’état humain. Une reconstruction de la projection ne doit jamais écraser inscriptions, notes, remises, annotations ou décisions éditoriales.'],
            ],
            'cardsheading' => 'Entrer dans l’université',
            'cards' => [
                ['title' => sprintf('%d voies Kristal', (int)$stats['projected_pathways']), 'body' => sprintf('%d étapes/cours dérivés des chemins pédagogiques du corpus.', (int)$stats['projected_courses']), 'url' => '/local/uckk/programs.php', 'actionlabel' => 'Voir les voies'],
                ['title' => 'Cours', 'body' => 'Explorer les cours et leurs référents MathKristal associés.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Explorer les cours'],
                ['title' => 'Bibliothèque', 'body' => 'Retrouver le MathKristal épinglé, les documents d’ancrage historiques et la provenance des sources.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Ouvrir la bibliothèque'],
            ],
        ];
    }
}
