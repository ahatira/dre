#!/usr/bin/env python3
from __future__ import annotations

import argparse
import sys
from xml.etree import ElementTree as ET


def main() -> int:
    parser = argparse.ArgumentParser(description="Validate BNPPRE generated XML against business rules")
    parser.add_argument("--input", required=True, help="Path to generated XML file")
    args = parser.parse_args()

    root = ET.parse(args.input).getroot()
    offers = root.findall("OFFER")

    errors: list[str] = []
    business_ids: set[str] = set()

    for offer in offers:
        bid = (offer.findtext("BUSINESS_ID") or "").strip()
        if not bid:
            errors.append("Missing BUSINESS_ID on one OFFER")
            continue
        if bid in business_ids:
            errors.append(f"{bid}: duplicate BUSINESS_ID")
        business_ids.add(bid)

        op = (offer.findtext("OPERATIONS_LIST/OPERATION_CODE") or "").strip()
        civility = (offer.findtext("BUSINESS_LEADERS_LIST/BUSINESS_LEADER/CIVILITY") or "").strip()
        avatar_url = (offer.findtext("BUSINESS_LEADERS_LIST/BUSINESS_LEADER/AVATAR_URL") or "").strip()

        if civility not in {"", "MR", "MRS"}:
            errors.append(f"{bid}: invalid CIVILITY '{civility}'")

        if avatar_url:
            avatar_lower = avatar_url.lower()
            if "/styles/" in avatar_url:
                errors.append(f"{bid}: AVATAR_URL still points to Drupal style derivative")
            if avatar_lower.endswith(".webp"):
                errors.append(f"{bid}: AVATAR_URL should keep original file format, got webp")
            if "broker-" in avatar_lower:
                errors.append(f"{bid}: AVATAR_URL should not use default broker placeholder")

        budget = offer.find("BUDGETS_LIST/BUDGET")
        period_code = (budget.findtext("PERIOD_CODE") if budget is not None else "") or ""
        unit_code = (budget.findtext("UNIT_CODE") if budget is not None else "") or ""

        quals = [
            (node.findtext("QUALIFICATION_CODE") or "").strip()
            for node in offer.findall("GLOBAL_SURFACES/SURFACE")
        ]
        qual_set = {q for q in quals if q}

        if op == "VEN":
            if period_code != "":
                errors.append(f"{bid}: VEN PERIOD_CODE must be empty")
            if unit_code != "GLO":
                errors.append(f"{bid}: VEN UNIT_CODE must be GLO")
            if qual_set != {"TOTAL", "DISPO"}:
                errors.append(f"{bid}: VEN GLOBAL_SURFACES must be exactly TOTAL+DISPO")

        if op == "LOC" and period_code not in {"", "ANN", "MEN"}:
            errors.append(f"{bid}: LOC PERIOD_CODE must be empty/ANN/MEN")

    print(f"Validated offers: {len(offers)}")
    if errors:
        print("Validation status: FAILED")
        for item in errors:
            print(f" - {item}")
        return 1

    print("Validation status: OK")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
