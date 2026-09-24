<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * SCSS callbacks for theme_ucmath.
 *
 * @package    theme_ucmath
 * @copyright  2026 Univers-Cité King Klown
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Compile Boost's default preset plus the mathematics identity.
 *
 * @param theme_config $theme Theme config.
 * @return string
 */
function theme_ucmath_get_main_scss_content(theme_config $theme): string {
    global $CFG;

    $boost = $CFG->dirroot . '/theme/boost/scss/preset/default.scss';
    $custom = __DIR__ . '/scss/ucmath.scss';

    $scss = is_readable($boost) ? file_get_contents($boost) : '';
    if (is_readable($custom)) {
        $scss .= "\n\n/* theme_ucmath */\n" . file_get_contents($custom);
    }

    return $scss;
}

/**
 * Set a restrained Bootstrap palette before Boost is compiled.
 *
 * @param theme_config $theme Theme config.
 * @return string
 */
function theme_ucmath_get_pre_scss(theme_config $theme): string {
    return implode("\n", [
        '$primary: #1457d9;',
        '$body-bg: #f7f8fa;',
        '$body-color: #15171a;',
        '$border-color: #d9dde4;',
        '$border-radius: .25rem;',
        '$font-family-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;',
    ]);
}
