#!/usr/bin/env python3
"""
Génère un XML avec les 100 premières offres + traductions FR→EN en local.
Simple et rapide pour démonstration.
"""

import xml.etree.ElementTree as ET
import argostranslate.package
import argostranslate.translate
from pathlib import Path
from collections import defaultdict

# Charger le package FR→EN
from_code = "fr"
to_code = "en"

def init_translation():
    """Initialise Argos Translate pour FR→EN"""
    print("🔧 Initialisation Argos Translate...")
    available_packages = argostranslate.package.get_available_packages()
    package_to_install = next(
        (pkg for pkg in available_packages if pkg.from_code == from_code and pkg.to_code == to_code),
        None
    )
    if package_to_install:
        argostranslate.package.install_from_path(package_to_install.download())
    print("   ✓ Package FR→EN chargé\n")

def translate_text(text):
    """Traduit un texte FR→EN avec Argos Translate"""
    if not text or not text.strip() or text.startswith('{{'):
        return ""
    try:
        translated = argostranslate.translate.translate(text, from_code, to_code)
        return translated
    except:
        return ""

def translate_offer(offer):
    """Traduit une offre (tous les champs FR → EN)"""
    translated_count = 0
    
    # Créer un mapping parent → enfants par tag
    for parent in offer.iter():
        children_by_tag = defaultdict(lambda: {'FR': None, 'EN': None})
        
        for child in parent:
            lang = child.get('LANGUAGE')
            if lang in ('FR', 'EN'):
                children_by_tag[child.tag][lang] = child
        
        # Traduire les paires FR→EN
        for tag, langs in children_by_tag.items():
            if langs['FR'] is not None and langs['EN'] is not None:
                fr_text = langs['FR'].text
                if fr_text and fr_text.strip() and not fr_text.startswith('{{'):
                    translation = translate_text(fr_text)
                    if translation:
                        langs['EN'].text = translation
                        translated_count += 1
    
    return translated_count

def main():
    full_xml = Path('data/xml/bnppre_offers_all_fr.xml')
    output_xml = Path('data/xml/bnppre_100_offers_FR_EN.xml')
    
    if not full_xml.exists():
        print(f"❌ Fichier introuvable: {full_xml}")
        return
    
    print(f"\n{'='*60}")
    print(f"GÉNÉRATION 100 OFFRES + TRADUCTIONS FR→EN")
    print(f"{'='*60}\n")
    
    # Initialiser la traduction
    init_translation()
    
    # Charger les 100 premières offres
    print(f"📖 Lecture de {full_xml}...")
    tree = ET.parse(full_xml)
    root = tree.getroot()
    all_offers = root.findall('.//OFFER')
    
    # Prendre les 100 premières
    selected_offers = all_offers[:100]
    print(f"   ✓ {len(selected_offers)} offres sélectionnées\n")
    
    # Créer le XML de sortie
    new_root = ET.Element('OFFERS_LIST')
    
    print(f"🔄 Traduction en cours (cela prend ~2-5 minutes)...\n")
    total_translated = 0
    
    for i, offer in enumerate(selected_offers, 1):
        translated_count = translate_offer(offer)
        total_translated += translated_count
        new_root.append(offer)
        
        if i % 10 == 0:
            print(f"   ✓ {i}/{len(selected_offers)} offres - {total_translated} champs traduits")
    
    print(f"\n   ✓ {len(selected_offers)} offres traduites")
    print(f"   ✓ {total_translated} champs traduits\n")
    
    # Sauvegarder
    print(f"💾 Sauvegarde dans {output_xml}...")
    tree = ET.ElementTree(new_root)
    tree.write(output_xml, encoding='utf-8', xml_declaration=True)
    
    size_mb = output_xml.stat().st_size / (1024 * 1024)
    print(f"✅ Fichier généré: {output_xml} ({size_mb:.1f} MB)\n")
    
    print(f"{'='*60}")
    print(f"✅ TERMINÉ - {len(selected_offers)} offres • {total_translated} traductions")
    print(f"{'='*60}\n")

if __name__ == '__main__':
    main()
