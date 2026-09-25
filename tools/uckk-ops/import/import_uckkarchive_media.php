<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/**
 * Operational importer for the recovered UCKK Archive media inventory.
 *
 * This CLI bootstrapper is intentionally outside mod/uckkarchive/cli.
 * It imports a recovery inventory into the existing mod_uckkarchive
 * data model and stores originals through Moodle File API.
 *
 * @package    mod_uckkarchive
 * @copyright  2026 Univers-Cité King Klown
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

define('CLI_SCRIPT', true);

$options = getopt('', [
    'inventory:',
    'originals:',
    'mode:',
    'cmid::',
    'archiveid::',
    'userid::',
    'allowmissingfiles::',
    'updatemetadata::',
    'forcenewversion::',
    'offset::',
    'limit::',
    'noautoarchive::',
]);

$inventorypath = trim((string)($options['inventory'] ?? ''));
$originalsdir = trim((string)($options['originals'] ?? ''));
$mode = strtolower(trim((string)($options['mode'] ?? 'dryrun')));
$cmid = max(0, (int)($options['cmid'] ?? 0));
$archiveid = max(0, (int)($options['archiveid'] ?? 0));
$userid = max(0, (int)($options['userid'] ?? 0));
$allowmissingfiles = option_bool($options, 'allowmissingfiles');
$updatemetadata = option_bool($options, 'updatemetadata');
$forcenewversion = option_bool($options, 'forcenewversion');
$offset = max(0, (int)($options['offset'] ?? 0));
$limit = max(0, (int)($options['limit'] ?? 0));
$noautoarchive = option_bool($options, 'noautoarchive');

if (!in_array($mode, ['dryrun', 'apply'], true)) {
    fail('Invalid --mode. Expected DryRun or Apply.', 2);
}
if ($inventorypath === '' || !is_readable($inventorypath)) {
    fail('Inventory is missing or unreadable: ' . $inventorypath, 2);
}
if ($originalsdir === '' || !is_dir($originalsdir)) {
    fail('Originals directory is missing: ' . $originalsdir, 2);
}

$configcandidates = [
    dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'config.php',
    dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'config.php',
];
$configpath = '';
foreach ($configcandidates as $candidate) {
    if (is_file($candidate)) {
        $configpath = $candidate;
        break;
    }
}
if ($configpath === '') {
    fail('Unable to locate Moodle config.php from importer path.', 2);
}

require_once($configpath);
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

$requiredtables = [
    'uckkarchive',
    'uckkarchive_media',
    'uckkarchive_media_version',
    'uckkarchive_media_source',
    'uckkarchive_media_tag',
    'uckkarchive_content_tag',
    'uckkarchive_content_marker',
];
foreach ($requiredtables as $tablename) {
    require_table($tablename);
}

$inventoryraw = file_get_contents($inventorypath);
if ($inventoryraw === false) {
    fail('Unable to read inventory.', 2);
}
$inventory = json_decode(remove_utf8_bom($inventoryraw), true);
if (!is_array($inventory)) {
    fail('Inventory JSON is invalid: ' . json_last_error_msg(), 2);
}
if (($inventory['inventory_metadata']['target_system'] ?? '') !== 'mod_uckkarchive') {
    fail('Inventory target_system must be mod_uckkarchive.', 2);
}
$items = $inventory['files'] ?? null;
if (!is_array($items)) {
    fail('Inventory does not contain a files array.', 2);
}

if ($offset > 0) {
    $items = array_slice($items, $offset);
}
if ($limit > 0) {
    $items = array_slice($items, 0, $limit);
}

$actinguser = resolve_acting_user($userid);
$GLOBALS['USER'] = $actinguser;
$userid = (int)$actinguser->id;

$scope = resolve_archive_scope($cmid, $archiveid);
if (!$scope && $mode === 'apply' && !$noautoarchive) {
    $scope = create_site_media_archive($userid);
}

$stats = [
    'mode' => $mode,
    'inventory' => $inventorypath,
    'originals' => $originalsdir,
    'selected' => count($items),
    'archive_action' => $scope ? 'existing' : ($noautoarchive ? 'required' : 'would_create_site_archive'),
    'archiveid' => $scope['archiveid'] ?? 0,
    'cmid' => $scope['cmid'] ?? 0,
    'contextid' => $scope['contextid'] ?? 0,
    'courseid' => $scope['courseid'] ?? 0,
    'created_media' => 0,
    'updated_media' => 0,
    'unchanged_media' => 0,
    'created_versions' => 0,
    'reused_versions' => 0,
    'created_sources' => 0,
    'updated_sources' => 0,
    'created_tags' => 0,
    'created_advisories' => 0,
    'updated_advisories' => 0,
    'missing_files' => 0,
    'errors' => [],
];

if (!$scope && $mode === 'apply') {
    fail('No usable mod_uckkarchive activity instance could be resolved or created.', 2);
}

foreach ($items as $index => $item) {
    try {
        if (!is_array($item)) {
            throw new RuntimeException('Inventory item is not an object.');
        }
        $result = process_item(
            $item,
            $scope,
            $originalsdir,
            $mode,
            $userid,
            $allowmissingfiles,
            $updatemetadata,
            $forcenewversion
        );
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $stats) && is_int($stats[$key]) && is_int($value)) {
                $stats[$key] += $value;
            }
        }
        $label = (string)($item['uckkarchive_media']['title'] ?? ('item #' . ($index + 1)));
        echo sprintf("[%s] %s\n", strtoupper($mode), $label);
    } catch (Throwable $e) {
        $stats['errors'][] = [
            'index' => $index,
            'title' => (string)($item['uckkarchive_media']['title'] ?? ''),
            'message' => $e->getMessage(),
        ];
        fwrite(STDERR, sprintf("[ERROR] #%d %s\n", $index + 1, $e->getMessage()));
        if ($mode === 'apply') {
            break;
        }
    }
}

if ($scope) {
    $stats['db_counts'] = [
        'media_total' => (int)$DB->count_records('uckkarchive_media'),
        'media_archive' => (int)$DB->count_records('uckkarchive_media', ['archiveid' => $scope['archiveid']]),
        'media_public_active_general' => (int)$DB->count_records_select(
            'uckkarchive_media',
            'status = :status AND visibility = :visibility AND audiencesuitability = :audience',
            ['status' => 'active', 'visibility' => 'public', 'audience' => 'general']
        ),
        'versions_total' => (int)$DB->count_records('uckkarchive_media_version'),
        'sources_total' => (int)$DB->count_records('uckkarchive_media_source'),
        'tags_total' => (int)$DB->count_records('uckkarchive_media_tag'),
        'markers_total' => (int)$DB->count_records('uckkarchive_content_marker'),
    ];
}

echo "---UCKK_IMPORT_RESULT_JSON---\n";
echo json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

if (!empty($stats['errors'])) {
    exit(2);
}
exit(0);

/**
 * Process one inventory item.
 *
 * @return array<string,int>
 */
function process_item(
    array $item,
    ?array $scope,
    string $originalsdir,
    string $mode,
    int $userid,
    bool $allowmissingfiles,
    bool $updatemetadata,
    bool $forcenewversion
): array {
    global $DB;

    $ops = $item['file_operations'] ?? [];
    $media = $item['uckkarchive_media'] ?? [];
    $source = $item['uckkarchive_media_source'] ?? [];
    $tags = $item['uckkarchive_media_tags'] ?? [];
    $advisories = $item['uckkarchive_content_advisories'] ?? [];

    if (!is_array($ops) || !is_array($media) || !is_array($source)) {
        throw new RuntimeException('Invalid inventory item structure.');
    }

    $proposed = trim((string)($ops['proposed_filename'] ?? ''));
    $original = trim((string)($ops['original_filename'] ?? ''));
    $title = trim((string)($media['title'] ?? ''));
    $mimetype = trim((string)($ops['mimetype'] ?? ''));

    if ($proposed === '' || $title === '') {
        throw new RuntimeException('Missing proposed_filename or media title.');
    }

    $sourcepath = safe_join($originalsdir, $proposed);
    if (!is_file($sourcepath) && $original !== '') {
        $fallback = safe_join($originalsdir, $original);
        if (is_file($fallback)) {
            $sourcepath = $fallback;
        }
    }

    $fileexists = is_file($sourcepath);
    if (!$fileexists && !$allowmissingfiles) {
        throw new RuntimeException('Original file not found: ' . $proposed);
    }

    $sha256 = $fileexists ? hash_file('sha256', $sourcepath) : '';
    $filesize = $fileexists ? filesize($sourcepath) : 0;
    $mediauuid = deterministic_uuid('uckkarchive-media:' . strtolower($proposed));

    $existing = $DB->get_record('uckkarchive_media', ['uuid' => $mediauuid], '*', IGNORE_MISSING);

    $result = [
        'created_media' => 0,
        'updated_media' => 0,
        'unchanged_media' => 0,
        'created_versions' => 0,
        'reused_versions' => 0,
        'created_sources' => 0,
        'updated_sources' => 0,
        'created_tags' => 0,
        'created_advisories' => 0,
        'updated_advisories' => 0,
        'missing_files' => $fileexists ? 0 : 1,
    ];

    if ($mode === 'dryrun') {
        if ($existing) {
            $result[$updatemetadata ? 'updated_media' : 'unchanged_media'] = 1;
        } else {
            $result['created_media'] = 1;
        }

        if ($fileexists && $scope) {
            $sameversion = $DB->record_exists('uckkarchive_media_version', [
                'mediaid' => (int)($existing->id ?? 0),
                'contenthash' => $sha256,
            ]);
            if ($existing && $sameversion && !$forcenewversion) {
                $result['reused_versions'] = 1;
            } else {
                $result['created_versions'] = 1;
            }
        }
        $result['created_tags'] = is_array($tags) ? count($tags) : 0;
        $result['created_advisories'] = is_array($advisories) ? count($advisories) : 0;
        return $result;
    }

    if (!$scope) {
        throw new RuntimeException('Missing archive scope during Apply.');
    }

    $metadata = [
        'inventory' => 'uckk_inventory.json',
        'inventory_version' => '2.0',
        'original_filename' => $original,
        'proposed_filename' => $proposed,
        'sha256' => $sha256,
        'language' => (string)($media['language'] ?? ''),
        'retentionclass' => (string)($media['retentionclass'] ?? ''),
        'redactionstate' => (string)($media['redactionstate'] ?? ''),
        'sourceownership' => (string)($source['sourceownership'] ?? ''),
        'attribution' => (string)($source['attribution'] ?? ''),
    ];

    $mediarecord = (object)[
        'uuid' => $mediauuid,
        'archiveid' => $scope['archiveid'],
        'courseid' => $scope['courseid'],
        'cmid' => $scope['cmid'],
        'contextid' => $scope['contextid'],
        'userid' => $userid,
        'ownerid' => $userid,
        'title' => $title,
        'summary' => (string)($media['description'] ?? ''),
        'description' => (string)($media['description'] ?? ''),
        'mediatype' => (string)($media['mediatype'] ?? 'document'),
        'mimetype' => $mimetype,
        'source' => 'uckk',
        'sourcetype' => (string)($source['sourcetype'] ?? 'imported'),
        'rightsstatus' => 'uckk_controlled',
        'status' => (string)($media['status'] ?? 'active'),
        'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
        'audiencesuitability' => (string)($media['audiencesuitability'] ?? 'general'),
        'provenance' => 'imported',
        'provenancehash' => $sha256,
        'culturalprotocol' => 0,
        'restricted' => 0,
        'searchable' => 1,
        'versionno' => 1,
        'createdby' => $userid,
        'modifiedby' => $userid,
        'timecreated' => time(),
        'timemodified' => time(),
        'metadata' => json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        // Compatibility aliases for older/current schema variants.
        'language' => (string)($media['language'] ?? ''),
        'retentionclass' => (string)($media['retentionclass'] ?? ''),
        'redactionstate' => (string)($media['redactionstate'] ?? ''),
    ];

    $transaction = $DB->start_delegated_transaction();

    if (!$existing) {
        $mediarecord = filter_record('uckkarchive_media', $mediarecord);
        $mediarecord->id = $DB->insert_record('uckkarchive_media', $mediarecord);
        $existing = $DB->get_record('uckkarchive_media', ['id' => $mediarecord->id], '*', MUST_EXIST);
        $result['created_media'] = 1;
    } else if ($updatemetadata) {
        $mediarecord->id = (int)$existing->id;
        $mediarecord->timecreated = (int)($existing->timecreated ?? time());
        $mediarecord->createdby = (int)($existing->createdby ?? $userid);
        if (!empty($existing->currentversionid)) {
            $mediarecord->currentversionid = (int)$existing->currentversionid;
        }
        if (!empty($existing->versionno)) {
            $mediarecord->versionno = (int)$existing->versionno;
        }
        $DB->update_record('uckkarchive_media', filter_record('uckkarchive_media', $mediarecord));
        $existing = $DB->get_record('uckkarchive_media', ['id' => $mediarecord->id], '*', MUST_EXIST);
        $result['updated_media'] = 1;
    } else {
        $result['unchanged_media'] = 1;
    }

    $mediaid = (int)$existing->id;

    // Source record: one deterministic source per recovered media object.
    $sourceuuid = deterministic_uuid('uckkarchive-media-source:' . $mediauuid);
    $sourceexisting = $DB->get_record('uckkarchive_media_source', ['uuid' => $sourceuuid], '*', IGNORE_MISSING);
    $sourcerecord = (object)[
        'uuid' => $sourceuuid,
        'mediaid' => $mediaid,
        'archiveid' => $scope['archiveid'],
        'courseid' => $scope['courseid'],
        'cmid' => $scope['cmid'],
        'contextid' => $scope['contextid'],
        'userid' => $userid,
        'sourcetype' => (string)($source['sourcetype'] ?? 'imported'),
        'sourcecomponent' => 'uckk_inventory',
        'sourceauthor' => (string)($source['attribution'] ?? ''),
        'attribution' => (string)($source['attribution'] ?? ''),
        'ownership' => (string)($source['sourceownership'] ?? ''),
        'sourceownership' => (string)($source['sourceownership'] ?? ''),
        'rightsstatus' => 'uckk_controlled',
        'status' => 'active',
        'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
        'provenancehash' => $sha256,
        'createdby' => $userid,
        'modifiedby' => $userid,
        'timecreated' => time(),
        'timemodified' => time(),
        'metadata' => json_encode([
            'inventory' => 'uckk_inventory.json',
            'sourceownership' => (string)($source['sourceownership'] ?? ''),
            'attribution' => (string)($source['attribution'] ?? ''),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ];
    if (!$sourceexisting) {
        $sourcerecord = filter_record('uckkarchive_media_source', $sourcerecord);
        $sourcerecord->id = $DB->insert_record('uckkarchive_media_source', $sourcerecord);
        $sourceexisting = $sourcerecord;
        $result['created_sources'] = 1;
    } else if ($updatemetadata) {
        $sourcerecord->id = (int)$sourceexisting->id;
        $sourcerecord->timecreated = (int)($sourceexisting->timecreated ?? time());
        $sourcerecord->createdby = (int)($sourceexisting->createdby ?? $userid);
        $DB->update_record('uckkarchive_media_source', filter_record('uckkarchive_media_source', $sourcerecord));
        $result['updated_sources'] = 1;
    }

    // If this schema variant has a media.sourceid column, link it.
    if (has_column('uckkarchive_media', 'sourceid') && !empty($sourceexisting->id)) {
        $DB->set_field('uckkarchive_media', 'sourceid', (int)$sourceexisting->id, ['id' => $mediaid]);
    }

    // Topic tags.
    if (is_array($tags)) {
        foreach ($tags as $tag) {
            $tagkey = normalise_key((string)$tag);
            if ($tagkey === '') {
                continue;
            }
            $tagexisting = $DB->get_record('uckkarchive_media_tag', [
                'mediaid' => $mediaid,
                'tagkey' => $tagkey,
            ], '*', IGNORE_MISSING);
            if ($tagexisting) {
                continue;
            }
            $tagrecord = (object)[
                'uuid' => deterministic_uuid('uckkarchive-media-tag:' . $mediauuid . ':' . $tagkey),
                'archiveid' => $scope['archiveid'],
                'courseid' => $scope['courseid'],
                'cmid' => $scope['cmid'],
                'contextid' => $scope['contextid'],
                'mediaid' => $mediaid,
                'userid' => $userid,
                'tag' => $tagkey,
                'tagkey' => $tagkey,
                'label' => $tagkey,
                'rawname' => (string)$tag,
                'tagtype' => 'topic',
                'source' => 'imported',
                'status' => 'active',
                'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
                'createdby' => $userid,
                'modifiedby' => $userid,
                'timecreated' => time(),
                'timemodified' => time(),
                'metadata' => '{}',
            ];
            $DB->insert_record('uckkarchive_media_tag', filter_record('uckkarchive_media_tag', $tagrecord));
            $result['created_tags']++;
        }
    }

    // Content advisories become first-class content tags + markers.
    if (is_array($advisories)) {
        foreach ($advisories as $advisory) {
            if (!is_array($advisory)) {
                continue;
            }
            $tagkey = normalise_key((string)($advisory['tagkey'] ?? ''));
            if ($tagkey === '') {
                continue;
            }
            $severity = normalise_key((string)($advisory['severity'] ?? 'notice')) ?: 'notice';
            $locatortype = normalise_key((string)($advisory['locator_type'] ?? 'manual_reference')) ?: 'manual_reference';
            $description = trim((string)($advisory['advisorytext'] ?? ''));
            $iscultural = in_array($tagkey, [
                'culturally_sensitive', 'sacred_content', 'ceremonial_content',
                'restricted_knowledge', 'community_permission_required',
                'elder_review_required', 'seasonal_or_contextual_access',
                'not_for_public_export',
            ], true) ? 1 : 0;

            $contenttag = $DB->get_record('uckkarchive_content_tag', ['tagkey' => $tagkey], '*', IGNORE_MISSING);
            if (!$contenttag) {
                $contenttagrecord = (object)[
                    'uuid' => deterministic_uuid('uckkarchive-content-tag:' . $tagkey),
                    'tagkey' => $tagkey,
                    'key' => $tagkey,
                    'name' => str_replace('_', ' ', $tagkey),
                    'label' => str_replace('_', ' ', $tagkey),
                    'category' => $iscultural ? 'cultural_protocol' : 'general',
                    'description' => '',
                    'defaultseverity' => $severity,
                    'defaultaudiencesuitability' => (string)($media['audiencesuitability'] ?? 'general'),
                    'defaultreviewstate' => 'reviewed',
                    'iscultural' => $iscultural,
                    'restrictsbydefault' => $severity === 'restricted' ? 1 : 0,
                    'requiresreview' => 1,
                    'status' => 'active',
                    'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
                    'sortorder' => 0,
                    'createdby' => $userid,
                    'modifiedby' => $userid,
                    'timecreated' => time(),
                    'timemodified' => time(),
                    'metadata' => '{}',
                ];
                $contenttagrecord = filter_record('uckkarchive_content_tag', $contenttagrecord);
                $contenttagrecord->id = $DB->insert_record('uckkarchive_content_tag', $contenttagrecord);
                $contenttag = $contenttagrecord;
            }

            $markeruuid = deterministic_uuid('uckkarchive-content-marker:' . $mediauuid . ':' . $tagkey . ':' . $locatortype . ':' . $description);
            $markerexisting = $DB->get_record('uckkarchive_content_marker', ['uuid' => $markeruuid], '*', IGNORE_MISSING);
            $markerrecord = (object)[
                'uuid' => $markeruuid,
                'archiveid' => $scope['archiveid'],
                'courseid' => $scope['courseid'],
                'cmid' => $scope['cmid'],
                'contextid' => $scope['contextid'],
                'mediaid' => $mediaid,
                'targettype' => 'media',
                'targetid' => $mediaid,
                'userid' => $userid,
                'tagid' => (int)($contenttag->id ?? 0),
                'tagkey' => $tagkey,
                'tag' => $tagkey,
                'category' => $iscultural ? 'cultural_protocol' : 'general',
                'severity' => $severity,
                'audiencesuitability' => (string)($media['audiencesuitability'] ?? 'general'),
                'reviewstate' => 'reviewed',
                'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
                'locatortype' => $locatortype,
                'locator' => '',
                'description' => $description,
                'note' => '',
                'culturalprotocol' => $iscultural,
                'restricted' => $severity === 'restricted' ? 1 : 0,
                'requirescontext' => $tagkey === 'requires_context' ? 1 : 0,
                'redacted' => 0,
                'createdby' => $userid,
                'modifiedby' => $userid,
                'timecreated' => time(),
                'timemodified' => time(),
                'metadata' => json_encode(['inventory' => 'uckk_inventory.json'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
            if (!$markerexisting) {
                $DB->insert_record('uckkarchive_content_marker', filter_record('uckkarchive_content_marker', $markerrecord));
                $result['created_advisories']++;
            } else if ($updatemetadata) {
                $markerrecord->id = (int)$markerexisting->id;
                $markerrecord->timecreated = (int)($markerexisting->timecreated ?? time());
                $markerrecord->createdby = (int)($markerexisting->createdby ?? $userid);
                $DB->update_record('uckkarchive_content_marker', filter_record('uckkarchive_content_marker', $markerrecord));
                $result['updated_advisories']++;
            }
        }
    }

    // Original file + media version. The version row is the File API itemid.
    if ($fileexists) {
        $existingversion = $DB->get_record('uckkarchive_media_version', [
            'mediaid' => $mediaid,
            'contenthash' => $sha256,
        ], '*', IGNORE_MISSING);

        if ($existingversion && !$forcenewversion) {
            $result['reused_versions'] = 1;
            if (has_column('uckkarchive_media', 'currentversionid')) {
                $DB->set_field('uckkarchive_media', 'currentversionid', (int)$existingversion->id, ['id' => $mediaid]);
            }
        } else {
            $nextversion = (int)$DB->get_field_sql(
                'SELECT COALESCE(MAX(versionnumber), 0) + 1 FROM {uckkarchive_media_version} WHERE mediaid = :mediaid',
                ['mediaid' => $mediaid]
            );
            if ($nextversion <= 0) {
                $nextversion = 1;
            }

            if (has_column('uckkarchive_media_version', 'iscurrent')) {
                $DB->set_field('uckkarchive_media_version', 'iscurrent', 0, ['mediaid' => $mediaid]);
            }

            $versionrecord = (object)[
                'uuid' => deterministic_uuid('uckkarchive-media-version:' . $mediauuid . ':' . $sha256 . ':' . $nextversion),
                'mediaid' => $mediaid,
                'archiveid' => $scope['archiveid'],
                'courseid' => $scope['courseid'],
                'cmid' => $scope['cmid'],
                'contextid' => $scope['contextid'],
                'userid' => $userid,
                'versionnumber' => $nextversion,
                'versionno' => $nextversion,
                'label' => 'Import recovery v' . $nextversion,
                'filearea' => 'media_original',
                'filename' => $proposed,
                'filepath' => '/',
                'filesize' => (int)$filesize,
                'mimetype' => $mimetype,
                'contenthash' => $sha256,
                'iscurrent' => 1,
                'status' => (string)($media['status'] ?? 'active'),
                'visibility' => normalise_visibility((string)($media['visibility'] ?? 'institution')),
                'provenancehash' => $sha256,
                'createdby' => $userid,
                'modifiedby' => $userid,
                'timecreated' => time(),
                'timemodified' => time(),
                'metadata' => json_encode([
                    'inventory' => 'uckk_inventory.json',
                    'original_filename' => $original,
                    'proposed_filename' => $proposed,
                    'sha256' => $sha256,
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
            $versionrecord = filter_record('uckkarchive_media_version', $versionrecord);
            $versionrecord->id = $DB->insert_record('uckkarchive_media_version', $versionrecord);

            $fs = get_file_storage();
            $filerecord = [
                'contextid' => $scope['contextid'],
                'component' => 'mod_uckkarchive',
                'filearea' => 'media_original',
                'itemid' => (int)$versionrecord->id,
                'filepath' => '/',
                'filename' => $proposed,
                'userid' => $userid,
            ];
            $existingfile = $fs->get_file(
                $scope['contextid'],
                'mod_uckkarchive',
                'media_original',
                (int)$versionrecord->id,
                '/',
                $proposed
            );
            if ($existingfile) {
                $existingfile->delete();
            }
            $fs->create_file_from_pathname($filerecord, $sourcepath);

            $mediaupdate = (object)[
                'id' => $mediaid,
                'currentversionid' => (int)$versionrecord->id,
                'versionno' => $nextversion,
                'mimetype' => $mimetype,
                'provenancehash' => $sha256,
                'timemodified' => time(),
                'modifiedby' => $userid,
            ];
            $DB->update_record('uckkarchive_media', filter_record('uckkarchive_media', $mediaupdate));
            $result['created_versions'] = 1;
        }
    }

    $transaction->allow_commit();
    return $result;
}

/**
 * Resolve existing module/archive scope.
 *
 * @return array<string,int>|null
 */
function resolve_archive_scope(int $cmid, int $archiveid): ?array {
    global $DB;

    if ($cmid > 0) {
        $cm = get_coursemodule_from_id('uckkarchive', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            throw new RuntimeException('Invalid -CmId / --cmid for mod_uckkarchive: ' . $cmid);
        }
        $context = context_module::instance((int)$cm->id, MUST_EXIST);
        return [
            'archiveid' => (int)$cm->instance,
            'cmid' => (int)$cm->id,
            'contextid' => (int)$context->id,
            'courseid' => (int)$cm->course,
        ];
    }

    if ($archiveid > 0) {
        $archive = $DB->get_record('uckkarchive', ['id' => $archiveid], '*', IGNORE_MISSING);
        if (!$archive) {
            throw new RuntimeException('Invalid -ArchiveId / --archiveid: ' . $archiveid);
        }
        $cm = get_coursemodule_from_instance('uckkarchive', $archiveid, (int)$archive->course, false, IGNORE_MISSING);
        if (!$cm) {
            throw new RuntimeException('Archive exists but has no Moodle course-module context: ' . $archiveid);
        }
        $context = context_module::instance((int)$cm->id, MUST_EXIST);
        return [
            'archiveid' => $archiveid,
            'cmid' => (int)$cm->id,
            'contextid' => (int)$context->id,
            'courseid' => (int)$archive->course,
        ];
    }

    // Prefer the dedicated recovery/media archive if it already exists.
    $archive = $DB->get_record('uckkarchive', ['archivecode' => 'UCKK-MEDIATHEQUE'], '*', IGNORE_MISSING);
    if (!$archive) {
        $archives = $DB->get_records('uckkarchive', null, 'id ASC', '*', 0, 1);
        $archive = $archives ? reset($archives) : false;
    }

    if ($archive) {
        $cm = get_coursemodule_from_instance('uckkarchive', (int)$archive->id, (int)$archive->course, false, IGNORE_MISSING);
        if ($cm) {
            $context = context_module::instance((int)$cm->id, MUST_EXIST);
            return [
                'archiveid' => (int)$archive->id,
                'cmid' => (int)$cm->id,
                'contextid' => (int)$context->id,
                'courseid' => (int)$archive->course,
            ];
        }
    }

    return null;
}

/**
 * Create one hidden Moodle-native archive activity on the site course.
 *
 * @return array<string,int>
 */
function create_site_media_archive(int $userid): array {
    global $DB, $CFG, $USER;

    $course = get_site();
    $module = $DB->get_record('modules', ['name' => 'uckkarchive'], '*', MUST_EXIST);

    $data = new stdClass();
    $data->course = (int)$course->id;
    $data->module = (int)$module->id;
    $data->modulename = 'uckkarchive';
    $data->section = 0;
    $data->name = 'Médiathèque UCKK';
    $data->intro = 'Instance technique Moodle-native pour la Médiathèque UCKK.';
    $data->introformat = FORMAT_HTML;
    $data->archivecode = 'UCKK-MEDIATHEQUE';
    $data->archivetype = 'course_memory';
    $data->archivepolicy = 'validated';
    $data->visibility = 'institution';
    $data->defaultvisibility = 'institution';
    $data->requirevalidation = 0;
    $data->allowpublicitems = 1;
    $data->allowexports = 1;
    $data->status = 'active';
    $data->visible = 0;
    $data->visibleoncoursepage = 0;
    $data->groupmode = 0;
    $data->groupingid = 0;
    $data->completion = COMPLETION_TRACKING_NONE;
    $data->completionview = 0;
    $data->completionexpected = 0;
    $data->showdescription = 0;
    $data->return = 0;
    $data->sr = 0;

    $originaluser = $USER;
    if ((int)($USER->id ?? 0) !== $userid) {
        $USER = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    }

    try {
        $created = add_moduleinfo($data, $course, null);
    } finally {
        $USER = $originaluser;
    }

    $cmid = (int)($created->coursemodule ?? 0);
    $archiveid = (int)($created->instance ?? 0);
    if ($cmid <= 0 || $archiveid <= 0) {
        throw new RuntimeException('Moodle did not return a valid course module/archive id.');
    }

    $context = context_module::instance($cmid, MUST_EXIST);
    return [
        'archiveid' => $archiveid,
        'cmid' => $cmid,
        'contextid' => (int)$context->id,
        'courseid' => (int)$course->id,
    ];
}

/** Resolve acting user, preferring an explicit id then the first site admin. */
function resolve_acting_user(int $userid): stdClass {
    global $DB;

    if ($userid > 0) {
        return $DB->get_record('user', ['id' => $userid, 'deleted' => 0], '*', MUST_EXIST);
    }

    $admins = get_admins();
    if ($admins) {
        $admin = reset($admins);
        if ($admin && !empty($admin->id)) {
            return $DB->get_record('user', ['id' => (int)$admin->id], '*', MUST_EXIST);
        }
    }

    return $DB->get_record('user', ['username' => 'admin', 'deleted' => 0], '*', MUST_EXIST);
}

/** Require a DB table. */
function require_table(string $tablename): void {
    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table($tablename);
    if (!$dbman->table_exists($table)) {
        fail('Required Moodle table is missing: ' . $tablename, 2);
    }
}

/** Filter an object to fields that actually exist in the target DB table. */
function filter_record(string $tablename, stdClass $record): stdClass {
    global $DB;
    static $columns = [];

    if (!isset($columns[$tablename])) {
        $columns[$tablename] = $DB->get_columns($tablename);
    }

    $out = new stdClass();
    foreach (get_object_vars($record) as $field => $value) {
        if (isset($columns[$tablename][$field])) {
            $out->{$field} = $value;
        }
    }
    return $out;
}

/** Return whether a DB table has a field. */
function has_column(string $tablename, string $field): bool {
    global $DB;
    static $columns = [];
    if (!isset($columns[$tablename])) {
        $columns[$tablename] = $DB->get_columns($tablename);
    }
    return isset($columns[$tablename][$field]);
}

/** Deterministic portable UUID derived from a stable inventory key. */
function deterministic_uuid(string $key): string {
    $hex = substr(hash('sha256', $key), 0, 32);
    $hex[12] = '5';
    $variant = hexdec($hex[16]);
    $hex[16] = dechex(($variant & 0x3) | 0x8);
    return substr($hex, 0, 8) . '-' .
        substr($hex, 8, 4) . '-' .
        substr($hex, 12, 4) . '-' .
        substr($hex, 16, 4) . '-' .
        substr($hex, 20, 12);
}

/** Normalise a user-authored key. */
function normalise_key(string $value): string {
    $value = core_text::strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9_\-]+/u', '_', $value) ?? '';
    $value = preg_replace('/_+/', '_', $value) ?? '';
    return trim($value, '_-');
}

/** Canonicalise the historical visibility alias. */
function normalise_visibility(string $visibility): string {
    $visibility = normalise_key($visibility);
    return $visibility === 'institutional' ? 'institution' : ($visibility ?: 'institution');
}

/** Safe join for a single filename beneath an originals directory. */
function safe_join(string $dir, string $filename): string {
    $filename = basename(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filename));
    return rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
}

/** Read a bool CLI option that may arrive as false, empty, 0 or 1. */
function option_bool(array $options, string $key): bool {
    if (!array_key_exists($key, $options)) {
        return false;
    }
    $value = $options[$key];
    if ($value === false || $value === null || $value === '') {
        return true;
    }
    return !in_array(strtolower((string)$value), ['0', 'false', 'no', 'off'], true);
}

/** Remove UTF-8 BOM if present. */
function remove_utf8_bom(string $text): string {
    return str_starts_with($text, "\xEF\xBB\xBF") ? substr($text, 3) : $text;
}

/** Fail with a concise CLI message. */
function fail(string $message, int $code = 1): never {
    fwrite(STDERR, '[UCKK IMPORT] ' . $message . PHP_EOL);
    exit($code);
}
