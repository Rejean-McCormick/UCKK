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
use local_uckk\local\atlas\ucc_domain_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * Return the four canonical UCC / Konnaxion domains.
 *
 * @package local_uckk
 */
final class get_ucc_domains extends external_api {
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_DEFAULT, 0),
        ]);
    }

    public static function execute(int $contextid = 0): array {
        $params = self::validate_parameters(self::execute_parameters(), ['contextid' => $contextid]);
        $context = $params['contextid'] > 0 ? context::instance_by_id($params['contextid']) : context_system::instance();
        self::validate_context($context);
        require_capability('local/uckk:viewcampus', $context);
        return array_map(static function(array $domain): array {
            return [
                'domainid' => $domain['domain_id'],
                'code' => $domain['code'],
                'surface' => $domain['surface'],
                'door' => $domain['door'],
                'question' => $domain['question'],
                'purpose' => $domain['purpose'],
                'uccvoieids' => array_values($domain['ucc_voie_ids']),
                'legacyvoieids' => array_values($domain['legacy_voie_ids']),
            ];
        }, ucc_domain_registry::all());
    }

    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(new external_single_structure([
            'domainid' => new external_value(PARAM_ALPHANUMEXT, 'Canonical UCC domain id'),
            'code' => new external_value(PARAM_ALPHANUMEXT, 'Domain code'),
            'surface' => new external_value(PARAM_TEXT, 'Konnaxion surface'),
            'door' => new external_value(PARAM_TEXT, 'Human door label'),
            'question' => new external_value(PARAM_TEXT, 'Domain guiding question'),
            'purpose' => new external_value(PARAM_TEXT, 'Domain purpose'),
            'uccvoieids' => new external_multiple_structure(new external_value(PARAM_RAW_TRIMMED, 'Canonical UCC Voie id')),
            'legacyvoieids' => new external_multiple_structure(new external_value(PARAM_ALPHANUMEXT, 'Legacy technical Voie id')),
        ]));
    }
}
