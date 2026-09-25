<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Resolve the public institutional site from Moodle's active theme.
 *
 * Moodle's resolved theme is the only state used for public-site switching.
 * This adapter deliberately does not own course, media or user data: those
 * remain live Moodle/plugin state in the database and Moodle file storage.
 *
 * @package    local_uckk
 * @copyright  2026 Univers-Cité King Klown
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uckk\local;

defined('MOODLE_INTERNAL') || die();

final class public_site_context {
    public const SITE_UCKK = 'uckk';
    public const SITE_UCC = 'ucc';
    public const SITE_MATH = 'math';

    public const THEME_UCKK = 'uckk';
    public const THEME_UCC = 'ucc';
    public const THEME_MATH = 'ucmath';

    /**
     * Canonical public-site registry.
     *
     * This is presentation routing metadata only. It must not become a second
     * store for Moodle courses, media, permissions or other runtime records.
     *
     * @return array<int, array{site: string, theme: string, label: string}>
     */
    public static function sites(): array {
        return [
            ['site' => self::SITE_UCKK, 'theme' => self::THEME_UCKK, 'label' => 'UCKK'],
            ['site' => self::SITE_UCC, 'theme' => self::THEME_UCC, 'label' => 'UCC'],
            ['site' => self::SITE_MATH, 'theme' => self::THEME_MATH, 'label' => 'Mathématiques'],
        ];
    }

    /** Return the current public site key from Moodle's resolved page theme. */
    public static function current(): string {
        global $PAGE;

        $themename = '';
        if (isset($PAGE)) {
            try {
                $themename = (string)$PAGE->theme->name;
            } catch (\Throwable $exception) {
                // Theme may not be initialised in CLI/install contexts.
                $themename = '';
            }
        }

        return self::from_theme_name($themename);
    }

    /** Map a Moodle theme shortname to a public site key. */
    public static function from_theme_name(string $themename): string {
        $themename = strtolower(trim($themename));

        foreach (self::sites() as $site) {
            if ($themename === $site['theme']) {
                return $site['site'];
            }
        }

        // UCKK is the canonical/default public identity for any non-mapped theme.
        return self::SITE_UCKK;
    }

    /** Whether the canonical UCKK public site is active. */
    public static function is_uckk(): bool {
        return self::current() === self::SITE_UCKK;
    }

    /** Whether the Univers-Cité Catho public site is active. */
    public static function is_ucc(): bool {
        return self::current() === self::SITE_UCC;
    }

    /** Whether the mathematics public site is active. */
    public static function is_math(): bool {
        return self::current() === self::SITE_MATH;
    }

    /** Whether the active site uses an alternate public-page corpus. */
    public static function is_alternate(): bool {
        return !self::is_uckk();
    }
}
