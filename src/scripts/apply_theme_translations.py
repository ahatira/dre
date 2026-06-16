#!/usr/bin/env python3
"""Apply translation catalog JSON to ps_theme .po files."""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

from regenerate_module_po import (
    EXPECTED_LANGS,
    PoEntry,
    collect_source_strings,
    generate_po,
    load_existing_translations,
    parse_po_file,
    ROOT,
)

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"
THEME_DIR = ROOT / "src/web/themes/custom/ps_theme"
THEME_NAME = "ps_theme"


def load_catalog() -> tuple[dict[str, dict[str, str]], dict[str, dict[str, list[str]]]]:
    path = CATALOG_DIR / f"{THEME_NAME}.json"
    raw = json.loads(path.read_text(encoding="utf-8"))
    singular: dict[str, dict[str, str]] = {}
    plural: dict[str, dict[str, list[str]]] = {}
    for msgid, value in raw.items():
        if isinstance(value.get("de"), list):
            plural[msgid] = {lang: value[lang] for lang in EXPECTED_LANGS if lang in value}
        else:
            singular[msgid] = {lang: value[lang] for lang in EXPECTED_LANGS if lang in value}
    return singular, plural


def apply_theme(dry_run: bool = False) -> dict:
    translations_dir = THEME_DIR / "translations"
    source_strings, source_plurals = collect_source_strings(THEME_DIR)
    catalog_singular, catalog_plural = load_catalog()
    msgids = sorted(set(source_strings) | set(catalog_singular))

    stats: dict[str, dict[str, int]] = {}

    for lang in EXPECTED_LANGS:
        translations = {
            msgid: catalog_singular[msgid].get(lang, msgid)
            for msgid in msgids
            if msgid in catalog_singular
        }

        plural_translations: dict[str, PoEntry] = {}
        for msgid, plural_msgid in source_plurals.items():
            if msgid not in catalog_plural:
                continue
            values = catalog_plural[msgid].get(lang, ["", ""])
            plural_translations[msgid] = PoEntry(
                msgid=msgid,
                msgid_plural=plural_msgid,
                msgstr_plural=values,
            )

        content = generate_po(
            THEME_NAME,
            lang,
            msgids,
            source_plurals,
            translations,
            plural_translations,
        )

        targets = [
            translations_dir / f"{THEME_NAME}.{lang}.po",
            translations_dir / f"{lang}.po" if lang in ("fr", "es") else None,
        ]
        if not dry_run:
            translations_dir.mkdir(parents=True, exist_ok=True)
            for target in targets:
                if target is not None:
                    target.write_text(content, encoding="utf-8")

        singular_count = sum(1 for msgid in msgids if translations.get(msgid, "").strip())
        stats[lang] = {"singular": singular_count, "total": len(msgids)}

    return {"strings": len(msgids), "stats": stats}


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()
    result = apply_theme(dry_run=args.dry_run)
    print(f"{THEME_NAME}: {result['strings']} strings")
    for lang, s in result["stats"].items():
        print(f"  {lang}: {s['singular']}/{s['total']}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
