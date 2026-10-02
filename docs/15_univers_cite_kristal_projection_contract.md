# 15 — Univers-Cité ↔ Kristal projection contract

## Status

Frozen UCKK consumer contract: `uckk.univers-cite-projection/1.0.0`.

## Boundary

A Univers-Cité is a scoped, rebuildable consumer projection over a pinned knowledge artifact. UCKK owns:

- scope membership and bridges;
- the domain-specific primary navigation view;
- derived indexes such as the glossary and chronology;
- Moodle public/course materializations;
- media-library presentation and explicit library bridges.

UCKK does **not** become the epistemic owner of the source assertions merely because it materializes them in Moodle.

## Domain neutrality

`primary=people` is correct for the current Christian intellectual corpus, but it is not a universal rule. Another Univers-Cité may choose `processes`, `installations`, `places`, `works` or another domain-specific view.

## Glossary rule

The glossary is a `derived_index`, not a second corpus and not a parallel canon. Terms/concepts may be stable referents, but their page is assembled by traversing linked people/collectives, works, sources and assertions. The current `ucc_glossary_map.json` uses legacy themes as concept candidates during migration; a theme is not automatically a canonical concept.

## Current migration state

The current Atlas pins a working Théophile semantic corpus. The contract is adopted now, but native Kristal v5 migration remains pending. The target baseline is Kristal `5.0.0-rc.3` with `kristal.referent-registry/1.0.0`.

Machine-readable files:

- `docs/contracts/univers-cite-projection/1.0.0/schema.json`;
- `docs/contracts/univers-cite-projection/1.0.0/ucc-current.example.json`.

## Specialized consumers

The Univers-Cité des mathématiques specializes this boundary through `uckk.math-university-projection/1.0.0`. See `docs/16_math_university_kristal_bridge.md`. The Math specialization adds a deterministic curriculum/page projector and a downstream SemantiK Architect articulation stage while preserving Kristal epistemic authority and Moodle transactional ownership.
