#!/usr/bin/env python3
"""
Translate a deferred translation backlog JSON file using LOCAL Argos Translate.

Usage:
    python3 translate-backlog-local.py input.json output.json [--batch-size 100] [--start-at 0]

Features:
- 100% offline translation (no API calls)
- Fast (~0.1s per text after model load)
- Batch processing with progress tracking
- Resume from interruption (--start-at)
- No rate limiting needed
"""

import argparse
import json
import sys
import time
from pathlib import Path

try:
    import argostranslate.translate
except ImportError:
    print("ERROR: argostranslate not installed. Run: pip install argostranslate", file=sys.stderr)
    print("Then install FR→EN package:", file=sys.stderr)
    print("  python3 -c 'import argostranslate.package; argostranslate.package.update_package_index(); pkg=[p for p in argostranslate.package.get_available_packages() if p.from_code==\"fr\" and p.to_code==\"en\"][0]; argostranslate.package.install_from_path(pkg.download())'", file=sys.stderr)
    sys.exit(1)


def translate_text(text: str, source: str = "fr", target: str = "en") -> str:
    """Translate text using Argos Translate (local)."""
    if not text or not text.strip():
        return ""
    
    try:
        result = argostranslate.translate.translate(text, source, target)
        return result if result else ""
    except Exception as e:
        print(f"Translation error for text '{text[:50]}...': {e}", file=sys.stderr)
        return ""


def translate_backlog(
    input_path: Path,
    output_path: Path,
    batch_size: int = 100,
    start_at: int = 0,
) -> None:
    """Translate a backlog JSON file by batches using local Argos Translate."""
    
    # Load input backlog
    with open(input_path, "r", encoding="utf-8") as f:
        backlog = json.load(f)
    
    total_entries = len(backlog)
    print(f"📊 Total entries: {total_entries}")
    print(f"📦 Batch size: {batch_size}")
    print(f"⏭️  Starting at: {start_at}")
    print(f"⚡ Using LOCAL Argos Translate (offline)")
    print()
    
    # Warm up the model (first translation is slow)
    print("🔥 Warming up translation model...")
    translate_text("test")
    print("✅ Model ready!\n")
    
    # Load existing progress if resuming
    progress = {}
    if output_path.exists() and start_at > 0:
        with open(output_path, "r", encoding="utf-8") as f:
            progress = json.load(f)
        print(f"✅ Loaded {len(progress)} existing translations")
        print()
    
    # Process entries
    entries = list(backlog.items())
    processed = 0
    skipped = 0
    start_time = time.time()
    
    for idx, (business_id, translations) in enumerate(entries):
        if idx < start_at:
            skipped += 1
            continue
        
        # Show progress every batch
        if processed > 0 and processed % batch_size == 0:
            elapsed = time.time() - start_time
            rate = processed / elapsed
            remaining = total_entries - idx
            eta = remaining / rate if rate > 0 else 0
            
            percent = (idx / total_entries) * 100
            print(f"📈 Progress: {idx}/{total_entries} ({percent:.1f}%)")
            print(f"   Rate: {rate:.1f} offers/s | ETA: {eta/3600:.1f}h")
            print(f"   Saving checkpoint...")
            
            with open(output_path, "w", encoding="utf-8") as f:
                json.dump(progress, f, ensure_ascii=False, indent=2)
            print()
        
        # Translate all fields for this business_id
        progress[business_id] = {}
        for field_key, source_text in translations.items():
            if not source_text or not source_text.strip():
                progress[business_id][field_key] = ""
                continue
            
            translated = translate_text(source_text, source="fr", target="en")
            progress[business_id][field_key] = translated
        
        processed += 1
        
        # Brief status every 10 offers
        if processed % 10 == 0:
            elapsed = time.time() - start_time
            rate = processed / elapsed
            print(f"  [{idx+1}/{total_entries}] {business_id} - {rate:.1f} offers/s", end="\r")
    
    # Final save
    print()  # New line after progress
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(progress, f, ensure_ascii=False, indent=2)
    
    total_time = time.time() - start_time
    print()
    print(f"✅ Translation complete!")
    print(f"   Total processed: {processed}")
    print(f"   Total skipped: {skipped}")
    print(f"   Total time: {total_time/3600:.2f} hours")
    print(f"   Average: {processed/total_time:.1f} offers/s")
    print(f"   Output: {output_path}")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Translate a deferred translation backlog using LOCAL Argos Translate"
    )
    parser.add_argument("input", type=Path, help="Input backlog JSON file")
    parser.add_argument("output", type=Path, help="Output translated JSON file")
    parser.add_argument(
        "--batch-size",
        type=int,
        default=100,
        help="Save progress every N entries (default: 100)",
    )
    parser.add_argument(
        "--start-at",
        type=int,
        default=0,
        help="Start at entry N (for resuming, default: 0)",
    )
    
    args = parser.parse_args()
    
    if not args.input.exists():
        print(f"ERROR: Input file not found: {args.input}", file=sys.stderr)
        return 1
    
    try:
        translate_backlog(
            input_path=args.input,
            output_path=args.output,
            batch_size=args.batch_size,
            start_at=args.start_at,
        )
        return 0
    except KeyboardInterrupt:
        print("\n⚠️  Interrupted by user. Progress saved.", file=sys.stderr)
        return 130
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        import traceback
        traceback.print_exc()
        return 1


if __name__ == "__main__":
    sys.exit(main())
