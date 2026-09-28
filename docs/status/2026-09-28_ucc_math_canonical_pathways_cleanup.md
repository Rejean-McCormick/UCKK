# UCC + Math — canonical pathway cleanup

Date: 2026-09-28

## Decision

The Univers-Cité chrétienne (UCC) and the Univers-Cité des mathématiques now use clean canonical pathway namespaces that are independent from historical UCKK identifiers.

### UCC

Canonical pathway ids use `ucc.path.*`.

The canonical curriculum and domain registries no longer contain `legacy_*` fields. Historical UCKK identifiers and the previous `ucc:voie:*` identifiers are isolated in `local/uckk/atlas/ucc_pathway_migrations.json`.

Compatibility rule:

> Legacy identifiers are readable, never writable.

The resolver `local_uckk\local\atlas\ucc_legacy_resolver` may resolve historical identifiers for reads and migrations. New integrations must write only canonical `ucc.path.*` ids and canonical `UCC-XXX-NNN` course ids.

### Univers-Cité des mathématiques

The existing public taxonomy is now represented canonically as:

- `math.path.structures`
- `math.path.spaces`
- `math.path.change`
- `math.path.uncertainty`

The source snapshot contains no historical Math pathway registry, so no artificial legacy mappings were created. `math_pathway_migrations.json` is intentionally empty and records that state explicitly.

## Files

Canonical:

- `local/uckk/atlas/ucc_curriculum_registry.json`
- `local/uckk/atlas/ucc_domains.json`
- `local/uckk/atlas/ucc_universe.json`
- `local/uckk/atlas/math_curriculum_registry.json`
- `local/uckk/atlas/math_universe.json`

Compatibility only:

- `local/uckk/atlas/ucc_pathway_migrations.json`
- `local/uckk/atlas/math_pathway_migrations.json`

`ucc_legacy_aliases.json` is retired. Compatibility is represented as typed migrations (`renamed_to`, `absorbed_into`, and reserved support for `split_into`, `merged_into`, `retired`) rather than as aliases that imply semantic equivalence.

## Runtime impact

- UCC external course/domain projections return canonical pathway ids only.
- UCC public program rendering resolves existing Moodle legacy identifiers at the compatibility boundary, then uses canonical pathway identity for presentation.
- Math public program sections are populated from `math_curriculum_registry.json`.
- No database schema change is required by this cleanup.
