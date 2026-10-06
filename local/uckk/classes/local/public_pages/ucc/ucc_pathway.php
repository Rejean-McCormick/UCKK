<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Public UCC pathway page builder.
 *
 * One controller renders all eleven canonical UCC pathways. Content is derived
 * from the canonical curriculum, domain, syllabus and Médiathèque registries so
 * pathway pages cannot silently drift away from the UCC catalogue.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\public_pages\ucc;

use coding_exception;
use moodle_url;
use local_uckk\local\atlas\ucc_curriculum_registry;
use local_uckk\local\atlas\ucc_domain_registry;
use local_uckk\local\atlas\ucc_mediatheque_registry;
use local_uckk\local\atlas\ucc_syllabus_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * Builds the public page definition for one canonical UCC pathway.
 */
final class ucc_pathway {
    /** Canonical public slugs keyed by canonical pathway id. */
    private const PATHWAY_SLUGS = [
        'ucc.path.arts-beauty-culture' => 'arts-beauty-culture',
        'ucc.path.language-letters-transmission' => 'language-letters-transmission',
        'ucc.path.creation-sciences-ecology' => 'creation-sciences-ecology',
        'ucc.path.theology-scripture-tradition' => 'theology-scripture-tradition',
        'ucc.path.philosophy-metaphysics-person' => 'philosophy-metaphysics-person',
        'ucc.path.cosmic-divine-continuum' => 'cosmic-divine-continuum',
        'ucc.path.works-institutions-administration' => 'works-institutions-administration',
        'ucc.path.education-universities-transmission' => 'education-universities-transmission',
        'ucc.path.health-care-dignity' => 'health-care-dignity',
        'ucc.path.economy-work-social-justice' => 'economy-work-social-justice',
        'ucc.path.law-politics-common-good' => 'law-politics-common-good',
    ];

    /** Public editorial identity keyed by canonical pathway id. */
    private const PATHWAY_IDENTITIES = [
        'ucc.path.arts-beauty-culture' => [
            'eyebrow' => 'Kreative · Créer',
            'body' => 'Explorer les œuvres, images, récits et pratiques culturelles des traditions chrétiennes, notamment catholiques, en les reliant à leurs sources et à leurs contextes.',
            'type' => 'kreative',
        ],
        'ucc.path.language-letters-transmission' => [
            'eyebrow' => 'Kreative · Créer',
            'body' => 'Étudier les mots, textes, langues, traductions et traditions de lecture qui rendent les corpus chrétiens — dont les traditions catholiques — intelligibles et transmissibles.',
            'type' => 'kreative',
        ],
        'ucc.path.philosophy-metaphysics-person' => [
            'eyebrow' => 'KonnectED · Comprendre',
            'body' => 'Explorer être, vérité, connaissance, personne, liberté, bien, mal, temps et loi naturelle à partir de positions et sources documentées.',
            'type' => 'konnected',
        ],
        'ucc.path.cosmic-divine-continuum' => [
            'eyebrow' => 'KonnectED · Comprendre',
            'body' => 'Parcourir un continuum de réflexions sur le divin, la nature, le cosmos, la science et l’expérience religieuse à partir de l’ouvrage-charnière Le Dieu cosmique de Jacques Languirand et Jean Proulx, sans transformer cette synthèse en doctrine UCC.',
            'type' => 'konnected',
        ],
        'ucc.path.theology-scripture-tradition' => [
            'eyebrow' => 'KonnectED · Comprendre',
            'body' => 'Lire les grands ensembles théologiques du corpus en distinguant sources, développement doctrinal, réception, statut épistémique et interprétation.',
            'type' => 'konnected',
        ],
        'ucc.path.creation-sciences-ecology' => [
            'eyebrow' => 'KonnectED · Comprendre',
            'body' => 'Étudier les rapports entre création, sciences, évolution, progrès, écologie et responsabilité envers le vivant.',
            'type' => 'konnected',
        ],
        'ucc.path.education-universities-transmission' => [
            'eyebrow' => 'KeenKonnect · Servir',
            'body' => 'Explorer écoles, universités, formation, recherche, accès au savoir et responsabilités institutionnelles.',
            'type' => 'keenkonnect',
        ],
        'ucc.path.health-care-dignity' => [
            'eyebrow' => 'KeenKonnect · Servir',
            'body' => 'Étudier le soin, la vulnérabilité, le corps, la dignité et les institutions cliniques catholiques.',
            'type' => 'keenkonnect',
        ],
        'ucc.path.works-institutions-administration' => [
            'eyebrow' => 'KeenKonnect · Servir',
            'body' => 'Comprendre comment des œuvres catholiques sont administrées, financées, auditées, transmises et rendues responsables.',
            'type' => 'keenkonnect',
        ],
        'ucc.path.economy-work-social-justice' => [
            'eyebrow' => 'Ethikos · Gouverner',
            'body' => 'Explorer travail, propriété, pauvreté, échange, don, justice sociale et responsabilité économique dans les traditions sociales chrétiennes, notamment catholiques.',
            'type' => 'ethikos',
        ],
        'ucc.path.law-politics-common-good' => [
            'eyebrow' => 'Ethikos · Gouverner',
            'body' => 'Étudier autorité, loi, droits, subsidiarité, institutions, guerre, paix et prudence politique avec une méthode documentaire et non partisane.',
            'type' => 'ethikos',
        ],
    ];

    /**
     * Generic definition used by tooling that resolves one class per public page.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'institutional',
            'eyebrow' => 'Voie UCC',
            'title' => 'Voie UCC',
            'summary' => 'Parcours public de l’Univers-Cité chrétienne.',
        ];
    }

    /**
     * Return all public pathway slugs.
     *
     * @return array<string, string>
     */
    public static function slugs(): array {
        return self::PATHWAY_SLUGS;
    }

    /**
     * Resolve a canonical pathway id from its public slug.
     *
     * @throws coding_exception Unknown slug.
     */
    public static function pathway_id_for_slug(string $slug): string {
        $slug = strtolower(trim($slug));
        $pathwayid = array_search($slug, self::PATHWAY_SLUGS, true);
        if ($pathwayid === false) {
            throw new coding_exception('Unknown public UCC pathway slug: ' . $slug);
        }
        return (string)$pathwayid;
    }

    /**
     * Resolve the public slug for a canonical pathway.
     *
     * @throws coding_exception Unknown canonical pathway.
     */
    public static function slug_for_pathway(string $pathwayid): string {
        $pathwayid = trim($pathwayid);
        if (!isset(self::PATHWAY_SLUGS[$pathwayid])) {
            throw new coding_exception('Unknown canonical UCC pathway: ' . $pathwayid);
        }
        return self::PATHWAY_SLUGS[$pathwayid];
    }

    /**
     * Build the public URL for one canonical pathway.
     */
    public static function url_for_pathway(string $pathwayid): moodle_url {
        return new moodle_url('/local/uckk/ucc_pathway.php', [
            'slug' => self::slug_for_pathway($pathwayid),
        ]);
    }

    /**
     * Return the public editorial identity for a pathway.
     *
     * @return array{eyebrow:string,title:string,body:string,type:string}
     * @throws coding_exception Unknown canonical pathway.
     */
    public static function identity_for_pathway(string $pathwayid): array {
        $pathway = ucc_curriculum_registry::get_pathway($pathwayid);
        if (!isset(self::PATHWAY_IDENTITIES[$pathwayid])) {
            throw new coding_exception('Missing public identity for UCC pathway: ' . $pathwayid);
        }
        return self::PATHWAY_IDENTITIES[$pathwayid] + ['title' => (string)$pathway['title']];
    }

    /**
     * Build a complete page definition for a public pathway slug.
     *
     * @return array<string, mixed>
     */
    public static function definition_for_slug(string $slug): array {
        $pathwayid = self::pathway_id_for_slug($slug);
        $pathway = ucc_curriculum_registry::get_pathway($pathwayid);
        $domain = ucc_domain_registry::for_pathway($pathwayid);
        $identity = self::identity_for_pathway($pathwayid);
        $coursecards = self::course_cards($pathway);
        $workcards = self::work_cards($pathway);
        $moodlecoursesurl = self::moodle_courses_url($pathway);

        $orientation = 'Question directrice du domaine « ' . (string)$domain['surface'] . ' » : '
            . (string)$domain['question'] . ' ' . (string)$domain['purpose'];

        $editorialbody = 'Cette page est générée depuis les registres canoniques UCC. '
            . 'Les plans de cours structurent lectures, activités et évaluations proposées; '
            . 'ils demeurent distincts des espaces Moodle et attendent la relecture éditoriale indiquée dans le registre.';
        if (!empty($pathway['editorial_role'])) {
            $editorialbody .= ' ' . (string)$pathway['editorial_role'];
        }

        $metadata = [
            ['label' => 'Code de la Voie', 'value' => (string)$pathway['code']],
            ['label' => 'Domaine', 'value' => (string)$domain['surface'] . ' · ' . (string)$domain['door']],
            ['label' => 'Cours canoniques', 'value' => (string)count($pathway['course_ids'] ?? [])],
            ['label' => 'Identifiant canonique', 'value' => $pathwayid],
        ];

        if (!empty($pathway['corpus_readiness']['average_score'])) {
            $metadata[] = [
                'label' => 'Couverture documentaire',
                'value' => (string)$pathway['corpus_readiness']['average_score'] . ' / 100',
            ];
        }

        $sections = [
            [
                'type' => 'orientation',
                'eyebrow' => (string)$domain['surface'] . ' · ' . (string)$domain['door'],
                'title' => 'Orientation de la Voie',
                'body' => $orientation,
            ],
            [
                'type' => 'courses',
                'eyebrow' => 'Parcours pédagogique',
                'title' => 'Les 10 cours de la Voie',
                'body' => 'Chaque carte ouvre le plan éditorial public du cours avec sa question centrale, ses objectifs, ses lectures, ses séances et son évaluation proposée.',
                'cards' => $coursecards,
            ],
        ];

        if (!empty($workcards)) {
            $sections[] = [
                'type' => 'references',
                'eyebrow' => 'Corpus et Médiathèque',
                'title' => 'Œuvres et auteurs principaux',
                'body' => 'Sélection des références publiques les plus structurantes mobilisées dans les cours de cette Voie. La liste complète reste disponible dans les références et lectures UCC.',
                'cards' => $workcards,
            ];
        }

        $sections[] = [
            'type' => 'editorial',
            'eyebrow' => 'Statut public',
            'title' => 'Cadre éditorial',
            'body' => $editorialbody,
        ];

        $definition = [
            'layout' => 'wide',
            'typography' => 'institutional',
            'eyebrow' => $identity['eyebrow'],
            'title' => $identity['title'],
            'subtitle' => 'Voie ' . (string)$pathway['code'] . ' · ' . (string)$domain['surface'] . ' · ' . (string)$domain['door'],
            'summary' => $identity['body'],
            'quicklinks' => [
                [
                    'label' => 'Toutes les Voies UCC',
                    'description' => 'Revenir à la cartographie des 11 Voies.',
                    'url' => '/local/uckk/programs.php',
                ],
                [
                    'label' => 'Plans et lectures de cette Voie',
                    'description' => 'Afficher les 10 plans éditoriaux de la Voie.',
                    'url' => (new moodle_url('/local/uckk/ucc_study.php', ['pathway' => $pathwayid]))->out(false),
                ],
                [
                    'label' => 'Références et œuvres',
                    'description' => 'Explorer les œuvres publiques rattachées à cette Voie.',
                    'url' => (new moodle_url('/local/uckk/ucc_study.php', [
                        'view' => 'references',
                        'pathway' => $pathwayid,
                    ]))->out(false),
                ],
                [
                    'label' => 'Espaces Moodle',
                    'description' => 'Voir les cours Moodle publics associés lorsqu’ils sont matérialisés.',
                    'url' => $moodlecoursesurl->out(false),
                ],
            ],
            'sections' => $sections,
            'cards' => [],
            'notices' => [
                [
                    'type' => 'institutional',
                    'title' => 'Plan éditorial, non validation académique',
                    'body' => 'Les plans et indicateurs de couverture documentaire décrivent l’état du corpus et du travail éditorial. Ils ne constituent ni une accréditation, ni une validation doctrinale, ni une attribution automatique de crédits.',
                ],
            ],
            'metadata' => $metadata,
            'metadataheading' => 'Repères de la Voie',
            'cta' => [
                'title' => 'Entrer dans la Voie',
                'body' => 'Commencer par les 10 plans de cours, puis suivre les lectures et les espaces Moodle publics disponibles.',
                'url' => (new moodle_url('/local/uckk/ucc_study.php', ['pathway' => $pathwayid]))->out(false),
                'label' => 'Explorer les 10 plans',
            ],
        ];

        // Keep the Voies navigation entry visibly active on a pathway detail page.
        $definition['navigation'] = site::navigation();
        foreach ($definition['navigation'] as $index => $item) {
            if (($item['key'] ?? '') === 'programs') {
                $definition['navigation'][$index]['active'] = true;
            }
        }

        return $definition;
    }

    /**
     * Build the ten public course cards for a pathway.
     *
     * @param array<string, mixed> $pathway Canonical pathway row.
     * @return array<int, array<string, mixed>>
     */
    private static function course_cards(array $pathway): array {
        $cards = [];
        foreach ($pathway['course_ids'] ?? [] as $courseid) {
            $course = ucc_curriculum_registry::get_course((string)$courseid);
            $syllabus = ucc_syllabus_registry::get_course((string)$courseid);
            $cards[] = [
                'eyebrow' => (string)$course['ucc_course_id'],
                'title' => (string)$course['title'],
                'body' => (string)($syllabus['central_question'] ?? ''),
                'url' => (new moodle_url('/local/uckk/ucc_study.php', [
                    'course' => (string)$course['ucc_course_id'],
                ]))->out(false),
                'actionlabel' => 'Voir le plan et les lectures',
                'type' => 'course',
                'shortname' => (string)$course['ucc_course_id'],
                'code' => (string)$course['ucc_course_id'],
            ];
        }
        return $cards;
    }

    /**
     * Build a concise selection of public works used by the pathway.
     *
     * @param array<string, mixed> $pathway Canonical pathway row.
     * @return array<int, array<string, mixed>>
     */
    private static function work_cards(array $pathway): array {
        $publicworks = [];
        foreach (ucc_mediatheque_registry::works() as $work) {
            if (($work['visibility'] ?? '') !== 'public') {
                continue;
            }
            if (!in_array((string)($work['status'] ?? ''), ['active', 'edition_review'], true)) {
                continue;
            }
            $publicworks[(string)$work['work_ref']] = $work;
        }

        $rolepriority = ['anchor' => 0, 'primary' => 1, 'supporting' => 2, 'editorial_complement' => 3];
        $selected = [];
        foreach ($pathway['course_ids'] ?? [] as $courseid) {
            foreach (ucc_mediatheque_registry::links_for_course((string)$courseid) as $link) {
                $ref = (string)($link['media_ref'] ?? '');
                if ($ref === '' || !isset($publicworks[$ref])) {
                    continue;
                }
                $role = (string)($link['role'] ?? 'supporting');
                $priority = $rolepriority[$role] ?? 9;
                if (!isset($selected[$ref])) {
                    $selected[$ref] = [
                        'work' => $publicworks[$ref],
                        'priority' => $priority,
                        'courses' => [],
                        'role' => $role,
                    ];
                }
                $selected[$ref]['priority'] = min((int)$selected[$ref]['priority'], $priority);
                $selected[$ref]['courses'][(string)$courseid] = true;
                if ($priority < ($rolepriority[(string)$selected[$ref]['role']] ?? 9)) {
                    $selected[$ref]['role'] = $role;
                }
            }
        }

        uasort($selected, static function(array $a, array $b): int {
            $priority = ((int)$a['priority']) <=> ((int)$b['priority']);
            if ($priority !== 0) {
                return $priority;
            }
            $frequency = count($b['courses']) <=> count($a['courses']);
            if ($frequency !== 0) {
                return $frequency;
            }
            return strcasecmp((string)$a['work']['title'], (string)$b['work']['title']);
        });

        $cards = [];
        foreach (array_slice($selected, 0, 8, true) as $entry) {
            $work = $entry['work'];
            $coursecount = count($entry['courses']);
            $creator = trim((string)($work['creator'] ?? ''));
            $body = $creator !== '' ? $creator . '. ' : '';
            $body .= $coursecount === 1
                ? 'Référence mobilisée dans 1 cours de cette Voie.'
                : 'Référence mobilisée dans ' . $coursecount . ' cours de cette Voie.';

            $sourceurl = trim((string)($work['sourceurl'] ?? ''));
            $ispublicurl = preg_match('~^https?://~i', $sourceurl) === 1;
            $cards[] = [
                'eyebrow' => self::role_label((string)$entry['role']),
                'title' => (string)$work['title'],
                'body' => $body,
                'url' => $ispublicurl ? $sourceurl : '',
                'actionlabel' => $ispublicurl ? 'Consulter la source' : '',
                'type' => 'reference',
            ];
        }
        return $cards;
    }

    /**
     * Resolve a public Moodle course catalogue URL for one pathway.
     *
     * When no visible category is materialised yet, fall back to the complete
     * public course explorer rather than inventing a category id.
     *
     * @param array<string, mixed> $pathway Canonical pathway row.
     */
    private static function moodle_courses_url(array $pathway): moodle_url {
        global $CFG, $DB;

        $fallback = new moodle_url('/local/uckk/courses.php');
        if (!isset($CFG, $DB)) {
            return $fallback;
        }

        if (!class_exists('xmldb_table')) {
            require_once($CFG->libdir . '/xmldb/xmldb_object.php');
        }
        if (!$DB->get_manager()->table_exists(new \xmldb_table('local_uckk_program'))) {
            return $fallback;
        }

        $pathwayid = (string)$pathway['pathway_id'];
        $code = strtoupper((string)$pathway['code']);
        $records = $DB->get_records_sql("\n            SELECT p.shortname, p.categoryid, c.idnumber AS categoryidnumber, c.visible AS categoryvisible\n              FROM {local_uckk_program} p\n         LEFT JOIN {course_categories} c ON c.id = p.categoryid\n             WHERE p.status = :status\n        ", ['status' => 'active']);

        foreach ($records as $record) {
            $identifiers = [
                trim((string)($record->shortname ?? '')),
                trim((string)($record->categoryidnumber ?? '')),
            ];
            $matches = false;
            foreach ($identifiers as $identifier) {
                if ($identifier !== '' && (
                    strcasecmp($identifier, $pathwayid) === 0
                    || strcasecmp($identifier, $code) === 0
                    || strcasecmp($identifier, 'UCC-' . $code) === 0
                )) {
                    $matches = true;
                    break;
                }
            }
            if (!$matches) {
                continue;
            }

            $categoryid = (int)($record->categoryid ?? 0);
            if ($categoryid > 0 && (int)($record->categoryvisible ?? 0) === 1) {
                return new moodle_url('/local/uckk/courses.php', ['categoryid' => $categoryid]);
            }
        }

        return $fallback;
    }

    /** Human public label for a reading role. */
    private static function role_label(string $role): string {
        $labels = [
            'anchor' => 'Œuvre-charnière',
            'primary' => 'Lecture principale',
            'supporting' => 'Lecture d’appui',
            'editorial_complement' => 'Complément éditorial',
        ];
        return $labels[$role] ?? 'Référence';
    }
}
