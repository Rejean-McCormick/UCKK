<?php
// This file is part of Moodle - https://moodle.org/.

/**
 * Tests for the public UCC pathway pages.
 *
 * @package local_uckk
 */

declare(strict_types=1);

namespace local_uckk;

use advanced_testcase;
use local_uckk\local\atlas\ucc_curriculum_registry;
use local_uckk\local\public_pages\ucc\ucc_pathway;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\public_pages\ucc\ucc_pathway
 */
final class ucc_public_pathway_test extends advanced_testcase {
    public function test_every_canonical_pathway_has_one_public_slug(): void {
        $pathways = ucc_curriculum_registry::pathways();
        $slugs = ucc_pathway::slugs();

        $this->assertCount(11, $pathways);
        $this->assertCount(11, $slugs);
        $this->assertCount(11, array_unique(array_values($slugs)));

        foreach ($pathways as $pathway) {
            $pathwayid = (string)$pathway['pathway_id'];
            $this->assertArrayHasKey($pathwayid, $slugs);
            $this->assertSame($pathwayid, ucc_pathway::pathway_id_for_slug($slugs[$pathwayid]));
        }
    }

    public function test_pathway_page_exposes_ten_course_cards(): void {
        foreach (ucc_pathway::slugs() as $pathwayid => $slug) {
            $definition = ucc_pathway::definition_for_slug($slug);
            $pathway = ucc_curriculum_registry::get_pathway($pathwayid);

            $this->assertSame($pathway['title'], $definition['title']);
            $this->assertNotEmpty($definition['quicklinks']);
            $this->assertNotEmpty($definition['sections']);

            $coursecards = [];
            foreach ($definition['sections'] as $section) {
                if (($section['type'] ?? '') === 'courses') {
                    $coursecards = $section['cards'] ?? [];
                    break;
                }
            }

            $this->assertCount(10, $coursecards, 'Each UCC pathway page must expose its ten canonical courses.');
            $this->assertSame($pathway['course_ids'][0], $coursecards[0]['eyebrow']);
        }
    }
}
