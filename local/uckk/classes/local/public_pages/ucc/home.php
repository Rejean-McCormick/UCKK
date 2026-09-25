<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// UCKK-Moodle uses Moodle as a technical platform for the
// Univers-Cité Catho.

/**
 * Public home page definition for local_uckk.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité Catho
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

/**
 * Public home page definition.
 *
 * @package local_uckk
 */
final class home {
    /**
     * Return the public page definition.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'display',

            'eyebrow' => 'Bibliothèque publique vivante',
            'title' => 'Univers-Cité Catho',
            'subtitle' => 'Créer. Comprendre. Servir. Gouverner.',
            'summary' => 'L’Univers-Cité Catho est un univers pédagogique de World Konnaxion consacré à l’exploration structurée du corpus catholique. Le Kristal organise les concepts et les sources; UCC transforme ce corpus en voies, cours, ressources, pratiques et évaluations.',

            'quicklinks' => [
                ['label' => 'Kreative · Créer', 'description' => 'Arts, beauté, culture, langage, lettres et transmission.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'KonnectED · Comprendre', 'description' => 'Philosophie, théologie, Écriture, sciences, création et écologie.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'KeenKonnect · Servir', 'description' => 'Éducation, universités, santé, soin, œuvres, institutions et administration.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'Ethikos · Gouverner', 'description' => 'Économie, travail, justice sociale, droit, politique, subsidiarité et bien commun.', 'url' => '/local/uckk/programs.php'],
                ['label' => 'Glossaire Kristal', 'description' => 'Entrer dans le corpus par ses concepts, positions, sources et relations.', 'url' => '/local/uckk/courses.php'],
                ['label' => 'Médiathèque UCC', 'description' => 'Explorer les documents, médias, œuvres et références reliés aux cours.', 'url' => '/local/uckk/mediatheque.php'],
            ],

            'sections' => [
                [
                    'type' => 'positioning',
                    'eyebrow' => 'Position',
                    'title' => 'Explorer un même corpus par quatre portes',
                    'body' => 'Créer avec Kreative, comprendre avec KonnectED, servir avec KeenKonnect et gouverner avec Ethikos : les quatre portes donnent accès au même corpus Kristal par des usages différents.',
                    'items' => [
                        'Relier directement cours, glossaire, sources et ressources au Kristal catholique.',
                        'Permettre des parcours larges ou très précis sans réduire le corpus à une simple liste de mots-clés.',
                        'Permettre d’entrer par une voie, un concept, un cours, une ressource, une institution, une œuvre ou une question.',
                    ],
                ],
                [
                    'type' => 'architecture',
                    'eyebrow' => 'Architecture',
                    'title' => 'Un cadre d’apprentissage familier, modernisé',
                    'body' => 'L’UCC reprend des formes connues — cours, parcours, ressources, activités, archives, discussions — et les modernise autour d’une logique plus ouverte : apprendre, relier, produire, vérifier, transmettre.',
                    'items' => [
                        'Les Voies organisent les grands domaines de savoir et d’action.',
                        'Les cours offrent des points d’entrée structurés dans chaque domaine.',
                        'Les Défis transforment une question en exercice, production ou preuve.',
                        'Les Assemblées donnent une forme collective à la discussion, à l’orientation et à la correction.',
                        'Le Registraire conserve les traces, preuves, décisions, versions et leçons utiles.',
                    ],
                ],
                [
                    'type' => 'method',
                    'eyebrow' => 'Méthode',
                    'title' => 'Corpus, cours, pratique et certification',
                    'body' => 'L’UCC relie les thèmes Kristal aux cours de l’Atlas, aux ressources de la Médiathèque et aux examens CertifiKation. Les modules de Konnaxion prolongent ensuite l’apprentissage dans la culture, les œuvres et la gouvernance.',
                    'items' => [
                        'Kristal : concepts, positions, sources, provenance et relations.',
                        'UCC : voies, cours conceptuels, artefacts de maîtrise et progression.',
                        'Konnaxion : Kreative, KonnectED, KeenKonnect et Ethikos.',
                        'CertifiKation : connaissance, explication, application et argumentation.',
                    ],
                ],
                [
                    'type' => 'boundary',
                    'eyebrow' => 'Note institutionnelle',
                    'title' => 'Reconnaissance UCC',
                    'body' => 'Les éventuelles reconnaissances UCC demeurent internes, sauf reconnaissance officielle future.',
                ],
            ],

            'cardsheading' => 'Portes d’entrée publiques',
            'cards' => [
                [
                    'title' => 'Voies',
                    'body' => 'Les grands parcours de lecture du monde : domaines, notions, cours, défis, preuves, archives et pratiques.',
                    'url' => '/local/uckk/programs.php',
                    'actionlabel' => 'Voir les Voies',
                    'type' => 'programs',
                ],
                [
                    'title' => 'Cours',
                    'body' => 'Les espaces structurés pour explorer les notions, ressources, activités et repères de progression.',
                    'url' => '/local/uckk/courses.php',
                    'actionlabel' => 'Explorer les cours',
                    'type' => 'courses',
                ],
                [
                    'title' => 'Défis',
                    'body' => 'Les exercices publics ou internes qui transforment une question en action, production, preuve et apprentissage.',
                    'url' => '/local/uckk/challenges.php',
                    'actionlabel' => 'Voir les Défis',
                    'type' => 'challenges',
                ],
                [
                    'title' => 'Assemblées',
                    'body' => 'Les lieux de discussion, d’orientation, de contestation, d’arbitrage et de légitimité collective.',
                    'url' => '/local/uckk/assemblies.php',
                    'actionlabel' => 'Voir les Assemblées',
                    'type' => 'assemblies',
                ],
                [
                    'title' => 'Médiathèque',
                    'body' => 'Les contenus publics, collections, références, médias et traces consultables.',
                    'url' => '/local/uckk/mediatheque.php',
                    'actionlabel' => 'Explorer la médiathèque',
                    'type' => 'media',
                ],
                [
                    'title' => 'Intégrité',
                    'body' => 'Le cadre qui protège la vérité des faits, la dignité des personnes, la qualité des preuves et la clarté des règles.',
                    'url' => '/local/uckk/integrity.php',
                    'actionlabel' => 'Voir le cadre',
                    'type' => 'integrity',
                ],
                [
                    'title' => 'Registraire',
                    'body' => 'La mémoire des traces publiques, décisions, versions, preuves, corrections et leçons utiles.',
                    'url' => '/local/uckk/archives.php',
                    'actionlabel' => 'Consulter le Registraire',
                    'type' => 'archives',
                ],
            ],

            'notices' => [
                [
                    'title' => 'Bibliothèque ouverte',
                    'body' => 'La connaissance doit circuler. L’UCC organise des ressources, parcours, archives et scènes d’apprentissage pour rendre le savoir plus accessible, plus relié et plus praticable.',
                    'type' => 'institutional',
                ],
                [
                    'title' => 'Corpus documenté et responsabilité',
                    'body' => 'Le Kristal et les cours doivent conserver la provenance, les niveaux de certitude et les limites de leurs sources. Une interprétation pédagogique ne devient pas automatiquement une vérité institutionnelle.',
                    'type' => 'light',
                ],
            ],

            'metadata' => [
                [
                    'label' => 'Composant technique',
                    'value' => 'local_uckk',
                ],
                [
                    'label' => 'Type de page',
                    'value' => 'Page publique institutionnelle',
                ],
                [
                    'label' => 'Nature',
                    'value' => 'Bibliothèque publique vivante et établissement virtuel de maîtrise progressive du corpus',
                ],
                [
                    'label' => 'Domaine',
                    'value' => 'corpus catholique',
                ],
                [
                    'label' => 'Rôle',
                    'value' => 'Branche éducative du mouvement kOA',
                ],
            ],

            'cta' => [
                'title' => 'Entrer dans la bibliothèque vivante',
                'body' => 'Commencer par une Voie fondatrice, explorer les cours, consulter les traces publiques et relier les idées au corpus catholique.',
                'url' => '/local/uckk/programs.php',
                'label' => 'Explorer les quatre portes',
            ],
        ];
    }
}