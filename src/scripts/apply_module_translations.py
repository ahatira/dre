#!/usr/bin/env python3
"""Apply translation catalog JSON files to module .po files."""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path

from regenerate_module_po import (
    EXPECTED_LANGS,
    CUSTOM_MODULES,
    PoEntry,
    collect_source_strings,
    generate_po,
    load_existing_translations,
    parse_po_file,
)

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"


def load_catalog(module_name: str) -> tuple[dict[str, dict[str, str]], dict[str, dict[str, list[str]]]]:
    path = CATALOG_DIR / f"{module_name}.json"
    if not path.exists():
        raise FileNotFoundError(path)

    raw = json.loads(path.read_text(encoding="utf-8"))
    singular: dict[str, dict[str, str]] = {}
    plural: dict[str, dict[str, list[str]]] = {}

    for msgid, value in raw.items():
        if isinstance(value.get("de"), list):
            plural[msgid] = {
                lang: value[lang]
                for lang in EXPECTED_LANGS
                if lang in value and isinstance(value[lang], list)
            }
        else:
            singular[msgid] = {
                lang: value[lang]
                for lang in EXPECTED_LANGS
                if lang in value
            }

    return singular, plural


def apply_module(module_name: str, dry_run: bool = False) -> dict:
    module_dir = CUSTOM_MODULES / module_name
    translations_dir = module_dir / "translations"
    source_strings, source_plurals = collect_source_strings(module_dir)
    msgids = sorted(source_strings)

    catalog_singular, catalog_plural = load_catalog(module_name)

    missing_catalog = sorted(set(msgids) - set(catalog_singular) - set(catalog_plural))
    if missing_catalog:
        raise ValueError(
            f"{module_name}: catalog missing {len(missing_catalog)} msgids, e.g. {missing_catalog[:3]}"
        )

    stats: dict[str, dict[str, int]] = {}

    for lang in EXPECTED_LANGS:
        translations = {
            msgid: catalog_singular[msgid][lang]
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
            module_name,
            lang,
            msgids,
            source_plurals,
            translations,
            plural_translations,
        )

        target = translations_dir / f"{module_name}.{lang}.po"
        if not dry_run:
            target.write_text(content, encoding="utf-8")

        singular_count = sum(1 for msgid in msgids if translations.get(msgid, "").strip())
        plural_count = len(plural_translations)
        stats[lang] = {
            "singular": singular_count,
            "plural": plural_count,
            "total": len(msgids),
        }

    return {
        "module": module_name,
        "strings": len(msgids),
        "stats": stats,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "modules",
        nargs="*",
        default=["ps_dictionary", "ps_surface", "ps_media", "ps_feature"],
    )
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    for module in args.modules:
        result = apply_module(module, dry_run=args.dry_run)
        print(f"{result['module']}: {result['strings']} strings")
        for lang, s in result["stats"].items():
            print(f"  {lang}: {s['singular']}/{s['total']} singular, {s['plural']} plural")

    return 0


if __name__ == "__main__":
    sys.exit(main())
