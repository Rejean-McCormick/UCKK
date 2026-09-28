<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// UCKK-Moodle supports the technical Moodle implementation of the
// Univers-Cité chrétienne.

/**
 * Public programs page definition for local_uckk.
 *
 * This class owns the public page definition for the Voies UCC page.
 * It may read active public program records for display only.
 *
 * It must not create programs, mutate pathways, enrol users, award recognitions,
 * validate competencies, or make accreditation claims.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

use moodle_url;
use local_uckk\local\atlas\ucc_curriculum_registry;
use local_uckk\local\atlas\ucc_legacy_resolver;

defined('MOODLE_INTERNAL') || die();

/**
 * Public programs page definition.
 *
 * @package local_uckk
 */
final class programs {
    /**
     * Return the public page definition.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        return self::with_program_cards([
            'layout' => 'wide',
            'typography' => 'institutional',
            'eyebrow' => 'Bibliothèque publique vivante',
            'title' => 'Voies UCC',
            'subtitle' => 'Parcours ouverts pour explorer et relier les savoirs, auteurs, œuvres et concepts du corpus.',
            'summary' => 'Les Voies UCC organisent la diffusion du savoir en parcours lisibles : cours, repères, pratiques, archives, médiathèque, défis et assemblées. Elles offrent un cadre d’apprentissage familier, modernisé et ouvert pour comprendre, produire, vérifier et agir avec méthode.',
            'sections' => [
                [
                    'type' => 'role',
                    'title' => 'Rôle des voies',
                    'body' => 'Une voie aide à circuler dans la bibliothèque UCC. Elle relie des cours, des notions, des lectures, des pratiques, des défis, des archives, des Assemblées et des repères de progression.',
                ],
                [
                    'type' => 'registry',
                    'eyebrow' => 'Répertoire public',
                    'title' => 'Voies actives',
                    'body' => 'Les cartes ci-dessous présentent les parcours actuellement ouverts au public. Les éléments en brouillon, cachés ou archivés ne sont pas affichés.',
                ],
            ],
            'cards' => [],
            'notices' => [
                [
                    'body' => 'Les éventuelles reconnaissances UCC demeurent internes, sauf reconnaissance officielle future.',
                    'type' => 'institutional',
                ],
            ],
            'metadata' => [
                ['label' => 'Source', 'value' => 'Bibliothèque publique UCC'],
                ['label' => 'Filtre public', 'value' => 'Voies ouvertes seulement'],
            ],
            'cta' => [
                'title' => 'Explorer les cours',
                'body' => 'Les espaces de cours associés aux Voies regroupent les savoirs, lectures, exercices et repères disponibles dans un cadre d’apprentissage familier et modernisé.',
                'url' => '/local/uckk/courses.php',
                'label' => 'Voir les cours ouverts',
            ],
        ]);
    }

    /**
     * Add live program cards from local_uckk_program to the public page definition.
     *
     * @param array<string, mixed> $definition Base page definition.
     * @return array<string, mixed>
     */
    private static function with_program_cards(array $definition): array {
        $cards = self::program_cards('active');

        // Program cards belong inside the public registry section only.
        // Keeping them at page level creates a second generic card section.
        $definition['cards'] = [];
        $definition['cardsheading'] = 'Repères publics';

        if (!isset($definition['sections']) || !is_array($definition['sections'])) {
            $definition['sections'] = [];
        }

        $attached = false;

        foreach ($definition['sections'] as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $title = (string)($section['title'] ?? '');
            $eyebrow = (string)($section['eyebrow'] ?? '');

            if (
                $title === 'Répertoire public'
                || $title === 'Voies actives'
                || $title === 'Voies et programmes actifs'
                || $eyebrow === 'Répertoire public'
            ) {
                $definition['sections'][$index]['cards'] = $cards;
                $attached = true;
                break;
            }
        }

        if (!$attached) {
            $definition['sections'][] = [
                'type' => 'registry',
                'eyebrow' => 'Répertoire public',
                'title' => 'Voies actives',
                'body' => 'Ces cartes présentent les voies actuellement ouvertes au public. Les éléments en brouillon ou non publiés ne sont pas affichés ici.',
                'cards' => $cards,
            ];
        }

        if (!isset($definition['metadata']) || !is_array($definition['metadata'])) {
            $definition['metadata'] = [];
        }

        $definition['metadata'][] = [
            'label' => 'Voies actives affichées',
            'value' => (string)count($cards),
        ];

        if (empty($cards)) {
            if (!isset($definition['notices']) || !is_array($definition['notices'])) {
                $definition['notices'] = [];
            }

            $definition['notices'][] = [
                'title' => 'Aucune voie ouverte',
                'body' => 'Aucune voie ouverte n’est actuellement disponible dans la bibliothèque publique UCC.',
                'type' => 'warning',
            ];
        }

        return $definition;
    }

    /**
     * Build public cards from active UCC program records.
     *
     * @param string $status Program status to display.
     * @return array<int, array<string, mixed>>
     */
    private static function program_cards(string $status): array {
        global $CFG, $DB;

        // Canonical Atlas pathways are the public source of truth. Moodle program
        // records are used only to discover an optional visible course-category URL.
        $categoryurls = [];

        if (isset($CFG) && isset($DB)) {
            if (!class_exists('xmldb_table')) {
                require_once($CFG->libdir . '/xmldb/xmldb_object.php');
            }

            if ($DB->get_manager()->table_exists(new \xmldb_table('local_uckk_program'))) {
                $records = $DB->get_records_sql("
                    SELECT p.shortname, p.fullname, p.status, p.categoryid,
                           c.name AS categoryname, c.idnumber AS categoryidnumber, c.visible AS categoryvisible
                      FROM {local_uckk_program} p
                 LEFT JOIN {course_categories} c ON c.id = p.categoryid
                     WHERE p.status = :status
                ", ['status' => $status]);

                foreach ($records as $record) {
                    $pathwayid = self::canonical_pathway_id_from_identifiers(
                        trim((string)($record->shortname ?? '')),
                        trim((string)($record->categoryidnumber ?? ''))
                    );
                    if ($pathwayid === '') {
                        continue;
                    }
                    $categoryid = (int)($record->categoryid ?? 0);
                    $categoryvisible = (int)($record->categoryvisible ?? 0);
                    if ($categoryid > 0 && $categoryvisible === 1) {
                        $categoryurls[$pathwayid] = (new moodle_url('/course/index.php', [
                            'categoryid' => $categoryid,
                        ]))->out(false);
                    }
                }
            }
        }

        $cards = [];
        foreach (ucc_curriculum_registry::pathways() as $pathway) {
            $pathwayid = (string)$pathway['pathway_id'];
            $publicidentity = self::public_program_identity($pathwayid, '', '', $pathwayid);
            if ($publicidentity === null) {
                continue;
            }

            $signature = self::voie_visual_signature($pathwayid, '', '', $pathwayid);
            $coursecount = count($pathway['course_ids'] ?? []);
            $body = $publicidentity['body'];
            if ($coursecount > 0) {
                $body .= ' ' . $coursecount . ' cours canoniques sont rattachés à cette Voie.';
            }

            $url = $categoryurls[$pathwayid] ?? (new moodle_url('/local/uckk/courses.php'))->out(false);
            $cards[] = [
                'eyebrow' => $publicidentity['eyebrow'],
                'title' => $publicidentity['title'],
                'body' => $body,
                'url' => $url,
                'actionlabel' => isset($categoryurls[$pathwayid]) ? 'Accéder aux cours' : 'Explorer les cours',
                'type' => $publicidentity['type'],
                'classes' => self::program_card_classes($publicidentity['type'], $signature),
            ];
        }

        return $cards;
    }

    /**
     * Canonical visual signatures for public Voie cards.
     *
     * The values are intentionally CSS-oriented only: they do not grant permissions,
     * change faculty pages, or mutate Atlas / Moodle records.
     */
    private const PATHWAY_VISUAL_SIGNATURES = [
        'ucc.path.arts-beauty-culture' => ['slug' => 'arts-beauty-culture'],
        'ucc.path.language-letters-transmission' => ['slug' => 'language-letters-transmission'],
        'ucc.path.creation-sciences-ecology' => ['slug' => 'creation-sciences-ecology'],
        'ucc.path.theology-scripture-tradition' => ['slug' => 'theology-scripture-tradition'],
        'ucc.path.philosophy-metaphysics-person' => ['slug' => 'philosophy-metaphysics-person'],
        'ucc.path.cosmic-divine-continuum' => ['slug' => 'cosmic-divine-continuum'],
        'ucc.path.works-institutions-administration' => ['slug' => 'works-institutions-administration'],
        'ucc.path.education-universities-transmission' => ['slug' => 'education-universities-transmission'],
        'ucc.path.health-care-dignity' => ['slug' => 'health-care-dignity'],
        'ucc.path.economy-work-social-justice' => ['slug' => 'economy-work-social-justice'],
        'ucc.path.law-politics-common-good' => ['slug' => 'law-politics-common-good'],
    ];

    /**
     * Build the CSS class list for a public program / faculty-link card.
     *
     * @param string $type Public card type.
     * @param array{slug:string,pathway_id:string,code:string} $signature Visual signature.
     * @return string
     */
    private static function program_card_classes(string $type, array $signature): string {
        $classes = [
            'local-uckk-public-card',
            'local-uckk-public-card--program',
            'local-uckk-faculty-link-card',
            'local-uckk-voie-card',
        ];

        $type = self::clean_modifier($type);
        if ($type !== '') {
            $classes[] = 'local-uckk-public-card--' . $type;
        }

        $slug = self::clean_modifier($signature['slug'] ?? '');
        if ($slug !== '') {
            $classes[] = 'local-uckk-voie-card--' . $slug;
            $classes[] = 'local-uckk-public-card--voie-' . $slug;
            $classes[] = 'local-uckk-faculty-link-card--' . $slug;
        } else {
            $classes[] = 'local-uckk-voie-card--unknown';
        }

        return implode(' ', array_values(array_unique(array_filter($classes))));
    }

    /**
     * Resolve a canonical pathway from stable Moodle identifiers.
     *
     * Historical identifiers are accepted only at this compatibility boundary;
     * the returned identity is always a canonical ucc.path.* id.
     *
     * @return string Canonical pathway id, or an empty string when unresolved.
     */
    private static function canonical_pathway_id_from_identifiers(
        string $shortname,
        string $categoryidnumber
    ): string {
        $candidates = array_values(array_filter([
            trim($categoryidnumber),
            trim($shortname),
        ], static fn(string $value): bool => $value !== ''));

        foreach ($candidates as $candidate) {
            foreach (ucc_curriculum_registry::pathways() as $pathway) {
                $code = strtoupper((string)$pathway['code']);
                if (strcasecmp($candidate, $pathway['pathway_id']) === 0
                    || strcasecmp($candidate, $code) === 0
                    || strcasecmp($candidate, 'UCC-' . $code) === 0) {
                    return (string)$pathway['pathway_id'];
                }
            }
            try {
                return ucc_legacy_resolver::resolve_pathway($candidate);
            } catch (\coding_exception $exception) {
                // Try the next exact stable identifier. Free-text legacy labels are not canonical keys.
            }
        }

        return '';
    }

    /**
     * Resolve a public pathway visual signature from canonical identity.
     *
     * @return array{slug:string,pathway_id:string,code:string}
     */
    private static function voie_visual_signature(
        string $shortname,
        string $fullname,
        string $categoryname,
        string $categoryidnumber
    ): array {
        unset($fullname, $categoryname); // Labels are presentation only, never identity keys.
        $pathwayid = self::canonical_pathway_id_from_identifiers($shortname, $categoryidnumber);

        if ($pathwayid !== '' && isset(self::PATHWAY_VISUAL_SIGNATURES[$pathwayid])) {
            $pathway = ucc_curriculum_registry::get_pathway($pathwayid);
            return self::PATHWAY_VISUAL_SIGNATURES[$pathwayid] + [
                'pathway_id' => $pathwayid,
                'code' => (string)$pathway['code'],
            ];
        }

        return ['slug' => '', 'pathway_id' => '', 'code' => ''];
    }

    /**
     * Public editorial overrides for program cards, keyed only by canonical pathways.
     *
     * @return array{eyebrow:string,title:string,body:string,type:string}|null
     */
    private static function public_program_identity(
        string $shortname,
        string $fullname,
        string $categoryname,
        string $categoryidnumber
    ): ?array {
        unset($fullname, $categoryname);
        $pathwayid = self::canonical_pathway_id_from_identifiers($shortname, $categoryidnumber);

        $identities = [
            'ucc.path.arts-beauty-culture' => ['eyebrow' => 'Kreative · Créer', 'body' => 'Explorer les œuvres, images, récits et pratiques culturelles des traditions chrétiennes, notamment catholiques, en les reliant à leurs sources et à leurs contextes.', 'type' => 'kreative'],
            'ucc.path.language-letters-transmission' => ['eyebrow' => 'Kreative · Créer', 'body' => 'Étudier les mots, textes, langues, traductions et traditions de lecture qui rendent les corpus chrétiens — dont les traditions catholiques — intelligibles et transmissibles.', 'type' => 'kreative'],
            'ucc.path.philosophy-metaphysics-person' => ['eyebrow' => 'KonnectED · Comprendre', 'body' => 'Explorer être, vérité, connaissance, personne, liberté, bien, mal, temps et loi naturelle à partir de positions et sources documentées.', 'type' => 'konnected'],
            'ucc.path.cosmic-divine-continuum' => ['eyebrow' => 'KonnectED · Comprendre', 'body' => 'Parcourir un continuum de réflexions sur le divin, la nature, le cosmos, la science et l’expérience religieuse à partir de l’ouvrage-charnière Le Dieu cosmique de Jacques Languirand et Jean Proulx, sans transformer cette synthèse en doctrine UCC.', 'type' => 'konnected'],
            'ucc.path.theology-scripture-tradition' => ['eyebrow' => 'KonnectED · Comprendre', 'body' => 'Lire les grands ensembles théologiques du corpus en distinguant sources, développement doctrinal, réception, statut épistémique et interprétation.', 'type' => 'konnected'],
            'ucc.path.creation-sciences-ecology' => ['eyebrow' => 'KonnectED · Comprendre', 'body' => 'Étudier les rapports entre création, sciences, évolution, progrès, écologie et responsabilité envers le vivant.', 'type' => 'konnected'],
            'ucc.path.education-universities-transmission' => ['eyebrow' => 'KeenKonnect · Servir', 'body' => 'Explorer écoles, universités, formation, recherche, accès au savoir et responsabilités institutionnelles.', 'type' => 'keenkonnect'],
            'ucc.path.health-care-dignity' => ['eyebrow' => 'KeenKonnect · Servir', 'body' => 'Étudier le soin, la vulnérabilité, le corps, la dignité et les institutions cliniques catholiques.', 'type' => 'keenkonnect'],
            'ucc.path.works-institutions-administration' => ['eyebrow' => 'KeenKonnect · Servir', 'body' => 'Comprendre comment des œuvres catholiques sont administrées, financées, auditées, transmises et rendues responsables.', 'type' => 'keenkonnect'],
            'ucc.path.economy-work-social-justice' => ['eyebrow' => 'Ethikos · Gouverner', 'body' => 'Explorer travail, propriété, pauvreté, échange, don, justice sociale et responsabilité économique dans les traditions sociales chrétiennes, notamment catholiques.', 'type' => 'ethikos'],
            'ucc.path.law-politics-common-good' => ['eyebrow' => 'Ethikos · Gouverner', 'body' => 'Étudier autorité, loi, droits, subsidiarité, institutions, guerre, paix et prudence politique avec une méthode documentaire et non partisane.', 'type' => 'ethikos'],
        ];

        if ($pathwayid === '' || !isset($identities[$pathwayid])) {
            return null;
        }
        $pathway = ucc_curriculum_registry::get_pathway($pathwayid);
        return $identities[$pathwayid] + ['title' => (string)$pathway['title']];
    }

    /**
     * Human label for a program type.
     *
     * @param string $programtype Program type.
     * @return string
     */
    private static function program_type_label(string $programtype): string {
        $labels = [
            'tronc_commun' => 'Tronc commun',
            'voie_uckk' => 'Voie UCC',
            'voie_secondaire' => 'Voie secondaire',
            'baccalaureat' => 'Voie UCC — Puissance opératoire',
            'baccalauréat' => 'Voie UCC — Puissance opératoire',
            'certificat' => 'Parcours d’initiation',
            'mineure' => 'Voie UCC — Initiation',
            'seminaire' => 'Séminaire',
            'séminaire' => 'Séminaire',
            'laboratoire' => 'Laboratoire',
            'lab' => 'Laboratoire',
            'atelier' => 'Atelier',
        ];

        if (isset($labels[$programtype])) {
            return $labels[$programtype];
        }

        $label = trim(str_replace('_', ' ', $programtype));

        return $label !== '' ? ucfirst($label) : '';
    }

    /**
     * Clean CSS modifier value.
     *
     * @param mixed $modifier Modifier.
     * @return string
     */
    private static function clean_modifier($modifier): string {
        if (!is_scalar($modifier)) {
            return '';
        }

        $modifier = strtolower(trim((string)$modifier));
        $modifier = preg_replace('/[^a-z0-9_-]+/', '-', $modifier) ?? '';
        $modifier = trim($modifier, '-');

        return $modifier;
    }
}