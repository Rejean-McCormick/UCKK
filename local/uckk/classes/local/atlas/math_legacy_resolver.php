<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/** Read-only resolver into the MATH-CURRICULUM-2.0 compatibility snapshot. It does not resolve current MathKristal projection paths. */
final class math_legacy_resolver {
    public const SCHEMA_VERSION = 'MATH-PATHWAY-MIGRATIONS-2.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/math_pathway_migrations.json';

    /** @return array<string, mixed>|null */
    public static function resolve_pathway(string $legacyid): ?array {
        $legacyid = trim($legacyid);
        if ($legacyid === '') {
            return null;
        }
        foreach (self::load()['pathways'] as $mapping) {
            if (($mapping['legacy_id'] ?? '') === $legacyid) {
                return $mapping;
            }
        }
        return null;
    }

    /** @return array<string, mixed> */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('Math pathway migration registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid Math pathway migration JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION) {
            throw new \coding_exception('Invalid Math pathway migration contract.');
        }
        return $doc;
    }
}
