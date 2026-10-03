<?php
// CLI validation of UCKK → Kristal-Kollection bindings.
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

use local_uckk\local\kristal_repository;

$result = kristal_repository::verify();
cli_writeln('Kristal bindings: ' . $result['count']);
if (!$result['ok']) {
    foreach ($result['missing'] as $ref) {
        cli_writeln('MISSING: ' . $ref);
    }
    foreach ($result['hash_mismatches'] as $row) {
        cli_writeln('HASH MISMATCH: ' . $row['kristal_ref']);
    }
    exit(1);
}
cli_writeln('UCKK Kristal-Kollection bindings: PASS');
exit(0);
