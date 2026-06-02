#!/usr/bin/env python3
"""
Génère un XML avec 10 offres par type + traductions FR→EN en local.
Version rapide pour démonstration.
"""

import xml.etree.ElementTree as ET
import argostranslate.package
import argostranslate.translate
from pathlib import Path
import json
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
    print("   ✓ Package FR→EN chargé")

def translate_text(text):
    """Traduit un texte FR→EN avec Argos Translate"""
    if not text or not text.strip() or text.startswith('{{'):
        return ""
    try:
        translated = argostranslate.translate.translate(text, from_code, to_code)
        return translated
    except:
        return ""

def select_sample(full_xml_path, per_type=10):
    """Sélectionne 10 offres par combinaison asset×transaction"""
    tree = ET.parse(full_xml_path)
    root = tree.getroot()
    
    # Grouper les offres par asset_type × transaction
    groups = defaultdict(list)
    
    for offer in root.findall('.//OFFER'):
        asset_type = offer.find('.//ASSET_TYPE')
        transaction = offer.find('.//OPERATION_TYPE')
        
        if asset_type is not None and asset_type.text and transaction is not None and transaction.text:
            key = (asset_type.text, transaction.text)
            groups[key].append(offer)
    
    # Sélectionner N offres de chaque groupe
    selected_offers = []
    for key, offers in sorted(groups.items()):
        selected_offers.extend(offers[:per_type])
    
    return selected_offers, groups

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
    output_xml = Path('data/xml/bnppre_FINAL_10_per_type_FR_EN.xml')
    
    if not full_xml.exists():
        print(f"❌ Fichier introuvable: {full_xml}")
        return
    
    print(f"\n{'='*60}")
    print(f"GÉNÉRATION ÉCHANTILLON 10 OFFRES/TYPE + TRADUCTIONS")
    print(f"{'='*60}\n")
    
    # Initialiser la traduction
    init_translation()
    
    # Sélectionner l'échantillon
    print(f"📖 Sélection de 10 offres par type...")
    selected_offers, groups = select_sample(full_xml, per_type=10)
    
    print(f"   ✓ {len(selected_offers)} offres sélectionnées")
    print(f"   ✓ {len(groups)} combinaisons asset×transaction\n")
    
    # Créer le XML de sortie
    new_root = ET.Element('OFFERS_LIST')
    
    print(f"🔄 Traduction en cours...\n")
    total_translated = 0
    
    for i, offer in enumerate(selected_offers, 1):
        translated_count = translate_offer(offer)
        total_translated += translated_count
        new_root.append(offer)
        
        if i % 10 == 0:
            print(f"   ✓ {i}/{len(selected_offers)} offres - {total_translated} champs traduits")
    
    print(f"   ✓ {len(selected_offers)} offres traduites")
    print(f"   ✓ {total_translated} champs traduits\n")
    
    # Sauvegarder
    print(f"💾 Sauvegarde dans {output_xml}...")
    tree = ET.ElementTree(new_root)
    tree.write(output_xml, encoding='utf-8', xml_declaration=True)
    
    size_mb = output_xml.stat().st_size / (1024 * 1024)
    print(f"✅ Fichier généré: {output_xml} ({size_mb:.1f} MB)")
    
    # Créer un rapport
    report = {
        'total_offers': len(selected_offers),
        'total_translations': total_translated,
        'combinations': {f"{k[0]}×{k[1]}": len(v) for k, v in groups.items()}
    }
    
    report_path = Path('data/xml/report_10_per_type.json')
    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(report, f, ensure_ascii=False, indent=2)
    
    print(f"📊 Rapport: {report_path}\n")
    print(f"{'='*60}")
    print(f"✅ TERMINÉ")
    print(f"{'='*60}\n")

if __name__ == '__main__':
    main()
