#!/usr/bin/env python3
"""Build the UCKK Math University projection from a pinned MathKristal artifact.

The builder is deliberately offline and deterministic. It selects curriculum/page
structure from the pinned Kristal; SemantiK Architect is a downstream linguistic
realizer and never selects mathematical content.
"""
from __future__ import annotations

import argparse
import hashlib
import json
from pathlib import Path
from typing import Any, Iterable

SCHEMA_VERSION = "MATH-UNIVERSITY-PROJECTION-1.0"
LOCK_SCHEMA_VERSION = "MATH-KRISTAL-LOCK-1.0"

PATH_META = {
    "foundations-to-euler": ("EUL", "Fondations vers Euler"),
    "analysis-to-functional-analysis": ("ANA", "Analyse vers l’analyse fonctionnelle"),
    "probability-to-martingales": ("PMA", "Probabilités vers les martingales"),
    "topology-to-pi1": ("TOP", "Topologie vers le groupe fondamental"),
    "logic-to-compactness": ("LOG", "Logique vers la compacité"),
    "number-theory-to-reciprocity": ("NTR", "Théorie des nombres vers la réciprocité quadratique"),
    "groups-to-sylow": ("GRP", "Groupes vers les théorèmes de Sylow"),
    "fourier-continuous": ("FOU", "Analyse de Fourier continue"),
    "algebra-to-algebraic-geometry": ("ALG", "Algèbre vers la géométrie algébrique"),
    "linear-algebra-to-coding": ("LAC", "Algèbre linéaire vers la théorie des codes"),
    "calculus-to-stokes": ("STK", "Calcul différentiel vers Stokes"),
    "graphs-to-matroids": ("MAT", "Graphes vers les matroïdes"),
    "number-theory-to-euler-products": ("NEP", "Théorie des nombres vers les produits d’Euler"),
    "probability-to-ito": ("ITO", "Probabilités vers le calcul d’Itô"),
}


def load_json(path: Path) -> dict[str, Any]:
    with path.open("r", encoding="utf-8") as fh:
        value = json.load(fh)
    if not isinstance(value, dict):
        raise ValueError(f"Expected JSON object: {path}")
    return value


def sha256_file(path: Path) -> str:
    h = hashlib.sha256()
    with path.open("rb") as fh:
        for chunk in iter(lambda: fh.read(1024 * 1024), b""):
            h.update(chunk)
    return h.hexdigest()


def external_id(value: Any) -> str | None:
    if isinstance(value, dict):
        item = value.get("external_id")
        if isinstance(item, str) and item:
            return item
    return None


def object_external_id(statement: dict[str, Any]) -> str | None:
    obj = statement.get("object")
    if not isinstance(obj, dict):
        return None
    val = obj.get("value")
    return external_id(val)


def epistemic_role(assertion: dict[str, Any]) -> str | None:
    statement = assertion.get("statement")
    if not isinstance(statement, dict):
        return None
    qualifiers = statement.get("qualifiers", [])
    if not isinstance(qualifiers, list):
        return None
    for qualifier in qualifiers:
        if not isinstance(qualifier, dict):
            continue
        predicate = qualifier.get("predicate")
        if external_id(predicate) != "math:epistemic_role":
            continue
        obj = qualifier.get("object")
        if isinstance(obj, dict) and obj.get("kind") == "string" and isinstance(obj.get("value"), str):
            return obj["value"]
    return None


def unique(values: Iterable[str]) -> list[str]:
    seen: set[str] = set()
    out: list[str] = []
    for value in values:
        if value and value not in seen:
            seen.add(value)
            out.append(value)
    return out


def build_projection(root: Path) -> tuple[dict[str, Any], dict[str, Any]]:
    index_path = root / "corpus" / "index.json"
    registry_path = root / "corpus" / "math.referent-registry.json"
    state_path = root / "corpus" / "math.structured-epistemic-state.json"
    paths_path = root / "corpus" / "pedagogical-paths.json"
    manifest_path = root / "manifest.sha256.json"

    for path in (index_path, registry_path, state_path, paths_path, manifest_path):
        if not path.is_file():
            raise FileNotFoundError(path)

    index = load_json(index_path)
    registry = load_json(registry_path)
    state = load_json(state_path)
    pedagogical = load_json(paths_path)
    manifest = load_json(manifest_path)

    release = str(index.get("corpus_version") or index.get("extensions", {}).get("corpus_version") or "")
    state_id = str(state.get("state_id") or "")
    registry_id = str(registry.get("registry_id") or "")
    if not release or not state_id.startswith("sha256:") or not registry_id.startswith("sha256:"):
        raise ValueError("MathKristal artifact is missing release/state/registry identity")

    referents_raw = registry.get("referents")
    assertions_raw = state.get("assertions")
    paths_raw = pedagogical.get("paths")
    if not isinstance(referents_raw, list) or not isinstance(assertions_raw, list) or not isinstance(paths_raw, dict):
        raise ValueError("MathKristal core structures are malformed")

    referents: dict[str, dict[str, Any]] = {
        str(item.get("ref")): item for item in referents_raw
        if isinstance(item, dict) and isinstance(item.get("ref"), str)
    }

    assertion_index: dict[str, list[dict[str, Any]]] = {}
    for assertion in assertions_raw:
        if not isinstance(assertion, dict):
            continue
        statement = assertion.get("statement")
        if not isinstance(statement, dict):
            continue
        sid = external_id(statement.get("subject"))
        oid = object_external_id(statement)
        for rid in unique([x for x in (sid, oid) if x]):
            assertion_index.setdefault(rid, []).append(assertion)

    pathways: list[dict[str, Any]] = []
    courses: list[dict[str, Any]] = []
    page_specs: list[dict[str, Any]] = []
    projected_refs: dict[str, dict[str, Any]] = {}

    for slug, sequence in paths_raw.items():
        if not isinstance(slug, str) or not isinstance(sequence, list) or not sequence:
            continue
        code, title = PATH_META.get(slug, ("KRS", slug.replace("-", " ").title()))
        pathway_id = f"math.path.{slug}"
        course_ids: list[str] = []
        sequence_refs = [str(ref) for ref in sequence]
        first_label = label_for(referents.get(sequence_refs[0]), sequence_refs[0])
        last_label = label_for(referents.get(sequence_refs[-1]), sequence_refs[-1])

        for pos, ref in enumerate(sequence_refs, start=1):
            referent = referents.get(ref)
            if referent is None:
                raise ValueError(f"Pedagogical path {slug} references unknown referent {ref}")
            projected_refs[ref] = referent
            course_id = f"MATH-{code}-{100 + pos}"
            page_id = f"math.page.{code.lower()}.{100 + pos}"
            course_ids.append(course_id)

            relevant = assertion_index.get(ref, [])
            assertion_ids = unique(
                str(a.get("assertion_id")) for a in relevant
                if isinstance(a.get("assertion_id"), str)
            )
            roles: dict[str, list[str]] = {}
            prereqs: list[str] = []
            source_refs: list[str] = []
            proof_refs: list[str] = []
            definition_refs: list[str] = []

            for assertion in relevant:
                aid = str(assertion.get("assertion_id") or "")
                role = epistemic_role(assertion) or "untyped"
                if aid:
                    roles.setdefault(role, []).append(aid)
                statement = assertion.get("statement") if isinstance(assertion.get("statement"), dict) else {}
                pred = external_id(statement.get("predicate"))
                sid = external_id(statement.get("subject"))
                oid = object_external_id(statement)
                if pred == "math:pedagogical_prerequisite" and sid == ref and oid:
                    prereqs.append(oid)
                if pred == "math:proves" and oid == ref and sid:
                    proof_refs.append(sid)
                if pred == "math:defines_in" and oid == ref and sid:
                    definition_refs.append(sid)
                evidence = assertion.get("evidence_refs", [])
                if isinstance(evidence, list):
                    for ev in evidence:
                        if not isinstance(ev, dict):
                            continue
                        source = ev.get("source_ref")
                        if isinstance(source, dict) and isinstance(source.get("source_id"), str):
                            source_refs.append(source["source_id"])

            label = label_for(referent, ref)
            classifications = [str(x) for x in referent.get("classifications", []) if isinstance(x, str)]
            prev_ref = sequence_refs[pos - 2] if pos > 1 else None
            next_ref = sequence_refs[pos] if pos < len(sequence_refs) else None

            courses.append({
                "math_course_id": course_id,
                "pathway_id": pathway_id,
                "title": label,
                "kristal_ref": ref,
                "page_spec_id": page_id,
                "sequence": pos,
                "prerequisite_refs": unique(prereqs),
                "source_refs": unique(source_refs),
            })

            obligations = [
                {
                    "obligation_id": "heading",
                    "kind": "heading",
                    "semantic_refs": [ref],
                    "required": True,
                },
                {
                    "obligation_id": "identity",
                    "kind": "referent_identity",
                    "semantic_refs": [ref],
                    "required": True,
                },
            ]
            if definition_refs:
                obligations.append({
                    "obligation_id": "definitions",
                    "kind": "definitions",
                    "semantic_refs": unique(definition_refs),
                    "required": True,
                })
            if prereqs:
                obligations.append({
                    "obligation_id": "prerequisites",
                    "kind": "pedagogical_prerequisites",
                    "semantic_refs": unique(prereqs),
                    "required": True,
                })
            obligations.append({
                "obligation_id": "core-assertions",
                "kind": "assertion_projection",
                "assertion_refs": assertion_ids,
                "required": True,
            })
            if proof_refs:
                obligations.append({
                    "obligation_id": "proofs",
                    "kind": "proof_links",
                    "semantic_refs": unique(proof_refs),
                    "required": True,
                })
            if source_refs:
                obligations.append({
                    "obligation_id": "provenance",
                    "kind": "source_provenance",
                    "source_refs": unique(source_refs),
                    "required": True,
                })

            page_specs.append({
                "page_spec_id": page_id,
                "math_course_id": course_id,
                "pathway_id": pathway_id,
                "subject": {
                    "ref": ref,
                    "label": label,
                    "classifications": classifications,
                },
                "path_context": {
                    "position": pos,
                    "previous_ref": prev_ref,
                    "next_ref": next_ref,
                },
                "selection": {
                    "assertion_refs": assertion_ids,
                    "assertion_refs_by_role": {k: unique(v) for k, v in sorted(roles.items())},
                    "definition_refs": unique(definition_refs),
                    "proof_refs": unique(proof_refs),
                    "pedagogical_prerequisite_refs": unique(prereqs),
                    "source_refs": unique(source_refs),
                },
                "communication_obligations": obligations,
                "rendering": {
                    "content_selection_owner": "UCKK Math University Projector",
                    "linguistic_realizer": "SemantiK Architect",
                    "mode": "compile_ahead_deterministic",
                    "language_rule": "render only against a RELEASED language/profile RuntimeSet",
                    "ai_runtime_required": False,
                },
            })

        pathways.append({
            "pathway_id": pathway_id,
            "kristal_path_id": slug,
            "code": code,
            "title": title,
            "description": (
                f"Projection pédagogique Kristal de « {first_label} » à « {last_label} ». "
                "Cette séquence guide l’apprentissage sans modifier les dépendances logiques du corpus."
            ),
            "course_ids": course_ids,
            "referent_sequence": sequence_refs,
        })

    statistics = index.get("statistics", {}) if isinstance(index.get("statistics"), dict) else {}
    projection = {
        "schema_version": SCHEMA_VERSION,
        "universe_id": "math",
        "name": "Univers-Cité des mathématiques",
        "generated_from": {
            "artifact": "mathkristal",
            "release": release,
            "state_id": state_id,
            "content_hash": state.get("content_hash"),
            "referent_registry_id": registry_id,
            "referent_profile_version": registry.get("profile_version"),
            "corpus_index_sha256": sha256_file(index_path),
            "pedagogical_paths_sha256": sha256_file(paths_path),
        },
        "policy": {
            "authority": "MathKristal is epistemic authority; this file is a rebuildable UCKK consumer projection.",
            "path_rule": str(pedagogical.get("policy") or "curated paths are projections"),
            "content_selection_owner": "UCKK Math University Projector",
            "linguistic_realizer": "SemantiK Architect",
            "moodle_role": "pedagogical runtime and transactional learning state",
            "human_state_preservation": "enrolments, grades, submissions, annotations and editorial overrides are never derived from Kristal and must not be overwritten by rebuilds",
        },
        "statistics": {
            "kristal_referents": int(statistics.get("referents", len(referents))),
            "kristal_assertions": int(statistics.get("assertions", len(assertions_raw))),
            "kristal_sources": int(statistics.get("sources", len(state.get("source_refs", [])) if isinstance(state.get("source_refs"), list) else 0)),
            "projected_pathways": len(pathways),
            "projected_courses": len(courses),
            "projected_page_specs": len(page_specs),
            "projected_unique_referents": len(projected_refs),
        },
        "pathways": pathways,
        "courses": courses,
        "referents": [
            {
                "ref": ref,
                "kind": item.get("kind"),
                "label": label_for(item, ref),
                "classifications": item.get("classifications", []),
            }
            for ref, item in sorted(projected_refs.items())
        ],
        "page_specs": page_specs,
        "semantik_architect": {
            "boundary": "articulation_not_content_selection",
            "request_source": "page_specs[].communication_obligations",
            "target_language_source": "request context",
            "target_locale_source": "request context",
            "capability_rule": "language/profile must be RELEASED for the selected immutable RuntimeSet",
            "runtime_policy": "compile ahead where possible; no LLM required in the canonical page-serving path",
        },
    }

    manifest_files = {
        item.get("path"): item for item in manifest.get("files", [])
        if isinstance(item, dict) and isinstance(item.get("path"), str)
    }
    lock = {
        "schema_version": LOCK_SCHEMA_VERSION,
        "artifact": "mathkristal",
        "release": release,
        "state_id": state_id,
        "content_hash": state.get("content_hash"),
        "referent_registry_id": registry_id,
        "artifact_status": state.get("artifact_status"),
        "grammar_contract": index.get("grammar_contract"),
        "source_store_integration": index.get("source_store_integration"),
        "pinned_files": {
            path: {
                "sha256": manifest_files.get(path, {}).get("sha256") or sha256_file(root / path),
                "bytes": manifest_files.get(path, {}).get("bytes") or (root / path).stat().st_size,
            }
            for path in [
                "corpus/index.json",
                "corpus/math.referent-registry.json",
                "corpus/math.structured-epistemic-state.json",
                "corpus/pedagogical-paths.json",
                "external-sources/catalog.json",
                "integration/source-store-contract.json",
            ]
        },
        "consumer": {
            "component": "local_uckk",
            "universe_id": "math",
            "projection_contract": "uckk.math-university-projection/1.0.0",
            "projection_file": "local/uckk/atlas/math_university_projection.json",
        },
    }
    return projection, lock


def label_for(referent: dict[str, Any] | None, fallback: str) -> str:
    if isinstance(referent, dict):
        labels = referent.get("labels")
        if isinstance(labels, list):
            for label in labels:
                if isinstance(label, dict) and label.get("lang") == "fr" and isinstance(label.get("text"), str):
                    return label["text"]
            for label in labels:
                if isinstance(label, dict) and isinstance(label.get("text"), str):
                    return label["text"]
    return fallback.rsplit(":", 1)[-1].replace("-", " ")


def write_json(path: Path, value: dict[str, Any]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    with path.open("w", encoding="utf-8", newline="\n") as fh:
        json.dump(value, fh, ensure_ascii=False, indent=2)
        fh.write("\n")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--mathkristal-root", required=True, type=Path)
    parser.add_argument("--output", required=True, type=Path)
    parser.add_argument("--lock-output", required=True, type=Path)
    parser.add_argument("--archive", type=Path, help="Optional distribution archive to hash into the lock")
    args = parser.parse_args()
    projection, lock = build_projection(args.mathkristal_root)
    if args.archive is not None:
        if not args.archive.is_file():
            raise FileNotFoundError(args.archive)
        lock["delivery_archive"] = {
            "filename": args.archive.name,
            "sha256": sha256_file(args.archive),
            "note": "Hash of the MathKristal distribution archive used to build this UCKK projection snapshot.",
        }
    write_json(args.output, projection)
    write_json(args.lock_output, lock)
    print(json.dumps({
        "projection": str(args.output),
        "lock": str(args.lock_output),
        "pathways": projection["statistics"]["projected_pathways"],
        "courses": projection["statistics"]["projected_courses"],
        "pages": projection["statistics"]["projected_page_specs"],
        "state_id": projection["generated_from"]["state_id"],
    }, ensure_ascii=False))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
