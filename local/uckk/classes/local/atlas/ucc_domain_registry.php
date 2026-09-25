<?php
// This file is part of Moodle - https://moodle.org/

/**
 * Canonical UCC domain registry.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité Catho
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/**
 * Reads and validates the four canonical UCC / Konnaxion domains.
 */
final class ucc_domain_registry {
    public const SCHEMA_VERSION = 'UCC-DOMAINS-1.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/ucc_domains.json';

    /** @var array<string, mixed>|null */
    private static ?array $cache = null;

    /**
     * Return the complete registry document.
     *
     * @return array<string, mixed>
     */
    public static function get(): array {
        if (self::$cache === null) {
            self::$cache = self::load();
        }
        return self::$cache;
    }

    /**
     * Return all four domains in stable order.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array {
        return self::get()['domains'];
    }

    /**
     * Get one domain by canonical id.
     *
     * @param string $domainid Domain id.
     * @return array<string, mixed>
     */
    public static function get_by_id(string $domainid): array {
        $domainid = trim($domainid);
        foreach (self::all() as $domain) {
            if ($domain['domain_id'] === $domainid) {
                return $domain;
            }
        }
        throw new \coding_exception('Unknown UCC domain: ' . $domainid);
    }

    /**
     * Resolve one canonical UCC Voie to its domain.
     *
     * @param string $uccvoieid Canonical UCC Voie id.
     * @return array<string, mixed>
     */
    public static function for_voie(string $uccvoieid): array {
        $uccvoieid = trim($uccvoieid);
        foreach (self::all() as $domain) {
            if (in_array($uccvoieid, $domain['ucc_voie_ids'], true)) {
                return $domain;
            }
        }
        throw new \coding_exception('No UCC domain for Voie: ' . $uccvoieid);
    }

    /**
     * Reset in-request cache.
     */
    public static function reset_cache(): void {
        self::$cache = null;
    }

    /**
     * Load and validate the registry.
     *
     * @return array<string, mixed>
     */
    private static function load(): array {
        global $CFG;
        $path = $CFG->dirroot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::RELATIVE_PATH);
        if (!is_readable($path)) {
            throw new \coding_exception('UCC domain registry is not readable: ' . $path);
        }
        try {
            $doc = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \coding_exception('Invalid UCC domain registry JSON: ' . $e->getMessage());
        }
        if (!is_array($doc) || ($doc['schema_version'] ?? '') !== self::SCHEMA_VERSION || ($doc['universe_id'] ?? '') !== 'ucc') {
            throw new \coding_exception('Invalid UCC domain registry contract.');
        }
        if (!isset($doc['domains']) || !is_array($doc['domains']) || count($doc['domains']) !== 4) {
            throw new \coding_exception('UCC domain registry must contain exactly four domains.');
        }
        $seen = [];
        foreach ($doc['domains'] as $domain) {
            if (!is_array($domain)) {
                throw new \coding_exception('UCC domain entry must be an object.');
            }
            foreach (['domain_id', 'code', 'surface', 'door', 'question', 'purpose', 'ucc_voie_ids', 'legacy_voie_ids'] as $field) {
                if (!array_key_exists($field, $domain)) {
                    throw new \coding_exception('UCC domain missing field: ' . $field);
                }
            }
            $id = (string)$domain['domain_id'];
            if (isset($seen[$id])) {
                throw new \coding_exception('Duplicate UCC domain: ' . $id);
            }
            $seen[$id] = true;
            if (!is_array($domain['ucc_voie_ids']) || !is_array($domain['legacy_voie_ids'])) {
                throw new \coding_exception('UCC domain Voie lists must be arrays: ' . $id);
            }
        }
        return $doc;
    }
}
