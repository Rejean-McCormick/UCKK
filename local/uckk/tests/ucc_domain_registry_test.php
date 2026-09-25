<?php
// This file is part of Moodle - https://moodle.org/

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\ucc_domain_registry;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\atlas\ucc_domain_registry
 */
final class ucc_domain_registry_test extends advanced_testcase {
    public function test_registry_exposes_four_canonical_domains(): void {
        $domains = ucc_domain_registry::all();
        $this->assertCount(4, $domains);
        $this->assertSame(
            ['kreative', 'konnected', 'keenkonnect', 'ethikos'],
            array_column($domains, 'domain_id')
        );
    }

    public function test_every_canonical_voie_belongs_to_exactly_one_domain(): void {
        $ids = [];
        foreach (ucc_domain_registry::all() as $domain) {
            foreach ($domain['ucc_voie_ids'] as $id) {
                $this->assertNotContains($id, $ids);
                $ids[] = $id;
                $this->assertSame($domain['domain_id'], ucc_domain_registry::for_voie($id)['domain_id']);
            }
        }
        $this->assertCount(10, $ids);
    }
}
