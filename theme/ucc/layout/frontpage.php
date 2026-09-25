<?php
// This file is part of Moodle - http://moodle.org/

/** Front page for Univers-Cité Catho. @package theme_ucc */
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
<body <?php echo $OUTPUT->body_attributes(['theme-ucc', 'theme-ucc--frontpage']); ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page-wrapper" class="ucc-page-wrapper">
    <main id="page-content" class="ucc-page" aria-label="<?php echo s(get_string('home')); ?>">
        <?php echo $publiccontent; ?>
        <div hidden aria-hidden="true"><?php echo $moodlemaincontent; ?></div>
    </main>
</div>
<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>
