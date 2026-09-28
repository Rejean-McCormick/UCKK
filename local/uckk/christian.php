<?php
// This file is part of UCKK-Moodle.
//
// Thin public controller for christian.

require_once(__DIR__ . '/../../config.php');

$slug = 'christian';
$context = context_system::instance();

\local_uckk\local\public_pages::setup_page($slug, $context);
$definition = \local_uckk\local\public_pages::definition($slug);

echo $OUTPUT->header();
echo $OUTPUT->render(new \local_uckk\output\public_page($slug, $definition));
echo $OUTPUT->footer();
