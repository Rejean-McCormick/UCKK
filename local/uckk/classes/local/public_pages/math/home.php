<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class home {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Univers-Cité des mathématiques',
            'title' => 'Comprendre. Démontrer. Modéliser.',
            'subtitle' => 'Un espace public minimal pour apprendre les mathématiques par les idées, les problèmes et les preuves.',
            'summary' => 'Les mathématiques sont ici un langage de précision et un outil pour rendre visibles des structures, des changements, des formes et de l’incertitude. On y apprend en formulant des problèmes, en construisant des preuves et en confrontant les modèles au réel.',
            'quicklinks' => [
                ['label' => 'Algèbre', 'description' => 'Structures, équations, symétries et transformations.', 'url' => '/local/uckk/courses.php?q=algèbre'],
                ['label' => 'Géométrie', 'description' => 'Formes, espaces, mesures et invariants.', 'url' => '/local/uckk/courses.php?q=géométrie'],
                ['label' => 'Analyse', 'description' => 'Limites, variations, continuité et approximation.', 'url' => '/local/uckk/courses.php?q=analyse'],
                ['label' => 'Probabilités', 'description' => 'Hasard, information, estimation et décision.', 'url' => '/local/uckk/courses.php?q=probabilités'],
            ],
            'sections' => [
                ['eyebrow' => '01', 'title' => 'Voir les structures', 'body' => 'Une bonne abstraction enlève le bruit sans perdre ce qui compte. L’algèbre, la géométrie et la théorie des graphes rendent visibles des relations qui seraient autrement difficiles à comparer.'],
                ['eyebrow' => '02', 'title' => 'Raisonner avec précision', 'body' => 'Définir, conjecturer, démontrer, réfuter et généraliser : la preuve n’est pas une décoration finale, mais une manière de savoir pourquoi une idée tient.'],
                ['eyebrow' => '03', 'title' => 'Modéliser le réel', 'body' => 'Les modèles mathématiques relient hypothèses et conséquences. Ils permettent de calculer, simuler, estimer l’incertitude et rendre les décisions plus explicites.'],
            ],
            'cardsheading' => 'Points d’entrée',
            'cards' => [
                ['title' => 'Parcours', 'body' => 'Organiser les domaines et les prérequis sans imposer un chemin unique.', 'url' => '/local/uckk/programs.php', 'actionlabel' => 'Voir les parcours'],
                ['title' => 'Cours', 'body' => 'Explorer les cours disponibles et chercher par notion.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Explorer les cours'],
                ['title' => 'Bibliothèque', 'body' => 'Consulter des ressources et médias publics lorsque disponibles.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Ouvrir la bibliothèque'],
            ],
        ];
    }
}
