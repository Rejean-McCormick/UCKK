<?php
// This file is part of Moodle - https://moodle.org/

/**
 * Canonical UCC domain registry.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité chrétienne
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace local_uckk\local\atlas;

defined('MOODLE_INTERNAL') || die();

/** Reads and validates the four canonical UCC / Konnaxion domains. */
final class ucc_domain_registry {
    public const SCHEMA_VERSION = 'UCC-DOMAINS-2.0';
    public const RELATIVE_PATH = 'local/uckk/atlas/ucc_domains.json';

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
    public static function all(): array {
        return self::get()['domains'];
    }

    /** @return array<string, mixed> */
    public static function get_by_id(string $domainid): array {
        $domainid = trim($domainid);
        foreach (self::all() as $domain) {
            if ($domain['domain_id'] === $domainid) {
                return $domain;
            }
        }
        throw new \coding_exception('Unknown UCC domain: ' . $domainid);
    }

    /** @return array<string, mixed> */
    public static function for_pathway(string $pathwayid): array {
        $pathwayid = trim($pathwayid);
        foreach (self::all() as $domain) {
            if (in_array($pathwayid, $domain['pathway_ids'], true)) {
                return $domain;
            }
        }
        throw new \coding_exception('No UCC domain for pathway: ' . $pathwayid);
    }

    public static function reset_cache(): void {
        self::$cache = null;
    }

    /** @return array<string, mixed> */
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
            foreach (['domain_id', 'code', 'surface', 'door', 'question', 'purpose', 'pathway_ids'] as $field) {
                if (!array_key_exists($field, $domain)) {
                    throw new \coding_exception('UCC domain missing field: ' . $field);
                }
            }
            $id = (string)$domain['domain_id'];
            if (isset($seen[$id])) {
                throw new \coding_exception('Duplicate UCC domain: ' . $id);
            }
            $seen[$id] = true;
            if (!is_array($domain['pathway_ids'])) {
                throw new \coding_exception('UCC domain pathway list must be an array: ' . $id);
            }
            foreach ($domain['pathway_ids'] as $pathwayid) {
                if (!is_string($pathwayid) || !preg_match('/^ucc\.path\.[a-z0-9-]+$/', $pathwayid)) {
                    throw new \coding_exception('Invalid canonical UCC pathway id in domain ' . $id . ': ' . (string)$pathwayid);
                }
            }
        }
        return $doc;
    }
}
