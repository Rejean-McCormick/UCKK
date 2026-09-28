<?php
// This file is part of Moodle - https://moodle.org/.
// @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

declare(strict_types=1);
namespace local_uckk\local\atlas;
defined('MOODLE_INTERNAL') || die();

/** Public editorial plans, independent from live Moodle course instances. */
final class ucc_syllabus_registry {
    private static ?array $cache = null;

    public static function document(): array {
        global $CFG;
        if (self::$cache === null) {
            $path = $CFG->dirroot . '/local/uckk/atlas/ucc_course_syllabi.json';
            if (!is_readable($path)) {
                throw new \coding_exception('UCC syllabus registry is not readable.');
            }
            try {
                $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \coding_exception('Invalid UCC syllabus JSON: ' . $e->getMessage());
            }
            if (!is_array($doc) || ($doc['schema_version'] ?? '') !== 'UCC-SYLLABI-1.0'
                    || ($doc['universe_id'] ?? '') !== 'ucc' || !is_array($doc['courses'] ?? null)) {
                throw new \coding_exception('Invalid UCC syllabus contract.');
            }
            $seen = [];
            foreach ($doc['courses'] as $plan) {
                $id = (string)($plan['course_id'] ?? '');
                $course = ucc_curriculum_registry::get_course($id);
                if (isset($seen[$id]) || ($plan['pathway_id'] ?? '') !== $course['pathway_id']
                        || empty($plan['central_question']) || count($plan['sessions'] ?? []) !== 5
                        || count($plan['objectives'] ?? []) < 3 || empty($plan['assessment']['task'])) {
                    throw new \coding_exception('Invalid UCC syllabus: ' . $id);
                }
                $weights = array_column($plan['assessment']['rubric'] ?? [], 'weight');
                if (array_sum($weights) !== 100) {
                    throw new \coding_exception('Invalid UCC assessment weights: ' . $id);
                }
                $seen[$id] = true;
            }
            if (count($seen) !== count(ucc_curriculum_registry::courses())) {
                throw new \coding_exception('Incomplete UCC syllabus registry.');
            }
            self::$cache = $doc;
        }
        return self::$cache;
    }

    public static function get_course(string $id): array {
        $id = strtoupper(trim($id));
        ucc_curriculum_registry::get_course($id);
        foreach (self::document()['courses'] as $plan) {
            if ($plan['course_id'] === $id) {
                return $plan;
            }
        }
        throw new \coding_exception('Unknown UCC syllabus: ' . $id);
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }
}
