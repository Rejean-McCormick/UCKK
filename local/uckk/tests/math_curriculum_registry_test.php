<?php
namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

use local_uckk\local\atlas\math_curriculum_registry;

final class math_curriculum_registry_test extends \advanced_testcase {
    public function test_registry_has_document_anchored_curriculum(): void {
        $doc = math_curriculum_registry::get();
        $this->assertSame('MATH-CURRICULUM-2.0', $doc['schema_version']);
        $this->assertCount(8, $doc['pathways']);
        $this->assertCount(64, $doc['courses']);
        $this->assertGreaterThanOrEqual(30, count($doc['concepts']));
        $this->assertCount(2, $doc['source_documents']);
    }

    public function test_canonical_pathway_ids_are_clean(): void {
        $ids = array_column(math_curriculum_registry::pathways(), 'pathway_id');
        $this->assertSame([
            'math.path.intelligibility-structure',
            'math.path.continuity-exponential',
            'math.path.cyclicity-pi',
            'math.path.complex-phase',
            'math.path.euler-synthesis',
            'math.path.information-computation-universe',
            'math.path.pi-randomness-experiment',
            'math.path.proportion-self-similarity',
        ], $ids);
    }

    public function test_every_course_is_grounded_in_concepts_and_sources(): void {
        foreach (math_curriculum_registry::courses() as $course) {
            $this->assertNotEmpty($course['concept_refs']);
            $this->assertNotEmpty($course['source_document_refs']);
            $this->assertStringStartsWith('MATH-', $course['math_course_id']);
        }
    }
}
