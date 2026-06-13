#!/usr/bin/env python3
"""Merge module .translations.py files into catalog JSON and regenerate PO."""

from __future__ import annotations

import importlib.util
import json
import subprocess
import sys
from pathlib import Path

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"
SCRIPTS_DIR = Path(__file__).resolve().parent


def merge(module: str) -> int:
    trans_path = CATALOG_DIR / f"{module}.translations.py"
    cat_path = CATALOG_DIR / f"{module}.json"
    if not trans_path.exists() or not cat_path.exists():
        print(f"skip {module}: missing files")
        return 0
    spec = importlib.util.spec_from_file_location(f"{module}_tr", trans_path)
    mod = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(mod)
    catalog = json.loads(cat_path.read_text(encoding="utf-8"))
    applied = 0
    for msgid, langs in mod.TRANSLATIONS.items():
        if msgid not in catalog:
            print(f"  warn: {msgid[:60]} not in catalog")
            continue
        for lang, trans in langs.items():
            catalog[msgid][lang] = trans
        applied += 1
    cat_path.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
    fr_same = sum(1 for k, v in catalog.items() if v.get("fr") == k)
    print(f"{module}: merged {applied}, FR=EN {fr_same}")
    return applied


def regenerate(module: str) -> None:
    for script in ("regenerate_module_po.py", "apply_module_translations.py"):
        subprocess.run([sys.executable, str(SCRIPTS_DIR / script), module], check=True)
    cfg = CATALOG_DIR / f"{module}.config.json"
    if cfg.exists():
        subprocess.run([sys.executable, str(SCRIPTS_DIR / "apply_config_translations.py"), module], check=True)


if __name__ == "__main__":
    for mod in sys.argv[1:]:
        merge(mod)
        regenerate(mod)
