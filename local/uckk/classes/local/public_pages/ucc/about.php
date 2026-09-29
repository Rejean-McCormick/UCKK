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
                    'body' => 'La sélection générale des auteurs, des œuvres, des sujets et des axes d’étude a été effectuée par l’intelligence artificielle. L’IA produit aussi une grande partie des synthèses et peut réexaminer les objections; ses erreurs, biais et simplifications doivent pouvoir être soumis à une nouvelle évaluation argumentée.',
                ],
                [
                    'title' => 'Deux ajouts déclarés du concepteur',
                    'body' => 'Pierre Teilhard de Chardin et Le Dieu cosmique : À la recherche du Dieu d’Einstein, de Jacques Languirand et Jean Proulx, ont été ajoutés explicitement par le concepteur. Ces ajouts ouvrent des passages entre le point de départ chrétien du corpus et des réflexions plus larges, notamment non chrétiennes, sur le divin, le cosmos, la nature et la raison. Ils visent le dialogue plutôt que le cloisonnement doctrinal et ne constituent pas une doctrine UCC.',
                ],
            ],
            'cardsheading' => 'Comprendre le projet',
            'cards' => [
                ['title' => 'Penseurs', 'body' => 'Le fil humain de l’encyclopédie.', 'url' => '/local/uckk/thinkers.php', 'actionlabel' => 'Explorer', 'type' => 'people'],
                ['title' => 'Glossaire', 'body' => 'Le fil lexical et conceptuel de l’encyclopédie.', 'url' => '/local/uckk/glossary.php', 'actionlabel' => 'Explorer', 'type' => 'glossary'],
                ['title' => 'Méthode éditoriale', 'body' => 'Provenance de la sélection par IA, objections, corrections et deux ajouts déclarés du concepteur.', 'url' => '/local/uckk/method.php', 'actionlabel' => 'Lire', 'type' => 'method'],
                ['title' => 'Transparence', 'body' => 'Statut institutionnel et relation avec l’Église catholique.', 'url' => '/local/uckk/transparency.php', 'actionlabel' => 'Consulter', 'type' => 'integrity'],
            ],
        ];
    }
}
