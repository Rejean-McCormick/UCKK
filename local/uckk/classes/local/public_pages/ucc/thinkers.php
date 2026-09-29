<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public thinkers page for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

use local_uckk\local\atlas\ucc_curriculum_registry;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

final class thinkers {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'editorial',
            'eyebrow' => 'Vue principale du corpus',
            'title' => 'Penseurs et auteurs',
            'subtitle' => 'Entrer dans le savoir par les humains qui ont formulé, transmis, commenté ou contesté des idées.',
            'summary' => 'Chaque personne — et, lorsque le corpus l’exige, certains collectifs intellectuels — devient un nœud relié à des œuvres, concepts, influences, critiques, traductions, héritages et transformations. Le but n’est pas de dresser un panthéon, mais de rendre visibles les relations qui structurent l’histoire intellectuelle.',
            'sections' => [
                [
                    'title' => 'Une personne, plusieurs contextes',
                    'body' => 'Un auteur peut être lu comme témoin d’une époque, comme penseur systématique, comme commentateur d’une tradition, comme innovateur, comme contradicteur ou comme source d’influence. Univers-Cité conserve ces rôles côte à côte lorsque les sources le permettent.',
                ],
                [
                    'title' => 'Comparer sans aplatir',
                    'body' => 'Les penseurs ne sont pas ramenés à une seule thèse. Les analyses cherchent à distinguer ce qu’ils affirment, ce qu’on leur attribue, la manière dont ils ont été reçus et les critiques formulées par d’autres traditions.',
                ],
                [
                    'title' => 'Le corpus chrétien comme point de départ',
                    'body' => 'Le projet commence avec de nombreux penseurs chrétiens et catholiques parce qu’ils ont produit un vaste patrimoine de commentaires, de synthèses, de controverses, d’œuvres littéraires, de philosophie, de théologie et de réflexion sociale. Ce point de départ n’est pas une frontière finale.',
                ],
                [
                    'title' => 'Le Dieu cosmique comme ouvrage-charnière',
                    'body' => 'Le parcours Dieu cosmique utilise Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx, comme point de départ comparatif. Le registre UCC associe actuellement à ce parcours des cours consacrés notamment à Einstein, Spinoza, Bergson et Teilhard de Chardin. La table des matières de l’ouvrage n’est pas utilisée comme structure de cours tant qu’elle n’a pas été vérifiée.',
                    'items' => self::resource_cards(),
                ],
                [
                    'title' => 'L’horizon encyclopédique',
                    'body' => 'À terme, le corpus vise à englober des intellectuels marquants de traditions et d’époques diverses afin que les idées puissent être suivies au-delà d’une école, d’une confession ou d’un territoire particuliers.',
                ],
            ],
            'cardsheading' => 'Penseurs et repères du parcours Dieu cosmique',
            'cards' => self::featured_cards(),
            'notices' => [
                [
                    'title' => 'Pas de lecture obligatoire',
                    'body' => 'Présenter un auteur dans le corpus ne signifie ni adhésion à ses positions ni condamnation. L’objectif est de rendre son apport, son contexte, ses arguments et sa réception plus lisibles.',
                    'type' => 'light',
                ],
            ],
        ];
    }

    /**
     * Cards grounded in the canonical UCC curriculum registry.
     *
     * The labels below are presentation labels. The linked course title is read
     * from ucc_curriculum_registry so this public page does not become a second
     * curriculum source of truth.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function featured_cards(): array {
        $specs = [
            [
                'courseid' => 'UCC-COS-101',
                'label' => 'Jacques Languirand et Jean Proulx — Le Dieu cosmique',
                'query' => 'Le Dieu cosmique',
                'eyebrow' => 'Ouvrage-charnière',
                'type' => 'work',
            ],
            [
                'courseid' => 'UCC-COS-102',
                'label' => 'Einstein',
                'query' => 'Einstein',
                'eyebrow' => 'Science, ordre et émerveillement',
                'type' => 'thinker',
            ],
            [
                'courseid' => 'UCC-COS-103',
                'label' => 'Spinoza',
                'query' => 'Spinoza',
                'eyebrow' => 'Dieu, nature et immanence',
                'type' => 'thinker',
            ],
            [
                'courseid' => 'UCC-COS-107',
                'label' => 'Bergson',
                'query' => 'Bergson',
                'eyebrow' => 'Vie, évolution et devenir',
                'type' => 'thinker',
            ],
            [
                'courseid' => 'UCC-COS-108',
                'label' => 'Teilhard de Chardin',
                'query' => 'Teilhard',
                'eyebrow' => 'Évolution, noosphère et Christ cosmique',
                'type' => 'thinker',
            ],
        ];

        $cards = [];
        foreach ($specs as $spec) {
            $course = ucc_curriculum_registry::get_course($spec['courseid']);
            $cards[] = [
                'eyebrow' => $spec['eyebrow'],
                'title' => $spec['label'],
                'body' => (string)$course['title'],
                'url' => (new moodle_url('/local/uckk/courses.php', ['q' => $spec['query']]))->out(false),
                'actionlabel' => 'Explorer le parcours public',
                'type' => $spec['type'],
                'classes' => 'local-uckk-thinker-card',
            ];
        }

        return $cards;
    }

    /** @return array<int, array<string, mixed>> */
    private static function resource_cards(): array {
        return [
            [
                'title' => 'Sources et œuvres',
                'body' => 'Relier les idées aux textes, œuvres ou documents qui permettent de les vérifier et de les contextualiser.',
                'url' => '/local/uckk/mediatheque.php',
                'actionlabel' => 'Ouvrir la Médiathèque',
                'type' => 'media',
            ],
            [
                'title' => 'Concepts',
                'body' => 'Passer d’une personne aux mots et expressions qu’elle emploie ou transforme.',
                'url' => '/local/uckk/glossary.php',
                'actionlabel' => 'Voir le Glossaire',
                'type' => 'glossary',
            ],
            [
                'title' => 'Corpus chrétien',
                'body' => 'Situer les penseurs issus des traditions chrétiennes dans leur histoire intellectuelle, artistique, sociale et institutionnelle.',
                'url' => '/local/uckk/christian.php',
                'actionlabel' => 'Explorer le corpus',
                'type' => 'corpus',
            ],
            [
                'title' => 'Provenance du corpus',
                'body' => 'La sélection générale des auteurs et des sujets a été effectuée par l’IA. Deux ajouts du concepteur sont déclarés : Pierre Teilhard de Chardin et Le Dieu cosmique, de Jacques Languirand et Jean Proulx.',
                'url' => '/local/uckk/method.php',
                'actionlabel' => 'Voir la règle éditoriale',
                'type' => 'method',
            ],
        ];
    }
}
