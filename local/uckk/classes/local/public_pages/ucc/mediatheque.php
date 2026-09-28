<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// UCKK-Moodle adapts Moodle as the pedagogical campus of the
// Univers-Cité chrétienne.

/**
 * Public catalogue page definition for local_uckk.
 *
 * This class owns the public page content definition for the UCC Médiathèque.
 * Media records, permissions, public filtering, rights, content advisories
 * and cultural protocol decisions remain owned by mod_uckkarchive.
 *
 * It must not:
 * - query media tables;
 * - expose private internal fields;
 * - decide access rights;
 * - bypass content advisories;
 * - bypass cultural protocols;
 * - build private file URLs;
 * - mutate Moodle data.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local\public_pages\ucc;

defined('MOODLE_INTERNAL') || die();

/**
 * Public catalogue page definition.
 *
 * @package local_uckk
 */
final class mediatheque {
    /**
     * Médiathèque explorer DOM id.
     */
    private const EXPLORER_ID = 'local-uckk-mediatheque-explorer';

    /**
     * Public Médiathèque search service.
     */
    private const SEARCH_SERVICE = 'mod_uckkarchive_search_mediatheque';

    /**
     * Return the public catalogue page definition.
     *
     * @return array<string, mixed>
     */
    public static function definition(): array {
        return [
            'layout' => 'wide',
            'typography' => 'institutional',

            'eyebrow' => 'Bibliothèque publique',
            'title' => 'Médiathèque chrétienne',
            'subtitle' => 'Explorer les œuvres, médias, collections, références et passages documentés de l’Univers-Cité chrétienne.',
            'summary' => 'La Médiathèque chrétienne constitue un fonds autonome. Elle rassemble les œuvres, documents, sons, images, vidéos et références rattachés à l’Univers-Cité chrétienne. Le fonds privilégie les œuvres publiées jusqu’en 1926 inclusivement, avec des exceptions déclarées. Le Dieu cosmique de Jacques Languirand et Jean Proulx est l’une de ces exceptions et sert d’ouvrage-charnière à une Voie dédiée.',
            'cardsheading' => 'Entrer dans la bibliothèque',

            'has_mediatheque_explorer' => true,
            'mediatheque_explorer_id' => self::EXPLORER_ID,
            'mediatheque_initial_state' => [
                'rootId' => self::EXPLORER_ID,
                'service' => self::SEARCH_SERVICE,
                'cmid' => 0,
                'archiveid' => 0,
                'query' => '',
                'filters' => [
                    'type' => 'all',
                    'mediatype' => 'all',
                    'collection' => '',
                    'tag' => '',
                    'source' => '',
                    'advisory' => 'all',
                    'cultural' => 'all',
                    'audience' => 'all',
                    'lang' => '',
                    'validation' => 'all',
                    'item' => '',
                ],
                'page' => 1,
                'perpage' => 12,
                'sort' => 'relevance',
                'sitewide' => true,
            ],

            'mediatheque' => [
                'explorer' => [
                    'key' => 'mediatheque_explorer',
                    'title' => 'Recherche publique',
                    'service' => self::SEARCH_SERVICE,
                    'surface' => 'local_uckk',
                    'dataowner' => 'mod_uckkarchive',
                ],
            ],

            'quicklinks' => [
                ['label' => 'Plans et lectures', 'description' => '110 plans de séminaire : passages, objectifs, activités, évaluations et limites documentaires.', 'url' => '/local/uckk/ucc_study.php'],
                ['label' => 'Références par cours', 'description' => 'Consulter les sources externes sélectionnées et retrouver leurs cours.', 'url' => '/local/uckk/ucc_study.php?view=references'],
                [
                    'label' => 'Rechercher',
                    'description' => 'Trouver des médias publics, collections ouvertes, références externes et passages documentés.',
                    'url' => '/local/uckk/mediatheque.php',
                ],
            ],

            'sections' => [
                [
                    'title' => 'Lire les sources, comparer, argumenter',
                    'body' => 'Les 11 Voies disposent de 110 plans de séminaire. Chaque plan propose une question, des passages à lire, cinq séances, une production et un barème. Un socle conseillé relie Écriture, théologie, histoire, vie spirituelle et méthode critique. Les plans restent soumis à relecture ; les espaces Moodle ouverts sont indiqués séparément.',
                    'items' => ['Œuvres jusqu’en 1926 inclusivement ; exceptions : Teilhard de Chardin, Fratelli tutti et Le Dieu cosmique.', 'Distinguer source biblique, définition conciliaire, théologien, témoignage historique et interprétation éditoriale.', 'Les langues, éditions, passages et droits encore à vérifier sont signalés.'],
                ],
                [
                    'type' => 'orientation',
                    'eyebrow' => 'Exploration',
                    'title' => 'Une bibliothèque vivante de savoirs publics',
                    'body' => 'L’explorateur cherche d’abord dans le fonds propre de l’Univers-Cité chrétienne. Les contenus reçus par bridge restent identifiés à leur médiathèque d’origine, afin de permettre les croisements entre corpus sans fusionner les fonds.',
                    'items' => [
                        'Recherche par texte, format, source, collection, langue et mot-clé.',
                        'Parcours libre dans les contenus publics accessibles.',
                        'Repérage de documents, médias et passages utiles aux cours, voies, archives et assemblées.',
                    ],
                ],
                [
                    'type' => 'orientation',
                    'eyebrow' => 'Ouvrage-charnière',
                    'title' => 'Le Dieu cosmique : une carte, pas une doctrine',
                    'body' => 'Le Dieu cosmique : À la recherche du Dieu d’Einstein (Jacques Languirand et Jean Proulx, Le Jour, 2008) est conservé comme référence explicite malgré la règle historique de 1926. La Médiathèque le traite comme une synthèse permettant de circuler entre différentes conceptions du divin et de rejoindre leurs sources primaires.',
                    'items' => [
                        'ISBN papier : 978-2-89044-764-6.',
                        'L’ouvrage demeure sous droit d’auteur : UCC référence les accès légaux et les métadonnées, sans republier le PDF ou l’EPUB.',
                        'La Voie du Dieu cosmique distingue les références attestées de l’ouvrage des lectures complémentaires ajoutées par UCC.',
                    ],
                ],
                [
                    'type' => 'orientation',
                    'eyebrow' => 'Maillage pédagogique',
                    'title' => 'Chaque cours ouvre sur des sources pertinentes',
                    'body' => 'Les 110 plans proposent des lectures choisies individuellement, avec passages et raisons pédagogiques. Le catalogue des références externes est accessible depuis Plans et lectures ; l’explorateur ci-dessous conserve les médias effectivement publiés dans Moodle.',
                    'items' => [
                        '110 cours sur 110 possèdent des liens documentaires.',
                        'Chaque lecture possède une justification propre au cours ; une proximité de mots-clés ne suffit pas.',
                        'Les références restent rattachées à leurs droits, à leur source externe et à leur provenance.',
                    ],
                ],
                [
                    'type' => 'boundary',
                    'eyebrow' => 'Cadre de consultation',
                    'title' => 'Un accès ouvert, avec respect des droits',
                    'body' => 'La Médiathèque ouvre ce qui peut être partagé publiquement. Certains contenus peuvent rester limités lorsque des droits, avis de contenu, permissions ou protocoles culturels l’exigent.',
                ],
            ],

            'cards' => [
                [
                    'title' => 'Médias publics',
                    'body' => 'Voir les vidéos, sons, images, documents et références accessibles dans la bibliothèque publique.',
                    'type' => 'media',
                ],
                [
                    'title' => 'Collections',
                    'body' => 'Explorer des regroupements de contenus liés aux cours, voies, thèmes, archives et recherches UCC.',
                    'type' => 'collection',
                ],
                [
                    'title' => 'Le Dieu cosmique',
                    'body' => 'Ouvrage-charnière de Jacques Languirand et Jean Proulx, relié à une Voie comparative sur science, philosophie, expérience religieuse et conceptions du divin.',
                    'url' => 'https://emprunt.bibliothequedesameriques.com/resources/550ad46fcdd23087a9787007',
                    'actionlabel' => 'Voir la notice et l’accès légal',
                    'type' => 'book',
                ],
                [
                    'title' => 'Passages documentés',
                    'body' => 'Accéder à des moments, pages, extraits ou segments qui éclairent une idée, une controverse ou un apprentissage.',
                    'type' => 'marker',
                ],
            ],

            'notices' => [
                [
                    'title' => 'Bibliothèque ouverte',
                    'body' => 'Le fonds chrétien est séparé par défaut des autres médiathèques. Le partage avec une autre Univers-Cité doit être explicite : médiathèque commune ou bridge vers une collection, un média ou un fonds complet.',
                    'type' => 'institutional',
                ],
                [
                    'title' => 'Consultation responsable',
                    'body' => 'Les résultats affichés respectent les droits, les avis de contenu, les permissions et les protocoles culturels définis par le moteur média institutionnel.',
                    'type' => 'light',
                ],
            ],

            'metadata' => [
                [
                    'label' => 'Surface publique',
                    'value' => 'local_uckk',
                ],
                [
                    'label' => 'Bibliothèque',
                    'value' => 'Médiathèque chrétienne',
                ],
                [
                    'label' => 'Service',
                    'value' => 'Recherche du catalogue public',
                ],
                [
                    'label' => 'Portée par défaut',
                    'value' => 'Fonds chrétien + bridges entrants explicites',
                ],
                [
                    'label' => 'Politique historique',
                    'value' => 'Œuvres originales ≤ 1926; exceptions déclarées : Teilhard de Chardin, Fratelli tutti et Le Dieu cosmique',
                ],
            ],

            'cta' => [
                'title' => 'Explorer la Médiathèque',
                'body' => 'Lance une recherche ou applique un filtre pour parcourir les contenus publics par format, source, collection, langue ou mot-clé.',
                'url' => '/local/uckk/mediatheque.php',
                'label' => 'Ouvrir la recherche',
            ],
        ];
    }
}