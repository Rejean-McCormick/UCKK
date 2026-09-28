<?php
namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

use local_uckk\local\atlas\math_legacy_resolver;

final class math_legacy_resolver_test extends \advanced_testcase {
    public function test_old_change_path_splits_into_focused_paths(): void {
        $mapping = math_legacy_resolver::resolve_pathway('math.path.change');
        $this->assertNotNull($mapping);
        $this->assertSame('split_into', $mapping['relation']);
        $this->assertSame([
            'math.path.continuity-exponential',
            'math.path.euler-synthesis',
        ], $mapping['canonical_ids']);
    }

    public function test_unknown_legacy_path_returns_null(): void {
        $this->assertNull(math_legacy_resolver::resolve_pathway('math.path.unknown'));
    }
}
