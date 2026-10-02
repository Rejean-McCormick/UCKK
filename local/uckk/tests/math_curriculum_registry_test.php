<?php
namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

use local_uckk\local\atlas\math_curriculum_registry;

final class math_curriculum_registry_test extends \advanced_testcase {
    public function test_legacy_registry_remains_readable_for_migration(): void {
        $doc = math_curriculum_registry::get();
        $this->assertSame('MATH-CURRICULUM-2.0', $doc['schema_version']);
        $this->assertNotEmpty($doc['pathways']);
        $this->assertNotEmpty($doc['courses']);
        $this->assertNotEmpty($doc['concepts']);
        $this->assertNotEmpty($doc['source_documents']);
    }

    public function test_legacy_pathway_ids_are_still_clean(): void {
        foreach (math_curriculum_registry::pathways() as $pathway) {
            $this->assertMatchesRegularExpression('/^math\.path\.[a-z0-9-]+$/', $pathway['pathway_id']);
        }
    }

    public function test_legacy_courses_remain_grounded_for_compatibility(): void {
        foreach (math_curriculum_registry::courses() as $course) {
            $this->assertNotEmpty($course['concept_refs']);
            $this->assertNotEmpty($course['source_document_refs']);
            $this->assertStringStartsWith('MATH-', $course['math_course_id']);
        }
    }
}
