<?php
// This file is part of Moodle - https://moodle.org/

/**
 * UCC Médiathèque reference and course-link registry.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/**
 * Read-only projection joining UCC courses with curated Médiathèque references.
 */
final class ucc_mediatheque_registry {
    public const REFERENCES_SCHEMA_VERSION = 'UCC-MEDIATHEQUE-REFS-1.1';
    public const LINKS_SCHEMA_VERSION = 'UCC-MEDIATHEQUE-LINKS-1.0';
    public const REFERENCES_RELATIVE_PATH = 'local/uckk/atlas/ucc_mediatheque_reference_registry.json';
    public const LINKS_RELATIVE_PATH = 'local/uckk/atlas/ucc_mediatheque_course_links.json';

    /** @var array<string, mixed>|null */
    private static ?array $referencescache = null;

    /** @var array<string, mixed>|null */
    private static ?array $linkscache = null;

    /** @return array<int, array<string, mixed>> */
    public static function works(): array {
        return self::references()['works'];
    }

    /**
     * @param string $mediaref Canonical UCC media/work ref.
     * @return array<string, mixed>
     */
    public static function get_work(string $mediaref): array {
        $mediaref = trim($mediaref);
        foreach (self::works() as $work) {
            if (($work['work_ref'] ?? '') === $mediaref) {
                return $work;
            }
        }
        throw new \coding_exception('Unknown UCC Médiathèque reference: ' . $mediaref);
    }

    /**
     * Return link rows for one canonical UCC course.
     *
     * @param string $courseid Canonical UCC course id.
     * @return array<int, array<string, mixed>>
     */
    public static function links_for_course(string $courseid): array {
        $courseid = strtoupper(trim($courseid));
        // Validate course identity against the canonical curriculum first.
        ucc_curriculum_registry::get_course($courseid);
        foreach (self::links()['course_links'] as $row) {
            if (($row['course_id'] ?? '') === $courseid) {
                return $row['media_refs'] ?? [];
            }
        }
        return [];
    }

    /**
     * Return link rows enriched with public reference metadata.
     *
     * @param string $courseid Canonical UCC course id.
     * @return array<int, array<string, mixed>>
     */
    public static function media_for_course(string $courseid): array {
        $result = [];
        foreach (self::links_for_course($courseid) as $link) {
            $work = self::get_work((string)$link['media_ref']);
            $result[] = [
                'media_ref' => (string)$link['media_ref'],
                'role' => (string)($link['role'] ?? 'supporting'),
                'matched_theme_refs' => array_values($link['matched_theme_refs'] ?? []),
                'rationale' => (string)($link['rationale'] ?? ''),
                'passage' => (string)($link['passage'] ?? ''),
                'language' => (string)($work['language'] ?? ''),
                'teachingnote' => (string)($work['teachingnote'] ?? ''),
                'reviewnotice' => ($link['role'] ?? '') === 'anchor'
                    ? 'Exemplaire légal requis ; table, bibliographie et passages du livre non vérifiés.'
                    : 'Lecture proposée ; collation du passage et contrôle de l’édition requis. Droits de copie non présumés.',
                'title' => (string)($work['title'] ?? ''),
                'creator' => (string)($work['creator'] ?? ''),
                'sourceurl' => (string)($work['sourceurl'] ?? ''),
                'rightsstatus' => (string)($work['rightsstatus'] ?? 'unknown'),
            ];
        }
        return $result;
    }

    /** @return array<string, mixed> */
    public static function stats(): array {
        return self::links()['stats'];
    }

    public static function reset_cache(): void {
        self::$referencescache = null;
        self::$linkscache = null;
    }

    /** @return array<string, mixed> */
    private static function references(): array {
        if (self::$referencescache === null) {
            self::$referencescache = self::load_json(self::REFERENCES_RELATIVE_PATH);
            $doc = self::$referencescache;
            if (($doc['schema_version'] ?? '') !== self::REFERENCES_SCHEMA_VERSION
                    || ($doc['universe_id'] ?? '') !== 'ucc'
                    || !isset($doc['works'])
                    || !is_array($doc['works'])) {
                throw new \coding_exception('Invalid UCC Médiathèque reference registry contract.');
            }
        }
        return self::$referencescache;
    }

    /** @return array<string, mixed> */
    private static function links(): array {
        if (self::$linkscache === null) {
            self::$linkscache = self::load_json(self::LINKS_RELATIVE_PATH);
            $doc = self::$linkscache;
            if (($doc['schema_version'] ?? '') !== self::LINKS_SCHEMA_VERSION
                    || ($doc['universe_id'] ?? '') !== 'ucc'
                    || !isset($doc['course_links'], $doc['stats'])
                    || !is_array($doc['course_links'])
                    || !is_array($doc['stats'])) {
                throw new \coding_exception('Invalid UCC Médiathèque course-link registry contract.');
            }
            self::validate_links($doc);
        }
        return self::$linkscache;
    }

    /**
     * @param array<string, mixed> $doc Link registry.
     */
    private static function validate_links(array $doc): void {
        $works = [];
        foreach (self::works() as $work) {
            $ref = (string)($work['work_ref'] ?? '');
            if ($ref === '' || isset($works[$ref])) {
                throw new \coding_exception('Invalid or duplicate UCC Médiathèque work ref: ' . $ref);
            }
            $works[$ref] = true;
        }

        $seen = [];
        foreach ($doc['course_links'] as $row) {
            $courseid = (string)($row['course_id'] ?? '');
            if ($courseid === '' || isset($seen[$courseid])) {
                throw new \coding_exception('Invalid or duplicate UCC course-link row: ' . $courseid);
            }
            $course = ucc_curriculum_registry::get_course($courseid);
            if (($row['pathway_id'] ?? '') !== $course['pathway_id']) {
                throw new \coding_exception('UCC course-link pathway mismatch for ' . $courseid);
            }
            $mediarefs = $row['media_refs'] ?? [];
            if (!is_array($mediarefs) || count($mediarefs) < 3 || count($mediarefs) > 6) {
                throw new \coding_exception('UCC course must expose between 3 and 6 Médiathèque links: ' . $courseid);
            }
            $seenmedia = [];
            foreach ($mediarefs as $link) {
                $ref = (string)($link['media_ref'] ?? '');
                if (isset($seenmedia[$ref]) || !in_array($link['role'] ?? '',
                        ['anchor', 'primary', 'supporting', 'editorial_complement'], true)
                        || empty($link['passage']) || empty($link['rationale'])) {
                    throw new \coding_exception('Invalid UCC editorial reading: ' . $courseid);
                }
                $seenmedia[$ref] = true;
                if (!isset($works[$ref])) {
                    throw new \coding_exception('UCC course-link references unknown media: ' . $ref);
                }
            }
            $seen[$courseid] = true;
        }

        if (count($seen) !== count(ucc_curriculum_registry::courses())) {
            throw new \coding_exception('UCC Médiathèque course-link registry must cover every canonical UCC course.');
        }
    }

    /**
     * @param string $relativepath Path relative to Moodle dirroot.
     * @return array<string, mixed>
     */
    private static function load_json(string $relativepath): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativepath);
        if (!is_readable($path)) {
            throw new \coding_exception('UCC Médiathèque registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid UCC Médiathèque registry JSON: ' . $e->getMessage());
        }
        if (!is_array($doc)) {
            throw new \coding_exception('Invalid UCC Médiathèque registry document.');
        }
        return $doc;
    }
}
