<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public about page definition for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class about {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'standard',
            'typography' => 'editorial',
            'eyebrow' => 'À propos du projet',
            'title' => 'Une encyclopédie qui relie personnes, mots et œuvres',
            'subtitle' => 'Univers-Cité chrétienne commence par un corpus chrétien important sans enfermer son horizon dans une seule tradition.',
            'summary' => 'Le projet vise à rendre lisible la circulation des idées dans l’histoire : qui les formule, avec quels mots, dans quelles œuvres, en réponse à quelles traditions, et comment elles sont ensuite reprises, corrigées ou transformées.',
            'sections' => [
                [
                    'title' => 'Une construction progressive',
                    'body' => 'Le corpus est destiné à évoluer. De nouveaux penseurs, auteurs catholiques ou non, œuvres littéraires, concepts et traditions peuvent y être ajoutés à mesure que les relations deviennent documentées et utiles.',
                ],
                [
                    'title' => 'Ni lecture unique, ni oubli du contexte',
                    'body' => 'Une même œuvre peut avoir une valeur doctrinale pour certains lecteurs, une valeur historique pour d’autres, une portée symbolique ou philosophique, ou encore témoigner d’une manière de penser aujourd’hui dépassée. Univers-Cité cherche à rendre ces niveaux lisibles plutôt qu’à les confondre.',
                ],
                [
                    'title' => 'Une méthode révisable',
                    'body' => 'Le corpus n’est pas présenté comme l’interprétation personnelle de son fondateur. L’intelligence artificielle produit ou arbitre une grande partie des synthèses; les erreurs et objections doivent pouvoir être soumises à une nouvelle évaluation argumentée.',
                ],
                [
                    'title' => 'Une exception déclarée : Teilhard de Chardin',
                    'body' => 'Autour de Pierre Teilhard de Chardin, certains rapprochements peuvent intégrer des idées personnelles et originales du fondateur. Cette contribution doit rester identifiable afin de ne pas être confondue avec la méthode générale du corpus.',
                ],
            ],
            'cardsheading' => 'Comprendre le projet',
            'cards' => [
                ['title' => 'Penseurs', 'body' => 'Le fil humain de l’encyclopédie.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Explorer', 'type' => 'people'],
                ['title' => 'Glossaire', 'body' => 'Le fil lexical et conceptuel de l’encyclopédie.', 'url' => '/local/uckk/glossary.php', 'actionlabel' => 'Explorer', 'type' => 'glossary'],
                ['title' => 'Méthode éditoriale', 'body' => 'IA, objections, corrections et exception Teilhard.', 'url' => '/local/uckk/method.php', 'actionlabel' => 'Lire', 'type' => 'method'],
                ['title' => 'Transparence', 'body' => 'Statut institutionnel et relation avec l’Église catholique.', 'url' => '/local/uckk/transparency.php', 'actionlabel' => 'Consulter', 'type' => 'integrity'],
            ],
        ];
    }
}
