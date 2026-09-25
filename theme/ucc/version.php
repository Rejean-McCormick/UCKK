<?php
// This file is part of Moodle - http://moodle.org/

/** Version metadata for theme_ucc. @package theme_ucc */
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_ucc';
$plugin->version = 2026092401;
$plugin->requires = 2025041400; // Moodle 5.0 or later.
$plugin->supported = [500, 503];
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '0.1.0';
$plugin->dependencies = [
    'local_uckk' => 2026092402,
];
