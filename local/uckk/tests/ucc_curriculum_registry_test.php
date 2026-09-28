<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\ucc_curriculum_registry;
use local_uckk\local\atlas\ucc_legacy_resolver;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\atlas\ucc_curriculum_registry
 * @covers \local_uckk\local\atlas\ucc_legacy_resolver
 */
final class ucc_curriculum_registry_test extends advanced_testcase {
    public function test_registry_exposes_eleven_pathways_and_one_hundred_ten_courses(): void {
        $this->assertCount(11, ucc_curriculum_registry::pathways());
        $this->assertCount(110, ucc_curriculum_registry::courses());
    }

    public function test_canonical_registry_contains_no_legacy_fields(): void {
        foreach (ucc_curriculum_registry::pathways() as $pathway) {
            foreach (array_keys($pathway) as $field) {
                $this->assertFalse(str_starts_with((string)$field, 'legacy_'));
            }
        }
        foreach (ucc_curriculum_registry::courses() as $course) {
            foreach (array_keys($course) as $field) {
                $this->assertFalse(str_starts_with((string)$field, 'legacy_'));
            }
        }
    }

    public function test_legacy_course_resolution_is_external_to_canonical_registry(): void {
        $canonicalid = ucc_legacy_resolver::resolve_course('AS101');
        $this->assertSame('UCC-OEU-101', $canonicalid);
        $this->assertSame($canonicalid, ucc_legacy_resolver::course('AS101')['ucc_course_id']);
    }

    public function test_legacy_pathway_resolution_accepts_uckk_and_ucc_v1_ids(): void {
        $expected = 'ucc.path.philosophy-metaphysics-person';
        $this->assertSame($expected, ucc_legacy_resolver::resolve_pathway('voie_metaphysique'));
        $this->assertSame($expected, ucc_legacy_resolver::resolve_pathway('ME'));
        $this->assertSame($expected, ucc_legacy_resolver::resolve_pathway('UCKK-ME'));
        $this->assertSame($expected, ucc_legacy_resolver::resolve_pathway('ucc:voie:philosophie-personne'));
    }

    public function test_courses_can_be_queried_by_domain_and_kristal_theme(): void {
        $this->assertCount(20, ucc_curriculum_registry::courses_by_domain('kreative'));
        $this->assertCount(40, ucc_curriculum_registry::courses_by_domain('konnected'));
        $this->assertCount(30, ucc_curriculum_registry::courses_by_domain('keenkonnect'));
        $this->assertCount(20, ucc_curriculum_registry::courses_by_domain('ethikos'));
        $this->assertNotEmpty(ucc_curriculum_registry::courses_by_theme('urn:theophile:theme:justice'));
    }
}
