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
 * @covers \local_uckk\local\public_login
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
        $this->assertSame('Univers-Cité chrétienne', $definition['title']);
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

    public function test_ucc_navigation_exposes_encyclopedic_entry_points(): void {
        $navigation = \local_uckk\local\public_pages\ucc\site::navigation();
        $keys = array_column($navigation, 'key');

        $this->assertContains('programs', $keys);
        $this->assertContains('courses', $keys);
        $this->assertContains('thinkers', $keys);
        $this->assertContains('glossary', $keys);
        $this->assertContains('christian', $keys);
        $this->assertContains('method', $keys);
        $this->assertContains('transparency', $keys);
    }

    public function test_ucc_people_view_is_primary_and_glossary_is_transversal(): void {
        $home = \local_uckk\local\public_pages\ucc\home::definition();
        $thinkers = \local_uckk\local\public_pages\ucc\thinkers::definition();
        $glossary = \local_uckk\local\public_pages\ucc\glossary::definition();

        $this->assertSame('Vue principale du corpus', $thinkers['eyebrow']);
        $this->assertSame('Index transversal', $glossary['eyebrow']);
        $this->assertStringContainsString('vue principale', mb_strtolower($home['sections'][0]['eyebrow'] . ' ' . $home['sections'][0]['body']));
        $this->assertStringContainsString('pas un second corpus', mb_strtolower($glossary['summary']));
    }

    public function test_ucc_atlas_declares_projection_contract_without_claiming_native_kristal_migration(): void {
        $path = __DIR__ . '/../atlas/ucc_universe.json';
        $data = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('uckk.univers-cite-projection/1.0.0', $data['knowledge']['projection_contract']);
        $this->assertSame('people', $data['knowledge']['primary_view']);
        $this->assertSame('derived_index', $data['knowledge']['glossary_mode']);
        $this->assertSame('legacy_semantic_source_pending_kristal_v5_migration', $data['knowledge']['projection_status']);
    }

    public function test_public_login_contract_keeps_explorer_available_for_each_univers_cite(): void {
        $sites = \local_uckk\local\public_site_context::sites();
        $this->assertSame(['uckk', 'ucc', 'ucmath'], array_column($sites, 'theme'));

        $source = file_get_contents(__DIR__ . '/../classes/local/public_login.php');
        $this->assertStringContainsString("'Explorer sans connexion'", $source);
        $this->assertStringContainsString("THEME_UCC", $source);
        $this->assertStringContainsString("THEME_MATH", $source);
    }
}
