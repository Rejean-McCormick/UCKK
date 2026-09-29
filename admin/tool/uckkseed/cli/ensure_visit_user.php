<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Ensure the UCKK read-only visit account exists.
 *
 * No password is embedded in source code. For production/recovery use an
 * environment variable or a mode-0600 password file. A generated password is
 * only printed when the operator explicitly requests --generate-password.
 *
 * @package    tool_uckkseed
 * @copyright  2026 Univers-Cité King Klown
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/user/lib.php');

[$options, $unrecognised] = cli_get_params(
    [
        'help' => false,
        'username' => 'visite',
        'password-env' => 'UCKK_VISIT_PASSWORD',
        'password-file' => false,
        'generate-password' => false,
        'keep-password' => false,
        'firstname' => 'Compte',
        'lastname' => 'Visite',
        'email' => 'visite@example.invalid',
        'role' => 'uckkpublicguest',
        'clean-roles' => true,
        'dry-run' => false,
        'json' => false,
    ],
    [
        'h' => 'help',
        'u' => 'username',
        'r' => 'role',
    ]
);

if (!empty($unrecognised)) {
    $unrecognised = implode(PHP_EOL . '  ', $unrecognised);
    cli_error("Unknown options:\n  " . $unrecognised);
}

if (!empty($options['help'])) {
    $help = <<<HELP
Ensure the UCKK read-only visit account exists.

The script has deliberately NO default password and NO --password command-line
option, so credentials do not land in Git, shell history, or process listings.

Options:
  -h, --help              Show this help.
  -u, --username          Username to create/update. Default: visite
  -r, --role              Role shortname to assign. Default: uckkpublicguest
      --password-env      Environment variable containing the password.
                          Default: UCKK_VISIT_PASSWORD
      --password-file     Read password from a local protected file.
      --generate-password Generate a strong password and display it once.
      --keep-password     Do not rotate an existing account password.
                          Refused when creating a new account.
      --firstname         First name. Default: Compte
      --lastname          Last name. Default: Visite
      --email             Email address. Default: visite@example.invalid
      --clean-roles       Remove all existing role assignments before assigning
                          the visit role. Default: 1
      --dry-run           Report intended changes without writing.
      --json              Print JSON output. Password values are never included.

Recommended PowerShell usage:
  $env:UCKK_VISIT_PASSWORD = '<strong-random-secret>'
  php admin/tool/uckkseed/cli/ensure_visit_user.php --clean-roles=1
  Remove-Item Env:UCKK_VISIT_PASSWORD

Alternative one-time generation:
  php admin/tool/uckkseed/cli/ensure_visit_user.php --generate-password=1

Required before running:
  php admin/tool/uckkseed/cli/seed.php --mode=apply --preset=roles,capabilities --force

HELP;
    cli_writeln($help);
    exit(0);
}

$username = clean_param((string)$options['username'], PARAM_USERNAME);
$firstname = trim(clean_param((string)$options['firstname'], PARAM_TEXT));
$lastname = trim(clean_param((string)$options['lastname'], PARAM_TEXT));
$email = clean_param((string)$options['email'], PARAM_EMAIL);
$roleshortname = clean_param((string)$options['role'], PARAM_ALPHANUMEXT);
$cleanroles = !empty($options['clean-roles']);
$dryrun = !empty($options['dry-run']);
$json = !empty($options['json']);
$keepPassword = !empty($options['keep-password']);
$generatePassword = !empty($options['generate-password']);
$passwordFile = trim((string)($options['password-file'] ?? ''));
$passwordEnv = trim((string)($options['password-env'] ?? 'UCKK_VISIT_PASSWORD'));

if ($username === '') {
    cli_error('Username cannot be empty.');
}
if ($firstname === '') {
    $firstname = 'Compte';
}
if ($lastname === '') {
    $lastname = 'Visite';
}
if ($email === '') {
    $email = $username . '@example.invalid';
}

$password = null;
$passwordSource = 'none';
$generatedPassword = false;

$sourceCount = (int)$generatePassword + (int)($passwordFile !== '');
$envValue = false;
if ($passwordEnv !== '') {
    $envValue = getenv($passwordEnv);
    if ($envValue !== false && $envValue !== '') {
        $sourceCount++;
    }
}
if ($keepPassword) {
    $sourceCount++;
}
if ($sourceCount > 1) {
    cli_error('Choose exactly one password mode: environment, --password-file, --generate-password, or --keep-password.');
}

if ($keepPassword) {
    $passwordSource = 'keep-existing';
} else if ($generatePassword) {
    $password = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    $passwordSource = 'generated';
    $generatedPassword = true;
} else if ($passwordFile !== '') {
    if (!is_file($passwordFile) || !is_readable($passwordFile)) {
        cli_error('Password file is missing or unreadable.');
    }
    if (PHP_OS_FAMILY !== 'Windows') {
        $perms = @fileperms($passwordFile);
        if ($perms !== false && (($perms & 0077) !== 0)) {
            cli_error('Password file must not be group/world accessible (use chmod 600).');
        }
    }
    $password = rtrim((string)file_get_contents($passwordFile), "\r\n");
    $passwordSource = 'file';
} else if ($envValue !== false && $envValue !== '') {
    $password = (string)$envValue;
    $passwordSource = 'environment';
}

$systemcontext = context_system::instance();
$role = $DB->get_record('role', ['shortname' => $roleshortname], '*', IGNORE_MISSING);
if (!$role) {
    cli_error(
        "Role '{$roleshortname}' was not found. Run: php admin/tool/uckkseed/cli/seed.php --mode=apply --preset=roles,capabilities --force"
    );
}

$user = $DB->get_record('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id], '*', IGNORE_MISSING);
if ($user && is_siteadmin((int)$user->id)) {
    cli_error("Refusing to modify '{$username}' because it is a site administrator.");
}
if (!$user && $keepPassword) {
    cli_error('--keep-password cannot be used when creating a new account.');
}
if (!$dryrun && !$keepPassword && ($password === null || $password === '')) {
    cli_error(
        "No password supplied. Set {$passwordEnv}, use --password-file, or explicitly request --generate-password=1."
    );
}
if (!$dryrun && $password !== null) {
    $policyError = '';
    if (!check_password_policy($password, $policyError)) {
        cli_error('Password does not satisfy the Moodle password policy: ' . $policyError);
    }
}

$result = [
    'ok' => true,
    'dryrun' => $dryrun,
    'username' => $username,
    'role' => $roleshortname,
    'passwordsource' => $passwordSource,
    'actions' => [],
];

if (!$user) {
    $result['actions'][] = 'create_user';
    if (!$dryrun) {
        $newuser = (object)[
            'auth' => 'manual',
            'confirmed' => 1,
            'policyagreed' => 1,
            'deleted' => 0,
            'suspended' => 0,
            'mnethostid' => $CFG->mnet_localhost_id,
            'username' => $username,
            'password' => hash_internal_user_password((string)$password),
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'emailstop' => 0,
            'maildisplay' => 0,
            'city' => '',
            'country' => '',
            'timezone' => '99',
            'lang' => current_language(),
            'description' => 'Compte de visite UCKK en lecture seule.',
            'descriptionformat' => FORMAT_PLAIN,
            'idnumber' => 'tool_uckkseed:visit_account',
        ];
        $userid = user_create_user($newuser, false, false);
        $user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    }
} else {
    $result['actions'][] = 'update_user';
    if (!$dryrun) {
        $user->auth = 'manual';
        $user->confirmed = 1;
        $user->deleted = 0;
        $user->suspended = 0;
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->idnumber = 'tool_uckkseed:visit_account';
        $user->description = 'Compte de visite UCKK en lecture seule.';
        $user->descriptionformat = FORMAT_PLAIN;
        $DB->update_record('user', $user);
        if (!$keepPassword) {
            update_internal_user_password($user, (string)$password);
            $result['actions'][] = 'rotate_password';
        }
        $user = $DB->get_record('user', ['id' => $user->id], '*', MUST_EXIST);
    }
}

if (!$dryrun && !$user) {
    cli_error('User creation/update failed.');
}
$userid = $dryrun && !$user ? 0 : (int)$user->id;

if ($cleanroles) {
    $result['actions'][] = 'clean_role_assignments';
    if (!$dryrun && $userid > 0) {
        $assignments = $DB->get_records('role_assignments', ['userid' => $userid]);
        foreach ($assignments as $assignment) {
            role_unassign(
                (int)$assignment->roleid,
                $userid,
                (int)$assignment->contextid,
                (string)$assignment->component,
                (int)$assignment->itemid
            );
        }
    }
}

$result['actions'][] = 'assign_visit_role';
if (!$dryrun && $userid > 0) {
    role_assign((int)$role->id, $userid, $systemcontext->id, 'tool_uckkseed', 0);
    $DB->delete_records('user_preferences', ['userid' => $userid, 'name' => 'auth_forcepasswordchange']);
}

$result['userid'] = $userid;
$result['roleid'] = (int)$role->id;
$result['systemcontextid'] = $systemcontext->id;

if ($json) {
    cli_writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
} else {
    cli_writeln('UCKK visit account ensured.');
    cli_writeln('Username: ' . $username);
    cli_writeln('Password source: ' . $passwordSource);
    cli_writeln('Role: ' . $roleshortname);
    cli_writeln('Clean roles: ' . ($cleanroles ? 'yes' : 'no'));
    cli_writeln('Dry run: ' . ($dryrun ? 'yes' : 'no'));
    if ($generatedPassword && !$dryrun) {
        cli_writeln('Generated password (shown once; store it securely now): ' . $password);
    }
}

$password = null;
exit(0);
