# MathKristal → UCKK projection builder

`build_math_university_projection.py` compiles a pinned MathKristal directory into the read-only/rebuildable UCKK Math University projection.

Example:

```bash
python tools/uckk-ops/kristal/build_math_university_projection.py \
  --mathkristal-root /path/to/mathkristal-v1.6.0 \
  --output local/uckk/atlas/math_university_projection.json \
  --lock-output local/uckk/atlas/math_kristal_lock.json \
  --archive /path/to/mathkristal-v1.6.0.zip
```

The builder performs content selection/topology only. SemantiK Architect is downstream and may articulate the generated communication obligations in a released language/profile RuntimeSet.

Do not run this builder as a request-time web operation. Build and validate projections ahead of materialization.
