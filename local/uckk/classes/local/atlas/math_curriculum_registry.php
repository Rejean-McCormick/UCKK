<?php
// This file is part of Moodle - https://moodle.org/

/** Canonical Univers-Cité des mathématiques pathway registry. */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

final class math_curriculum_registry {
    public const SCHEMA_VERSION = 'MATH-CURRICULUM-1.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/math_curriculum_registry.json';

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

    /** @return array<string, mixed> */
    public static function get_pathway(string $pathwayid): array {
        $pathwayid = trim($pathwayid);
        foreach (self::pathways() as $pathway) {
            if ($pathway['pathway_id'] === $pathwayid) {
                return $pathway;
            }
        }
        throw new \coding_exception('Unknown Math pathway: ' . $pathwayid);
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('Math curriculum registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid Math curriculum registry JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION || ($doc['universe_id'] ?? '') !== 'math') {
            throw new \coding_exception('Invalid Math curriculum registry contract.');
        }
        if (!isset($doc['pathways']) || !is_array($doc['pathways']) || count($doc['pathways']) !== 4) {
            throw new \coding_exception('Math curriculum registry must contain exactly four canonical pathways.');
        }
        $seen = [];
        foreach ($doc['pathways'] as $pathway) {
            $id = (string)($pathway['pathway_id'] ?? '');
            if (!preg_match('/^math\.path\.[a-z0-9-]+$/', $id) || isset($seen[$id])) {
                throw new \coding_exception('Invalid or duplicate Math pathway id: ' . $id);
            }
            foreach (array_keys($pathway) as $field) {
                if (str_starts_with((string)$field, 'legacy_')) {
                    throw new \coding_exception('Legacy field leaked into canonical Math pathway: ' . $field);
                }
            }
            $seen[$id] = true;
        }
        return $doc;
    }
}
