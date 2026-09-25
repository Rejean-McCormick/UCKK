<?php
// This file is part of UCKK-Moodle.

/** Tests for the public site/theme adapter. @package local_uckk */
namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\public_site_context
 * @covers \local_uckk\local\public_pages\ucc\site
 * @covers \local_uckk\local\public_pages\ucc\home
 * @covers \local_uckk\local\public_pages\math\site
 * @covers \local_uckk\local\public_pages\math\home
 */
final class public_site_context_test extends \advanced_testcase {
    public function test_theme_name_is_the_only_site_selector(): void {
        $this->assertSame(
            \local_uckk\local\public_site_context::SITE_UCC,
            \local_uckk\local\public_site_context::from_theme_name('ucc')
        );
        $this->assertSame(
            \local_uckk\local\public_site_context::SITE_MATH,
            \local_uckk\local\public_site_context::from_theme_name('ucmath')
        );
        $this->assertSame(
            \local_uckk\local\public_site_context::SITE_UCKK,
            \local_uckk\local\public_site_context::from_theme_name('uckk')
        );
        $this->assertSame(
            \local_uckk\local\public_site_context::SITE_UCKK,
            \local_uckk\local\public_site_context::from_theme_name('boost')
        );
    }

    public function test_site_registry_owns_all_theme_mappings_once(): void {
        $sites = \local_uckk\local\public_site_context::sites();
        $this->assertCount(3, $sites);
        $this->assertSame(['uckk', 'ucc', 'ucmath'], array_column($sites, 'theme'));
        $this->assertSame(['uckk', 'ucc', 'math'], array_column($sites, 'site'));
    }

    public function test_ucc_home_is_a_distinct_content_set(): void {
        $definition = \local_uckk\local\public_pages\ucc\home::definition();
        $this->assertSame('Univers-Cité Catho', $definition['title']);
        $this->assertNotEmpty($definition['sections']);
        $this->assertNotEmpty($definition['quicklinks']);
    }

    public function test_math_home_is_a_distinct_content_set(): void {
        $definition = \local_uckk\local\public_pages\math\home::definition();
        $this->assertSame('Comprendre. Démontrer. Modéliser.', $definition['title']);
        $this->assertSame('mathematical-minimalism', $definition['visualstyle']);
        $this->assertNotEmpty($definition['navigation']);
        $this->assertNotEmpty($definition['sections']);
    }
}
