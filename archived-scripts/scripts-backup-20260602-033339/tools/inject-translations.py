#!/usr/bin/env python3
"""
Inject translations from done.json into an existing XML file.
"""

import argparse
import json
import sys
from pathlib import Path
from xml.etree import ElementTree as ET


def load_translations(done_path: Path) -> dict:
    """Load completed translations."""
    with open(done_path, "r", encoding="utf-8") as f:
        return json.load(f)


def inject_translations(
    input_xml: Path,
    translations: dict,
    output_xml: Path
) -> None:
    """Inject translations into XML."""
    
    print(f"📂 Loading XML: {input_xml}")
    tree = ET.parse(input_xml)
    root = tree.getroot()
    offers = root.findall("OFFER")
    print(f"✅ Loaded {len(offers)} offers\n")
    
    print(f"🔄 Injecting translations...")
    
    translated_count = 0
    skipped_count = 0
    
    for offer in offers:
        business_id = offer.findtext("BUSINESS_ID", "").strip()
        
        if not business_id or business_id not in translations:
            skipped_count += 1
            continue
        
        offer_translations = translations[business_id]
        
        # Inject each translation field
        for field_name, translated_text in offer_translations.items():
            # Find or create the field element
            field_elem = offer.find(field_name)
            if field_elem is None:
                # Field doesn't exist, create it (shouldn't happen normally)
                continue
            
            # Set the translated text
            field_elem.text = translated_text
        
        translated_count += 1
        
        if translated_count % 50 == 0:
            print(f"  Progress: {translated_count}/{len(offers)}")
    
    print(f"\n✅ Translations injected:")
    print(f"   Translated: {translated_count}")
    print(f"   Skipped: {skipped_count}")
    
    # Write output
    print(f"\n💾 Writing output XML...")
    output_xml.parent.mkdir(parents=True, exist_ok=True)
    ET.indent(tree, space="  ")
    tree.write(output_xml, encoding="utf-8", xml_declaration=True)
    
    size_mb = output_xml.stat().st_size / 1024 / 1024
    print(f"✅ Output XML created: {output_xml}")
    print(f"   Size: {size_mb:.1f} MB")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Inject translations into existing XML"
    )
    parser.add_argument("input_xml", type=Path, help="Input XML file (FR only)")
    parser.add_argument("translations", type=Path, help="Translations done.json file")
    parser.add_argument("output_xml", type=Path, help="Output XML file (FR+EN)")
    
    args = parser.parse_args()
    
    if not args.input_xml.exists():
        print(f"ERROR: Input XML not found: {args.input_xml}", file=sys.stderr)
        return 1
    
    if not args.translations.exists():
        print(f"ERROR: Translations file not found: {args.translations}", file=sys.stderr)
        return 1
    
    try:
        print(f"📂 Loading translations: {args.translations}")
        translations = load_translations(args.translations)
        print(f"✅ Loaded {len(translations)} business_ids with translations\n")
        
        inject_translations(
            input_xml=args.input_xml,
            translations=translations,
            output_xml=args.output_xml,
        )
        return 0
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
