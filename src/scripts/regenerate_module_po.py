#!/usr/bin/env python3
"""Regenerate Drupal-compatible .po files for custom modules."""

from __future__ import annotations

import argparse
import re
import sys
from dataclasses import dataclass, field
from datetime import datetime, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
CUSTOM_MODULES = ROOT / "src/web/modules/custom"
EXPECTED_LANGS = ("de", "es", "fr", "it", "lb", "nl", "pl")

PHP_PATTERNS = [
    re.compile(r"(?:\bt|\$this->t)\(\s*'((?:\\'|[^'])*)'"),
    re.compile(r'(?:\bt|\$this->t)\(\s*"((?:\\"|[^"])*)"'),
    re.compile(r"new\s+TranslatableMarkup\(\s*'((?:\\'|[^'])*)'"),
    re.compile(r'new\s+TranslatableMarkup\(\s*"((?:\\"|[^"])*)"'),
    re.compile(r"@Translation\(\s*'((?:\\'|[^'])*)'"),
    re.compile(r'@Translation\(\s*"((?:\\"|[^"])*)"'),
]

JS_PATTERNS = [
    re.compile(r"Drupal\.t\(\s*'((?:\\'|[^'])*)'"),
    re.compile(r'Drupal\.t\(\s*"((?:\\"|[^"])*)"'),
    re.compile(
        r"Drupal\.formatPlural\([^,]+,\s*'((?:\\'|[^'])*)'\s*,\s*'((?:\\'|[^'])*)'"
    ),
    re.compile(
        r'Drupal\.formatPlural\([^,]+,\s*"((?:\\"|[^"])*)"\s*,\s*"((?:\\"|[^"])*)"'
    ),
]

TWIG_PATTERNS = [
    re.compile(r"\{\{\s*'((?:\\'|[^'])*)'\s*\|\s*t"),
    re.compile(r'\{\{\s*"((?:\\"|[^"])*)"\s*\|\s*t'),
    re.compile(r"\{%\s*trans\s*%\}(.*?)\{%\s*endtrans\s*%\}", re.DOTALL),
]

YAML_KEYS = {"title", "label", "description", "admin_label", "category"}
YAML_STRING = re.compile(r"^(\s*)({}):\s*['\"](.+?)['\"]\s*$".format("|".join(YAML_KEYS)))

SKIP_DIRS = {"tests", "vendor", "node_modules"}
SKIP_EXTENSIONS = {".md", ".json", ".lock", ".sql", ".sh", ".png", ".jpg", ".svg", ".css", ".map"}

PLURAL_FORMS = {
    "de": "nplurals=2; plural=(n != 1);",
    "es": "nplurals=2; plural=(n != 1);",
    "fr": "nplurals=2; plural=(n > 1);",
    "it": "nplurals=2; plural=(n != 1);",
    "lb": "nplurals=2; plural=(n != 1);",
    "nl": "nplurals=2; plural=(n != 1);",
    "pl": "nplurals=2; plural=(n != 1);",
}


@dataclass
class PoEntry:
    msgid: str
    msgstr: str = ""
    msgid_plural: str = ""
    msgstr_plural: list[str] = field(default_factory=list)


def unescape(s: str) -> str:
    return s.replace("\\'", "'").replace('\\"', '"').replace("\\n", "\n")


def normalize_twig_trans(block: str) -> str:
    lines = [line.strip() for line in block.strip().splitlines() if line.strip()]
    return " ".join(lines)


def extract_from_file(path: Path) -> tuple[set[str], dict[str, str]]:
    strings: set[str] = set()
    plurals: dict[str, str] = {}
    try:
        content = path.read_text(encoding="utf-8")
    except (OSError, UnicodeDecodeError):
        return strings, plurals

    suffix = path.suffix.lower()
    if suffix == ".php":
        for pattern in PHP_PATTERNS:
            for match in pattern.finditer(content):
                strings.add(unescape(match.group(1)))
    elif suffix == ".js":
        for pattern in JS_PATTERNS[:2]:
            for match in pattern.finditer(content):
                strings.add(unescape(match.group(1)))
        for pattern in JS_PATTERNS[2:]:
            for match in pattern.finditer(content):
                singular = unescape(match.group(1))
                plural = unescape(match.group(2))
                strings.add(singular)
                plurals[singular] = plural
    elif suffix in {".twig", ".html.twig"} or ".twig" in path.name:
        for pattern in TWIG_PATTERNS[:2]:
            for match in pattern.finditer(content):
                strings.add(unescape(match.group(1)))
        for match in TWIG_PATTERNS[2].finditer(content):
            strings.add(normalize_twig_trans(match.group(1)))
    elif suffix in {".yml", ".yaml"}:
        for line in content.splitlines():
            match = YAML_STRING.match(line)
            if match:
                value = match.group(3)
                if value and not value.startswith("@"):
                    strings.add(value)

    clean = {s for s in strings if s.strip()}
    return clean, plurals


def collect_source_strings(module_dir: Path) -> tuple[set[str], dict[str, str]]:
    all_strings: set[str] = set()
    all_plurals: dict[str, str] = {}
    for path in module_dir.rglob("*"):
        if not path.is_file():
            continue
        if path.suffix.lower() in SKIP_EXTENSIONS:
            continue
        if any(part in SKIP_DIRS for part in path.parts):
            continue
        if "config" in path.parts and "language" in path.parts:
            continue
        if "config" in path.parts and "install" in path.parts and path.suffix.lower() in {".yml", ".yaml"}:
            continue
        if "config" in path.parts and "optional" in path.parts and path.suffix.lower() in {".yml", ".yaml"}:
            continue
        if path.suffix.lower() not in {".php", ".js", ".twig", ".yml", ".yaml"} and ".twig" not in path.name:
            continue
        found, plurals = extract_from_file(path)
        all_strings |= found
        all_plurals.update(plurals)
    return all_strings, all_plurals


def decode_po_quoted(raw: str) -> str:
    value = raw.strip()
    if value.startswith('"') and value.endswith('"'):
        value = value[1:-1]

    result: list[str] = []
    i = 0
    while i < len(value):
        if value[i] == "\\" and i + 1 < len(value):
            nxt = value[i + 1]
            if nxt == "n":
                result.append("\n")
                i += 2
                continue
            if nxt == "t":
                result.append("\t")
                i += 2
                continue
            if nxt == '"':
                result.append('"')
                i += 2
                continue
            if nxt == "\\":
                result.append("\\")
                i += 2
                continue
        result.append(value[i])
        i += 1
    return "".join(result)


def parse_po_file(path: Path) -> tuple[dict[str, PoEntry], dict[str, PoEntry]]:
    """Return (singular_entries, plural_entries keyed by singular msgid)."""
    singular: dict[str, PoEntry] = {}
    plural: dict[str, PoEntry] = {}
    if not path.exists():
        return singular, plural

    msgid_parts: list[str] = []
    msgid_plural_parts: list[str] = []
    msgstr_parts: list[str] = []
    msgstr_plural: dict[int, list[str]] = {}
    state: str | None = None

    def flush() -> None:
        nonlocal msgid_parts, msgid_plural_parts, msgstr_parts, msgstr_plural, state
        msgid = "".join(msgid_parts)
        if not msgid:
            msgid_parts = []
            msgid_plural_parts = []
            msgstr_parts = []
            msgstr_plural = {}
            state = None
            return

        if msgid.startswith("src/"):
            msgid_parts = []
            msgid_plural_parts = []
            msgstr_parts = []
            msgstr_plural = {}
            state = None
            return

        msgstr = "".join(msgstr_parts)
        msgid_plural = "".join(msgid_plural_parts)
        if msgid_plural:
            entry = PoEntry(
                msgid=msgid,
                msgid_plural=msgid_plural,
                msgstr_plural=[
                    "".join(msgstr_plural.get(i, [])) for i in sorted(msgstr_plural)
                ],
            )
            plural[msgid] = entry
        else:
            singular[msgid] = PoEntry(msgid=msgid, msgstr=msgstr)

        msgid_parts = []
        msgid_plural_parts = []
        msgstr_parts = []
        msgstr_plural = {}
        state = None

    for raw_line in path.read_text(encoding="utf-8", errors="replace").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#"):
            continue
        if line.startswith("msgid "):
            flush()
            state = "msgid"
            msgid_parts = [decode_po_quoted(line[6:].strip())]
            continue
        if line.startswith("msgid_plural "):
            state = "msgid_plural"
            msgid_plural_parts = [decode_po_quoted(line[13:].strip())]
            continue
        if line.startswith("msgstr["):
            idx = int(line.split("[")[1].split("]")[0])
            state = f"msgstr_plural:{idx}"
            msgstr_plural[idx] = [decode_po_quoted(line.split("]", 1)[1].strip())]
            continue
        if line.startswith("msgstr "):
            state = "msgstr"
            msgstr_parts = [decode_po_quoted(line[7:].strip())]
            continue
        if line.startswith('"') and line.endswith('"'):
            chunk = decode_po_quoted(line)
            if state == "msgid":
                msgid_parts.append(chunk)
            elif state == "msgid_plural":
                msgid_plural_parts.append(chunk)
            elif state == "msgstr":
                msgstr_parts.append(chunk)
            elif state and state.startswith("msgstr_plural:"):
                idx = int(state.split(":")[1])
                msgstr_plural[idx].append(chunk)

    flush()
    return singular, plural


def load_existing_translations(translations_dir: Path, module_name: str, lang: str) -> tuple[dict[str, str], dict[str, PoEntry]]:
    candidates = [
        translations_dir / f"{lang}.po",
        translations_dir / f"{module_name}.{lang}.po",
    ]
    singular_map: dict[str, str] = {}
    plural_map: dict[str, PoEntry] = {}
    for candidate in candidates:
        singular, plural = parse_po_file(candidate)
        for msgid, entry in singular.items():
            if entry.msgstr.strip() and msgid not in singular_map:
                singular_map[msgid] = entry.msgstr
        for msgid, entry in plural.items():
            if any(part.strip() for part in entry.msgstr_plural) and msgid not in plural_map:
                plural_map[msgid] = entry
    return singular_map, plural_map


def po_quote(value: str) -> list[str]:
    escaped = (
        value.replace("\\", "\\\\")
        .replace('"', '\\"')
        .replace("\t", "\\t")
        .replace("\n", "\\n")
    )
    if len(escaped) <= 70:
        return [f'"{escaped}"']
    lines: list[str] = []
    current = ""
    for char in escaped:
        if len(current) >= 70:
            lines.append(f'"{current}"')
            current = ""
        current += char
    if current:
        lines.append(f'"{current}"')
    return lines


def append_po_lines(lines: list[str], prefix: str, value: str) -> None:
    quoted = po_quote(value)
    for index, part in enumerate(quoted):
        lines.append(f"{prefix if index == 0 else ''}{part}")


def write_po_entry(lines: list[str], entry: PoEntry, translations: dict[str, str], plural_translations: dict[str, PoEntry]) -> None:
    append_po_lines(lines, "msgid ", entry.msgid)
    if entry.msgid_plural:
        append_po_lines(lines, "msgid_plural ", entry.msgid_plural)
        existing = plural_translations.get(entry.msgid)
        for i in range(2):
            value = ""
            if existing and i < len(existing.msgstr_plural):
                value = existing.msgstr_plural[i]
            append_po_lines(lines, f"msgstr[{i}] ", value)
        return

    value = translations.get(entry.msgid, entry.msgstr)
    append_po_lines(lines, "msgstr ", value)


def generate_po(
    module_name: str,
    lang: str,
    msgids: list[str],
    plurals: dict[str, str],
    translations: dict[str, str],
    plural_translations: dict[str, PoEntry],
) -> str:
    plural_forms = PLURAL_FORMS.get(lang, "nplurals=2; plural=(n != 1);")
    lines = [
        f"# {lang.upper()} translation for {module_name} module.",
        'msgid ""',
        'msgstr ""',
        f'"Project-Id-Version: {module_name}\\n"',
        f'"PO-Revision-Date: {datetime.now(timezone.utc).strftime("%Y-%m-%d %H:%M+0000")}\\n"',
        f'"Language: {lang}\\n"',
        '"MIME-Version: 1.0\\n"',
        '"Content-Type: text/plain; charset=UTF-8\\n"',
        '"Content-Transfer-Encoding: 8bit\\n"',
        f'"Plural-Forms: {plural_forms}\\n"',
        "",
    ]

    for msgid in msgids:
        if msgid in plurals:
            entry = PoEntry(msgid=msgid, msgid_plural=plurals[msgid])
        else:
            entry = PoEntry(msgid=msgid)
        write_po_entry(lines, entry, translations, plural_translations)
        lines.append("")

    return "\n".join(lines).rstrip() + "\n"


def regenerate_module(module_name: str, dry_run: bool = False) -> dict:
    module_dir = CUSTOM_MODULES / module_name
    translations_dir = module_dir / "translations"
    if not module_dir.is_dir():
        raise FileNotFoundError(module_name)

    source_strings, source_plurals = collect_source_strings(module_dir)
    msgids = sorted(source_strings)

    generated: list[str] = []
    preserved_counts: dict[str, int] = {}

    for lang in EXPECTED_LANGS:
        translations, plural_translations = load_existing_translations(
            translations_dir, module_name, lang
        )
        preserved = sum(1 for msgid in msgids if translations.get(msgid, "").strip())
        preserved += sum(
            1 for msgid in source_plurals if msgid in plural_translations
        )
        preserved_counts[lang] = preserved

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
            translations_dir.mkdir(parents=True, exist_ok=True)
            target.write_text(content, encoding="utf-8")
        generated.append(str(target.relative_to(ROOT)))

    pot_content = generate_po(module_name, "en", msgids, source_plurals, {}, {})
    pot_target = translations_dir / f"{module_name}.pot"
    if not dry_run:
        pot_target.write_text(pot_content, encoding="utf-8")
        for short in translations_dir.glob("*.po"):
            if re.fullmatch(r"[a-z]{2,3}", short.stem):
                short.unlink()

    return {
        "module": module_name,
        "strings": len(msgids),
        "plurals": len(source_plurals),
        "preserved_by_lang": preserved_counts,
        "generated": generated,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument(
        "modules",
        nargs="*",
        default=["ps_dictionary", "ps_surface", "ps_media", "ps_feature"],
        help="Module machine names to regenerate",
    )
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    results = []
    for module in args.modules:
        result = regenerate_module(module, dry_run=args.dry_run)
        results.append(result)
        print(
            f"{result['module']}: {result['strings']} strings, "
            f"{result['plurals']} plurals, preserved={result['preserved_by_lang']}"
        )

    return 0


if __name__ == "__main__":
    sys.exit(main())
