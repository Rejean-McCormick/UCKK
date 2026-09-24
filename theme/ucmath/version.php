<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Version metadata for theme_ucmath.
 *
 * @package    theme_ucmath
 * @copyright  2026 Univers-Cité King Klown
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_ucmath';
$plugin->version = 2026092401;
$plugin->requires = 2025041400; // Moodle 5.0 or later.
$plugin->supported = [500, 503];
$plugin->maturity = MATURITY_ALPHA;
$plugin->release = '0.1.1';
$plugin->dependencies = [
    'local_uckk' => 2026092401,
];
