<?php
// This file is part of UCKK-Moodle.

namespace local_uckk\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Resolve UCKK Kristals from the canonical Kristal-Kollection repository.
 *
 * UCKK owns bindings/materializations only. Canonical Kristal bytes live under
 * Kristal-Kollection/universities/uckk and are addressed by stable kristal_ref.
 */
final class kristal_repository {
    private const BINDING_RELATIVE = 'atlas/kristal-bindings/manifest.json';
    private const CONFIG_RELATIVE = 'atlas/kristal-bindings/config.json';

    /** @return array<string,mixed> */
    public static function config(): array {
        $path = self::plugin_root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::CONFIG_RELATIVE);
        return self::read_json($path);
    }

    /** @return array<string,mixed> */
    public static function manifest(): array {
        $path = self::plugin_root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, self::BINDING_RELATIVE);
        return self::read_json($path);
    }

    public static function root(): string {
        $config = self::config();
        $envname = trim((string)($config['root_env'] ?? 'KRISTAL_KOLLECTION_ROOT'));
        $root = $envname !== '' ? getenv($envname) : false;
        if (!is_string($root) || trim($root) === '') {
            $root = (string)($config['default_windows_root'] ?? '');
        }
        $root = rtrim(trim($root), "\\/");
        if ($root === '') {
            throw new \RuntimeException('Kristal-Kollection root is not configured.');
        }
        return $root;
    }

    /** @return array<string,mixed>|null */
    public static function binding(string $kristalref): ?array {
        $manifest = self::manifest();
        foreach (($manifest['entries'] ?? []) as $entry) {
            if (is_array($entry) && (string)($entry['kristal_ref'] ?? '') === $kristalref) {
                return $entry;
            }
        }
        return null;
    }

    public static function resolve(string $kristalref): string {
        $entry = self::binding($kristalref);
        if ($entry === null) {
            throw new \RuntimeException('Unknown UCKK Kristal reference: ' . $kristalref);
        }
        $relative = str_replace('\\', '/', (string)($entry['repository_relative_path'] ?? ''));
        if ($relative === '' || str_starts_with($relative, '/') || preg_match('#(^|/)\.\.(/|$)#', $relative)) {
            throw new \RuntimeException('Unsafe Kristal repository path for ' . $kristalref);
        }
        return self::root() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    }

    /** @return array<string,mixed> */
    public static function load(string $kristalref): array {
        return self::read_json(self::resolve($kristalref));
    }

    /** @return array{ok:bool,count:int,missing:string[],hash_mismatches:array<int,array<string,string>>} */
    public static function verify(): array {
        $manifest = self::manifest();
        $entries = is_array($manifest['entries'] ?? null) ? $manifest['entries'] : [];
        $missing = [];
        $mismatches = [];
        foreach ($entries as $entry) {
            if (!is_array($entry)) {
                continue;
            }
            $ref = (string)($entry['kristal_ref'] ?? '');
            $path = self::resolve($ref);
            if (!is_file($path)) {
                $missing[] = $ref;
                continue;
            }
            $expected = strtolower((string)($entry['sha256'] ?? ''));
            $actual = hash_file('sha256', $path);
            if ($expected !== '' && $actual !== $expected) {
                $mismatches[] = ['kristal_ref' => $ref, 'expected' => $expected, 'actual' => $actual ?: ''];
            }
        }
        return ['ok' => $missing === [] && $mismatches === [], 'count' => count($entries), 'missing' => $missing, 'hash_mismatches' => $mismatches];
    }

    private static function plugin_root(): string {
        return dirname(__DIR__, 2);
    }

    /** @return array<string,mixed> */
    private static function read_json(string $path): array {
        if (!is_readable($path)) {
            throw new \RuntimeException('Unreadable JSON file: ' . $path);
        }
        $decoded = json_decode((string)file_get_contents($path), true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON file: ' . $path);
        }
        return $decoded;
    }
}
