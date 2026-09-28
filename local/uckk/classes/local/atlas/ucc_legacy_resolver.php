<?php
// This file is part of Moodle - https://moodle.org/

/**
 * Read-only UCC legacy identifier resolver.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/**
 * Compatibility boundary for historical UCKK and UCC-v1 identifiers.
 *
 * Legacy identifiers may be resolved for reads/migrations, but this class never
 * writes them back into canonical UCC records.
 */
final class ucc_legacy_resolver {
    public const SCHEMA_VERSION = 'UCC-PATHWAY-MIGRATIONS-1.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/ucc_pathway_migrations.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /** @return array<string, mixed> */
    public static function get(): array {
        if (self::$cache === null) {
            self::$cache = self::load();
        }
        return self::$cache;
    }

    public static function resolve_pathway(string $legacyid): string {
        $needle = trim($legacyid);
        foreach (self::get()['pathways'] as $mapping) {
            $candidates = [
                (string)($mapping['legacy_id'] ?? ''),
                (string)($mapping['legacy_code'] ?? ''),
                (string)($mapping['legacy_category_idnumber'] ?? ''),
            ];
            foreach ($candidates as $candidate) {
                if ($candidate !== '' && strcasecmp($candidate, $needle) === 0) {
                    return (string)$mapping['canonical_id'];
                }
            }
        }
        throw new \coding_exception('Unknown legacy UCC pathway identifier: ' . $legacyid);
    }

    public static function resolve_course(string $legacyid): string {
        $needle = strtoupper(trim($legacyid));
        foreach (self::get()['courses'] as $mapping) {
            if (strtoupper((string)$mapping['legacy_id']) === $needle) {
                return (string)$mapping['canonical_id'];
            }
        }
        throw new \coding_exception('Unknown legacy UCC course identifier: ' . $legacyid);
    }

    /** @return array<string, mixed> */
    public static function course(string $legacyid): array {
        return ucc_curriculum_registry::get_course(self::resolve_course($legacyid));
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('UCC migration registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid UCC migration registry JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION || ($doc['universe_id'] ?? '') !== 'ucc') {
            throw new \coding_exception('Invalid UCC migration registry contract.');
        }
        if (($doc['policy']['legacy_mode'] ?? '') !== 'read_only_resolution') {
            throw new \coding_exception('UCC migration registry must be read-only.');
        }
        return $doc;
    }
}
