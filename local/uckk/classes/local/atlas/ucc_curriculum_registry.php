<?php
// This file is part of Moodle - https://moodle.org/

/**
 * Canonical UCC curriculum registry.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/**
 * Read-only canonical index for UCC domains, pathways, courses and Kristal bindings.
 *
 * Historical UCKK/UCC-v1 identifiers are deliberately excluded from this registry.
 * Use ucc_legacy_resolver for read-only migration compatibility.
 */
final class ucc_curriculum_registry {
    public const SCHEMA_VERSION = 'UCC-CURRICULUM-2.1';
    public const RELATIVE_PATH = 'local/uckk/atlas/ucc_curriculum_registry.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /** @return array<string, mixed> */
    public static function get(): array {
        if (self::$cache === null) {
            self::$cache = self::load();
        }
        return self::$cache;
    }

    /** @return array<int, array<string, mixed>> */
    public static function courses(): array {
        return self::get()['courses'];
    }

    /** @return array<int, array<string, mixed>> */
    public static function pathways(): array {
        return self::get()['pathways'];
    }

    /** @return array<string, mixed> */
    public static function get_pathway(string $pathwayid): array {
        $pathwayid = trim($pathwayid);
        foreach (self::pathways() as $pathway) {
            if ($pathway['pathway_id'] === $pathwayid) {
                return $pathway;
            }
        }
        throw new \coding_exception('Unknown canonical UCC pathway: ' . $pathwayid);
    }

    /**
     * @param string $courseid Canonical UCC course id.
     * @return array<string, mixed>
     */
    public static function get_course(string $courseid): array {
        $courseid = strtoupper(trim($courseid));
        foreach (self::courses() as $course) {
            if ($course['ucc_course_id'] === $courseid) {
                return $course;
            }
        }
        throw new \coding_exception('Unknown UCC course: ' . $courseid);
    }

    /**
     * @param string $domainid Canonical UCC domain id.
     * @return array<int, array<string, mixed>>
     */
    public static function courses_by_domain(string $domainid): array {
        ucc_domain_registry::get_by_id($domainid);
        return array_values(array_filter(self::courses(), static function(array $course) use ($domainid): bool {
            return $course['domain_id'] === $domainid;
        }));
    }

    /**
     * Return courses aligned with one Kristal theme reference.
     *
     * @param string $themeref Canonical ref such as urn:theophile:theme:justice.
     * @return array<int, array<string, mixed>>
     */
    public static function courses_by_theme(string $themeref): array {
        $themeref = trim($themeref);
        return array_values(array_filter(self::courses(), static function(array $course) use ($themeref): bool {
            return in_array($themeref, $course['kristal_theme_refs'], true);
        }));
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('UCC curriculum registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid UCC curriculum registry JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION || ($doc['universe_id'] ?? '') !== 'ucc') {
            throw new \coding_exception('Invalid UCC curriculum registry contract.');
        }
        if (!isset($doc['courses'], $doc['pathways']) || !is_array($doc['courses']) || !is_array($doc['pathways'])) {
            throw new \coding_exception('UCC curriculum registry missing courses or pathways.');
        }
        if (count($doc['courses']) !== 110 || count($doc['pathways']) !== 11) {
            throw new \coding_exception('UCC curriculum registry must contain 11 pathways and 110 courses.');
        }

        $seenpathways = [];
        foreach ($doc['pathways'] as $pathway) {
            $id = (string)($pathway['pathway_id'] ?? '');
            if (!preg_match('/^ucc\.path\.[a-z0-9-]+$/', $id) || isset($seenpathways[$id])) {
                throw new \coding_exception('Invalid or duplicate canonical UCC pathway id: ' . $id);
            }
            foreach (array_keys($pathway) as $field) {
                if (str_starts_with((string)$field, 'legacy_')) {
                    throw new \coding_exception('Legacy field leaked into canonical UCC pathway: ' . $field);
                }
            }
            $seenpathways[$id] = true;
        }

        $seen = [];
        foreach ($doc['courses'] as $course) {
            $id = (string)($course['ucc_course_id'] ?? '');
            $pathwayid = (string)($course['pathway_id'] ?? '');
            if (!preg_match('/^UCC-[A-Z]{3}-[0-9]{3}$/', $id) || isset($seen[$id])) {
                throw new \coding_exception('Invalid or duplicate canonical UCC course id: ' . $id);
            }
            if (!isset($seenpathways[$pathwayid])) {
                throw new \coding_exception('UCC course references unknown canonical pathway: ' . $pathwayid);
            }
            foreach (array_keys($course) as $field) {
                if (str_starts_with((string)$field, 'legacy_')) {
                    throw new \coding_exception('Legacy field leaked into canonical UCC course: ' . $field);
                }
            }
            $seen[$id] = true;
        }
        return $doc;
    }
}
