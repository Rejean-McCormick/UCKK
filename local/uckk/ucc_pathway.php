<?php
// This file is part of Moodle - https://moodle.org/.
// @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

/**
 * Public detail page for one canonical UCC pathway.
 *
 * The page is read-only. It derives content from the UCC canonical registries and
 * does not create courses, enrolments, grades, recognitions or archive records.
 *
 * @package local_uckk
 */

declare(strict_types=1);

require_once(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

use local_uckk\local\public_pages\ucc\site as ucc_site;
use local_uckk\local\public_pages\ucc\ucc_pathway;
use local_uckk\output\public_page;

global $OUTPUT, $PAGE;

// This controller is intentionally public, even when Moodle's global
// forcelogin setting is enabled. It exposes read-only canonical/public content only.

$slug = strtolower(trim(required_param('slug', PARAM_ALPHANUMEXT)));

try {
    $pathwayid = ucc_pathway::pathway_id_for_slug($slug);
    $specific = ucc_pathway::definition_for_slug($slug);
} catch (coding_exception $exception) {
    http_response_code(404);
    throw new moodle_exception('invalidparameter', 'error', '', 'slug');
}

$context = context_system::instance();
$url = new moodle_url('/local/uckk/ucc_pathway.php', ['slug' => $slug]);
$title = (string)$specific['title'];

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_pagelayout('local_uckk_public');
$PAGE->set_title($title . ' — Univers-Cité chrétienne');
$PAGE->set_heading(ucc_site::heading());
$PAGE->set_cacheable(false);

$PAGE->navbar->ignore_active();
$PAGE->navbar->add(ucc_site::heading(), new moodle_url('/local/uckk/index.php'));
$PAGE->navbar->add('Voies UCC', new moodle_url('/local/uckk/programs.php'));
$PAGE->navbar->add($title);

// Top-level replacement is intentional: page-specific lists (sections, metadata,
// navigation, quicklinks) replace the UCC defaults rather than being recursively merged.
$definition = array_replace(
    ucc_site::base_definition(),
    ucc_pathway::definition(),
    $specific
);


echo $OUTPUT->header();
echo $OUTPUT->render(new public_page('ucc_pathway', $definition));
echo $OUTPUT->footer();
