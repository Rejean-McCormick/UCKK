# Moodle-native public site switcher

## Goal

Test two genuinely different public experiences on one Moodle instance:

- `theme_uckk` → Univers-Cité King Klown;
- `theme_ucmath` → Univers-Cité des mathématiques.

The switch changes both the visual theme and the public editorial set without
creating a second UCKK-specific session flag or duplicating Moodle runtime data.

## Architectural boundary

The implementation follows the existing UCKK/Moodle split:

- **Git (`uckk-moodle`)** owns versionable definitions: plugin code, public-page
  copy, theme assets, academic seed JSON and other declarative source files.
- **Moodle database** owns live runtime records: courses actually created,
  visibility, categories, activities, users, enrolments, progress and plugin
  state.
- **Moodle file storage (`moodledata`)** owns uploaded/runtime files and media.
- **Ops Console / seed tooling** moves or reconciles versioned source into the
  Moodle runtime where appropriate.

The public-site switcher is a presentation/editorial boundary only. It must not
become another database, another course registry or another media catalogue.

## Switch state

Moodle **session themes** are the only switch state. When **Allow theme changes
in the URL** is enabled, Moodle resolves URLs such as:

- `/?theme=uckk`
- `/?theme=ucmath`

`local_uckk\local\public_site_context` reads Moodle's already-resolved theme and
maps it to a public identity. There is no extra cookie, local storage value,
custom session key or user preference.

The mapping is declared once in `public_site_context::sites()` and reused by the
renderer. `theme_uckk` remains the default identity for an unmapped theme.

Because one public URL can render different content after the session theme
changes, `local_uckk` public pages are explicitly marked non-cacheable. This
avoids pinning one identity in browser/proxy cache under a URL later used by the
other identity.

## Public content structure

UCKK already uses one class per public page in:

`local/uckk/classes/local/public_pages/`

The mathematics site follows the same convention instead of introducing a
second content architecture:

`local/uckk/classes/local/public_pages/math/`

with `home.php`, `courses.php`, `programs.php`, etc., plus `site.php` for shared
navigation and identity metadata.

This means the Math public copy is versioned in Git just like the existing UCKK
public copy, while runtime lists remain live.

## Runtime data rules

### Courses

The Math catalogue does **not** read `academic_registry_json/courses.json` at
request time and does not maintain a second list. `local/uckk/courses.php`
continues to build cards from the live Moodle course state and its existing
visibility/permission rules.

`academic_registry_json` remains a declarative/seed source, not the public
runtime query source.

If a later requirement needs a Math-only catalogue, prefer a native Moodle
classification already present in runtime data (for example course categories
or another established Moodle metadata mechanism) rather than inventing a
parallel `math_courses.json` registry.

### Media

The Math library reuses the existing `mod_uckkarchive` public mediatheque
service. Search, access policy, ownership and files remain with the existing
Moodle/plugin storage. The switch changes labels and presentation only.

### Users and permissions

The theme is not an access-control boundary. Authentication, capabilities,
visibility and permissions stay with Moodle and the owning plugins.

## Theme separation

`theme_ucmath` is a direct child of Boost, not of `theme_uckk`. It owns its own
SCSS, public/frontpage layouts and override of
`local_uckk/public_page.mustache`.

When `theme_ucmath` is active, the UCKK public stylesheet is not loaded. This
keeps the visual identities independent without duplicating the underlying
Moodle data model.

## Why not Workplace tenants or separate instances?

This prototype is one Moodle installation with two public faces. If the future
requirement becomes separate users, separate administration, independent
permissions, separate private catalogues or legal/organisational isolation,
then the theme switcher should not be stretched into pseudo-multitenancy.
Re-evaluate Moodle Workplace tenants or separate Moodle instances instead.

## Enable and test

1. Install/upgrade `local_uckk` and install `theme_ucmath`.
2. In **Site administration → Appearance → Themes → Theme settings**, enable
   **Allow theme changes in the URL**.
3. Keep `theme_uckk` as the normal site theme if UCKK is the default.
4. Purge Moodle caches.
5. Test `/?theme=uckk` and `/?theme=ucmath`, then navigate through the public
   pages and verify that the selected session theme persists.

For the prototype, avoid forcing a course/category theme while testing because
those theme contexts may supersede the session theme in Moodle's theme
resolution hierarchy.
