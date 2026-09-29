<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public editorial method page for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class method {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'standard',
            'typography' => 'editorial',
            'eyebrow' => 'Méthode éditoriale',
            'title' => 'Faire arbitrer les arguments plutôt que publier une opinion personnelle',
            'subtitle' => 'La sélection générale du corpus a été effectuée par l’intelligence artificielle; cette provenance est déclarée, révisable et ne fait pas de l’IA une autorité infaillible ou parfaitement neutre.',
            'summary' => 'La sélection générale des auteurs, des œuvres, des sujets et des axes d’étude a été effectuée par l’intelligence artificielle. Deux ajouts relèvent toutefois d’un choix explicite du concepteur : Pierre Teilhard de Chardin et Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx. Le corpus demeure révisable et les objections argumentées peuvent conduire à une correction, une nuance ou au maintien d’un passage.',
            'sections' => [
                [
                    'title' => 'Provenance de la sélection générale',
                    'body' => 'La sélection générale des auteurs, des œuvres, des sujets et des axes d’étude a été effectuée par l’intelligence artificielle. Elle ne doit donc pas être présentée comme une liste personnelle du concepteur. Cette provenance ne garantit pas une neutralité parfaite : les modèles, les consignes, les sources disponibles et les révisions peuvent introduire des biais.',
                ],
                [
                    'title' => 'Rôle de l’IA dans le travail éditorial',
                    'body' => 'L’IA peut produire des synthèses, comparer des interprétations, repérer des tensions et réexaminer un passage à la lumière d’une objection. Le but est de réduire la dépendance à l’opinion d’une seule personne, sans prétendre que l’IA serait parfaitement neutre ou objective.',
                ],
                [
                    'title' => 'Une correction doit apporter un argument',
                    'body' => 'Une demande de correction n’est pas acceptée simplement parce qu’elle est formulée. L’argument, le contexte et, lorsque possible, les sources pertinentes sont réévalués. Le résultat peut être une correction, une nuance, l’ajout d’une perspective concurrente ou le maintien du texte.',
                ],
                [
                    'title' => 'Les erreurs de l’IA restent des erreurs',
                    'body' => 'Des coquilles, simplifications, rapprochements abusifs ou affirmations insuffisamment étayées peuvent apparaître. Le corpus est donc conçu comme révisable. Une décision éditoriale précédente peut elle-même être réexaminée.',
                ],
                [
                    'title' => 'Deux ajouts explicitement déclarés',
                    'body' => 'Deux éléments ont été introduits explicitement par le concepteur : Pierre Teilhard de Chardin et l’ouvrage Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx. Leur présence sert de point de passage entre le corpus chrétien et une interrogation plus large sur le divin, le cosmos, la nature, la raison et l’expérience religieuse. L’ajout de l’ouvrage permet notamment de rejoindre des philosophes et des perspectives non chrétiennes auxquels il renvoie, afin d’éviter le cloisonnement doctrinal et les lectures intégristes et de favoriser le dialogue. Ces références ne constituent pas pour autant une doctrine UCC.',
                ],
                [
                    'title' => 'Séparer fait, interprétation et hypothèse',
                    'body' => 'Lorsque cela est pertinent, le site doit distinguer les données historiques, les positions d’un auteur, les interprétations proposées par des commentateurs et les hypothèses contemporaines. La juxtaposition vaut mieux qu’une fusion artificielle de perspectives incompatibles.',
                ],
            ],
            'cardsheading' => 'Appliquer la méthode',
            'cards' => [
                ['title' => 'Penseurs', 'body' => 'Présenter les auteurs dans leur contexte et distinguer leurs propres positions de leur réception.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Voir la vue principale', 'type' => 'people'],
                ['title' => 'Glossaire', 'body' => 'Conserver les sens concurrents d’un terme lorsqu’ils dépendent de l’époque ou de l’auteur.', 'url' => '/local/uckk/glossary.php', 'actionlabel' => 'Voir l’index transversal', 'type' => 'glossary'],
                ['title' => 'Transparence', 'body' => 'Séparer la méthode éditoriale du statut institutionnel et de toute reconnaissance ecclésiale.', 'url' => '/local/uckk/transparency.php', 'actionlabel' => 'Voir le statut', 'type' => 'integrity'],
            ],
            'notices' => [
                [
                    'title' => 'L’IA n’est pas une autorité finale sur la réalité',
                    'body' => 'Elle sert ici de mécanisme éditorial révisable. La qualité du corpus dépend aussi de la provenance des sources, de la précision des arguments et de la possibilité de contester les synthèses produites.',
                    'type' => 'light',
                ],
            ],
        ];
    }
}
