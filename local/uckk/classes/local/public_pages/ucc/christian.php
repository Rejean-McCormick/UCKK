<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public Christian corpus page for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class christian {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'editorial',
            'eyebrow' => 'Point de départ du corpus',
            'title' => 'Corpus chrétien',
            'subtitle' => 'Relire un héritage religieux comme histoire de pouvoir, d’art, de soin, d’éducation, de pensée et de transmission.',
            'summary' => 'Le christianisme n’est pas abordé ici sous un seul angle. Le corpus cherche à rendre visibles ses dimensions spirituelles, intellectuelles, sociales, artistiques et institutionnelles, ainsi que ses tensions, ses controverses et ses transformations historiques.',
            'sections' => [
                [
                    'title' => 'Une histoire qui ne se réduit ni au pouvoir ni à l’apologie',
                    'body' => 'Les institutions religieuses ont pu être liées au pouvoir, à la norme et aux conflits. Elles ont aussi soutenu des œuvres d’éducation, de santé, de secours, de culture et de transmission. Le corpus doit permettre de lire ces dimensions ensemble, sans en effacer les contradictions.',
                ],
                [
                    'title' => 'Arts, œuvres et patrimoine',
                    'body' => 'Des communautés, ordres, mécènes, institutions et auteurs chrétiens ont participé à la production et à la conservation d’œuvres artistiques, littéraires, architecturales et musicales. Ces œuvres peuvent être étudiées pour leur portée religieuse, esthétique, historique ou symbolique.',
                ],
                [
                    'title' => 'Québec : éducation, santé et œuvres sociales',
                    'body' => 'Au Québec, l’histoire des institutions chrétiennes — en particulier catholiques — croise durablement celle de l’éducation, des soins de santé, de l’assistance et de nombreuses œuvres sociales. Ces trajectoires méritent d’être documentées dans leurs apports, leurs limites et leurs transformations.',
                ],
                [
                    'title' => 'Une tradition de commentaires et de réinterprétations',
                    'body' => 'Au cœur du christianisme se trouvent aussi des générations d’intellectuels qui ont commenté des textes, reformulé des doctrines, confronté la foi à la philosophie ou aux sciences, et proposé des lectures parfois compatibles, parfois opposées. Leur juxtaposition fait partie de la richesse du corpus.',
                ],
                [
                    'title' => 'Un point de départ, pas une frontière',
                    'body' => 'Les auteurs catholiques et les œuvres de tradition catholique ont une place importante dans le corpus actuel. Univers-Cité chrétienne est néanmoins conçue pour accueillir progressivement d’autres traditions, disciplines et figures majeures de l’histoire intellectuelle humaine.',
                ],
            ],
            'cardsheading' => 'Explorer le corpus',
            'cards' => [
                ['title' => 'Penseurs et auteurs', 'body' => 'Suivre les personnes qui ont produit, interprété et transformé cet héritage.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Voir les penseurs', 'type' => 'people'],
                ['title' => 'Glossaire', 'body' => 'Suivre les concepts, expressions et changements de sens qui structurent les débats.', 'url' => '/local/uckk/glossary.php', 'actionlabel' => 'Voir les mots', 'type' => 'glossary'],
                ['title' => 'Voies', 'body' => 'Parcourir les domaines actuels : arts, lettres, philosophie, théologie, sciences, éducation, santé, institutions, économie et politique.', 'url' => '/local/uckk/programs.php', 'actionlabel' => 'Voir les Voies', 'type' => 'pathways'],
                ['title' => 'Médiathèque', 'body' => 'Consulter les documents, œuvres, médias et références reliés au corpus.', 'url' => '/local/uckk/mediatheque.php', 'actionlabel' => 'Consulter', 'type' => 'media'],
            ],
            'notices' => [
                [
                    'title' => 'Corpus catholique ≠ statut catholique',
                    'body' => 'Étudier des auteurs, institutions ou œuvres catholiques ne signifie pas que le site possède une reconnaissance, une mission canonique ou une approbation officielle de l’Église catholique.',
                    'type' => 'institutional',
                ],
            ],
        ];
    }
}
