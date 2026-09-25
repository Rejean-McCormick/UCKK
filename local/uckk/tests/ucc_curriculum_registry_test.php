<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\ucc_curriculum_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\atlas\ucc_curriculum_registry
 */
final class ucc_curriculum_registry_test extends advanced_testcase {
    public function test_registry_exposes_ten_voies_and_one_hundred_courses(): void {
        $this->assertCount(10, ucc_curriculum_registry::voies());
        $this->assertCount(100, ucc_curriculum_registry::courses());
    }

    public function test_canonical_and_legacy_course_ids_resolve_to_same_course(): void {
        $canonical = ucc_curriculum_registry::get_course('UCC-ART-101');
        $legacy = ucc_curriculum_registry::get_course_by_legacy_id($canonical['legacy_course_id']);
        $this->assertSame($canonical['ucc_course_id'], $legacy['ucc_course_id']);
    }

    public function test_courses_can_be_queried_by_domain_and_kristal_theme(): void {
        $this->assertCount(20, ucc_curriculum_registry::courses_by_domain('kreative'));
        $this->assertCount(30, ucc_curriculum_registry::courses_by_domain('konnected'));
        $this->assertCount(30, ucc_curriculum_registry::courses_by_domain('keenkonnect'));
        $this->assertCount(20, ucc_curriculum_registry::courses_by_domain('ethikos'));
        $this->assertNotEmpty(ucc_curriculum_registry::courses_by_theme('urn:theophile:theme:justice'));
    }
}
