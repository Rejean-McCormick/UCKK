# UCKK Kristal tools

UCKK owns **Atlas blueprints and bindings**, not a second copy of Kristal bytes.

Canonical UCKK Kristals live under `%KRISTAL_KOLLECTION_ROOT%\universities\uckk\` (default `C:\mycode\Kristal\Kristal-Kollection`).

```bash
python tools/uckk-ops/kristal/build_atlas_kristal_hierarchy.py
python tools/uckk-ops/kristal/validate_atlas_kristal_hierarchy.py
```

The builder writes directly to Kristal-Kollection and refreshes `local/uckk/atlas/kristal-bindings/`. Do **not** recreate `local/uckk/atlas/kristals/`.

Après toute modification directe d'un Kristal canonique, synchroniser les hashes/paths UCKK + Médiathèque avec :

```bash
python tools/uckk-ops/kristal/sync_kristal_kollection_bindings.py
```
