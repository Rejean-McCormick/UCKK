<?php
// This file is part of Moodle - https://moodle.org/

/** Rebuildable MathKristal -> UCKK consumer projection. */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

final class math_university_projection {
    public const SCHEMA_VERSION = 'MATH-UNIVERSITY-PROJECTION-1.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/math_university_projection.json';

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
    public static function pathways(): array {
        return self::get()['pathways'];
    }

    /** @return array<int, array<string, mixed>> */
    public static function courses(): array {
        return self::get()['courses'];
    }

    /** @return array<int, array<string, mixed>> */
    public static function page_specs(): array {
        return self::get()['page_specs'];
    }

    /** @return array<string, mixed> */
    public static function statistics(): array {
        return self::get()['statistics'];
    }

    /** @return array<string, mixed> */
    public static function generated_from(): array {
        return self::get()['generated_from'];
    }

    /** @return array<string, mixed> */
    public static function get_pathway(string $pathwayid): array {
        $pathwayid = trim($pathwayid);
        foreach (self::pathways() as $pathway) {
            if (($pathway['pathway_id'] ?? '') === $pathwayid) {
                return $pathway;
            }
        }
        throw new \coding_exception('Unknown Math Kristal pathway: ' . $pathwayid);
    }

    /** @return array<string, mixed> */
    public static function get_page_spec(string $pagespecid): array {
        $pagespecid = trim($pagespecid);
        foreach (self::page_specs() as $pagespec) {
            if (($pagespec['page_spec_id'] ?? '') === $pagespecid) {
                return $pagespec;
            }
        }
        throw new \coding_exception('Unknown Math page specification: ' . $pagespecid);
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('Math university projection is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid Math university projection JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION || ($doc['universe_id'] ?? '') !== 'math') {
            throw new \coding_exception('Invalid Math university projection contract.');
        }
        if (!isset($doc['generated_from']) || !is_array($doc['generated_from'])) {
            throw new \coding_exception('Math university projection must declare its pinned Kristal source.');
        }
        $stateid = (string)($doc['generated_from']['state_id'] ?? '');
        if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stateid)) {
            throw new \coding_exception('Math university projection has an invalid Kristal state id.');
        }
        if (!isset($doc['pathways']) || !is_array($doc['pathways']) || !$doc['pathways']) {
            throw new \coding_exception('Math university projection must contain at least one pathway.');
        }
        if (!isset($doc['courses']) || !is_array($doc['courses']) || !$doc['courses']) {
            throw new \coding_exception('Math university projection must contain at least one projected course.');
        }
        if (!isset($doc['page_specs']) || !is_array($doc['page_specs']) || !$doc['page_specs']) {
            throw new \coding_exception('Math university projection must contain page specifications.');
        }

        $pathways = [];
        foreach ($doc['pathways'] as $pathway) {
            $id = (string)($pathway['pathway_id'] ?? '');
            if (!preg_match('/^math\.path\.[a-z0-9-]+$/', $id) || isset($pathways[$id])) {
                throw new \coding_exception('Invalid or duplicate projected Math pathway id: ' . $id);
            }
            $pathways[$id] = true;
        }

        $courses = [];
        foreach ($doc['courses'] as $course) {
            $id = (string)($course['math_course_id'] ?? '');
            if (!preg_match('/^MATH-[A-Z0-9]{3}-1[0-9]{2}$/', $id) || isset($courses[$id])) {
                throw new \coding_exception('Invalid or duplicate projected Math course id: ' . $id);
            }
            if (!isset($pathways[(string)($course['pathway_id'] ?? '')])) {
                throw new \coding_exception('Projected Math course references unknown pathway: ' . $id);
            }
            if (!str_starts_with((string)($course['kristal_ref'] ?? ''), 'urn:mathkristal:')) {
                throw new \coding_exception('Projected Math course is missing its Kristal referent: ' . $id);
            }
            $courses[$id] = true;
        }

        $pages = [];
        foreach ($doc['page_specs'] as $pagespec) {
            $id = (string)($pagespec['page_spec_id'] ?? '');
            $courseid = (string)($pagespec['math_course_id'] ?? '');
            if (!preg_match('/^math\.page\.[a-z0-9]+\.1[0-9]{2}$/', $id) || isset($pages[$id])) {
                throw new \coding_exception('Invalid or duplicate Math page spec id: ' . $id);
            }
            if (!isset($courses[$courseid])) {
                throw new \coding_exception('Math page spec references unknown projected course: ' . $id);
            }
            if (($pagespec['rendering']['content_selection_owner'] ?? '') !== 'UCKK Math University Projector') {
                throw new \coding_exception('Math page spec must keep content selection outside SemantiK Architect: ' . $id);
            }
            $pages[$id] = true;
        }

        $stats = $doc['statistics'] ?? [];
        if (!is_array($stats)
            || (int)($stats['projected_pathways'] ?? -1) !== count($doc['pathways'])
            || (int)($stats['projected_courses'] ?? -1) !== count($doc['courses'])
            || (int)($stats['projected_page_specs'] ?? -1) !== count($doc['page_specs'])) {
            throw new \coding_exception('Math university projection statistics do not match its contents.');
        }
        return $doc;
    }
}
