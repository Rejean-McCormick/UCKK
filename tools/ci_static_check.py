#!/usr/bin/env python3
"""Fast, dependency-free static sanity checks for the UCKK Moodle source tree.

This intentionally does NOT bootstrap Moodle, a database, Behat, Selenium, or a web
server. It catches low-cost syntax breakage that can easily be introduced during
rapid/AI-assisted editing.
"""
from __future__ import annotations

import argparse
import json
import py_compile
import shutil
import subprocess
import sys
from pathlib import Path

SKIP_DIRS = {'.git', 'node_modules', 'vendor', '.idea', '.vscode'}


def files_with(root: Path, suffix: str):
    for p in root.rglob(f'*{suffix}'):
        if p.is_file() and not any(part in SKIP_DIRS for part in p.parts):
            yield p


def run_one(cmd: list[str]) -> tuple[bool, str]:
    r = subprocess.run(cmd, stdout=subprocess.PIPE, stderr=subprocess.STDOUT, text=True)
    return r.returncode == 0, r.stdout.strip()


def main() -> int:
    ap = argparse.ArgumentParser()
    ap.add_argument('--allow-missing-tools', action='store_true', help='Warn instead of fail if php/node are unavailable (useful for local Windows validation).')
    args = ap.parse_args()
    root = Path(__file__).resolve().parents[1]
    errors: list[str] = []
    warnings: list[str] = []

    php_files = sorted(files_with(root, '.php'))
    php = shutil.which('php')
    if not php:
        msg = f'PHP CLI unavailable; {len(php_files)} PHP files were not linted.'
        (warnings if args.allow_missing_tools else errors).append(msg)
    else:
        for p in php_files:
            ok, out = run_one([php, '-l', str(p)])
            if not ok:
                errors.append(f'PHP syntax: {p.relative_to(root)}\n{out}')

    js_files = sorted(files_with(root, '.js'))
    node = shutil.which('node')
    if not node:
        msg = f'Node unavailable; {len(js_files)} JavaScript files were not syntax-checked.'
        (warnings if args.allow_missing_tools else errors).append(msg)
    else:
        for p in js_files:
            ok, out = run_one([node, '--check', str(p)])
            if not ok:
                errors.append(f'JavaScript syntax: {p.relative_to(root)}\n{out}')

    py_files = sorted(files_with(root, '.py'))
    for p in py_files:
        try:
            py_compile.compile(str(p), doraise=True)
        except Exception as exc:
            errors.append(f'Python syntax: {p.relative_to(root)}\n{exc}')

    json_files = sorted(files_with(root, '.json'))
    json_checked = 0
    json_php_wrappers = 0
    for p in json_files:
        try:
            raw = p.read_bytes()
            # Some historical Moodle data files use a .json name but are actually
            # PHP wrappers. They are covered by PHP linting, not JSON parsing.
            stripped = raw.lstrip(b'\xef\xbb\xbf\x00\t\r\n ')
            if stripped.startswith(b'<?php'):
                json_php_wrappers += 1
                continue
            if not (stripped.startswith(b'{') or stripped.startswith(b'[')):
                warnings.append(f'Skipped non-JSON .json file: {p.relative_to(root)}')
                continue
            text = raw.decode('utf-8-sig')
            json.loads(text)
            json_checked += 1
        except Exception as exc:
            errors.append(f'JSON parse: {p.relative_to(root)}\n{exc}')

    print('UCKK Moodle static sanity')
    print(f'  PHP syntax:        {len(php_files)} files' + (' (checked)' if php else ' (tool missing)'))
    print(f'  JavaScript syntax: {len(js_files)} files' + (' (checked)' if node else ' (tool missing)'))
    print(f'  Python syntax:     {len(py_files)} files')
    print(f'  JSON parsed:       {json_checked} files')
    if json_php_wrappers:
        print(f'  PHP-backed .json:  {json_php_wrappers} file(s), intentionally skipped as JSON')

    if warnings:
        print('\nWarnings:')
        for w in warnings:
            print(f'  - {w}')
    if errors:
        print('\nFAILED:')
        for e in errors:
            print(f'\n{e}')
        return 1
    print('\nOK: no syntax-level breakage detected.')
    return 0

if __name__ == '__main__':
    raise SystemExit(main())
