#!/usr/bin/env python3
"""Merge missing source strings into translation catalog from existing .po files."""

from __future__ import annotations

import json
import sys
from pathlib import Path

from regenerate_module_po import (
    CUSTOM_MODULES,
    EXPECTED_LANGS,
    collect_source_strings,
    parse_po_file,
)

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"


def po_translations(module: str) -> dict[str, dict[str, str]]:
    trans_dir = CUSTOM_MODULES / module / "translations"
    per_lang: dict[str, dict[str, str]] = {lang: {} for lang in EXPECTED_LANGS}
    for lang in EXPECTED_LANGS:
        for candidate in (trans_dir / f"{module}.{lang}.po", trans_dir / f"{lang}.po"):
            if not candidate.exists():
                continue
            singular, plural = parse_po_file(candidate)
            for msgid, entry in singular.items():
                if entry.msgstr.strip():
                    per_lang[lang][msgid] = entry.msgstr
            for msgid, entry in plural.items():
                if any(x.strip() for x in entry.msgstr_plural):
                    per_lang[lang][msgid] = entry.msgstr_plural[0]
            break
    return per_lang


def merge_catalog(module: str) -> int:
    catalog_path = CATALOG_DIR / f"{module}.json"
    catalog = {}
    if catalog_path.exists():
        catalog = json.loads(catalog_path.read_text(encoding="utf-8"))

    source, _ = collect_source_strings(CUSTOM_MODULES / module)
    po = po_translations(module)
    added = 0

    for msgid in sorted(source):
        if msgid in catalog:
            continue
        entry: dict[str, str] = {}
        for lang in EXPECTED_LANGS:
            if msgid in po[lang]:
                entry[lang] = po[lang][msgid]
            elif lang == "fr" and msgid in po.get("fr", {}):
                entry[lang] = po["fr"][msgid]
            elif "en" in po and msgid in po["en"]:
                entry[lang] = po["en"][msgid]
            else:
                # Fallback: use FR if any, else English msgid.
                entry[lang] = po["fr"].get(msgid) or msgid
        catalog[msgid] = entry
        added += 1

    catalog_path.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
    return added


def main() -> int:
    modules = sys.argv[1:] or [
        "ps_feature",
        "ps_dictionary",
        "ps_surface",
        "ps_media",
    ]
    for module in modules:
        added = merge_catalog(module)
        print(f"{module}: added {added} catalog entries")
    return 0


if __name__ == "__main__":
    sys.exit(main())
