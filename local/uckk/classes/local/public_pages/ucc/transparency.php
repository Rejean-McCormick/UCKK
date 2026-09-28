<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Public transparency page for Univers-Cité chrétienne. @package local_uckk */
namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

final class transparency {
    /** @return array<string, mixed> */
    public static function definition(): array {
        return [
            'layout' => 'standard',
            'typography' => 'editorial',
            'eyebrow' => 'Statut et transparence',
            'title' => 'Une initiative chrétienne indépendante',
            'subtitle' => 'Le nom public décrit l’orientation du projet; il ne revendique aucune autorité ecclésiale qui n’a pas été officiellement accordée.',
            'summary' => 'Univers-Cité chrétienne ne bénéficie actuellement d’aucune approbation ni reconnaissance officielle de l’Église catholique romaine. Le site distingue explicitement l’étude d’un corpus catholique de l’existence d’un statut institutionnel catholique.',
            'sections' => [
                [
                    'title' => 'Ce que signifie « chrétienne »',
                    'body' => 'Le terme décrit le point de départ intellectuel, historique et culturel du projet ainsi que l’importance actuelle des traditions chrétiennes dans son corpus. Il ne signifie pas que le site est une institution officielle d’une Église particulière.',
                ],
                [
                    'title' => 'Relation avec l’Église catholique',
                    'body' => 'Une démarche de présentation, d’examen et d’orientation a été entreprise auprès de l’autorité compétente de l’Église catholique afin de présenter le projet et de connaître, le cas échéant, les conditions d’une éventuelle approbation ou reconnaissance. Cette démarche ne doit pas être présentée comme une approbation en cours tant qu’une autorité compétente ne l’a pas formellement qualifiée ainsi.',
                ],
                [
                    'title' => 'Réponses officielles',
                    'body' => 'Toute réponse officielle pertinente reçue au sujet du statut, de l’autorité compétente ou de la procédure applicable doit être indiquée ici de manière factuelle, avec une formulation qui ne dépasse pas ce qui a réellement été communiqué.',
                ],
                [
                    'title' => 'Reconnaissances internes',
                    'body' => 'Les parcours, niveaux, attestations ou reconnaissances produits à l’intérieur de l’environnement UCC demeurent internes sauf reconnaissance externe explicitement obtenue et documentée.',
                ],
            ],
            'cardsheading' => 'Distinguer les niveaux',
            'cards' => [
                ['title' => 'Identité chrétienne', 'body' => 'Orientation intellectuelle et culturelle du projet.', 'type' => 'corpus'],
                ['title' => 'Corpus catholique', 'body' => 'Sous-corpus historique et intellectuel documenté au sein du projet.', 'url' => '/local/uckk/christian.php', 'actionlabel' => 'Explorer le corpus', 'type' => 'library'],
                ['title' => 'Reconnaissance ecclésiale', 'body' => 'Statut qui ne peut être affirmé que sur la base d’une décision officielle de l’autorité compétente.', 'type' => 'integrity'],
                ['title' => 'Méthode éditoriale', 'body' => 'Règles distinctes concernant l’IA, les corrections et les interventions personnelles.', 'url' => '/local/uckk/method.php', 'actionlabel' => 'Lire la méthode', 'type' => 'method'],
            ],
            'notices' => [
                [
                    'title' => 'Formulation de référence',
                    'body' => 'Le projet est une initiative chrétienne indépendante. Il ne bénéficie actuellement d’aucune approbation ni reconnaissance officielle de l’Église catholique romaine.',
                    'type' => 'institutional',
                ],
            ],
        ];
    }
}
