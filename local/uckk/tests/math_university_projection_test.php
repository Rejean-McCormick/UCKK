<?php
namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

use local_uckk\local\atlas\math_university_projection;

final class math_university_projection_test extends \advanced_testcase {
    public function test_projection_is_pinned_to_mathkristal(): void {
        $doc = math_university_projection::get();
        $this->assertSame('MATH-UNIVERSITY-PROJECTION-1.0', $doc['schema_version']);
        $this->assertSame('1.6.0', $doc['generated_from']['release']);
        $this->assertSame('sha256:35aef425c4029076e1af3ada6a52413e975ed402ce0c64644943b11a5849ff71', $doc['generated_from']['state_id']);
        $this->assertSame('sha256:140aad0f12061f1717c0b9f960871b515279f68052f93c54a4d034f06fb37e1a', $doc['generated_from']['referent_registry_id']);
    }

    public function test_projection_is_dynamic_and_page_driven(): void {
        $doc = math_university_projection::get();
        $this->assertCount(14, $doc['pathways']);
        $this->assertCount(72, $doc['courses']);
        $this->assertCount(72, $doc['page_specs']);
        $this->assertSame('SemantiK Architect', $doc['policy']['linguistic_realizer']);
        $this->assertSame('page_specs.communication_obligations', $doc['semantik_architect']['request_source']);
    }

    public function test_every_projected_course_and_page_keeps_kristal_identity(): void {
        $pages = [];
        foreach (math_university_projection::page_specs() as $page) {
            $pages[$page['math_course_id']] = $page;
        }
        foreach (math_university_projection::courses() as $course) {
            $this->assertStringStartsWith('urn:mathkristal:', $course['kristal_ref']);
            $this->assertArrayHasKey($course['math_course_id'], $pages);
            $this->assertSame($course['kristal_ref'], $pages[$course['math_course_id']]['subject']['ref']);
            $this->assertSame('UCKK Math University Projector', $pages[$course['math_course_id']]['rendering']['content_selection_owner']);
            $this->assertFalse($pages[$course['math_course_id']]['rendering']['ai_runtime_required']);
        }
    }
}
