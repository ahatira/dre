#!/usr/bin/env python3
"""
Add translations to a sample XML from the full translation backlog.
"""

import argparse
import json
import sys
from pathlib import Path
from xml.etree import ElementTree as ET


def load_translations(backlog_path: Path) -> dict:
    """Load translation backlog."""
    with open(backlog_path, "r", encoding="utf-8") as f:
        return json.load(f)


def get_sample_business_ids(xml_path: Path) -> set[str]:
    """Extract business_ids from sample XML."""
    tree = ET.parse(xml_path)
    root = tree.getroot()
    
    business_ids = set()
    for offer in root.findall("OFFER"):
        business_id = offer.findtext("BUSINESS_ID", "").strip()
        if business_id:
            business_ids.add(business_id)
    
    return business_ids


def create_sample_backlog(
    full_backlog_path: Path,
    sample_xml_path: Path,
    output_backlog_path: Path
) -> None:
    """Create a translation backlog for sample XML only."""
    
    print(f"📂 Loading full backlog: {full_backlog_path}")
    full_backlog = load_translations(full_backlog_path)
    print(f"✅ Loaded {len(full_backlog)} entries\n")
    
    print(f"📂 Extracting business_ids from sample: {sample_xml_path}")
    sample_ids = get_sample_business_ids(sample_xml_path)
    print(f"✅ Found {len(sample_ids)} business_ids\n")
    
    # Filter backlog for sample only
    sample_backlog = {
        bid: translations 
        for bid, translations in full_backlog.items() 
        if bid in sample_ids
    }
    
    print(f"🔄 Creating sample backlog...")
    print(f"   Entries: {len(sample_backlog)}")
    
    # Count total fields
    total_fields = sum(len(fields) for fields in sample_backlog.values())
    print(f"   Fields to translate: {total_fields}")
    
    # Write sample backlog
    output_backlog_path.parent.mkdir(parents=True, exist_ok=True)
    with open(output_backlog_path, "w", encoding="utf-8") as f:
        json.dump(sample_backlog, f, ensure_ascii=False, indent=2)
    
    size_mb = output_backlog_path.stat().st_size / 1024 / 1024
    print(f"\n✅ Sample backlog créé: {output_backlog_path}")
    print(f"   Taille: {size_mb:.1f} MB")
    print(f"\n⏱️  Temps de traduction estimé: {total_fields * 0.1 / 60:.1f} minutes")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Create translation backlog for sample XML"
    )
    parser.add_argument("full_backlog", type=Path, help="Full translation backlog")
    parser.add_argument("sample_xml", type=Path, help="Sample XML file")
    parser.add_argument("output", type=Path, help="Output sample backlog")
    
    args = parser.parse_args()
    
    if not args.full_backlog.exists():
        print(f"ERROR: Full backlog not found: {args.full_backlog}", file=sys.stderr)
        return 1
    
    if not args.sample_xml.exists():
        print(f"ERROR: Sample XML not found: {args.sample_xml}", file=sys.stderr)
        return 1
    
    try:
        create_sample_backlog(
            full_backlog_path=args.full_backlog,
            sample_xml_path=args.sample_xml,
            output_backlog_path=args.output,
        )
        return 0
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
