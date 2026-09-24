<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Minimal public layout for local_uckk pages under theme_ucmath. @package theme_ucmath */
defined('MOODLE_INTERNAL') || die();

echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <?php echo $OUTPUT->standard_head_html(); ?>
</head>
<body <?php echo $OUTPUT->body_attributes(['theme-ucmath', 'theme-ucmath--public']); ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page-wrapper" class="ucmath-page-wrapper">
    <main id="page-content" class="ucmath-page">
        <?php echo $OUTPUT->course_content_header(); ?>
        <?php echo $OUTPUT->main_content(); ?>
        <?php echo $OUTPUT->course_content_footer(); ?>
    </main>
</div>
<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>
