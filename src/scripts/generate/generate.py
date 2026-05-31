#!/usr/bin/env python3
"""
Générateur d'offres BNPPRE - Script unifié

Modes:
  scrape    - Scrape le site bnppre.fr et génère XML FR
  sample    - Crée un échantillon d'offres avec traductions optionnelles
  translate - Traduit un fichier XML existant FR→EN

Usage:
  python3 generate.py scrape --limit 100 --output data/xml/offers.xml
  python3 generate.py sample --source offers.xml --per-type 50 --translate --output sample.xml
  python3 generate.py translate --source offers.xml --output offers_translated.xml
"""
from __future__ import annotations

import argparse
import copy
import datetime as dt
import html as html_lib
import gzip
import json
import pathlib
import re
import subprocess
import sys
import unicodedata
import urllib.error
import urllib.request
import urllib.parse
from xml.etree import ElementTree as ET
from collections import defaultdict

# Traduction (optionnelle)
try:
    import argostranslate.package
    import argostranslate.translate
    TRANSLATION_AVAILABLE = True
except ImportError:
    TRANSLATION_AVAILABLE = False


DEFAULT_URLS = [
    "https://www.bnppre.fr/a-louer/bureau/paris-75/paris-17-75017/location-bureau-532-m2-non-divisible-OLBUR2537462.html",
    "https://www.bnppre.fr/a-louer/bureau/hauts-de-seine-92/colombes-92700/location-bureau-40972-m2-divisible-OLBUR2108079.html",
    "https://www.bnppre.fr/a-louer/bureau/paris-75/paris-17-75017/location-bureau-1626-m2-non-divisible-OLBUR2536970.html",
]

TYPE_CODE_MAP = {
    "bureau": "BUR",
    "bureaux": "BUR",
    "commerce": "COM",
    "local commercial": "COM",
    "activite": "ACT",
    "activité": "ACT",
    "entrepot": "ENT",
    "entrepôt": "ENT",
    "coworking": "COW",
    "terrain": "TER",
}

OPERATION_CODE_MAP = {
    "location": "LOC",
    "louer": "LOC",
    "vente": "VEN",
    "vendre": "VEN",
}

SITEMAP_INDEX_URL = "https://www.bnppre.fr/sitemap.xml"

OPERATION_PATH_MAP = {
    "LOC": "a-louer",
    "VEN": "a-vendre",
}

FEMALE_FIRST_NAMES = {
    "adele", "agathe", "alexandra", "alexia", "alice", "amelie", "anais", "anne", "audrey", "aurore",
    "beatrice", "camille", "capucine", "carla", "caroline", "catherine", "celine", "charlotte", "chloe", "claire",
    "clara", "constance", "delphine", "diane", "elena", "elise", "elodie", "emilie", "emma", "eva",
    "fanny", "florence", "gabrielle", "helene", "ines", "iris", "jade", "jeanne", "julia", "julie",
    "laetitia", "laura", "lea", "lena", "lila", "lina", "lisa", "lise", "louise", "lucie", "madison",
    "manon", "margaux", "margot", "marie", "marine", "mathilde", "maya", "melanie", "nathalie", "noemie",
    "oceane", "pauline", "romane", "salome", "sarah", "sophie", "valerie", "victoria", "zoe",
}

MALE_FIRST_NAMES = {
    "adrien", "alain", "alexandre", "antoine", "arthur", "axel", "benjamin", "bruno", "cedric", "charles",
    "christophe", "clement", "damien", "daniel", "david", "edouard", "emmanuel", "enzo", "eric", "fabien",
    "florian", "francois", "gabriel", "georges", "gregory", "guillaume", "hugo", "jacques", "jean", "jeremy",
    "jerome", "joel", "jonathan", "jordan", "jules", "julien", "kevin", "laurent", "leo", "loic", "louis",
    "luc", "lucas", "maxime", "matthieu", "mehdi", "michael", "mohamed", "nicolas", "olivier", "pierre",
    "quentin", "raphael", "remi", "romain", "samuel", "sebastien", "stephane", "thomas", "valentin", "victor",
    "vincent", "virgile", "xavier", "yanis", "yassine", "yohan",
}


# ============================================================================
# TRADUCTION LOCALE (Argos Translate)
# ============================================================================

def init_translator():
    """Initialise Argos Translate pour FR→EN"""
    if not TRANSLATION_AVAILABLE:
        print("❌ argostranslate non installé. Installez-le avec:")
        print("   pip install argostranslate")
        sys.exit(1)
    
    print("🔧 Initialisation du traducteur local...")
    
    available_packages = argostranslate.package.get_available_packages()
    fr_en_package = next(
        (pkg for pkg in available_packages 
         if pkg.from_code == "fr" and pkg.to_code == "en"),
        None
    )
    
    if fr_en_package:
        argostranslate.package.install_from_path(fr_en_package.download())
    
    print("   ✓ Traducteur FR→EN prêt\n")

def translate_text(text):
    """Traduit un texte FR→EN avec Argos Translate"""
    if not text or not text.strip() or text.startswith('{{'):
        return ""
    
    try:
        return argostranslate.translate.translate(text, "fr", "en")
    except Exception:
        return ""

def translate_offer(offer):
    """
    Traduit tous les champs FR→EN d'une offre en live.
    Parcourt l'arbre XML et traduit chaque élément LANGUAGE="FR"
    vers son élément frère LANGUAGE="EN".
    """
    fields_translated = 0
    
    def process_element(elem):
        nonlocal fields_translated
        
        children = list(elem)
        if not children:
            return
        
        i = 0
        while i < len(children):
            child = children[i]
            
            if child.get('LANGUAGE') == 'FR' and child.text and child.text.strip():
                for j in range(i + 1, len(children)):
                    sibling = children[j]
                    if sibling.tag == child.tag and sibling.get('LANGUAGE') == 'EN':
                        translation = translate_text(child.text)
                        if translation:
                            sibling.text = translation
                            fields_translated += 1
                        break
            
            process_element(child)
            i += 1
    
    process_element(offer)
    return fields_translated

# ============================================================================
# MODE SCRAPE - Scraper le site bnppre.fr
# ============================================================================

def cmd_scrape(args) -> int:
    """Mode scrape: Scrape le site bnppre.fr et génère XML FR"""
    print(f"\n{'='*70}")
    print(f"MODE SCRAPE - Extraction depuis bnppre.fr")
    print(f"{'='*70}\n")
    
    template_path = pathlib.Path(args.template)
    output_path = pathlib.Path(args.output)
    urls = resolve_target_urls(args)
    if not urls:
        raise RuntimeError("No offer URLs resolved. Check mode/filters/inputs.")

    template_root = ET.parse(template_path).getroot()
    output_root = ET.Element(template_root.tag)
    offer_template = template_root.find("OFFER")
    if offer_template is None:
        raise RuntimeError("Template does not contain an OFFER node")

    report_offers = []
    skipped_offers = 0

    for idx, url in enumerate(urls, 1):
        try:
            print(f"[{idx}/{len(urls)}] {url.split('/')[-1][:50]}...", end=" ", flush=True)
            html = fetch_html(url)
        except RuntimeError as e:
            print(f"✗ SKIP (fetch): {e}")
            skipped_offers += 1
            continue
        
        try:
            offer_data = extract_offer_data(url, html)
        except RuntimeError as e:
            print(f"✗ SKIP (extract): {e}")
            skipped_offers += 1
            continue
        
        report_offers.append({
            "business_id": offer_data["BUSINESS_ID"],
            "title": offer_data["SOURCE_TITLE"],
            "transaction": offer_data["SOURCE_TRANSACTION"],
            "asset_type": offer_data["SOURCE_ACTIF"],
            "surface": offer_data["SURFACE_VALUE"],
        })

        offer_node = copy.deepcopy(offer_template)
        apply_offer_data(offer_node, offer_data)
        output_root.append(offer_node)
        print("✓")

    indent_xml(output_root)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    ET.ElementTree(output_root).write(output_path, encoding="utf-8", xml_declaration=True)

    size_mb = output_path.stat().st_size / (1024 * 1024)
    
    print(f"\n{'='*70}")
    print(f"✅ TERMINÉ - {len(report_offers)} offres • {skipped_offers} erreurs")
    print(f"📁 {output_path} ({size_mb:.1f} MB)")
    print(f"{'='*70}\n")
    return 0

# ============================================================================
# MODE SAMPLE - Créer un échantillon avec traductions
# ============================================================================

def cmd_sample(args) -> int:
    """Mode sample: Crée un échantillon d'offres avec traductions optionnelles"""
    print(f"\n{'='*70}")
    print(f"MODE SAMPLE - Échantillon d'offres")
    print(f"{'='*70}\n")
    
    source_path = pathlib.Path(args.source)
    if not source_path.exists():
        print(f"❌ Fichier source introuvable: {source_path}")
        sys.exit(1)
    
    print(f"📖 Lecture de {source_path}...")
    tree = ET.parse(source_path)
    root = tree.getroot()
    
    all_offers = root.findall('.//OFFER')
    
    if args.per_type:
        groups = defaultdict(list)
        
        for offer in all_offers:
            type_elem = offer.find('TYPE_CODE')
            type_code = type_elem.text.strip() if type_elem is not None and type_elem.text else None
            
            operations = offer.find('OPERATIONS_LIST')
            operation_code = None
            if operations is not None:
                op_elem = operations.find('OPERATION_CODE')
                if op_elem is not None and op_elem.text:
                    operation_code = op_elem.text.strip()
            
            if type_code and operation_code:
                key = (type_code, operation_code)
                groups[key].append(offer)
        
        selected = []
        print(f"   ✓ {len(groups)} combinaisons trouvées\n")
        
        for (type_code, operation_code), offers in sorted(groups.items()):
            count = min(args.per_type, len(offers))
            selected.extend(offers[:count])
            print(f"   • {type_code} × {operation_code}: {count} offres")
        
        print(f"\n   ✓ {len(selected)} offres sélectionnées\n")
    else:
        selected = all_offers[:args.num_offers]
        print(f"   ✓ {len(selected)} offres sélectionnées\n")
    
    # Traduction si demandée
    if args.translate:
        if not TRANSLATION_AVAILABLE:
            print("❌ Traduction impossible: argostranslate non installé")
            sys.exit(1)
        
        init_translator()
        print("🔄 Traduction en cours...\n")
        
        total_fields = 0
        for i, offer in enumerate(selected, 1):
            bid_elem = offer.find('BUSINESS_ID')
            bid = bid_elem.text if bid_elem is not None else "?"
            
            print(f"   [{i}/{len(selected)}] Offre {bid}...", end=" ", flush=True)
            
            fields = translate_offer(offer)
            total_fields += fields
            
            print(f"{fields} champs")
        
        print(f"\n   ✓ {total_fields} champs traduits\n")
    
    # Créer le nouveau XML
    new_root = ET.Element('OFFERS_LIST')
    for offer in selected:
        new_root.append(offer)
    
    # Sauvegarder
    output_path = pathlib.Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    print(f"💾 Sauvegarde dans {output_path}...")
    indent_xml(new_root)
    ET.ElementTree(new_root).write(output_path, encoding='utf-8', xml_declaration=True)
    
    size_mb = output_path.stat().st_size / (1024 * 1024)
    
    print(f"\n{'='*70}")
    print(f"✅ TERMINÉ - {len(selected)} offres")
    print(f"📁 {output_path} ({size_mb:.2f} MB)")
    print(f"{'='*70}\n")
    return 0

# ============================================================================
# MODE TRANSLATE - Traduire un fichier existant
# ============================================================================

def cmd_translate(args) -> int:
    """Mode translate: Traduit un fichier XML existant FR→EN"""
    print(f"\n{'='*70}")
    print(f"MODE TRANSLATE - Traduction FR→EN")
    print(f"{'='*70}\n")
    
    source_path = pathlib.Path(args.source)
    if not source_path.exists():
        print(f"❌ Fichier source introuvable: {source_path}")
        sys.exit(1)
    
    if not TRANSLATION_AVAILABLE:
        print("❌ argostranslate non installé")
        sys.exit(1)
    
    init_translator()
    
    print(f"📖 Lecture de {source_path}...")
    tree = ET.parse(source_path)
    root = tree.getroot()
    
    offers = root.findall('.//OFFER')
    print(f"   ✓ {len(offers)} offres trouvées\n")
    
    print("🔄 Traduction en cours...\n")
    
    total_fields = 0
    for i, offer in enumerate(offers, 1):
        bid_elem = offer.find('BUSINESS_ID')
        bid = bid_elem.text if bid_elem is not None else "?"
        
        print(f"   [{i}/{len(offers)}] Offre {bid}...", end=" ", flush=True)
        
        fields = translate_offer(offer)
        total_fields += fields
        
        print(f"{fields} champs")
    
    print(f"\n   ✓ {total_fields} champs traduits\n")
    
    # Sauvegarder
    output_path = pathlib.Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    print(f"💾 Sauvegarde dans {output_path}...")
    indent_xml(root)
    tree.write(output_path, encoding='utf-8', xml_declaration=True)
    
    size_mb = output_path.stat().st_size / (1024 * 1024)
    
    print(f"\n{'='*70}")
    print(f"✅ TERMINÉ - {len(offers)} offres • {total_fields} traductions")
    print(f"📁 {output_path} ({size_mb:.2f} MB)")
    print(f"{'='*70}\n")
    return 0

# ============================================================================
# MAIN - CLI avec 3 modes
# ============================================================================

def main() -> int:
    parser = argparse.ArgumentParser(
        description='Générateur d\'offres BNPPRE - Script unifié',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog='''
Exemples:
  # Scraper 100 offres du site
  python3 generate.py scrape --limit 100 --output data/xml/offers.xml
  
  # Créer un échantillon de 50 par type avec traductions
  python3 generate.py sample --source data/xml/offers.xml --per-type 50 --translate --output data/xml/sample.xml
  
  # Traduire un fichier existant
  python3 generate.py translate --source data/xml/offers.xml --output data/xml/offers_FR_EN.xml
        '''
    )
    
    subparsers = parser.add_subparsers(dest='command', help='Commande à exécuter')
    
    # ========== COMMANDE SCRAPE ==========
    scrape_parser = subparsers.add_parser('scrape', help='Scraper le site bnppre.fr')
    scrape_parser.add_argument("--template", default="src/scripts/generate/templates/bnppre_offer_template.xml", help="XML template path")
    scrape_parser.add_argument("--output", required=True, help="Output XML path")
    scrape_parser.add_argument("--url", action="append", dest="urls", help="Offer URL to scrape (repeatable)")
    scrape_parser.add_argument("--mode", choices=["explicit", "all"], default="all", help="URL selection mode")
    scrape_parser.add_argument("--asset", action="append", dest="assets", help="Asset filter (repeatable), e.g. bureau")
    scrape_parser.add_argument("--operation", action="append", dest="operations", help="Operation filter LOC|VEN (repeatable)")
    scrape_parser.add_argument("--limit", type=int, help="Maximum number of offers")
    scrape_parser.add_argument("--sitemap-index", default=SITEMAP_INDEX_URL, help="Sitemap index URL")
    
    # ========== COMMANDE SAMPLE ==========
    sample_parser = subparsers.add_parser('sample', help='Créer un échantillon avec traductions optionnelles')
    sample_parser.add_argument('--source', required=True, help='Fichier XML source')
    sample_parser.add_argument('--output', required=True, help='Fichier XML de sortie')
    sample_parser.add_argument('--num-offers', type=int, default=10, help='Nombre d\'offres (mode simple)')
    sample_parser.add_argument('--per-type', type=int, help='Nombre d\'offres par TYPE×OPERATION (prioritaire)')
    sample_parser.add_argument('--translate', action='store_true', help='Activer la traduction FR→EN')
    
    # ========== COMMANDE TRANSLATE ==========
    translate_parser = subparsers.add_parser('translate', help='Traduire un fichier XML existant')
    translate_parser.add_argument('--source', required=True, help='Fichier XML source')
    translate_parser.add_argument('--output', required=True, help='Fichier XML de sortie')
    
    args = parser.parse_args()
    
    if not args.command:
        parser.print_help()
        sys.exit(1)
    
    # Router vers la bonne commande
    if args.command == 'scrape':
        return cmd_scrape(args)
    elif args.command == 'sample':
        return cmd_sample(args)
    elif args.command == 'translate':
        return cmd_translate(args)
    
    return 0


def fetch_html(url: str) -> str:
    request = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
    last_error: Exception | None = None
    for _ in range(3):
        try:
            with urllib.request.urlopen(request, timeout=30) as response:
                payload = response.read()
                is_gzip = url.lower().endswith(".gz") or payload[:2] == b"\x1f\x8b"
                if is_gzip:
                    payload = gzip.decompress(payload)
                return payload.decode("utf-8", errors="ignore")
        except (urllib.error.URLError, TimeoutError) as error:
            last_error = error
    raise RuntimeError(f"Unable to fetch {url}: {last_error}")


def resolve_target_urls(args: argparse.Namespace) -> list[str]:
    """Resolve target offer URLs from explicit input or sitemap discovery."""
    explicit_urls = args.urls or []
    if args.mode == "explicit":
        return (explicit_urls or DEFAULT_URLS)[: args.limit]

    asset_filters = [normalize_key(item) for item in (args.assets or []) if item]
    operation_filters = normalize_operation_filters(args.operations)
    discovered = discover_offer_urls(
        sitemap_index_url=args.sitemap_index,
        asset_filters=asset_filters,
        operation_filters=operation_filters,
        limit=args.limit,
    )

    # Explicit URLs are prepended in discovery modes so operators can force hotfix pages.
    merged = dedupe_urls(explicit_urls + discovered)
    return merged[: args.limit]


def discover_offer_urls(sitemap_index_url: str, asset_filters: list[str], operation_filters: list[str], limit: int) -> list[str]:
    """Discover offer URLs from sitemap index and apply operation/asset filtering."""
    urls: list[str] = []
    sitemap_urls = collect_sitemap_urls(sitemap_index_url)
    for sitemap_url in sitemap_urls:
        for page_url in parse_sitemap_loc_entries(fetch_html(sitemap_url)):
            if not is_offer_url(page_url):
                continue
            if not matches_operation_filter(page_url, operation_filters):
                continue
            if not matches_asset_filter(page_url, asset_filters):
                continue
            urls.append(page_url)
            if limit is not None and len(urls) >= limit:
                return dedupe_urls(urls)
    return dedupe_urls(urls)


def collect_sitemap_urls(index_url: str) -> list[str]:
    """Return sitemap file URLs from a sitemap index (with fallback to direct sitemap format)."""
    content = fetch_html(index_url)
    loc_entries = parse_sitemap_loc_entries(content)
    if not loc_entries:
        return []

    # If the index directly contains offer URLs, return the index as a sitemap payload source.
    if any(is_offer_url(url) for url in loc_entries):
        return [index_url]
    return loc_entries


def parse_sitemap_loc_entries(xml_text: str) -> list[str]:
    try:
        root = ET.fromstring(xml_text)
    except ET.ParseError:
        return []
    loc_nodes = root.findall(".//{*}loc")
    return [clean_text(node.text or "") for node in loc_nodes if clean_text(node.text or "")]


def is_offer_url(url: str) -> bool:
    return bool(re.search(r"/a-(?:louer|vendre)/[^/]+/.+-(O[LV][A-Z]+\d+)\.html$", url, re.I))


def matches_operation_filter(url: str, operation_filters: list[str]) -> bool:
    if not operation_filters:
        return True
    path = urllib.parse.urlparse(url).path.lower()
    expected_paths = {OPERATION_PATH_MAP.get(item, "").lower() for item in operation_filters}
    expected_paths.discard("")
    return any(f"/{segment}/" in path for segment in expected_paths)


def matches_asset_filter(url: str, asset_filters: list[str]) -> bool:
    if not asset_filters:
        return True
    path = urllib.parse.urlparse(url).path.lower()
    return any(f"/{asset}/" in path for asset in asset_filters)


def dedupe_urls(urls: list[str]) -> list[str]:
    seen = set()
    deduped = []
    for item in urls:
        if item in seen:
            continue
        seen.add(item)
        deduped.append(item)
    return deduped


def normalize_operation_filters(values: list[str] | None) -> list[str]:
    if not values:
        return []
    normalized: list[str] = []
    for item in values:
        value = normalize_key(item).upper()
        if value in {"LOC", "VEN"}:
            normalized.append(value)
            continue
        if value in {"LOUER", "LOCATION"}:
            normalized.append("LOC")
            continue
        if value in {"VENDRE", "VENTE"}:
            normalized.append("VEN")
    return dedupe_urls(normalized)




def extract_offer_data(url: str, html: str) -> dict:
    title = extract_page_title(html)
    # Extract node_id for BUSINESS_ID (Drupal entity ID)
    node_id = extract_node_id(html)
    if not node_id:
        raise RuntimeError(f"Unable to extract node_id from {url}")
    
    # Extract BNPPRE reference for TECHNICAL_ID
    bnppre_reference = first_non_empty(
        extract_js_var(html, "offerReference"),
        extract_reference_from_title(title),
        extract_reference_from_url(url),
    )
    if not bnppre_reference:
        raise RuntimeError(f"Unable to derive BNPPRE reference for {url}")

    offer_transaction = extract_js_var(html, "offerTransaction") or "Location"
    offer_actif = extract_js_var(html, "offerActif") or infer_asset_label(url, title)
    description_paragraphs = extract_description_paragraphs(html)
    page_description = extract_description_block(html)
    summary_text = extract_summary_text(html, title, offer_actif)
    description_secondary = extract_secondary_description(html, description_paragraphs, title)
    surface_value = extract_surface_value(html)
    price_value, starting_price = extract_price_value(html)
    price_format = extract_price_format(html)
    operation_code = map_operation_code(offer_transaction, url)
    availability = extract_availability(html)
    street_address = extract_address_line(html)
    postal_code = first_non_empty(extract_js_var(html, "offerPostaCode"), extract_meta_location(html, "postalCode"), extract_postal_from_url(url))
    city = first_non_empty(extract_js_var(html, "offerLocality"), extract_meta_location(html, "addressLocality"), extract_city_from_url(url))
    longitude, latitude = extract_geo_coordinates(html)
    contact_full_name = extract_contact_name(html)
    contact_firstname, contact_lastname = split_contact_name(contact_full_name)
    contact_phone = extract_contact_phone(html)
    contact_mail = extract_js_var(html, "offerBroker")
    contact_uid = extract_contact_uid(html)
    contact_avatar_raw = extract_contact_avatar(html)
    contact_avatar = sanitize_contact_avatar(contact_avatar_raw)
    contact_civility = infer_contact_civility(html, contact_full_name, contact_mail, contact_avatar_raw)
    division_rows = extract_divisions(html, bnppre_reference, offer_actif, availability)
    technical_rows = extract_technical_rows(html)
    global_surfaces = build_global_surfaces(surface_value, offer_actif, operation_code, division_rows, technical_rows)
    budgets = build_budgets(price_value, price_format, starting_price, operation_code, division_rows)
    media_rows = extract_media_rows(html, title)
    diagnostics = extract_diagnostics(html)

    return {
        "BUSINESS_ID": node_id,
        "TECHNICAL_ID": bnppre_reference,
        "REFERENTIEL_SOURCE": "BNPPRE",
        "COUNTRY": "FR",
        "LAST_UPDATE_DATE": now_date(),
        "TIMESTAMP": now_timestamp(),
        "TYPE_CODE": map_type_code(offer_actif, url, title),
        "NATURE_CODE": map_type_code(offer_actif, url, title),
        "SURFACE_UNIT_CODE": extract_surface_unit_code(html),
        "CURRENCY": "EUR",
        "LANGUAGE_PRIMARY": "FR",
        "LANGUAGE_SECONDARY": "EN",
        "AVAILABILITY_PRIMARY": availability,
        "AVAILABILITY_SECONDARY": "",
        "SUMMARY_DESCRIPTION_PRIMARY": summary_text,
        "SUMMARY_DESCRIPTION_SECONDARY": "",
        "DESCRIPTION_2_PRIMARY": description_secondary,
        "DESCRIPTION_2_SECONDARY": "",
        "FULL_DESCRIPTION_PRIMARY": page_description,
        "FULL_DESCRIPTION_SECONDARY": "",
        "PUBLICATION_END_DATE": "2999-01-01",
        "OPERATION_CODE": operation_code,
        "SURFACE_VALUE": surface_value,
        "SEARCH_ENGINE_SURFACE_TYPE": "NONE",
        "GLOBAL_SURFACE_QUALIFICATION_CODE": "TOTAL",
        "GLOBAL_SURFACES": global_surfaces,
        "IS_DIVISIBLE": "true" if is_divisible(html, url) else "false",
        "ZIP_CODE": postal_code,
        "CITY": city,
        "COUNTRY_ISO": "FR",
        "STREET_NUMBER": extract_street_number(street_address),
        "STREET_NAME": extract_street_name(street_address),
        "ADDRESS_LINE_1": street_address,
        "GEOGRAPHICAL_ZONE": extract_geographical_zone(html),
        "BUILDING_NAME": extract_building_name(html),
        "DISTRICT": extract_district(html, city, postal_code),
        "LONGITUDE": longitude,
        "LATITUDE": latitude,
        "DIAGNOSTICS": diagnostics,
        "TECHNICAL_ELEMENTS": technical_rows,
        "MEDIA": media_rows,
        "BUSINESS_LEADERS": [
            {
                "BUSINESS_LEADER_CIVILITY": contact_civility,
                "BUSINESS_LEADER_LASTNAME": contact_lastname,
                "BUSINESS_LEADER_FIRSTNAME": contact_firstname,
                "BUSINESS_LEADER_PHONE_NUMBER": contact_phone,
                "BUSINESS_LEADER_MAIL": contact_mail,
                "BUSINESS_LEADER_UID": contact_uid,
                "BUSINESS_LEADER_AVATAR_URL": contact_avatar,
                "BUSINESS_LEADER_CREATION_DATE": now_date(),
                "BUSINESS_LEADER_LAST_UPDATE_DATE": now_date(),
            }
        ],
        "MAIN_BUSINESS_LEADER_UID": contact_uid,
        "BUDGETS": budgets,
        "BUDGET_STARTING_PRICE": "true" if starting_price else "false",
        "BUDGET_VALUE": price_value,
        "BUDGET_PERIOD_CODE": infer_budget_period_code(price_format, operation_code),
        "BUDGET_UNIT_CODE": infer_budget_unit_code(price_format),
        "BUDGET_HT": "true",
        "BUDGET_CC": "false",
        "FEES": "From listing page",
        "DIVISIONS": division_rows,
        "SOURCE_URL": url,
        "SOURCE_TITLE": title,
        "SOURCE_ACTIF": offer_actif,
        "SOURCE_TRANSACTION": offer_transaction,
    }


def apply_offer_data(offer_node: ET.Element, data: dict) -> None:
    replace_list_items(offer_node, "GLOBAL_SURFACES", "SURFACE", data.get("GLOBAL_SURFACES", []), fill_global_surface_item)
    replace_list_items(offer_node, "BUDGETS_LIST", "BUDGET", data.get("BUDGETS", []), fill_budget_item)
    replace_list_items(offer_node, "DIAGNOSTICS_LIST", "DIAGNOSTIC", data.get("DIAGNOSTICS", []), fill_diagnostic_item)
    replace_list_items(offer_node, "TECHNICAL_ELEMENTS_LIST", "TECHNICAL_ELEMENT", data.get("TECHNICAL_ELEMENTS", []), fill_technical_item)
    replace_list_items(offer_node, "MEDIA_LIST", "MEDIA", data.get("MEDIA", []), fill_media_item)
    replace_list_items(offer_node, "BUSINESS_LEADERS_LIST", "BUSINESS_LEADER", data.get("BUSINESS_LEADERS", []), fill_business_leader_item)
    divisions = data.get("DIVISIONS", [])
    if divisions:
        replace_list_items(offer_node, "DIVISIONS_LIST", "DIVISION", divisions, fill_division_item)
    else:
        divisions_parent = offer_node.find("DIVISIONS_LIST")
        if divisions_parent is not None:
            offer_node.remove(divisions_parent)
    fill_placeholders(offer_node, data)


def replace_list_items(parent: ET.Element, container_tag: str, item_tag: str, items: list[dict], filler) -> None:
    container = parent.find(container_tag)
    if container is None:
        return
    template_item = container.find(item_tag)
    if template_item is None:
        return

    children = list(container)
    for child in children:
        container.remove(child)

    if not items:
        container.append(copy.deepcopy(template_item))
        return

    for item in items:
        clone = copy.deepcopy(template_item)
        filler(clone, item)
        container.append(clone)


def fill_diagnostic_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, {
        "DIAGNOSTIC_TYPE": item.get("DIAGNOSTIC_TYPE", ""),
        "DIAGNOSTIC_CLASS": item.get("DIAGNOSTIC_CLASS", ""),
        "DIAGNOSTIC_VALUE": item.get("DIAGNOSTIC_VALUE", ""),
        "DIAGNOSTIC_DATE": item.get("DIAGNOSTIC_DATE", now_date()),
        "DIAGNOSTIC_VALIDITY_END_DATE": item.get("DIAGNOSTIC_VALIDITY_END_DATE", "2999-01-01"),
        "DIAGNOSTIC_NO_CLASSIFICATION": item.get("DIAGNOSTIC_NO_CLASSIFICATION", ""),
        "DIAGNOSTIC_NON_APPLICABLE": item.get("DIAGNOSTIC_NON_APPLICABLE", ""),
    })


def fill_global_surface_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, {
        "SURFACE_VALUE": item.get("SURFACE_VALUE", "0.00"),
        "TYPE_CODE": item.get("TYPE_CODE", "BUR"),
        "NATURE_CODE": item.get("NATURE_CODE", "BUR"),
        "SEARCH_ENGINE_SURFACE_TYPE": item.get("SEARCH_ENGINE_SURFACE_TYPE", "NONE"),
        "GLOBAL_SURFACE_QUALIFICATION_CODE": item.get("GLOBAL_SURFACE_QUALIFICATION_CODE", "TOTAL"),
    })


def fill_budget_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, {
        "BUDGET_STARTING_PRICE": item.get("BUDGET_STARTING_PRICE", "false"),
        "BUDGET_VALUE": item.get("BUDGET_VALUE", "0.00"),
        "OPERATION_CODE": item.get("OPERATION_CODE", "LOC"),
        "BUDGET_PERIOD_CODE": item.get("BUDGET_PERIOD_CODE", ""),
        "BUDGET_UNIT_CODE": item.get("BUDGET_UNIT_CODE", "SUR"),
        "BUDGET_HT": item.get("BUDGET_HT", "true"),
        "BUDGET_CC": item.get("BUDGET_CC", "false"),
    })
    value_type_node = element.find("VALUE_TYPE")
    if value_type_node is not None:
        value_type_node.text = item.get("BUDGET_VALUE_TYPE", "")


def fill_technical_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, item)


def fill_media_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, item)


def fill_business_leader_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, item)


def fill_division_item(element: ET.Element, item: dict) -> None:
    fill_placeholders(element, item)


def fill_placeholders(element: ET.Element, data: dict) -> None:
    if element.text:
        for key, value in data.items():
            if isinstance(value, (list, dict)):
                continue
            element.text = element.text.replace(f"{{{{{key}}}}}", xml_text(value))
    for attr_name, attr_value in list(element.attrib.items()):
        for key, value in data.items():
            if isinstance(value, (list, dict)):
                continue
            attr_value = attr_value.replace(f"{{{{{key}}}}}", xml_text(value))
        element.attrib[attr_name] = attr_value
    for child in list(element):
        fill_placeholders(child, data)


def extract_page_title(html: str) -> str:
    match = re.search(r"<title>(.*?)</title>", html, re.I | re.S)
    return clean_text(match.group(1)) if match else ""


def extract_reference_from_title(title: str) -> str:
    match = re.search(r"(O[LV][A-Z]+\d+)", title)
    return match.group(1) if match else ""


def extract_reference_from_url(url: str) -> str:
    match = re.search(r"(O[LV][A-Z]+\d+)(?:\.html)?$", url)
    return match.group(1) if match else ""


def extract_js_var(html: str, name: str) -> str:
    patterns = [
        rf"var\s+{re.escape(name)}\s*=\s*'([^']*)';",
        rf'var\s+{re.escape(name)}\s*=\s*"([^"]*)";',
        rf"{re.escape(name)}\s*=\s*'([^']*)';",
        rf'{re.escape(name)}\s*=\s*"([^"]*)";',
    ]
    for pattern in patterns:
        match = re.search(pattern, html, re.S)
        if match:
            return decode_js_string(match.group(1))
    return ""


def decode_js_string(value: str) -> str:
    try:
        decoded = bytes(value, "utf-8").decode("unicode_escape")
    except UnicodeDecodeError:
        decoded = value
    return html_lib.unescape(decoded)


def extract_meta_location(html: str, key: str) -> str:
    pattern = rf'<meta[^>]+itemprop="{re.escape(key)}"[^>]+content="([^"]*)"'
    match = re.search(pattern, html, re.I | re.S)
    return clean_text(html_lib.unescape(match.group(1))) if match else ""


def extract_description_block(html: str) -> str:
    match = re.search(r'<div id="description".*?<div class="offer--block-description">(.*?)</div></div></h2>', html, re.I | re.S)
    if not match:
        match = re.search(r'<div id="description".*?<div class="offer--block-description">(.*?)</div>', html, re.I | re.S)
    return html_to_text(match.group(1)) if match else ""


def extract_summary_text(html: str, title: str, offer_actif: str) -> str:
    meta_description = extract_meta_content(html, "description") or extract_meta_content(html, "og:description")
    if meta_description:
        return truncate_text(meta_description, 220)
    paragraphs = extract_description_paragraphs(html)
    if paragraphs:
        return paragraphs[0]
    if title:
        return title
    return f"Location {offer_actif}".strip()


def extract_secondary_description(html: str, paragraphs: list[str], title: str) -> str:
    if len(paragraphs) >= 2:
        return "\n\n".join(paragraphs[1:])
    meta_description = extract_meta_content(html, "description") or extract_meta_content(html, "og:description")
    if meta_description:
        return meta_description
    if paragraphs:
        return paragraphs[0]
    return title


def extract_description_paragraphs(html: str) -> list[str]:
    match = re.search(r'<div id="description".*?<div class="offer--block-description">(.*?)</div></div></h2>', html, re.I | re.S)
    if not match:
        match = re.search(r'<div id="description".*?<div class="offer--block-description">(.*?)</div>', html, re.I | re.S)
    if not match:
        return []
    block = match.group(1)
    paragraphs = re.findall(r'<p[^>]*>(.*?)</p>', block, re.I | re.S)
    cleaned = [clean_text(html_to_text(item)) for item in paragraphs]
    return [item for item in cleaned if item]


def extract_meta_content(html: str, name: str) -> str:
    patterns = [
        rf'<meta[^>]+name="{re.escape(name)}"[^>]+content="([^"]+)"',
        rf'<meta[^>]+property="{re.escape(name)}"[^>]+content="([^"]+)"',
    ]
    for pattern in patterns:
        match = re.search(pattern, html, re.I | re.S)
        if match:
            return clean_text(html_lib.unescape(match.group(1)))
    return ""


def extract_node_id(html: str) -> str:
    """Extract Drupal node_id from data-nid attribute."""
    match = re.search(r'data-nid="([0-9]+)"', html)
    return match.group(1) if match else ""


def extract_surface_value(html: str) -> str:
    patterns = [
        r'<p class="surface-value">\s*([^<]+?)\s*</p>',
        r'<div class="lot-surface">\s*([^<]+?)\s*</div>',
        r"var\s+offerSurface\s*=\s*'([^']+)';",
    ]
    for pattern in patterns:
        match = re.search(pattern, html, re.I | re.S)
        if match:
            return normalize_number_text(match.group(1))
    return ""


def extract_price_value(html: str) -> tuple[str, bool]:
    if "à partir de" in html or "a partir de" in html.lower():
        match = re.search(r'<span[^>]+data-value="price-value">\s*([^<]+?)\s*</span>', html, re.I | re.S)
        if match:
            return normalize_number_text(match.group(1)), True
    match = re.search(r"dl_price:\s*'([^']+)'", html)
    if match:
        return normalize_number_text(match.group(1)), False
    match = re.search(r'<span[^>]+data-value="price-value">\s*([^<]+?)\s*</span>', html, re.I | re.S)
    if match:
        return normalize_number_text(match.group(1)), False
    return "0.00", False


def extract_price_format(html: str) -> str:
    match = re.search(r'<p class="price-format">\s*([^<]+?)\s*</p>', html, re.I | re.S)
    return clean_text(match.group(1)) if match else ""


def extract_availability(html: str) -> str:
    # Primary source from the rendered offer block:
    # <div class="offer-content-availability"> ... <span class="availability-value">...</span>
    match = re.search(r'<span class="availability-value">\s*([^<]+?)\s*</span>', html, re.I | re.S)
    if match:
        return normalize_availability_text(match.group(1))

    match = re.search(r'Disponibilit[ée]\s*:\s*([^<]+)', html, re.I | re.S)
    if match:
        return normalize_availability_text(match.group(1))

    match = re.search(r'lot-availability-value-value highlight">\s*([^<]+?)\s*<', html, re.I | re.S)
    return normalize_availability_text(match.group(1)) if match else ""


def normalize_availability_text(value: str) -> str:
    value = clean_text(html_lib.unescape(value))
    if not value:
        return ""
    if re.match(r"^\d{2}/\d{2}/\d{4}$", value):
        return value
    return value[0].upper() + value[1:]


def extract_address_line(html: str) -> str:
    street = extract_meta_location(html, "streetAddress")
    if street:
        return street
    match = re.search(r"var\s+offerAddress\s*=\s*'([^']+)';", html)
    return clean_text(html_lib.unescape(match.group(1).replace("\\u0020", " "))) if match else ""


def extract_geo_coordinates(html: str) -> tuple[str, str]:
    match = re.search(r'var\s+geocode\s*=\s*(\{.*?\});', html, re.I | re.S)
    if not match:
        return "", ""
    try:
        data = json.loads(match.group(1))
        location = data["results"][0]["geometry"]["location"]
        return str(location.get("lng", "")), str(location.get("lat", ""))
    except Exception:
        return "", ""


def extract_contact_name(html: str) -> str:
    match = re.search(r'<div class="contact-info">.*?<p class="h3">\s*([^<]+?)\s*</p>', html, re.I | re.S)
    return clean_text(match.group(1)) if match else ""


def split_contact_name(full_name: str) -> tuple[str, str]:
    if not full_name:
        return "", ""
    parts = full_name.split()
    if len(parts) == 1:
        return full_name, ""
    return " ".join(parts[:-1]), parts[-1]


def infer_contact_civility(html: str, full_name: str, email: str, avatar_url: str) -> str:
    explicit = extract_explicit_civility(html, full_name)
    if explicit:
        return explicit

    avatar_lower = avatar_url.lower()
    if "broker-homme" in avatar_lower:
        return "MR"
    if "broker-femme" in avatar_lower:
        return "MRS"

    candidates = []
    first_name = split_contact_name(full_name)[0].split(" ")[0] if full_name else ""
    if first_name:
        candidates.append(first_name)

    email_local = email.split("@", 1)[0] if email and "@" in email else ""
    if email_local:
        email_local = re.split(r"[._-]+", email_local)[0]
        if email_local:
            candidates.append(email_local)

    female_score = 0
    male_score = 0
    for candidate in candidates:
        normalized = normalize_key(candidate)
        if normalized in FEMALE_FIRST_NAMES:
            female_score += 1
        if normalized in MALE_FIRST_NAMES:
            male_score += 1

    if female_score > male_score and female_score > 0:
        return "MRS"
    if male_score > female_score and male_score > 0:
        return "MR"
    return ""


def extract_explicit_civility(html: str, full_name: str) -> str:
    # First, try civility tokens directly before the contact full name.
    if full_name:
        escaped_name = re.escape(full_name)
        around_name = re.search(
            rf"(?:^|[^A-Za-z])(mme|madame|mlle|mademoiselle|m\.|mr\.|monsieur)\s+{escaped_name}",
            html,
            re.I,
        )
        if around_name:
            return normalize_civility_token(around_name.group(1))

    return ""


def normalize_civility_token(token: str) -> str:
    normalized = normalize_key(token)
    if normalized in {"mme", "madame", "mlle", "mademoiselle"}:
        return "MRS"
    if normalized in {"m", "mr", "monsieur"}:
        return "MR"
    return ""


def extract_contact_phone(html: str) -> str:
    match = re.search(r'<a class="phone-link" href="tel:([^"]+)"', html, re.I | re.S)
    return match.group(1) if match else ""


def extract_contact_uid(html: str) -> str:
    match = re.search(r'data-uid="([^"]+)"', html, re.I | re.S)
    if match:
        return match.group(1)
    match = re.search(r"var\s+offerBrokerTeamId\s*=\s*'([^']+)';", html)
    return match.group(1) if match else "0"


def extract_contact_avatar(html: str) -> str:
    match = re.search(r'<div class="picture">\s*<img[^>]+src="([^"]+)"', html, re.I | re.S)
    if not match:
        return ""
    return normalize_media_url(match.group(1))


def sanitize_contact_avatar(url: str) -> str:
    if not url:
        return ""
    lowered = url.lower()
    if re.search(r"/broker-[a-z0-9_-]+\.(png|jpe?g|webp|svg)$", lowered):
        return ""
    return url


def extract_building_name(html: str) -> str:
    match = re.search(r'<div class="building-name">\s*([^<]+?)\s*</div>', html, re.I | re.S)
    return clean_text(match.group(1)) if match else ""


def extract_geographical_zone(html: str) -> str:
    match = re.search(r'<meta[^>]+itemprop="addressRegion"[^>]+content="([^"]+)"', html, re.I | re.S)
    if match:
        return clean_text(html_lib.unescape(match.group(1)))
    match = re.search(r'var\s+geocode\s*=\s*(\{.*?\});', html, re.I | re.S)
    if not match:
        return ""
    try:
        data = json.loads(match.group(1))
        components = data["results"][0]["address_components"]
        for component in components:
            if "administrative_area_level_1" in component.get("types", []):
                return clean_text(component.get("long_name", ""))
    except Exception:
        return ""
    return ""


def extract_district(html: str, city: str, postal_code: str) -> str:
    match = re.search(r'<div class="district">\s*([^<]+?)\s*</div>', html, re.I | re.S)
    if match:
        return clean_text(match.group(1))
    if postal_code.startswith("75") and city.lower() == "paris":
        return f"Paris {postal_code[2:4]}"
    return ""


def extract_street_number(street_address: str) -> str:
    match = re.match(r"\s*(\d+[\w/-]*)\b", street_address)
    return match.group(1) if match else ""


def extract_street_name(street_address: str) -> str:
    street_number = extract_street_number(street_address)
    if not street_number:
        return street_address
    return clean_text(street_address[len(street_number):])


def extract_surface_unit_code(html: str) -> str:
    text = html.lower()
    if "ha" in text and "m²" not in text:
        return "HA"
    return "M2"


def is_divisible(html: str, url: str) -> bool:
    if "non-divisible" in url:
        return False
    return bool(re.search(r'Divisible\s+dès|divisible', html, re.I))


def extract_media_rows(html: str, title: str) -> list[dict]:
    rows: list[dict] = []
    seen: set[str] = set()
    image_candidates: list[tuple[str, str]] = []
    for match in re.finditer(r'(<img[^>]+src="([^"]+)"[^>]*>)', html, re.I | re.S):
        tag = match.group(1)
        src = match.group(2)
        alt_match = re.search(r'alt="([^"]*)"', tag, re.I | re.S)
        image_candidates.append((src, clean_text(alt_match.group(1)) if alt_match else ""))
    for match in re.finditer(r'<source[^>]+srcset="([^"]+)"', html, re.I | re.S):
        image_candidates.append((match.group(1).split()[0], ""))

    normalized_candidates: list[tuple[str, str]] = []
    alt_by_url: dict[str, str] = {}
    for raw_url, raw_alt in image_candidates:
        url = normalize_media_url(raw_url)
        if not url:
            continue
        normalized_candidates.append((url, raw_alt))
        if raw_alt and (url not in alt_by_url or len(raw_alt) > len(alt_by_url[url])):
            alt_by_url[url] = raw_alt

    for index, (url, raw_alt) in enumerate(normalized_candidates, start=1):
        if "offers/" not in url and "/offres/" not in url and "360" not in url:
            continue
        if url in seen:
            continue
        seen.add(url)
        image_alt = raw_alt or alt_by_url.get(url, "") or title
        rows.append(
            {
                "MEDIA_TYPE_CODE": "EXT",
                "MEDIA_VALUE": pathlib.Path(url).name,
                "MEDIA_URL": url,
                "MEDIA_ORDER": str(index),
                "MEDIA_WATERMARK": "0",
                "MEDIA_COPYRIGHT_PRIMARY": "",
                "MEDIA_COPYRIGHT_SECONDARY": "",
                "MEDIA_ALTERNATIVE_PRIMARY": image_alt,
                "MEDIA_ALTERNATIVE_SECONDARY": "",
            }
        )

    for visit_url in extract_viewer_urls(html, mode="360"):
        if visit_url in seen:
            continue
        seen.add(visit_url)
        rows.append(
            {
                "MEDIA_TYPE_CODE": "VIS",
                "MEDIA_VALUE": "360",
                "MEDIA_URL": visit_url,
                "MEDIA_ORDER": str(len(rows) + 1),
                "MEDIA_WATERMARK": "0",
                "MEDIA_COPYRIGHT_PRIMARY": "",
                "MEDIA_COPYRIGHT_SECONDARY": "",
                "MEDIA_ALTERNATIVE_PRIMARY": "Visite 360",
                "MEDIA_ALTERNATIVE_SECONDARY": "",
            }
        )

    for video_url in extract_viewer_urls(html, mode="video"):
        if video_url in seen:
            continue
        seen.add(video_url)
        rows.append(
            {
                "MEDIA_TYPE_CODE": "VID",
                "MEDIA_VALUE": pathlib.Path(video_url).name if video_url.startswith("http") else "video",
                "MEDIA_URL": video_url,
                "MEDIA_ORDER": str(len(rows) + 1),
                "MEDIA_WATERMARK": "0",
                "MEDIA_COPYRIGHT_PRIMARY": "",
                "MEDIA_COPYRIGHT_SECONDARY": "",
                "MEDIA_ALTERNATIVE_PRIMARY": "Vidéo",
                "MEDIA_ALTERNATIVE_SECONDARY": "",
            }
        )

    if ("Visite 360°" in html or "360°" in html) and not any(row["MEDIA_TYPE_CODE"] == "VIS" for row in rows):
        fallback_url = extract_page_url(html)
        if fallback_url and fallback_url not in seen:
            seen.add(fallback_url)
            rows.append(
                {
                    "MEDIA_TYPE_CODE": "VIS",
                    "MEDIA_VALUE": "360",
                    "MEDIA_URL": fallback_url,
                    "MEDIA_ORDER": str(len(rows) + 1),
                    "MEDIA_WATERMARK": "0",
                    "MEDIA_COPYRIGHT_PRIMARY": "",
                    "MEDIA_COPYRIGHT_SECONDARY": "",
                    "MEDIA_ALTERNATIVE_PRIMARY": "Visite 360",
                    "MEDIA_ALTERNATIVE_SECONDARY": "",
                }
            )

    if rows:
        special_rows = [row for row in rows if row.get("MEDIA_TYPE_CODE") in {"VIS", "VID"}]
        regular_rows = [row for row in rows if row.get("MEDIA_TYPE_CODE") not in {"VIS", "VID"}]
        kept_regular = regular_rows[: max(0, 40 - len(special_rows))]
        return kept_regular + special_rows

    return [
        {
            "MEDIA_TYPE_CODE": "EXT",
            "MEDIA_VALUE": "",
            "MEDIA_URL": "",
            "MEDIA_ORDER": "1",
            "MEDIA_WATERMARK": "0",
            "MEDIA_COPYRIGHT_PRIMARY": "",
            "MEDIA_COPYRIGHT_SECONDARY": "",
            "MEDIA_ALTERNATIVE_PRIMARY": title,
            "MEDIA_ALTERNATIVE_SECONDARY": "",
        }
    ]


def extract_viewer_urls(html: str, mode: str) -> list[str]:
    urls: list[str] = []
    if mode == "360":
        pattern = r'(?is)<div class="media-viewer-slide[^\"]*?360[^\"]*"[^>]*>.*?<iframe[^>]+src="([^"]+)"'
    else:
        pattern = r'(?is)<div class="media-viewer-slide[^\"]*?video[^\"]*"[^>]*>.*?(?:<iframe[^>]+src="([^"]+)"|<video[^>]+src="([^"]+)"|<source[^>]+src="([^"]+)")'

    for match in re.finditer(pattern, html, re.I | re.S):
        if mode == "360":
            url = match.group(1)
        else:
            url = next((group for group in match.groups() if group), "")
        url = absolute_url(clean_text(url))
        if url:
            urls.append(url)

    # Backup for pages where video embeds are not wrapped in dedicated viewer slide classes.
    if mode == "video" and not urls:
        backup = re.finditer(
            r'(?is)(https?://[^"\'\s>]*(?:youtube\.com|youtu\.be|vimeo\.com|dailymotion\.com|wistia\.com|player\.vimeo\.com)[^"\'\s>]*)',
            html,
            re.I | re.S,
        )
        for match in backup:
            url = absolute_url(clean_text(match.group(1)))
            if url and "/channel/" not in url and "/user/" not in url:
                urls.append(url)

    unique_urls: list[str] = []
    seen: set[str] = set()
    for url in urls:
        if url in seen:
            continue
        seen.add(url)
        unique_urls.append(url)
    return unique_urls


def extract_technical_rows(html: str) -> list[dict]:
    rows: list[dict] = []
    for group_match in re.finditer(r'<div class="block-prestations">\s*<h3>(.*?)</h3>\s*<ul>(.*?)</ul>\s*</div>', html, re.I | re.S):
        group_label = clean_text(html_to_text(group_match.group(1)))
        group_slug = slugify(group_label).upper()
        items_block = group_match.group(2)
        for item_match in re.finditer(r'<li class="([^"]+)">.*?<p>(.*?)</p>', items_block, re.I | re.S):
            class_name = item_match.group(1)
            raw_item_label = clean_text(html_to_text(item_match.group(2)))
            item_label, item_value, item_unit = split_technical_label_value(raw_item_label)
            element_slug = slugify(class_name.replace("tec-", "")).upper().replace("-", "_")
            rows.append(
                {
                    "TECHNICAL_CODE_GROUP": group_slug,
                    "TECHNICAL_CODE_TYPOLOGIE": "STANDARD",
                    "TECHNICAL_CODE_ELEMENT": f"TEC_{element_slug}",
                    "TECHNICAL_LIBELLE_ELEMENT": item_label,
                    "LANGUAGE_PRIMARY": "FR",
                    "LANGUAGE_SECONDARY": "EN",
                    "TECHNICAL_LABEL_PRIMARY": item_label,
                    "TECHNICAL_LABEL_SECONDARY": "",
                    "TECHNICAL_VALUE": item_value,
                    "TECHNICAL_UNIT_DERIVED": item_unit,
                    "TECHNICAL_COMPLEMENT_PRIMARY": "",
                    "TECHNICAL_COMPLEMENT_SECONDARY": "",
                }
            )
    return rows


def split_technical_label_value(raw_label: str) -> tuple[str, str, str]:
    label = clean_text(raw_label)
    if ":" not in label:
        return label, "", ""

    head, tail = label.split(":", 1)
    parsed_label = clean_text(head)
    parsed_value = clean_text(tail)
    if not parsed_value:
        return parsed_label, "", ""

    normalized_bool = normalize_french_boolean(parsed_value)
    if normalized_bool:
        return parsed_label, normalized_bool, ""

    numeric_match = re.match(r"^([+-]?\d[\d\s.,]*)(?:\s+(.+))?$", parsed_value)
    if numeric_match:
        number = numeric_match.group(1).replace(" ", "").replace(",", ".")
        unit = clean_text(numeric_match.group(2) or "")
        return parsed_label, number, unit

    return parsed_label, parsed_value, ""


def normalize_french_boolean(value: str) -> str:
    lowered = normalize_key(value)
    if lowered in {"oui", "yes", "true", "vrai"}:
        return "true"
    if lowered in {"non", "no", "false", "faux"}:
        return "false"
    return ""


def extract_diagnostics(html: str) -> list[dict]:
    section = re.search(r'<section id="energy-diags".*?</section>', html, re.I | re.S)
    if not section:
        return [
            {
                "DIAGNOSTIC_TYPE": "DPE",
                "DIAGNOSTIC_CLASS": "",
                "DIAGNOSTIC_VALUE": "",
                "DIAGNOSTIC_DATE": now_date(),
                "DIAGNOSTIC_VALIDITY_END_DATE": "2999-01-01",
                "DIAGNOSTIC_NO_CLASSIFICATION": "",
                "DIAGNOSTIC_NON_APPLICABLE": "",
            }
        ]
    block = section.group(0)
    diagnostics = []
    for css_class, diagnostic_type in (("conso-energy", "DPE"), ("conso-gas", "GES")):
        if css_class not in block:
            continue
        diagnostic_class = extract_energy_grade(block, css_class)
        diagnostic_value = extract_energy_legend(block, css_class)
        is_unavailable = not diagnostic_class or diagnostic_class == "X" or diagnostic_value == "?"
        diagnostics.append(
            {
                "DIAGNOSTIC_TYPE": diagnostic_type,
                "DIAGNOSTIC_CLASS": "" if is_unavailable else diagnostic_class,
                "DIAGNOSTIC_VALUE": "" if is_unavailable else diagnostic_value,
                "DIAGNOSTIC_DATE": now_date(),
                "DIAGNOSTIC_VALIDITY_END_DATE": "2999-01-01",
                "DIAGNOSTIC_NO_CLASSIFICATION": "",
                "DIAGNOSTIC_NON_APPLICABLE": "",
            }
        )
    return diagnostics or [
        {
            "DIAGNOSTIC_TYPE": "DPE",
            "DIAGNOSTIC_CLASS": "",
                "DIAGNOSTIC_VALUE": "",
            "DIAGNOSTIC_DATE": now_date(),
            "DIAGNOSTIC_VALIDITY_END_DATE": "2999-01-01",
            "DIAGNOSTIC_NO_CLASSIFICATION": "",
            "DIAGNOSTIC_NON_APPLICABLE": "",
        }
    ]


def extract_energy_grade(block: str, css_class: str) -> str:
    match = re.search(rf'<figure class="{re.escape(css_class)}"[^>]*>.*?<svg[^>]+class="class-([^"]+)"', block, re.I | re.S)
    if match:
        return clean_text(match.group(1))
    match = re.search(rf'<figure class="{re.escape(css_class)}"[^>]*data-class="([^"]*)"', block, re.I | re.S)
    if match:
        return clean_text(match.group(1))
    return ""


def extract_energy_legend(block: str, css_class: str) -> str:
    match = re.search(rf'<figure class="{re.escape(css_class)}"[^>]*>.*?<text[^>]+class="legend"[^>]*>([^<]+)</text>', block, re.I | re.S)
    if match:
        value = clean_text(html_lib.unescape(match.group(1)))
        match_value = re.match(r"^([\d\s.,]+)", value)
        if match_value:
            return clean_text(match_value.group(1)).replace(" ", "").replace(",", ".")
        return value
    return ""


def map_type_code(offer_actif: str, url: str, title: str) -> str:
    label = normalize_key(offer_actif)
    if label in TYPE_CODE_MAP:
        return TYPE_CODE_MAP[label]
    for key, value in TYPE_CODE_MAP.items():
        if key in normalize_key(url) or key in normalize_key(title):
            return value
    return "BUR"


def map_operation_code(offer_transaction: str, url: str) -> str:
    label = normalize_key(offer_transaction)
    if label in OPERATION_CODE_MAP:
        return OPERATION_CODE_MAP[label]
    if "louer" in url:
        return "LOC"
    if "vendre" in url:
        return "VEN"
    return "LOC"


def infer_asset_label(url: str, title: str) -> str:
    for label in ["Bureau", "Commerce", "Activité", "Entrepôt", "Coworking", "Terrain"]:
        if normalize_key(label) in normalize_key(url) or normalize_key(label) in normalize_key(title):
            return label
    return "Bureau"


def infer_budget_period_code(price_format: str, operation_code: str = "LOC") -> str:
    if operation_code == "VEN":
        return ""
    if "/an" in price_format.lower():
        return "ANN"
    if "/mois" in price_format.lower():
        return "MEN"
    return "ANN"


def infer_budget_unit_code(price_format: str) -> str:
    text = price_format.lower()
    if "m²" in text or "m2" in text:
        return "SUR"
    return "GLO"


def normalize_media_url(raw_url: str) -> str:
    raw_url = html_lib.unescape(raw_url)
    raw_url = raw_url.split("?")[0]
    if raw_url.startswith("http"):
        url = raw_url
    else:
        url = absolute_url(raw_url)
    match = re.search(r"/sites/default/files/styles/[^/]+/public/(.+?)(?:\.webp)?$", url)
    if match:
        return f"https://www.bnppre.fr/sites/default/files/{match.group(1)}"
    return url


def truncate_text(value: str, limit: int) -> str:
    value = clean_text(value)
    if len(value) <= limit:
        return value
    return value[: limit - 1].rstrip() + "…"


def extract_postal_from_url(url: str) -> str:
    match = re.search(r"-(\d{5})/", url)
    return match.group(1) if match else ""


def extract_city_from_url(url: str) -> str:
    match = re.search(r"/([a-z-]+)-(\d{5})/", url)
    if match:
        return clean_text(match.group(1).replace("-", " ").title())
    return ""


def extract_divisions(html: str, business_id: str, offer_actif: str, availability: str) -> list[dict]:
    wrappers = list(re.finditer(r'<div class="lot-divisions-wrapper">.*?<tbody>(.*?)</tbody>.*?</div>', html, re.I | re.S))
    if not wrappers:
        return []
    lot_availability = extract_lot_availability(html)
    rows = []
    lot_index = 1
    for wrapper in wrappers:
        tbody = wrapper.group(1)
        for row_match in re.finditer(r'<tr class="lot-division">(.*?)</tr>', tbody, re.I | re.S):
            row = row_match.group(1)
            surface = extract_cell_text(row, "lot-division-surface")
            if not surface:
                continue
            nature_label = extract_cell_text(row, "lot-division-nature")
            division_price_m2 = normalize_number_text(extract_cell_text(row, "lot-division-price-m2"))
            division_price_global = normalize_number_text(extract_cell_text(row, "lot-division-price-global"))
            rows.append(
                {
                    "DIVISION_FLOOR": extract_cell_text(row, "lot-division-floor"),
                    "DIVISION_BUILDING_NAME": extract_cell_text(row, "division-building"),
                    "DIVISION_TYPE_CODE": map_type_code(offer_actif, "", business_id),
                    "DIVISION_NATURE_CODE": map_type_code(offer_actif, "", business_id),
                    "DIVISION_NATURE_LABEL": nature_label,
                    "DIVISION_LOT": f"{business_id}/{lot_index}",
                    "DIVISION_SURFACE_VALUE": normalize_number_text(surface),
                    "DIVISION_SEARCH_ENGINE_SURFACE_TYPE": "NONE",
                    "DIVISION_SURFACE_QUALIFICATION_CODE": "DISPO",
                    "DIVISION_AVAILABILITY_PRIMARY": lot_availability or availability or "Immédiate",
                    "DIVISION_AVAILABILITY_SECONDARY": "",
                    "DIVISION_PRICE_M2": division_price_m2,
                    "DIVISION_PRICE_GLOBAL": division_price_global,
                    "LANGUAGE_PRIMARY": "FR",
                    "LANGUAGE_SECONDARY": "EN",
                }
            )
            lot_index += 1
    return rows


def extract_lot_availability(html: str) -> str:
    match = re.search(r'<span class="lot-availability-value-value highlight">\s*([^<]+?)\s*</span>', html, re.I | re.S)
    if not match:
        return ""
    return normalize_availability_text(match.group(1))


def build_global_surfaces(surface_value: str, offer_actif: str, operation_code: str, divisions: list[dict], technical_rows: list[dict]) -> list[dict]:
    type_code = map_type_code(offer_actif, "", "")
    rows: list[dict] = []
    seen: set[tuple[str, str, str]] = set()

    def add_surface(value: str, search_type: str, qualification: str) -> None:
        if not value:
            return
        key = (value, search_type, qualification)
        if key in seen:
            return
        seen.add(key)
        rows.append(
            {
                "SURFACE_VALUE": format_decimal_2(value),
                "TYPE_CODE": type_code,
                "NATURE_CODE": type_code,
                "SEARCH_ENGINE_SURFACE_TYPE": search_type,
                "GLOBAL_SURFACE_QUALIFICATION_CODE": qualification,
            }
        )

    division_values = [item.get("DIVISION_SURFACE_VALUE", "") for item in divisions if item.get("DIVISION_SURFACE_VALUE")]
    total_from_divisions = sum_decimal_strings(division_values)
    total_value = surface_value or total_from_divisions
    add_surface(total_value, "NONE", "TOTAL")

    # CRM expects VEN offers to expose both TOTAL and DISPO at global level.
    if operation_code == "VEN":
        add_surface(total_value, type_code, "DISPO")

    if division_values:
        if operation_code != "VEN":
            add_surface(select_min_budget_value(division_values), type_code, "MINIM")
        if operation_code != "VEN":
            add_surface(total_from_divisions, type_code, "DISPO")

    if operation_code != "VEN":
        # Try to extract ETREF from technical elements first
        etref_value = extract_etref_from_technical(technical_rows)
        if not etref_value:
            # Fallback: for LOC, use DISPO or TOTAL as ETREF
            etref_value = total_from_divisions or total_value
        if etref_value:
            add_surface(etref_value, "NONE", "ETREF")

    return rows


def extract_etref_from_technical(technical_rows: list[dict]) -> str:
    for row in technical_rows:
        label = normalize_key(row.get("TECHNICAL_LIBELLE_ELEMENT", ""))
        code = normalize_key(row.get("TECHNICAL_CODE_ELEMENT", ""))
        if "effectif theorique" in label or "effectif thorique" in label or "tec effectif thorique" in code:
            value = clean_text(row.get("TECHNICAL_VALUE", ""))
            if not value:
                continue
            normalized = value.replace(" ", "").replace(",", ".")
            try:
                return f"{float(normalized):.2f}"
            except ValueError:
                return ""
    return ""


def sum_decimal_strings(values: list[str]) -> str:
    total = 0.0
    for value in values:
        try:
            total += float(value)
        except ValueError:
            continue
    return f"{total:.2f}" if total > 0 else ""


def format_decimal_2(value: str) -> str:
    try:
        return f"{float(value):.2f}"
    except ValueError:
        return value


def build_budgets(price_value: str, price_format: str, starting_price: bool, operation_code: str, divisions: list[dict]) -> list[dict]:
    m2_values = [item.get("DIVISION_PRICE_M2", "") for item in divisions if item.get("DIVISION_PRICE_M2")]
    global_values = [item.get("DIVISION_PRICE_GLOBAL", "") for item in divisions if item.get("DIVISION_PRICE_GLOBAL")]

    period_code = infer_budget_period_code(price_format, operation_code)
    has_multiple_lots = len(set(m2_values)) > 1 or len(set(global_values)) > 1

    budget_value = ""
    budget_unit = "SUR"
    budget_value_type = ""

    if operation_code == "VEN":
        budget_value = select_min_budget_value(global_values) or price_value
        budget_unit = "GLO"
        period_code = ""
    else:
        if m2_values:
            budget_value = select_min_budget_value(m2_values)
            budget_unit = "SUR"
            if starting_price or has_multiple_lots:
                budget_value_type = "MIN"
        elif global_values:
            budget_value = select_min_budget_value(global_values)
            budget_unit = "GLO"
        else:
            budget_value = price_value
            budget_unit = infer_budget_unit_code(price_format)
            if budget_unit == "SUR" and (starting_price or period_code == "MEN"):
                budget_value_type = "MIN"

    if not budget_value:
        return []

    return [
        {
            "BUDGET_STARTING_PRICE": "true" if starting_price else "false",
            "BUDGET_VALUE": format_decimal_budget(budget_value),
            "BUDGET_VALUE_TYPE": budget_value_type,
            "OPERATION_CODE": operation_code,
            "BUDGET_PERIOD_CODE": period_code,
            "BUDGET_UNIT_CODE": budget_unit,
            "BUDGET_HT": "true",
            "BUDGET_CC": "false",
        }
    ]


def format_decimal_budget(value: str) -> str:
    try:
        return f"{float(value):.2f}"
    except ValueError:
        return value


def select_min_budget_value(values: list[str]) -> str:
    parsed: list[tuple[float, str]] = []
    for value in values:
        try:
            parsed.append((float(value), value))
        except ValueError:
            continue
    if not parsed:
        return values[0] if values else ""
    parsed.sort(key=lambda item: item[0])
    return parsed[0][1]


def extract_cell_text(row: str, class_name: str) -> str:
    match = re.search(rf'<td class="{re.escape(class_name)}">\s*(.*?)(?=<td class=|</tr>|$)', row, re.I | re.S)
    if not match:
        return ""
    value = re.sub(r'</(?:td|div)>\s*$', '', match.group(1), flags=re.I)
    return clean_text(html_to_text(value))


def extract_page_url(html: str) -> str:
    match = re.search(r'<link rel="canonical" href="([^"]+)"', html, re.I | re.S)
    return match.group(1) if match else ""


def extract_page_title(html: str) -> str:
    match = re.search(r"<title>(.*?)</title>", html, re.I | re.S)
    return clean_text(match.group(1)) if match else ""


def extract_surface_unit_code(html: str) -> str:
    text = html.lower()
    if "ha" in text and "m²" not in text:
        return "HA"
    return "M2"


def html_to_text(fragment: str) -> str:
    fragment = re.sub(r"<br\s*/?>", "\n", fragment, flags=re.I)
    fragment = re.sub(r"<[^>]+>", " ", fragment)
    fragment = html_lib.unescape(fragment)
    return clean_text(fragment)


def clean_text(value: str) -> str:
    value = html_lib.unescape(value)
    value = value.replace("\xa0", " ")
    return re.sub(r"\s+", " ", value).strip()


def normalize_number_text(value: str) -> str:
    value = clean_text(value)
    value = value.replace("€", "")
    value = value.replace("m²", "")
    value = value.replace("m2", "")
    value = value.replace("/an", "")
    value = value.replace("/mois", "")
    value = value.replace(" ", "")
    value = value.replace(",", ".")
    return value


def xml_text(value) -> str:
    return "" if value is None else str(value)


def normalize_key(value: str) -> str:
    value = clean_text(value).lower()
    normalized = unicodedata.normalize("NFKD", value)
    normalized = "".join(char for char in normalized if not unicodedata.combining(char))
    normalized = normalized.replace("&", " and ")
    normalized = re.sub(r"[^a-z0-9]+", " ", normalized)
    return re.sub(r"\s+", " ", normalized).strip()


def slugify(value: str) -> str:
    return normalize_key(value).replace(" ", "_")


def absolute_url(value: str) -> str:
    return value if value.startswith("http") else f"https://www.bnppre.fr{value}"


def first_non_empty(*values: str) -> str:
    for value in values:
        if value:
            return value
    return ""


def now_date() -> str:
    return dt.date.today().isoformat()


def now_timestamp() -> str:
    return dt.datetime.now().strftime("%Y-%m-%d %H:%M:%S.000")


def now_datetime() -> str:
    return dt.datetime.now().isoformat(timespec="seconds")


def indent_xml(element: ET.Element, level: int = 0) -> None:
    indent = "\n" + level * "  "
    if len(element):
        if not element.text or not element.text.strip():
            element.text = indent + "  "
        for child in element:
            indent_xml(child, level + 1)
        if not child.tail or not child.tail.strip():
            child.tail = indent
    if level and (not element.tail or not element.tail.strip()):
        element.tail = indent


if __name__ == "__main__":
    raise SystemExit(main())
