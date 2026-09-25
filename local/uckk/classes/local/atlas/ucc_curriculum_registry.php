<?php
// This file is part of Moodle - https://moodle.org/

/**
 * Canonical UCC curriculum registry.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité Catho
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/**
 * Read-only canonical index for UCC domains, Voies, courses and Kristal bindings.
 */
final class ucc_curriculum_registry {
    public const SCHEMA_VERSION = 'UCC-CURRICULUM-1.0';
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
    public static function voies(): array {
        return self::get()['voies'];
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
     * Resolve a legacy UCKK course id to its canonical UCC course.
     *
     * @param string $legacyid Legacy course id such as ME101.
     * @return array<string, mixed>
     */
    public static function get_course_by_legacy_id(string $legacyid): array {
        $legacyid = strtoupper(trim($legacyid));
        foreach (self::courses() as $course) {
            if ($course['legacy_course_id'] === $legacyid) {
                return $course;
            }
        }
        throw new \coding_exception('Unknown legacy UCC course alias: ' . $legacyid);
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
        if (!isset($doc['courses'], $doc['voies']) || !is_array($doc['courses']) || !is_array($doc['voies'])) {
            throw new \coding_exception('UCC curriculum registry missing courses or Voies.');
        }
        if (count($doc['courses']) !== 100 || count($doc['voies']) !== 10) {
            throw new \coding_exception('UCC curriculum registry must contain 10 Voies and 100 courses.');
        }
        $seen = [];
        foreach ($doc['courses'] as $course) {
            $id = (string)($course['ucc_course_id'] ?? '');
            if (!preg_match('/^UCC-[A-Z]{3}-[0-9]{3}$/', $id) || isset($seen[$id])) {
                throw new \coding_exception('Invalid or duplicate canonical UCC course id: ' . $id);
            }
            $seen[$id] = true;
        }
        return $doc;
    }
}
