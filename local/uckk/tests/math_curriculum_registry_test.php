<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\math_curriculum_registry;

defined('MOODLE_INTERNAL') || die();

/** @covers \local_uckk\local\atlas\math_curriculum_registry */
final class math_curriculum_registry_test extends advanced_testcase {
    public function test_registry_exposes_four_canonical_pathways(): void {
        $this->assertSame([
            'math.path.structures',
            'math.path.spaces',
            'math.path.change',
            'math.path.uncertainty',
        ], array_column(math_curriculum_registry::pathways(), 'pathway_id'));
    }

    public function test_registry_declares_no_legacy_identifiers(): void {
        foreach (math_curriculum_registry::pathways() as $pathway) {
            foreach (array_keys($pathway) as $field) {
                $this->assertFalse(str_starts_with((string)$field, 'legacy_'));
            }
        }
    }
}
