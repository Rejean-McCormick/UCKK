<?php
// This file is part of Moodle - https://moodle.org/

namespace local_uckk\external;

use context;
use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_uckk\local\atlas\ucc_curriculum_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * Return one canonical UCC course index entry.
 *
 * @package local_uckk
 */
final class get_ucc_course extends external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_DEFAULT, 0),
            'ucccourseid' => new external_value(PARAM_RAW_TRIMMED, 'Canonical UCC course id'),
        ]);
    }

    public static function execute(int $contextid = 0, string $ucccourseid = ''): array {
        $params = self::validate_parameters(self::execute_parameters(), [
            'contextid' => $contextid,
            'ucccourseid' => $ucccourseid,
        ]);
        $context = $params['contextid'] > 0 ? context::instance_by_id($params['contextid']) : context_system::instance();
        self::validate_context($context);
        require_capability('local/uckk:viewcampus', $context);
        $course = ucc_curriculum_registry::get_course($params['ucccourseid']);
        return [
            'ucccourseid' => $course['ucc_course_id'],
            'legacycourseid' => $course['legacy_course_id'],
            'uccvoieid' => $course['voie_id'],
            'legacyvoieid' => $course['legacy_voie_id'],
            'domainid' => $course['domain_id'],
            'title' => $course['title'],
            'primarysurface' => $course['primary_surface'],
            'themerefs' => array_values($course['kristal_theme_refs']),
            'coveragelevel' => $course['corpus_readiness']['level'],
            'coveragescore' => (int)$course['corpus_readiness']['coverage_score'],
        ];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'ucccourseid' => new external_value(PARAM_RAW_TRIMMED, 'Canonical UCC course id'),
            'legacycourseid' => new external_value(PARAM_ALPHANUMEXT, 'Legacy technical course id'),
            'uccvoieid' => new external_value(PARAM_RAW_TRIMMED, 'Canonical UCC Voie id'),
            'legacyvoieid' => new external_value(PARAM_ALPHANUMEXT, 'Legacy technical Voie id'),
            'domainid' => new external_value(PARAM_ALPHANUMEXT, 'UCC domain id'),
            'title' => new external_value(PARAM_TEXT, 'Course title'),
            'primarysurface' => new external_value(PARAM_TEXT, 'Primary Konnaxion surface'),
            'themerefs' => new external_multiple_structure(new external_value(PARAM_RAW_TRIMMED, 'Kristal theme ref')),
            'coveragelevel' => new external_value(PARAM_ALPHANUMEXT, 'Corpus coverage level'),
            'coveragescore' => new external_value(PARAM_INT, 'Corpus coverage score'),
        ]);
    }
}
