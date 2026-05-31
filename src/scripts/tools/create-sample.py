#!/usr/bin/env python3
"""
Create a representative sample XML from the full generated XML file.

Takes N offers per asset type and transaction type combination.
Uses the report.json file for metadata.
"""

import argparse
import json
import sys
from collections import defaultdict
from pathlib import Path
from xml.etree import ElementTree as ET


def load_report(report_path: Path) -> dict:
    """Load report JSON file."""
    with open(report_path, "r", encoding="utf-8") as f:
        return json.load(f)


def load_xml_offers(xml_path: Path) -> tuple[ET.Element, dict[str, ET.Element]]:
    """Load XML and return root and dict of OFFER elements by business_id."""
    tree = ET.parse(xml_path)
    root = tree.getroot()
    offers_dict = {}
    
    for offer in root.findall("OFFER"):
        business_id = offer.findtext("BUSINESS_ID", "").strip()
        if business_id:
            offers_dict[business_id] = offer
    
    return root, offers_dict


def select_sample_business_ids(
    report_offers: list[dict],
    per_combination: int = 50
) -> tuple[set[str], dict]:
    """Select N business_ids per asset×transaction combination from report."""
    
    # Group offers by asset×transaction
    groups = defaultdict(list)
    
    for offer in report_offers:
        asset = offer.get("asset_type", "Unknown")
        transaction = offer.get("transaction", "Unknown")
        business_id = offer.get("business_id", "")
        
        if business_id:
            key = (asset, transaction)
            groups[key].append(business_id)
    
    # Select N from each group
    selected_ids = set()
    stats = {}
    
    print("📊 SÉLECTION PAR COMBINAISON:\n")
    for key, group_ids in sorted(groups.items()):
        asset, transaction = key
        sample = group_ids[:per_combination]
        selected_ids.update(sample)
        stats[key] = len(sample)
        print(f"  {asset:25s} × {transaction:10s} → {len(sample):3d} offres")
    
    return selected_ids, stats


def create_sample_xml(
    input_xml: Path,
    report_json: Path,
    output_xml: Path,
    per_combination: int = 50
) -> None:
    """Create sample XML with N offers per asset×transaction."""
    
    print(f"📂 Loading report: {report_json}")
    report = load_report(report_json)
    report_offers = report.get("offers", [])
    print(f"✅ Loaded {len(report_offers)} offers metadata\n")
    
    print(f"🎯 Selecting {per_combination} business_ids per combination...\n")
    selected_ids, stats = select_sample_business_ids(report_offers, per_combination)
    
    print(f"\n📊 TOTAL: {len(selected_ids)} business_ids sélectionnés")
    print(f"📊 COMBINAISONS: {len(stats)}\n")
    
    print(f"📂 Loading XML: {input_xml}")
    root, offers_dict = load_xml_offers(input_xml)
    print(f"✅ Loaded {len(offers_dict)} offers from XML\n")
    
    # Create new XML with selected offers
    print("🔄 Creating sample XML...")
    new_root = ET.Element(root.tag, root.attrib)
    
    found = 0
    for business_id in selected_ids:
        if business_id in offers_dict:
            new_root.append(offers_dict[business_id])
            found += 1
    
    print(f"✅ {found} offers added to sample XML")
    
    # Write output
    output_xml.parent.mkdir(parents=True, exist_ok=True)
    tree = ET.ElementTree(new_root)
    ET.indent(tree, space="  ")
    tree.write(output_xml, encoding="utf-8", xml_declaration=True)
    
    print(f"\n✅ Sample XML créé: {output_xml}")
    print(f"   Taille: {output_xml.stat().st_size / 1024 / 1024:.1f} MB")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Create representative sample from full XML using report.json"
    )
    parser.add_argument("input", type=Path, help="Input XML file")
    parser.add_argument("report", type=Path, help="Input report.json file")
    parser.add_argument("output", type=Path, help="Output sample XML file")
    parser.add_argument(
        "--per-combination",
        type=int,
        default=50,
        help="Number of offers per asset×transaction combination (default: 50)"
    )
    
    args = parser.parse_args()
    
    if not args.input.exists():
        print(f"ERROR: Input XML file not found: {args.input}", file=sys.stderr)
        return 1
    
    if not args.report.exists():
        print(f"ERROR: Report file not found: {args.report}", file=sys.stderr)
        return 1
    
    try:
        create_sample_xml(
            input_xml=args.input,
            report_json=args.report,
            output_xml=args.output,
            per_combination=args.per_combination,
        )
        return 0
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
