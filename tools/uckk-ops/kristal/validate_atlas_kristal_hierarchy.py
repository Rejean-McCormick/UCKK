#!/usr/bin/env python3
"""Validate UCKK bindings against the canonical Kristal-Kollection hierarchy."""
from __future__ import annotations
import hashlib, json, os
from pathlib import Path
ROOT = Path(__file__).resolve().parents[3]
BINDINGS = ROOT / "local/uckk/atlas/kristal-bindings/manifest.json"
def load(path: Path): return json.loads(path.read_text(encoding="utf-8"))
def main() -> int:
    errors=[]; binding=load(BINDINGS); repo=binding.get("repository") or {}
    kroot=Path(os.environ.get(str(repo.get("root_env") or "KRISTAL_KOLLECTION_ROOT")) or str(repo.get("default_windows_root") or r"C:\mycode\Kristal\Kristal-Kollection"))
    entries=binding.get("entries") or []
    if len(entries)!=111: errors.append(f"binding entry count != 111: {len(entries)}")
    ids=[e.get("kristal_ref") for e in entries]
    if len(ids)!=len(set(ids)): errors.append("duplicate kristal_ref values")
    by_id={e.get("kristal_ref"):e for e in entries}
    for e in entries:
        parent=e.get("parent_kristal_ref")
        if parent and parent not in by_id: errors.append(f"unresolved parent {parent}")
        rel=str(e.get("repository_relative_path") or ""); path=kroot / Path(rel)
        if not path.is_file(): errors.append(f"missing canonical Kristal: {rel}"); continue
        actual=hashlib.sha256(path.read_bytes()).hexdigest()
        if actual!=str(e.get("sha256") or "").lower(): errors.append(f"hash mismatch: {rel}")
        obj=load(path)
        if obj.get("kristal_id")!=e.get("kristal_ref"): errors.append(f"id mismatch: {rel}")
        if (obj.get("target") or {}).get("portable_contract")!="kristal_state/6.0": errors.append(f"portable contract mismatch: {rel}")
    if errors: print(json.dumps({"ok":False,"errors":errors},ensure_ascii=False,indent=2)); return 1
    print(json.dumps({"ok":True,"kristals":len(entries),"root":str(kroot)},ensure_ascii=False,indent=2)); return 0
if __name__ == "__main__": raise SystemExit(main())
