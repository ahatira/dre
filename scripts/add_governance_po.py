#!/usr/bin/env python3
"""Append missing import governance strings to custom module .po files."""

from __future__ import annotations

import re
import sys
from pathlib import Path

SCRIPT_DIR = Path(__file__).resolve().parent
if str(SCRIPT_DIR) not in sys.path:
    sys.path.insert(0, str(SCRIPT_DIR))

ROOT = Path(__file__).resolve().parents[1] / "src" / "web" / "modules" / "custom"
LANGS = ("fr", "de", "es", "it", "nl", "pl", "lb")
TRAIT_MODULES = ("ps_feature", "ps_offer", "ps_agent", "ps_media")

GOVERNANCE_FILES = {
    "ps_core": (
        "ps_core/src/Form/GovernanceGlobalSettingsForm.php",
        "ps_core/src/Controller/GovernanceAdminOverviewController.php",
    ),
    "ps_feature": ("ps_feature/src/Form/FeatureCatalogueGovernanceSettingsForm.php",),
    "ps_offer": ("ps_offer/src/Form/OfferImportGovernanceSettingsForm.php",),
    "ps_agent": ("ps_agent/src/Form/AgentImportGovernanceSettingsForm.php",),
    "ps_media": ("ps_media/src/Form/MediaImportGovernanceSettingsForm.php",),
    "ps_dictionary": ("ps_dictionary/src/Form/DictionaryImportGovernanceSettingsForm.php",),
}


def extract_t_strings(path: Path) -> set[str]:
    text = path.read_text(encoding="utf-8")
    strings: set[str] = set()
    for match in re.finditer(
        r"\$this->t\(\s*'((?:\\'|[^'])*)'|\$this->t\(\s*\"((?:\\\"|[^\"])*)\"",
        text,
    ):
        raw = match.group(1) or match.group(2)
        strings.add(raw.replace("\\'", "'").replace('\\"', '"'))
    return strings


def unquote_po(value: str) -> str:
    return value.replace("\\n", "\n").replace('\\"', '"').replace("\\\\", "\\")


def quote_po(value: str) -> str:
    return value.replace("\\", "\\\\").replace('"', '\\"').replace("\n", "\\n")


def parse_po(path: Path) -> dict[str, str]:
    if not path.exists():
        return {}
    entries: dict[str, str] = {}
    msgid: str | None = None
    msgstr: str | None = None
    mode: str | None = None
    for line in path.read_text(encoding="utf-8").splitlines():
        if line.startswith("msgid "):
            if msgid is not None and msgstr is not None:
                entries[msgid] = msgstr
            msgid = unquote_po(line[7:-1])
            msgstr = ""
            mode = "id"
        elif line.startswith("msgstr "):
            msgstr = unquote_po(line[8:-1])
            mode = "str"
        elif line.startswith('"') and mode:
            chunk = unquote_po(line[1:-1])
            if mode == "id":
                msgid += chunk
            else:
                msgstr += chunk
    if msgid is not None and msgstr is not None:
        entries[msgid] = msgstr
    return entries


def append_po_entries(path: Path, entries: dict[str, str]) -> int:
    if not entries:
        return 0
    text = path.read_text(encoding="utf-8")
    if not text.endswith("\n"):
        text += "\n"
    blocks = []
    for msgid, msgstr in entries.items():
        blocks.append(f'\nmsgid "{quote_po(msgid)}"\nmsgstr "{quote_po(msgstr)}"\n')
    path.write_text(text + "".join(blocks), encoding="utf-8")
    return len(entries)


def required_strings(module: str) -> set[str]:
    strings: set[str] = set()
    for rel in GOVERNANCE_FILES[module]:
        strings |= extract_t_strings(ROOT / rel)
    if module in TRAIT_MODULES:
        strings |= extract_t_strings(ROOT / "ps_core/src/Form/SnapshotSyncFieldsFormTrait.php")
    return strings


def load_manual_translations() -> dict[str, dict[str, str]]:
    from governance_po_data import MANUAL  # noqa: PLC0415

    return MANUAL


def main() -> int:
    manual = load_manual_translations()
    added_total = 0

    # Pass 1: complete ps_core catalogs first so domain modules can inherit.
    for lang in LANGS:
        po_path = ROOT / "ps_core" / "translations" / f"ps_core.{lang}.po"
        existing = parse_po(po_path)
        to_add = {
            msgid: manual[msgid][lang]
            for msgid in required_strings("ps_core")
            if msgid not in existing or not existing[msgid].strip()
            if msgid in manual and manual[msgid].get(lang, "").strip()
        }
        added_total += append_po_entries(po_path, to_add)

    core_po = {
        lang: parse_po(ROOT / "ps_core" / "translations" / f"ps_core.{lang}.po")
        for lang in LANGS
    }

    for module in GOVERNANCE_FILES:
        if module == "ps_core":
            continue
        needed = required_strings(module)
        for lang in LANGS:
            po_path = ROOT / module / "translations" / f"{module}.{lang}.po"
            if not po_path.exists():
                print(f"skip missing file: {po_path}", file=sys.stderr)
                continue
            existing = parse_po(po_path)
            to_add: dict[str, str] = {}
            for msgid in sorted(needed):
                if msgid in existing and existing[msgid].strip():
                    continue
                if msgid in core_po[lang] and core_po[lang][msgid].strip():
                    to_add[msgid] = core_po[lang][msgid]
                elif msgid in manual and lang in manual[msgid] and manual[msgid][lang].strip():
                    to_add[msgid] = manual[msgid][lang]
                else:
                    print(f"missing translation: [{module}.{lang}] {msgid[:80]}", file=sys.stderr)
            count = append_po_entries(po_path, to_add)
            added_total += count
            if count:
                print(f"{module}.{lang}.po: +{count}")

    print(f"done: {added_total} entries added")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
