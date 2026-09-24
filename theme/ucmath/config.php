<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Theme configuration for the Univers-Cité des mathématiques prototype.
 *
 * This is a direct Boost child theme. It deliberately does not inherit from
 * theme_uckk so the two public identities remain visually independent.
 *
 * @package    theme_ucmath
 * @copyright  2026 Univers-Cité King Klown
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$THEME->name = 'ucmath';
$THEME->parents = ['boost'];
$THEME->sheets = [];
$THEME->editor_sheets = [];
$THEME->usefallback = true;
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

$THEME->scss = function($theme) {
    return theme_ucmath_get_main_scss_content($theme);
};
$THEME->prescsscallback = 'theme_ucmath_get_pre_scss';

$drawers = [
    'theme' => 'boost',
    'file' => 'drawers.php',
    'regions' => ['side-pre'],
    'defaultregion' => 'side-pre',
];

$drawersnoregions = [
    'theme' => 'boost',
    'file' => 'drawers.php',
    'regions' => [],
];

$columns1 = [
    'theme' => 'boost',
    'file' => 'columns1.php',
    'regions' => [],
];

$THEME->layouts = [
    'base' => $drawersnoregions,
    'standard' => $drawers,
    'course' => $drawers + ['options' => ['langmenu' => true]],
    'coursecategory' => $drawers,
    'incourse' => $drawers,
    'frontpage' => [
        'theme' => 'ucmath',
        'file' => 'frontpage.php',
        'regions' => [],
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    'local_uckk_public' => [
        'theme' => 'ucmath',
        'file' => 'public.php',
        'regions' => [],
        'options' => ['nonavbar' => true, 'langmenu' => true],
    ],
    'mydashboard' => $drawers + ['options' => ['nonavbar' => true, 'langmenu' => true]],
    'mycourses' => $drawers + ['options' => ['nonavbar' => true]],
    'mypublic' => $drawers,
    'admin' => $drawers,
    'report' => $drawers,
    'login' => [
        'theme' => 'boost',
        'file' => 'login.php',
        'regions' => [],
        'options' => ['langmenu' => true],
    ],
    'popup' => $columns1 + ['options' => ['nofooter' => true, 'nonavbar' => true]],
    'frametop' => $columns1 + ['options' => ['nofooter' => true, 'nocoursefooter' => true]],
    'embedded' => ['theme' => 'boost', 'file' => 'embedded.php', 'regions' => []],
    'maintenance' => ['theme' => 'boost', 'file' => 'maintenance.php', 'regions' => []],
    'secure' => $drawers,
    'print' => $columns1 + ['options' => ['nofooter' => true, 'nonavbar' => false, 'noactivityheader' => true]],
    'redirect' => ['theme' => 'boost', 'file' => 'embedded.php', 'regions' => []],
];

$THEME->enable_dock = false;
$THEME->yuicssmodules = [];
$THEME->requiredblocks = '';
$THEME->addblockposition = BLOCK_ADDBLOCK_POSITION_FLATNAV;
$THEME->iconsystem = \core\output\icon_system::FONTAWESOME;
$THEME->haseditswitch = true;
$THEME->usescourseindex = true;
$THEME->activityheaderconfig = ['notitle' => true];

unset($drawers, $drawersnoregions, $columns1);
