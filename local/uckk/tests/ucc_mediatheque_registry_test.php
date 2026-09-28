<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\ucc_curriculum_registry;
use local_uckk\local\atlas\ucc_mediatheque_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\atlas\ucc_mediatheque_registry
 */
final class ucc_mediatheque_registry_test extends advanced_testcase {
    public function test_every_ucc_course_has_three_to_six_media_links(): void {
        foreach (ucc_curriculum_registry::courses() as $course) {
            $links = ucc_mediatheque_registry::links_for_course($course['ucc_course_id']);
            $this->assertGreaterThanOrEqual(3, count($links), $course['ucc_course_id']);
            $this->assertLessThanOrEqual(6, count($links), $course['ucc_course_id']);
        }
    }

    public function test_link_stats_cover_all_ucc_courses(): void {
        $stats = ucc_mediatheque_registry::stats();
        $this->assertSame(110, (int)$stats['courses']);
        $this->assertSame(110, (int)$stats['courses_with_media']);
        $this->assertGreaterThanOrEqual(3, (int)$stats['min_media_per_course']);
    }

    public function test_cosmic_pathway_keeps_anchor_work(): void {
        $media = ucc_mediatheque_registry::media_for_course('UCC-COS-101');
        $this->assertNotEmpty($media);
        $this->assertSame('ucc.work.le-dieu-cosmique-2008', $media[0]['media_ref']);
        $this->assertSame('anchor', $media[0]['role']);
    }

    public function test_reference_metadata_exposes_course_and_theme_projections(): void {
        $work = ucc_mediatheque_registry::get_work('ucc.work.theophile.rn');
        $this->assertNotEmpty($work['metadata']['course_refs']);
        $this->assertNotEmpty($work['metadata']['kristal_theme_refs']);
    }
}
