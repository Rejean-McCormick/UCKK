<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Front page for the Univers-Cité des mathématiques theme. @package theme_ucmath */
defined('MOODLE_INTERNAL') || die();

$slug = 'home';
$definition = \local_uckk\local\public_pages::definition($slug);
$publiccontent = $OUTPUT->render(new \local_uckk\output\public_page($slug, $definition));
$moodlemaincontent = $OUTPUT->main_content();

echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->standard_head_html(); ?>
</head>
<body <?php echo $OUTPUT->body_attributes(['theme-ucmath', 'theme-ucmath--frontpage']); ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page-wrapper" class="ucmath-page-wrapper">
    <main id="page-content" class="ucmath-page" aria-label="<?php echo s(get_string('home')); ?>">
        <?php echo $publiccontent; ?>
        <div hidden aria-hidden="true"><?php echo $moodlemaincontent; ?></div>
    </main>
</div>
<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>
