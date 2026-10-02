# Math University — MathKristal bridge

Date: 2026-10-01

## Decision

The Univers-Cité des mathématiques now treats MathKristal as its pinned epistemic/reference authority and UCKK as a rebuildable pedagogical projection consumer.

Current pin:

- MathKristal `1.6.0`;
- state `sha256:35aef425c4029076e1af3ada6a52413e975ed402ce0c64644943b11a5849ff71`;
- referent registry `sha256:140aad0f12061f1717c0b9f960871b515279f68052f93c54a4d034f06fb37e1a`.

The generated projection currently exposes 14 pedagogical paths, 72 projected course steps and 72 semantic page specifications.

## Runtime boundary

Public Math pages read the generated projection. The previous 8-path / 64-course registry remains available only for compatibility with existing Moodle materializations and migration work.

SemantiK Architect is the linguistic realization stage. It receives already-selected communication obligations and cannot become the mathematical content selector.

## Data preservation

No enrolment, grade, submission, user annotation or other transactional Moodle state is generated from Kristal. Projection rebuilds must preserve all human state.

## Public catalogue binding

The public Math course explorer now treats `MATH-<CODE>-1xx` in a Moodle course `shortname` or `idnumber` as a read-only binding to the current MathKristal projection. Bound cards inherit the projected pathway label and expose the Kristal subject and semantic page specification. The AJAX endpoint uses the same binding and can search projection metadata in Math context.

This is not a materializer: it does not create or mutate Moodle courses. Historical courses remain independent until an explicit migration/materialization operation assigns a projected stable id.

## Validation

- projection schema and compact example validate against Draft 2020-12;
- 6 pinned MathKristal source files match lock hashes and byte counts;
- distribution archive SHA-256 matches the lock;
- deterministic rebuild reproduces the projection and lock byte-for-byte;
- current projection: 14 pathways, 72 course steps, 72 page specs;
- changed PHP files pass syntax lint; projection builder passes Python bytecode compilation.
