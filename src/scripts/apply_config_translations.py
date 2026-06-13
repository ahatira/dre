#!/usr/bin/env python3
"""Apply config translation catalog JSON to module config/{install|optional}/language/{lang}/."""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path
from typing import Any

import yaml

from regenerate_module_po import CUSTOM_MODULES, EXPECTED_LANGS

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"


def yaml_scalar(value: Any) -> str:
    if value is None:
        return "null"
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, (int, float)):
        return str(value)
    text = str(value)
    if text == "":
        return "''"
    if "\n" in text:
        return None
    if any(ch in text for ch in "'\":{}[]&*#?|-<>=!%@`"):
        return json.dumps(text, ensure_ascii=False)
    if " " in text or text.startswith(("/", ".")):
        return f"'{text}'"
    return text


def dump_list(data: list[Any], indent: int = 0) -> list[str]:
    lines: list[str] = []
    pad = " " * indent
    for item in data:
        if isinstance(item, dict):
            if not item:
                lines.append(f"{pad}- {{}}")
                continue
            first = True
            for key, value in item.items():
                if first:
                    if isinstance(value, dict):
                        lines.append(f"{pad}- {key}:")
                        lines.extend(dump_mapping(value, indent + 4))
                    elif isinstance(value, list):
                        lines.append(f"{pad}- {key}:")
                        lines.extend(dump_list(value, indent + 4))
                    else:
                        scalar = yaml_scalar(value)
                        if scalar is None:
                            raise ValueError(f"Unsupported nested value for key {key}")
                        lines.append(f"{pad}- {key}: {scalar}")
                    first = False
                else:
                    key_pad = f"{pad}  "
                    if isinstance(value, dict):
                        lines.append(f"{key_pad}{key}:")
                        lines.extend(dump_mapping(value, indent + 4))
                    elif isinstance(value, list):
                        lines.append(f"{key_pad}{key}:")
                        lines.extend(dump_list(value, indent + 4))
                    else:
                        scalar = yaml_scalar(value)
                        if scalar is None:
                            raise ValueError(f"Unsupported nested value for key {key}")
                        lines.append(f"{key_pad}{key}: {scalar}")
            continue
        if isinstance(item, list):
            lines.append(f"{pad}-")
            lines.extend(dump_list(item, indent + 2))
            continue
        scalar = yaml_scalar(item)
        if scalar is None:
            raise ValueError("Unsupported list item value")
        lines.append(f"{pad}- {scalar}")
    return lines


def dump_mapping(data: dict[str, Any], indent: int = 0) -> list[str]:
    lines: list[str] = []
    pad = " " * indent
    for key, value in data.items():
        if key == "elements" and isinstance(value, str):
            lines.append(f"{pad}elements: |-")
            for element_line in value.split("\n"):
                lines.append(f"{pad}  {element_line}")
            continue
        if isinstance(value, dict):
            lines.append(f"{pad}{key}:")
            lines.extend(dump_mapping(value, indent + 2))
            continue
        if isinstance(value, list):
            lines.append(f"{pad}{key}:")
            lines.extend(dump_list(value, indent + 2))
            continue
        scalar = yaml_scalar(value)
        if scalar is None:
            raise ValueError(f"Unsupported nested value for key {key}")
        lines.append(f"{pad}{key}: {scalar}")
    return lines


def dump_config(data: dict[str, Any]) -> str:
    return "\n".join(dump_mapping(data)) + "\n"


def apply_config_module(module_name: str, dry_run: bool = False) -> dict:
    catalog_path = CATALOG_DIR / f"{module_name}.config.json"
    if not catalog_path.exists():
        raise FileNotFoundError(catalog_path)

    catalog = json.loads(catalog_path.read_text(encoding="utf-8"))
    module_dir = CUSTOM_MODULES / module_name
    written: list[str] = []

    for catalog_key, per_lang in catalog.items():
        target_scope = "install"
        filename = catalog_key
        if catalog_key.startswith("optional:"):
            target_scope = "optional"
            filename = catalog_key.split(":", 1)[1]

        for lang in EXPECTED_LANGS:
            if lang not in per_lang:
                raise ValueError(f"{catalog_key}: missing language {lang}")

            target_dir = module_dir / "config" / target_scope / "language" / lang
            target = target_dir / filename
            content = dump_config(per_lang[lang])

            if not dry_run:
                target_dir.mkdir(parents=True, exist_ok=True)
                target.write_text(content, encoding="utf-8")
            written.append(str(target.relative_to(CUSTOM_MODULES.parent.parent.parent)))

    return {
        "module": module_name,
        "files": len(catalog),
        "languages": len(EXPECTED_LANGS),
        "written": written,
    }


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("modules", nargs="+", help="Module machine names")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    for module in args.modules:
        result = apply_config_module(module, dry_run=args.dry_run)
        print(
            f"{result['module']}: {result['files']} config files × "
            f"{result['languages']} languages"
        )

    return 0


if __name__ == "__main__":
    sys.exit(main())
