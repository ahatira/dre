#!/usr/bin/env python3
"""
Script pour injecter les traductions dans le XML en mappant correctement
les champs _SECONDARY du JSON vers les éléments LANGUAGE="EN" du XML.
"""

import json
import xml.etree.ElementTree as ET
from pathlib import Path
import sys

def load_translations(json_path):
    """Charge le fichier de traductions done.json"""
    with open(json_path, 'r', encoding='utf-8') as f:
        return json.load(f)

def inject_translations(xml_path, translations, output_path):
    """
    Injecte les traductions dans le XML.
    
    Mapping: field_name_SECONDARY (JSON) → element LANGUAGE="EN" (XML)
    Exemple: SUMMARY_DESCRIPTION_SECONDARY → <SUMMARY_DESCRIPTION LANGUAGE="EN">
    """
    tree = ET.parse(xml_path)
    root = tree.getroot()
    
    translated_count = 0
    field_count = 0
    
    # Pour chaque offre dans le XML
    for offer in root.findall('.//OFFER'):
        business_id_elem = offer.find('BUSINESS_ID')
        if business_id_elem is None or business_id_elem.text is None:
            continue
        
        business_id = business_id_elem.text.strip()
        
        if not business_id or business_id not in translations:
            continue
        
        offer_translations = translations[business_id]
        
        # Pour chaque champ traduit dans le JSON
        for field_key, translated_text in offer_translations.items():
            if not field_key.endswith('_SECONDARY'):
                continue
            
            # Extraire le nom du champ sans le suffixe _SECONDARY
            field_name = field_key[:-len('_SECONDARY')]
            
            # Chercher tous les éléments avec LANGUAGE="EN"
            en_elements = find_all_elements_with_language(offer, field_name, 'EN')
            
            for en_element in en_elements:
                en_element.text = translated_text
                field_count += 1
            
        translated_count += 1
    
    # Sauvegarder le XML modifié
    tree.write(output_path, encoding='utf-8', xml_declaration=True)
    
    return translated_count, field_count

def find_all_elements_with_language(parent, element_name, language):
    """
    Trouve tous les éléments par nom avec l'attribut LANGUAGE spécifié.
    Recherche récursive dans toute la hiérarchie.
    """
    results = []
    for child in parent:
        if child.tag == element_name and child.get('LANGUAGE') == language:
            results.append(child)
        # Recherche récursive
        results.extend(find_all_elements_with_language(child, element_name, language))
    return results

def main():
    if len(sys.argv) < 4:
        print("Usage: inject-translations-fixed.py <xml_input> <translations.json> <xml_output>")
        print()
        print("Exemple:")
        print("  ./inject-translations-fixed.py \\")
        print("      data/xml/bnppre_sample_50_per_type.xml \\")
        print("      data/xml/bnppre_sample_translations.done.json \\")
        print("      data/xml/bnppre_sample_50_per_type_FR_EN.xml")
        sys.exit(1)
    
    xml_input = Path(sys.argv[1])
    json_input = Path(sys.argv[2])
    xml_output = Path(sys.argv[3])
    
    if not xml_input.exists():
        print(f"❌ Fichier XML introuvable: {xml_input}")
        sys.exit(1)
    
    if not json_input.exists():
        print(f"❌ Fichier JSON introuvable: {json_input}")
        sys.exit(1)
    
    print(f"📖 Chargement des traductions depuis {json_input}...")
    translations = load_translations(json_input)
    print(f"   ✓ {len(translations)} offres chargées")
    
    print(f"📝 Injection des traductions dans {xml_input}...")
    offers_count, fields_count = inject_translations(xml_input, translations, xml_output)
    
    print(f"   ✓ {offers_count} offres traduites")
    print(f"   ✓ {fields_count} champs traduits")
    print(f"✅ Fichier généré: {xml_output}")
    print(f"   Taille: {xml_output.stat().st_size / (1024*1024):.1f} MB")

if __name__ == '__main__':
    main()
