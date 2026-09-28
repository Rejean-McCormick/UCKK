<?php
// This file is part of Moodle - https://moodle.org/.
// @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

declare(strict_types=1);
namespace local_uckk;
use advanced_testcase;
use local_uckk\local\atlas\ucc_syllabus_registry;
use local_uckk\local\atlas\ucc_mediatheque_registry;
use local_uckk\local\atlas\ucc_curriculum_registry;
defined('MOODLE_INTERNAL') || die();

/** @covers \local_uckk\local\atlas\ucc_syllabus_registry */
final class ucc_syllabus_registry_test extends advanced_testcase {
    public function test_all_plans_match_canonical_courses_and_readings(): void {
        foreach (ucc_curriculum_registry::courses() as $course) {
            $plan = ucc_syllabus_registry::get_course($course['ucc_course_id']);
            $this->assertSame($course['pathway_id'], $plan['pathway_id']);
            $this->assertSame($course['title'], $plan['title']);
            $this->assertSame(ucc_mediatheque_registry::links_for_course($course['ucc_course_id']), $plan['readings']);
            $this->assertSame(100, array_sum(array_column($plan['assessment']['rubric'], 'weight')));
            $this->assertNull($plan['credits']);
        }
    }

    public function test_marialogy_distinguishes_historical_position_and_definition(): void {
        $plan = ucc_syllabus_registry::get_course('UCC-THE-107');
        $this->assertSame('ucc.work.editorial.ineffabilis', $plan['readings'][0]['media_ref']);
        $this->assertContains('ucc.work.theophile.th-mary', array_column($plan['readings'], 'media_ref'));
        $this->assertStringContainsString('1854', implode(' ', $plan['contextual_cautions']));
    }

    public function test_unknown_course_is_rejected(): void {
        $this->expectException(\coding_exception::class);
        ucc_syllabus_registry::get_course('UCC-THE-999');
    }

    public function test_archive_course_uses_documentary_method_not_keyword_match(): void {
        $readings = ucc_mediatheque_registry::media_for_course('UCC-OEU-105');
        $this->assertSame('ucc.work.editorial.history-method', $readings[0]['media_ref']);
        $this->assertNotEmpty($readings[0]['passage']);
        $this->assertNotEmpty($readings[0]['rationale']);
    }
}
