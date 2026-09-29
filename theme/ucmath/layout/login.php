<?php
// This file is part of Moodle - http://moodle.org/

/** Shared public login shell for an alternate Univers-Cité. @package theme_ucmath */
defined('MOODLE_INTERNAL') || die();

$login = \local_uckk\local\public_login::definition();
$homeurl = new moodle_url('/local/uckk/index.php', ['theme' => $login['theme']]);
$loginagain = optional_param('loginagain', 0, PARAM_BOOL);

if (!$loginagain && isloggedin() && !isguestuser()) {
    redirect($homeurl);
}

$bodyattributes = $OUTPUT->body_attributes(['theme-ucmath', 'local-uckk-public-login-layout']);

echo $OUTPUT->doctype();
?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body <?php echo $bodyattributes; ?>>
<?php echo $OUTPUT->standard_top_of_body_html(); ?>
<div id="page-wrapper" class="local-uckk-public-login-page-wrapper">
    <div id="page" class="local-uckk-public-login-page">
        <div id="page-content" class="local-uckk-public-login-shell">
            <aside class="login-layout-left local-uckk-public-login-visual" aria-labelledby="local-uckk-login-title">
                <div class="login-layout-left-content">
                    <p class="local-uckk-public-login-eyebrow"><?php echo s($login['eyebrow']); ?></p>
                    <h1 id="local-uckk-login-title"><?php echo s($login['title']); ?></h1>
                    <p><?php echo s($login['summary']); ?></p>
                    <div class="login-layout-stats" aria-label="Repères publics">
                        <?php foreach ($login['points'] as $point): ?>
                            <p><strong><?php echo s($point['title']); ?></strong> <?php echo s($point['body']); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <p class="local-uckk-public-login-skip-wrap">
                        <a class="btn btn-light local-uckk-public-login-skip" href="<?php echo s($login['exploreurl']); ?>">
                            <?php echo s($login['explorelabel']); ?>
                        </a>
                    </p>
                </div>
            </aside>
            <main id="region-main" class="login-layout-right" aria-label="<?php echo s(get_string('login')); ?>">
                <div class="login-layout-right-content"><?php echo $OUTPUT->main_content(); ?></div>
            </main>
        </div>
    </div>
</div>
<?php echo $OUTPUT->standard_end_of_body_html(); ?>
</body>
</html>
