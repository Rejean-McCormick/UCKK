<?php
// This file is part of Moodle - http://moodle.org/

/** SCSS callbacks for theme_ucc. @package theme_ucc */
defined('MOODLE_INTERNAL') || die();

/**
 * Compile Boost plus the UCC visual identity.
 *
 * @param theme_config $theme Theme config.
 * @return string
 */
function theme_ucc_get_main_scss_content(theme_config $theme): string {
    global $CFG;

    $files = [
        $CFG->dirroot . '/theme/boost/scss/preset/default.scss',
        __DIR__ . '/scss/ucc.scss',
    ];

    $scss = '';
    foreach ($files as $file) {
        if (is_readable($file)) {
            $scss .= "\n\n/* " . basename($file) . " */\n" . file_get_contents($file);
        }
    }

    return $scss;
}

/**
 * Bootstrap palette for UCC before Boost is compiled.
 *
 * @param theme_config $theme Theme config.
 * @return string
 */
function theme_ucc_get_pre_scss(theme_config $theme): string {
    return implode("\n", [
        '$primary: #7b2434;',
        '$body-bg: #f7f2e8;',
        '$body-color: #202027;',
        '$border-color: #d8cfbf;',
        '$border-radius: .35rem;',
        '$font-family-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;',
    ]);
}
