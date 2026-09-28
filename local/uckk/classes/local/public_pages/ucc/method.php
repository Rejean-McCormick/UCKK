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
            'subtitle' => 'L’intelligence artificielle sert d’outil de synthèse, de confrontation et d’arbitrage éditorial; elle n’est ni infaillible ni soustraite à la critique.',
            'summary' => 'Dans la mesure du possible, le contenu n’est pas présenté comme l’interprétation personnelle du fondateur. Lorsqu’une correction est proposée, son argument peut être soumis à une nouvelle évaluation afin de décider si le passage doit être maintenu, nuancé ou corrigé.',
            'sections' => [
                [
                    'title' => 'Rôle de l’IA',
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
                    'title' => 'Exception déclarée : Teilhard de Chardin',
                    'body' => 'Le fondateur se réserve un grain de sel explicite autour de Pierre Teilhard de Chardin : certains ponts, rapprochements ou développements peuvent être personnels et originaux. Ils doivent être identifiés comme tels pour ne pas être confondus avec une synthèse attribuable au corpus ou à l’IA.',
                ],
                [
                    'title' => 'Séparer fait, interprétation et hypothèse',
                    'body' => 'Lorsque cela est pertinent, le site doit distinguer les données historiques, les positions d’un auteur, les interprétations proposées par des commentateurs et les hypothèses contemporaines. La juxtaposition vaut mieux qu’une fusion artificielle de perspectives incompatibles.',
                ],
            ],
            'cardsheading' => 'Appliquer la méthode',
            'cards' => [
                ['title' => 'Penseurs', 'body' => 'Présenter les auteurs dans leur contexte et distinguer leurs propres positions de leur réception.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Voir le premier axe', 'type' => 'people'],
                ['title' => 'Glossaire', 'body' => 'Conserver les sens concurrents d’un terme lorsqu’ils dépendent de l’époque ou de l’auteur.', 'url' => '/local/uckk/glossary.php', 'actionlabel' => 'Voir le second axe', 'type' => 'glossary'],
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
