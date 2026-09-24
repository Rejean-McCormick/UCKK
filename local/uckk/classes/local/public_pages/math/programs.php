<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class programs {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Parcours',
            'title' => 'Cartographier les mathématiques',
            'subtitle' => 'Des domaines reliés plutôt qu’une simple liste de matières.',
            'summary' => 'Les parcours organisent les domaines autour de grandes familles de questions plutôt que d’une simple succession de matières.',
            'sections' => [
                ['title' => 'Structures', 'body' => 'Algèbre, logique, combinatoire, théorie des nombres et structures discrètes.'],
                ['title' => 'Espaces', 'body' => 'Géométrie, topologie, mesure et représentations.'],
                ['title' => 'Changements', 'body' => 'Analyse, équations différentielles, systèmes dynamiques et optimisation.'],
                ['title' => 'Incertitude', 'body' => 'Probabilités, statistique, information et processus aléatoires.'],
            ],
            'cardsheading' => 'Explorer',
            'cards' => [
                ['title' => 'Tous les cours', 'body' => 'Voir le catalogue de cours actuellement visible.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Ouvrir le catalogue'],
                ['title' => 'Index des cours', 'body' => 'Parcourir directement l’index complet des espaces de cours.', 'url' => '/course/index.php', 'actionlabel' => 'Voir l’index'],
            ],
        ];
    }
}
