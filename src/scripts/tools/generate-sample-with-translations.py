#!/usr/bin/env python3
"""
Génère un XML avec 50 offres par type + traductions FR→EN en local.
SANS fichiers JSON intermédiaires.
"""

import xml.etree.ElementTree as ET
import argostranslate.package
import argostranslate.translate
from pathlib import Path
import sys

# Charger le package FR→EN
from_code = "fr"
to_code = "en"

def init_translation():
    """Initialise Argos Translate pour FR→EN"""
    print("🔧 Initialisation Argos Translate...")
    
    available_packages = argostranslate.package.get_available_packages()
    package_to_install = next(
        (
            pkg for pkg in available_packages
            if pkg.from_code == from_code and pkg.to_code == to_code
        ),
        None
    )
    
    if package_to_install:
        argostranslate.package.install_from_path(package_to_install.download())
    
    print("   ✓ Package FR→EN chargé")

def translate_text(text):
    """Traduit un texte FR→EN avec Argos Translate"""
    if not text or not text.strip():
        return ""
    
    try:
        translated = argostranslate.translate.translate(text, from_code, to_code)
        return translated
    except Exception as e:
        print(f"   ⚠ Erreur traduction: {e}")
        return ""

def load_sample_business_ids(sample_xml_path):
    """Charge les business_ids de l'échantillon"""
    tree = ET.parse(sample_xml_path)
    ids = set()
    
    for offer in tree.getroot().findall('.//OFFER'):
        bid_elem = offer.find('BUSINESS_ID')
        if bid_elem is not None and bid_elem.text:
            ids.add(bid_elem.text.strip())
    
    return ids

def translate_and_generate(full_xml_path, sample_ids, output_path):
    """
    Extrait les offres, traduit en live, et génère le XML final.
    """
    print(f"📖 Lecture de {full_xml_path}...")
    tree = ET.parse(full_xml_path)
    root = tree.getroot()
    
    print(f"🔄 Traduction et génération...")
    
    new_root = ET.Element('OFFERS_LIST')
    processed = 0
    translated_fields = 0
    
    for offer in root.findall('.//OFFER'):
        # Vérifier si l'offre est dans l'échantillon
        bid_elem = offer.find('BUSINESS_ID')
        if bid_elem is None or bid_elem.text is None:
            continue
        
        business_id = bid_elem.text.strip()
        if business_id not in sample_ids:
            continue
        
        # Traduire tous les champs avec LANGUAGE="FR"
        for elem in offer.iter():
            if elem.get('LANGUAGE') == 'FR' and elem.text and elem.text.strip():
                # Trouver l'élément EN correspondant
                parent = find_parent(offer, elem)
                if parent is not None:
                    # Chercher l'élément EN avec le même tag
                    en_elem = parent.find(f"./{elem.tag}[@LANGUAGE='EN']")
                    if en_elem is not None:
                        # Traduire
                        translation = translate_text(elem.text)
                        if translation:
                            en_elem.text = translation
                            translated_fields += 1
        
        new_root.append(offer)
        processed += 1
        
        if processed % 10 == 0:
            print(f"   ✓ {processed}/{len(sample_ids)} offres - {translated_fields} champs traduits")
    
    print(f"   ✓ {processed} offres traitées")
    print(f"   ✓ {translated_fields} champs traduits")
    
    # Sauvegarder
    print(f"💾 Sauvegarde dans {output_path}...")
    tree = ET.ElementTree(new_root)
    tree.write(output_path, encoding='utf-8', xml_declaration=True)
    
    size_mb = Path(output_path).stat().st_size / (1024 * 1024)
    print(f"✅ Fichier généré: {output_path} ({size_mb:.1f} MB)")

def find_parent(root, target):
    """Trouve le parent d'un élément"""
    for parent in root.iter():
        for child in parent:
            if child == target:
                return parent
    return None

def main():
    if len(sys.argv) < 4:
        print("Usage: generate-sample-with-translations.py <full_fr.xml> <sample_ref.xml> <output.xml>")
        print()
        print("Exemple:")
        print("  ./generate-sample-with-translations.py \\")
        print("      data/xml/bnppre_offers_all_fr.xml \\")
        print("      data/xml/bnppre_sample_50_per_type.xml \\")
        print("      data/xml/bnppre_FINAL_FR_EN.xml")
        sys.exit(1)
    
    full_xml = Path(sys.argv[1])
    sample_ref = Path(sys.argv[2])
    output_xml = Path(sys.argv[3])
    
    if not full_xml.exists():
        print(f"❌ Fichier introuvable: {full_xml}")
        sys.exit(1)
    
    if not sample_ref.exists():
        print(f"❌ Fichier introuvable: {sample_ref}")
        sys.exit(1)
    
    print(f"\n{'='*60}")
    print(f"GÉNÉRATION ÉCHANTILLON AVEC TRADUCTIONS LOCALES")
    print(f"{'='*60}\n")
    
    # Initialiser la traduction
    init_translation()
    
    # Charger les IDs de l'échantillon
    print(f"📖 Lecture de {sample_ref}...")
    sample_ids = load_sample_business_ids(sample_ref)
    print(f"   ✓ {len(sample_ids)} business_ids trouvés\n")
    
    # Traduire et générer
    translate_and_generate(full_xml, sample_ids, output_xml)
    
    print(f"\n{'='*60}")
    print(f"✅ TERMINÉ")
    print(f"{'='*60}\n")

if __name__ == '__main__':
    main()
