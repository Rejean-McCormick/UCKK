<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// UCKK-Moodle provides the technical Moodle implementation for the
// Univers-Cité Catho.

/**
 * Public about page definition for UCC.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité Catho
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

/**
 * Public about page definition.
 *
 * @package local_uckk
 */
final class about {
    /**
     * Return the public page definition.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        return [
            'layout' => 'standard',
            'typography' => 'editorial',
            'eyebrow' => 'Situer l’UCC',
            'title' => 'À propos',
            'subtitle' => 'L’Univers-Cité Catho est un univers pédagogique consacré à l’exploration structurée du corpus catholique.',
            'summary' => 'UCC organise l’exploration du corpus catholique autour d’un Kristal commun, de voies pédagogiques, d’une Médiathèque et de quatre portes Konnaxion : Kreative, KonnectED, KeenKonnect et Ethikos.',
            'sections' => [
                [
                    'title' => 'Ce qu’est l’UCC',
                    'body' => 'L’Univers-Cité Catho est un univers pédagogique. Elle rassemble des Voies, des cours, des défis, des archives, une médiathèque, des Assemblées et des repères publics pour apprendre à explorer le corpus catholique : ses concepts, sources, traditions, œuvres, institutions, débats et applications.',
                ],
                [
                    'title' => 'Une bibliothèque publique vivante',
                    'body' => 'La première fonction de l’UCC est la diffusion du savoir. Les pages publiques donnent accès à des connaissances, méthodes, références, cours et parcours d’apprentissage conçus pour être consultés, reliés, pratiqués et partagés. Dans l’esprit kOA, le savoir doit circuler : il doit aider à comprendre, à construire, à décider et à agir.',
                ],
                [
                    'title' => 'Dix voies, quatre portes, un même corpus',
                    'body' => 'Les Voies UCC sont dix parcours spécialisés regroupés sous quatre portes : Kreative — Créer; KonnectED — Comprendre; KeenKonnect — Servir; Ethikos — Gouverner. Chaque cours est relié aux thèmes du Kristal catholique afin que le glossaire, les sources, la Médiathèque et l’évaluation parlent le même langage.',
                ],
                [
                    'title' => 'Un cadre d’apprentissage modernisé',
                    'body' => 'UCC utilise un cadre familier d’apprentissage — cours, voies, niveaux, exercices, archives, traces et projets — en le modernisant pour servir la lecture critique des systèmes, la production de preuves, la mémoire collective et l’action située. Les parcours servent à s’orienter dans le savoir, à pratiquer des méthodes et à construire des artefacts utiles.',
                ],
                [
                    'title' => 'Organisation',
                    'body' => 'Les pages publiques donnent accès aux repères institutionnels de l’UCC : Voies, cours, défis, Assemblées, Médiathèque, Registraire, intégrité et informations générales. Les espaces internes, inscriptions, rôles, permissions, validations et dossiers privés restent gérés dans les espaces appropriés.',
                ],
            ],
            'cardsheading' => 'Repères institutionnels',
            'cards' => [
                [
                    'title' => 'Bibliothèque publique',
                    'body' => 'Un accès ouvert aux cours, archives, ressources, cartes de lecture et repères utiles pour comprendre les systèmes et apprendre à agir.',
                    'type' => 'library',
                ],
                [
                    'title' => 'Établissement',
                    'body' => 'Un établissement virtuel de maîtrise progressive du corpus pour lire les systèmes, produire des preuves, agir avec méthode et relier le savoir, les œuvres, le service et le bien commun.',
                    'type' => 'institution',
                ],
                [
                    'title' => 'Voies',
                    'body' => 'Des parcours publics pour apprendre à lire un domaine, pratiquer des méthodes, produire des artefacts et relier les savoirs entre eux.',
                    'url' => '/local/uckk/programs.php',
                    'actionlabel' => 'Explorer',
                    'type' => 'pathways',
                ],
                [
                    'title' => 'Cours publics',
                    'body' => 'Des portes d’entrée vers les notions, méthodes, exercices et espaces d’apprentissage disponibles dans chaque Voie.',
                    'url' => '/local/uckk/courses.php',
                    'actionlabel' => 'Accéder',
                    'type' => 'courses',
                ],
                [
                    'title' => 'Canon',
                    'body' => 'Un cadre de vocabulaire, de limites et de cohérence institutionnelle pour stabiliser l’identité, les règles et les repères UCC.',
                    'type' => 'canon',
                ],
                [
                    'title' => 'Registraire',
                    'body' => 'Une mémoire structurée des traces publiques, décisions, preuves, corrections et versions utiles à la compréhension de l’UCC.',
                    'url' => '/local/uckk/archives.php',
                    'actionlabel' => 'Consulter',
                    'type' => 'archives',
                ],
            ],
            'notices' => [
                [
                    'body' => 'Les éventuelles reconnaissances UCC demeurent internes, sauf reconnaissance officielle future.',
                    'type' => 'light',
                ],
            ],
        ];
    }
}