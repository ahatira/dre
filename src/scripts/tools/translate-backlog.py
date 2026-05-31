#!/usr/bin/env python3
"""
Translate a deferred translation backlog JSON file by batches.

Usage:
    python3 translate-backlog.py input.json output.json [--batch-size 100] [--start-at 0]

Features:
- Batch processing with progress tracking
- Resume from interruption (--start-at)
- Rate limiting to avoid API restrictions
- Progress file for monitoring
"""

import argparse
import json
import sys
import time
from pathlib import Path

try:
    from deep_translator import GoogleTranslator
except ImportError:
    print("ERROR: deep-translator not installed. Run: pip install deep-translator", file=sys.stderr)
    sys.exit(1)


def translate_text(text: str, source: str = "fr", target: str = "en") -> str:
    """Translate text using GoogleTranslator."""
    if not text or not text.strip():
        return ""
    
    try:
        translator = GoogleTranslator(source=source, target=target)
        result = translator.translate(text)
        return result if result else ""
    except Exception as e:
        print(f"Translation error: {e}", file=sys.stderr)
        return ""


def translate_backlog(
    input_path: Path,
    output_path: Path,
    batch_size: int = 100,
    start_at: int = 0,
    delay: float = 0.5,
) -> None:
    """Translate a backlog JSON file by batches."""
    
    # Load input backlog
    with open(input_path, "r", encoding="utf-8") as f:
        backlog = json.load(f)
    
    total_entries = len(backlog)
    print(f"📊 Total entries: {total_entries}")
    print(f"📦 Batch size: {batch_size}")
    print(f"⏭️  Starting at: {start_at}")
    print(f"⏱️  Delay between translations: {delay}s")
    print()
    
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
    
    for idx, (business_id, translations) in enumerate(entries):
        if idx < start_at:
            skipped += 1
            continue
        
        # Show progress every batch
        if processed % batch_size == 0 and processed > 0:
            percent = (idx / total_entries) * 100
            print(f"📈 Progress: {idx}/{total_entries} ({percent:.1f}%) - Saving checkpoint...")
            with open(output_path, "w", encoding="utf-8") as f:
                json.dump(progress, f, ensure_ascii=False, indent=2)
        
        # Translate all fields for this business_id
        progress[business_id] = {}
        for field_key, source_text in translations.items():
            if not source_text or not source_text.strip():
                progress[business_id][field_key] = ""
                continue
            
            translated = translate_text(source_text, source="fr", target="en")
            progress[business_id][field_key] = translated
            
            # Brief status
            if processed % 10 == 0:
                print(f"  [{idx+1}/{total_entries}] {business_id} - {field_key[:30]}")
            
            # Rate limiting
            time.sleep(delay)
        
        processed += 1
    
    # Final save
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(progress, f, ensure_ascii=False, indent=2)
    
    print()
    print(f"✅ Translation complete!")
    print(f"   Total processed: {processed}")
    print(f"   Total skipped: {skipped}")
    print(f"   Output: {output_path}")


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Translate a deferred translation backlog JSON file"
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
    parser.add_argument(
        "--delay",
        type=float,
        default=0.5,
        help="Delay between translations in seconds (default: 0.5)",
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
            delay=args.delay,
        )
        return 0
    except KeyboardInterrupt:
        print("\n⚠️  Interrupted by user. Progress saved.", file=sys.stderr)
        return 130
    except Exception as e:
        print(f"ERROR: {e}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    sys.exit(main())
