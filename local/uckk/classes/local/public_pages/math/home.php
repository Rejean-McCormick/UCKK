<?php
namespace local_uckk\local\public_pages\math;
defined('MOODLE_INTERNAL') || die();
final class home {
    public static function definition(): array {
        return site::base_definition() + [
            'eyebrow' => 'Univers-Cité des mathématiques',
            'title' => 'Comprendre les structures qui rendent le monde mathématiquement intelligible.',
            'subtitle' => 'Du changement continu à la phase, puis vers l’information, la calculabilité et l’expérimentation sur π.',
            'summary' => 'Le curriculum est organisé autour de deux documents d’ancrage. Le premier relie e, π et i à trois formes imbriquées de représentation — évolution continue, cyclicité et phase — et les réunit dans la formule d’Euler. Le second ouvre les parcours vers l’univers mathématique, l’information, la complexité algorithmique, la normalité de π et les protocoles expérimentaux. La démarche reste structurelle : une réussite mathématique ne constitue pas, à elle seule, une preuve métaphysique.',
            'quicklinks' => [
                ['label' => 'e — changement', 'description' => 'Continuité, dérivées, exponentielle et équations différentielles.', 'url' => '/local/uckk/programs.php#exp'],
                ['label' => 'π — cyclicité', 'description' => 'Cercle, radians, périodicité, ondes et Fourier.', 'url' => '/local/uckk/programs.php#cyc'],
                ['label' => 'i — phase', 'description' => 'Nombres complexes, rotation, argument et phase.', 'url' => '/local/uckk/programs.php#pha'],
                ['label' => 'Information & π', 'description' => 'Calculabilité, complexité, normalité et mathématiques expérimentales.', 'url' => '/local/uckk/programs.php#inf'],
            ],
            'sections' => [
                ['eyebrow' => '01', 'title' => 'e → π → i', 'body' => 'Le fil directeur est un ordre de spécification conceptuelle : variation continue, fermeture cyclique, puis position interne dans le cycle.'],
                ['eyebrow' => '02', 'title' => 'Euler comme convergence', 'body' => 'La formule d’Euler relie exponentielle, trigonométrie et rotation complexe dans une même structure analytique.'],
                ['eyebrow' => '03', 'title' => 'Tester plutôt que surinterpréter', 'body' => 'Les parcours contemporains sur π distinguent motifs, normalité, hasard algorithmique, robustesse aux bases, hypothèses nulles et pénalités de complexité.'],
                ['eyebrow' => '04', 'title' => 'Une frontière épistémique explicite', 'body' => 'Le curriculum étudie les implications philosophiques des structures mathématiques sans transformer automatiquement leur efficacité en conclusion ontologique.'],
            ],
            'cardsheading' => 'Entrer dans le curriculum',
            'cards' => [
                ['title' => '8 parcours conceptuels', 'body' => '64 cours organisés autour des concepts explicitement portés par les documents d’ancrage.', 'url' => '/local/uckk/programs.php', 'actionlabel' => 'Voir les parcours'],
                ['title' => 'Cours', 'body' => 'Explorer les cours et leurs concepts associés.', 'url' => '/local/uckk/courses.php', 'actionlabel' => 'Explorer les cours'],
                ['title' => 'Bibliothèque', 'body' => 'Retrouver les documents d’ancrage et les références explicitement citées.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Ouvrir la bibliothèque'],
            ],
        ];
    }
}
