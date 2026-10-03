#!/usr/bin/env python3
"""Build the UCKK Kristal hierarchy from the canonical Atlas voie blueprints.

The generated hierarchy is intentionally a set of *stubs*: it reserves stable
Kristal identities and the parent/child/navigation topology without pretending
that the epistemic content has already been crystallized.

Ownership is non-overlapping:
- the university Kristal owns only the ordered voie topology;
- each voie Kristal owns only its ordered course topology and voie scope;
- each course Kristal owns only its course scope and glossary-key references;
- the glossary index is a derived navigation index, never a second knowledge canon.

The target portable contract is kristal_state/6.0 and the aligned Kristall
baseline is 7.0.0-draft.3.2. Canonical Kristal bytes live in the shared Kristal-Kollection repository. Médiathèque kOA catalogs hashes, versions and media/source relations by reference without duplicating those bytes.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import shutil
from collections import defaultdict
from pathlib import Path
from typing import Any

HIERARCHY_FORMAT = "uckk.kristal-hierarchy/1.0.0"
STUB_FORMAT = "uckk.kristal-stub/1.0.0"
GLOSSARY_FORMAT = "uckk.glossary-navigation-index/1.0.0"
PORTABLE_CONTRACT = "kristal_state/6.0"
KRISTALL_BASELINE = "7.0.0-draft.3.2"
MEDIATHEQUE_AUTHORITY = "Médiathèque UCKK"
ROOT_KRISTAL_ID = "urn:uckk:kristal:universite"


def load_json(path: Path) -> dict[str, Any]:
    value = json.loads(path.read_text(encoding="utf-8"))
    if not isinstance(value, dict):
        raise ValueError(f"Expected JSON object: {path}")
    return value


def dump_json(path: Path, value: Any) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(value, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def sha256(path: Path) -> str:
    return "sha256:" + hashlib.sha256(path.read_bytes()).hexdigest()


def voie_kristal_id(voie_id: str) -> str:
    return f"urn:uckk:kristal:voie:{voie_id}"


def course_kristal_id(course_id: str) -> str:
    return f"urn:uckk:kristal:cours:{course_id}"


def pointer_escape(value: str) -> str:
    return value.replace("~", "~0").replace("/", "~1")


def concept_items(course: dict[str, Any], voie_file: str, course_index: int) -> list[dict[str, Any]]:
    items: list[dict[str, Any]] = []
    master = course.get("concept_maitre")
    if isinstance(master, dict) and master.get("concept_id"):
        items.append({
            "key": str(master["concept_id"]),
            "label": master.get("nom"),
            "definition": master.get("definition_courte"),
            "role": "concept_maitre",
            "source_ref": {
                "path": f"local/uckk/atlas/voies/{voie_file}",
                "json_pointer": f"/cours_conceptuels/{course_index}/concept_maitre",
            },
        })
    for assoc_index, assoc in enumerate(course.get("concepts_associes") or []):
        if not isinstance(assoc, dict) or not assoc.get("concept_id"):
            continue
        items.append({
            "key": str(assoc["concept_id"]),
            "label": assoc.get("nom"),
            "definition": assoc.get("definition_courte"),
            "role": "concept_associe",
            "source_ref": {
                "path": f"local/uckk/atlas/voies/{voie_file}",
                "json_pointer": f"/cours_conceptuels/{course_index}/concepts_associes/{assoc_index}",
            },
        })
    return items


def common_stub(kind: str, kristal_id: str, title: str, parent: str | None) -> dict[str, Any]:
    return {
        "format": STUB_FORMAT,
        "status": "stub",
        "kind": kind,
        "kristal_id": kristal_id,
        "title": title,
        "parent_kristal_ref": parent,
        "target": {
            "portable_contract": PORTABLE_CONTRACT,
            "kristall_baseline": KRISTALL_BASELINE,
        },
        "mediatheque": {
            "authority": MEDIATHEQUE_AUTHORITY,
            "artifact_locator": None,
            "publication_status": "not_published",
            "storage_mode": "external_repository_reference",
            "repository_id": "Kristal-Kollection",
            "rule": "Canonical Kristal bytes live in Kristal-Kollection; Médiathèque catalogs references/hashes and UCKK keeps bindings/materializations.",
        },
        "build": {
            "mode": "successive_ai_assisted_construction",
            "stage": "identity_and_topology_reserved",
            "semantic_content_status": "not_crystallized",
        },
        "repository": {
            "id": "Kristal-Kollection",
            "ownership": "canonical_kristal_bytes",
        },
    }


def build(repo_root: Path, output_dir: Path) -> dict[str, Any]:
    atlas_root = repo_root / "local/uckk/atlas"
    manifest_path = atlas_root / "atlas_manifest.json"
    source_manifest = load_json(manifest_path)
    items = source_manifest.get("items")
    if not isinstance(items, list) or not items:
        raise ValueError("atlas_manifest.json has no items")

    if output_dir.exists():
        shutil.rmtree(output_dir)
    output_dir.mkdir(parents=True, exist_ok=True)

    hierarchy_entries: list[dict[str, Any]] = []
    glossary_occurrences: dict[str, list[dict[str, Any]]] = defaultdict(list)
    glossary_labels: dict[str, set[str]] = defaultdict(set)
    glossary_definitions: dict[str, set[str]] = defaultdict(set)

    ordered_voies: list[dict[str, Any]] = []
    total_courses = 0

    for voie_pos, manifest_item in enumerate(items, start=1):
        voie_file = str(manifest_item["file"])
        voie_path = atlas_root / "voies" / voie_file
        voie = load_json(voie_path)
        voie_id = str(voie["voie_id"])
        code = str(voie["code"])
        title = str(voie["nom"])
        vkid = voie_kristal_id(voie_id)
        voie_dir = output_dir / "voies" / code
        course_values = list(voie.get("cours_conceptuels") or [])
        course_values.sort(key=lambda c: int(c.get("ordre", 0)))
        total_courses += len(course_values)

        course_refs: list[dict[str, Any]] = []
        for idx, course in enumerate(course_values):
            course_id = str(course["cours_id"])
            ckid = course_kristal_id(course_id)
            course_dir = voie_dir / "cours" / course_id
            concepts = concept_items(course, voie_file, idx)
            glossary_keys = [c["key"] for c in concepts]
            previous_ref = course_kristal_id(str(course_values[idx - 1]["cours_id"])) if idx > 0 else None
            next_ref = course_kristal_id(str(course_values[idx + 1]["cours_id"])) if idx + 1 < len(course_values) else None
            stub = common_stub("course", ckid, str(course.get("nom") or course_id), vkid)
            stub.update({
                "course": {
                    "course_id": course_id,
                    "order": int(course.get("ordre", idx + 1)),
                    "voie_id": voie_id,
                    "voie_code": code,
                    "sequence": {"previous": previous_ref, "next": next_ref},
                },
                "source_blueprint": {
                    "path": f"local/uckk/atlas/voies/{voie_file}",
                    "json_pointer": f"/cours_conceptuels/{idx}",
                    "source_sha256": sha256(voie_path),
                },
                "scope": {
                    "owns": ["course_scope", "course_semantic_content_when_crystallized"],
                    "does_not_own": ["university_topology", "voie_topology", "other_course_content", "mediatheque_bytes"],
                },
                "glossary": {
                    "index_ref": "universities/uckk/glossary/index.json",
                    "keys": glossary_keys,
                    "rule": "Keys are navigation references; definitions are resolved through the glossary index/source blueprint until this course Kristal is crystallized.",
                },
                "univers_cite": {
                    "materialization_status": "not_materialized",
                    "rule": "Univers-Cité content is generated/interpreted from the released course Kristal; it is not the source of truth.",
                },
            })
            dump_json(course_dir / "kristal.stub.json", stub)

            course_ref = {
                "course_id": course_id,
                "order": stub["course"]["order"],
                "title": stub["title"],
                "kristal_ref": ckid,
                "path": f"universities/uckk/voies/{code}/cours/{course_id}/kristal.stub.json",
            }
            course_refs.append(course_ref)
            hierarchy_entries.append({"kind": "course", **course_ref, "parent_kristal_ref": vkid})

            for concept in concepts:
                key = concept["key"]
                if concept.get("label"):
                    glossary_labels[key].add(str(concept["label"]))
                if concept.get("definition"):
                    glossary_definitions[key].add(str(concept["definition"]))
                glossary_occurrences[key].append({
                    "course_id": course_id,
                    "course_kristal_ref": ckid,
                    "voie_id": voie_id,
                    "voie_kristal_ref": vkid,
                    "role": concept["role"],
                    "source_ref": concept["source_ref"],
                })

        previous_voie = voie_kristal_id(str(items[voie_pos - 2]["voie_id"])) if voie_pos > 1 else None
        next_voie = voie_kristal_id(str(items[voie_pos]["voie_id"])) if voie_pos < len(items) else None
        vstub = common_stub("voie", vkid, title, ROOT_KRISTAL_ID)
        vstub.update({
            "voie": {
                "voie_id": voie_id,
                "code": code,
                "order": voie_pos,
                "domain": voie.get("domaine_operatoire"),
                "target_level": voie.get("niveau_vise"),
                "sequence": {"previous": previous_voie, "next": next_voie},
            },
            "source_blueprint": {
                "path": f"local/uckk/atlas/voies/{voie_file}",
                "json_pointer": "",
                "source_sha256": sha256(voie_path),
            },
            "scope": {
                "owns": ["voie_scope", "ordered_course_topology"],
                "does_not_own": ["university_topology", "course_semantic_content", "mediatheque_bytes"],
            },
            "children": course_refs,
            "glossary": {
                "index_ref": "universities/uckk/glossary/index.json",
                "mode": "derived_navigation_index",
            },
        })
        dump_json(voie_dir / "kristal.stub.json", vstub)

        voie_ref = {
            "voie_id": voie_id,
            "code": code,
            "order": voie_pos,
            "title": title,
            "kristal_ref": vkid,
            "path": f"universities/uckk/voies/{code}/kristal.stub.json",
            "course_count": len(course_refs),
        }
        ordered_voies.append(voie_ref)
        hierarchy_entries.append({"kind": "voie", **voie_ref, "parent_kristal_ref": ROOT_KRISTAL_ID})

    root_stub = common_stub("university", ROOT_KRISTAL_ID, "Univers-Cité King Klown", None)
    root_stub.update({
        "university": {
            "university_id": "uckk",
            "ordered_voie_count": len(ordered_voies),
            "course_kristal_count": total_courses,
        },
        "source_blueprint": {
            "path": "local/uckk/atlas/atlas_manifest.json",
            "source_sha256": sha256(manifest_path),
        },
        "scope": {
            "owns": ["university_scope", "ordered_voie_topology", "global_glossary_navigation"],
            "does_not_own": ["voie_semantic_content", "course_semantic_content", "mediatheque_bytes"],
        },
        "children": ordered_voies,
        "glossary": {
            "index_ref": "universities/uckk/glossary/index.json",
            "mode": "derived_navigation_index",
        },
        "univers_cite": {
            "materialization_status": "not_materialized",
            "rule": "A released UCKK university Kristal is interpreted by AI into the Univers-Cité surface; Moodle remains transactional/runtime state.",
        },
    })
    dump_json(output_dir / "university" / "kristal.stub.json", root_stub)
    hierarchy_entries.insert(0, {
        "kind": "university",
        "kristal_ref": ROOT_KRISTAL_ID,
        "title": root_stub["title"],
        "path": "universities/uckk/university/kristal.stub.json",
        "parent_kristal_ref": None,
    })

    glossary_terms: list[dict[str, Any]] = []
    for key in sorted(glossary_occurrences):
        labels = sorted(glossary_labels.get(key, set()))
        definitions = sorted(glossary_definitions.get(key, set()))
        glossary_terms.append({
            "key": key,
            "labels": labels,
            "definition_status": "defined_in_blueprint" if definitions else "pending_definition",
            "definition_candidates": definitions,
            "occurrences": glossary_occurrences[key],
            "navigation_rule": "One glossary key may navigate to several course Kristals without duplicating semantic ownership.",
        })
    glossary = {
        "format": GLOSSARY_FORMAT,
        "status": "derived_navigation_index",
        "authority": "Not an epistemic canon; definitions point back to source blueprints until promoted into released Kristals.",
        "stats": {
            "keys": len(glossary_terms),
            "defined_in_blueprint": sum(1 for t in glossary_terms if t["definition_status"] == "defined_in_blueprint"),
            "pending_definition": sum(1 for t in glossary_terms if t["definition_status"] == "pending_definition"),
            "multi_course_keys": sum(1 for t in glossary_terms if len(t["occurrences"]) > 1),
        },
        "terms": glossary_terms,
    }
    dump_json(output_dir / "glossary" / "index.json", glossary)

    hierarchy = {
        "format": "kristal-kollection.university-hierarchy/1.0.0",
        "repository": {
            "id": "Kristal-Kollection",
            "canonical_subtree": "universities/uckk",
            "github": "https://github.com/Rejean-McCormick/Kristal-Kollection",
        },
        "status": "stub_hierarchy",
        "bootstrap": {
            "mode": "atlas_to_kristal_bootstrap_only",
            "source": "local/uckk/atlas/atlas_manifest.json + local/uckk/atlas/voies/*.json",
            "authority_after_release": "Kristal/Kristall",
            "materialization_rule": "After a Kristal is released in Kristal-Kollection and cataloged by Médiathèque, Atlas/Univers-Cité/Moodle are projections/materializations rather than independent authorities."
        },
        "source": {
            "atlas_manifest": "local/uckk/atlas/atlas_manifest.json",
            "atlas_manifest_sha256": sha256(manifest_path),
        },
        "alignment": {
            "portable_contract": PORTABLE_CONTRACT,
            "kristall_baseline": KRISTALL_BASELINE,
            "mediatheque_authority": MEDIATHEQUE_AUTHORITY,
        },
        "invariants": [
            "Exactly one university Kristal roots this hierarchy.",
            "Each Atlas voie has exactly one voie Kristal.",
            "Each conceptual course has exactly one course Kristal.",
            "Parent Kristals reference child Kristals but never embed their semantic content.",
            "The glossary is a navigation index over stable keys, not a second semantic canon.",
            "Canonical Kristal bytes are stored once in Kristal-Kollection; Médiathèque and UCKK reference them.",
            "Univers-Cité is a materialization/interpreted surface of released Kristals, not their source of truth.",
        ],
        "counts": {
            "university_kristals": 1,
            "voie_kristals": len(ordered_voies),
            "course_kristals": total_courses,
            "total_kristals": 1 + len(ordered_voies) + total_courses,
        },
        "root_kristal_ref": ROOT_KRISTAL_ID,
        "entries": hierarchy_entries,
    }
    dump_json(output_dir / "manifest.json", hierarchy)

    tree_lines = [
        "# UCKK Kristal tree",
        "",
        f"- **{ROOT_KRISTAL_ID}** — Univers-Cité King Klown",
    ]
    for voie in ordered_voies:
        tree_lines.append(f"  - **{voie['code']}** — {voie['title']} — `{voie['kristal_ref']}`")
        vstub = load_json(output_dir / "voies" / voie["code"] / "kristal.stub.json")
        for course in vstub["children"]:
            tree_lines.append(f"    - {course['course_id']} — {course['title']} — `{course['kristal_ref']}`")
    (output_dir / "TREE.md").write_text("\n".join(tree_lines) + "\n", encoding="utf-8")

    readme = f'''# UCKK Kristal hierarchy

This directory reserves the canonical curriculum Kristal identities for UCKK.

```text
1 UCKK Kristal
  -> {len(ordered_voies)} voie Kristals
      -> {total_courses} course Kristals
```

Total: **{1 + len(ordered_voies) + total_courses} Kristal identities**.

## No-overlap rule

- `university/kristal.stub.json` owns only the university scope and ordered voie topology.
- `voies/<CODE>/kristal.stub.json` owns only the voie scope and ordered course topology.
- `voies/<CODE>/cours/<COURSE>/kristal.stub.json` owns only that course scope.
- Parent files reference children; they do not copy child semantic content.
- `glossary/index.json` is a derived navigation index. It is not a second knowledge canon.

## Bootstrap, then authority inversion

The current `atlas/voies/*.json` files are used only to bootstrap stable Kristal identities and topology. After a Kristal is AI-built, validated and published, **Kristal/Kristall becomes the source of truth**. Atlas/Univers-Cité/Moodle then become projections/materializations of those released Kristals.

## Lifecycle

```text
Atlas blueprint (bootstrap only)
  -> Kristal stub (this tree)
  -> AI-assisted Kristal construction
  -> validation / crystallization
  -> canonical Kristal version in Kristal-Kollection
  -> Médiathèque catalogs reference/hash/media links
  -> Univers-Cité AI interpretation/materialization
  -> Moodle runtime state
```

The stubs target **{PORTABLE_CONTRACT}** and Kristall **{KRISTALL_BASELINE}**, but are intentionally not presented as valid released `kristal_state` artifacts before their semantic build is complete.

## Entry points

- `manifest.json` — machine-readable index of all Kristals.
- `TREE.md` — human-readable navigation tree.
- `glossary/index.json` — transversal glossary/navigation keys derived from the Atlas blueprints.
- `glossary/README.md` — glossary ownership/definition rules.
- `university/kristal.stub.json` — root UCKK Kristal.

Regenerate with:

```bash
python tools/uckk-ops/kristal/build_atlas_kristal_hierarchy.py
```
'''
    (output_dir / "README.md").write_text(readme, encoding="utf-8")

    glossary_readme = f'''# UCKK glossary navigation index

`index.json` is a **derived navigation index**, not an epistemic canon.

- Stable `concept_id` values are navigation keys.
- A key may point to several course Kristals without duplicating its meaning.
- Definitions are copied only as candidates/source pointers from the current Atlas bootstrap blueprints.
- Missing definitions stay `pending_definition`; the builder never invents one.
- Once released Kristals contain the canonical definitions, this index should be rebuilt from those Kristals instead of from Atlas JSON.

Current bootstrap inventory: **{len(glossary_terms)} keys**, **{sum(1 for t in glossary_terms if t["definition_status"] == "defined_in_blueprint")} with an explicit blueprint definition**, **{sum(1 for t in glossary_terms if t["definition_status"] == "pending_definition")} pending explicit definition**.
'''
    (output_dir / "glossary" / "README.md").write_text(glossary_readme, encoding="utf-8")
    return hierarchy



def write_uckk_bindings(repo_root: Path, output_dir: Path, hierarchy: dict[str, Any]) -> None:
    entries = []
    for item in hierarchy.get("entries") or []:
        path_text = str(item.get("path") or "")
        prefix = "universities/uckk/"
        local_rel = path_text[len(prefix):] if path_text.startswith(prefix) else path_text
        file_path = output_dir / local_rel
        digest = hashlib.sha256(file_path.read_bytes()).hexdigest()
        row = dict(item)
        row["repository_relative_path"] = path_text
        row["sha256"] = digest
        row["bytes"] = file_path.stat().st_size
        entries.append(row)
    binding = {
        "format": "uckk.kristal-bindings/1.0.0",
        "authority": "Bindings only. Canonical Kristal bytes are owned by Kristal-Kollection.",
        "repository": {
            "id": "Kristal-Kollection",
            "root_env": "KRISTAL_KOLLECTION_ROOT",
            "default_windows_root": r"C:\mycode\Kristal\Kristal-Kollection",
            "github": "https://github.com/Rejean-McCormick/Kristal-Kollection",
            "uckk_subtree": "universities/uckk",
        },
        "counts": hierarchy.get("counts") or {},
        "root_kristal_ref": hierarchy.get("root_kristal_ref"),
        "entries": entries,
    }
    binding_dir = repo_root / "local/uckk/atlas/kristal-bindings"
    dump_json(binding_dir / "manifest.json", binding)
    dump_json(binding_dir / "config.json", {
        "format": "uckk.kristal-repository-config/1.0.0",
        "repository_id": "Kristal-Kollection",
        "root_env": "KRISTAL_KOLLECTION_ROOT",
        "default_windows_root": r"C:\mycode\Kristal\Kristal-Kollection",
        "manifest": "manifest.json",
        "mode": "external_repository_reference",
    })

def canonical_bytes(root: Path) -> dict[str, bytes]:
    return {
        p.relative_to(root).as_posix(): p.read_bytes()
        for p in sorted(root.rglob("*"))
        if p.is_file()
    }


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--repo-root", default=None)
    parser.add_argument("--output", default=None, help="Canonical UCKK subtree; defaults to KRISTAL_KOLLECTION_ROOT/universities/uckk")
    parser.add_argument("--kristal-root", default=None, help="Kristal-Kollection root (or KRISTAL_KOLLECTION_ROOT)")
    parser.add_argument("--check", action="store_true", help="regenerate in a temporary directory and compare")
    args = parser.parse_args()

    script = Path(__file__).resolve()
    repo_root = Path(args.repo_root).resolve() if args.repo_root else script.parents[3]
    if args.output:
        output_arg = Path(args.output)
        output = output_arg.resolve() if output_arg.is_absolute() else (repo_root / output_arg).resolve()
    else:
        import os
        root_value = args.kristal_root or os.environ.get("KRISTAL_KOLLECTION_ROOT") or r"C:\mycode\Kristal\Kristal-Kollection"
        output = (Path(root_value).expanduser() / "universities" / "uckk").resolve()

    if args.check:
        import tempfile
        with tempfile.TemporaryDirectory(prefix="uckk-kristal-check-") as tmp:
            candidate = Path(tmp) / "kristals"
            build(repo_root, candidate)
            actual = canonical_bytes(output)
            expected = canonical_bytes(candidate)
            if actual != expected:
                missing = sorted(set(expected) - set(actual))
                extra = sorted(set(actual) - set(expected))
                changed = sorted(k for k in set(actual) & set(expected) if actual[k] != expected[k])
                print(json.dumps({"ok": False, "missing": missing, "extra": extra, "changed": changed}, indent=2))
                return 1
            print(json.dumps({"ok": True, "files": len(actual)}, indent=2))
            return 0

    result = build(repo_root, output)
    write_uckk_bindings(repo_root, output, result)
    print(json.dumps({"ok": True, "output": str(output), "bindings": str(repo_root / "local/uckk/atlas/kristal-bindings/manifest.json"), "counts": result["counts"]}, ensure_ascii=False, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
