<?php
// This file is part of UCKK-Moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.

/** Tests for the public site/theme adapter. @package local_uckk */

namespace local_uckk;

defined('MOODLE_INTERNAL') || die();

/**
 * @covers \local_uckk\local\public_site_context
 * @covers \local_uckk\local\public_pages\math\site
 * @covers \local_uckk\local\public_pages\math\home
 */
final class public_site_context_test extends \advanced_testcase {
    public function test_theme_name_is_the_only_site_selector(): void {
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

    public function test_site_registry_owns_theme_mapping_once(): void {
        $sites = \local_uckk\local\public_site_context::sites();
        $this->assertSame('uckk', $sites[0]['theme']);
        $this->assertSame('ucmath', $sites[1]['theme']);
        $this->assertSame('math', $sites[1]['site']);
    }

    public function test_math_home_is_a_distinct_content_set(): void {
        $definition = \local_uckk\local\public_pages\math\home::definition();

        $this->assertSame('Comprendre. Démontrer. Modéliser.', $definition['title']);
        $this->assertSame('mathematical-minimalism', $definition['visualstyle']);
        $this->assertNotEmpty($definition['navigation']);
        $this->assertNotEmpty($definition['sections']);
    }
}
