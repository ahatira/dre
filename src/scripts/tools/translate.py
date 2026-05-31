#!/usr/bin/env python3
"""
Script de traduction FR → EN pour generate-bnppre-offers.py
Utilise deep-translator (gratuit, sans API key)
"""
import sys
from deep_translator import GoogleTranslator

def translate_text(text: str, source: str = "fr", target: str = "en") -> str:
    """
    Traduit un texte d'une langue source vers une langue cible.
    
    Args:
        text: Texte à traduire
        source: Langue source (défaut: "fr")
        target: Langue cible (défaut: "en")
    
    Returns:
        Texte traduit
    """
    if not text or not text.strip():
        return ""
    
    try:
        translator = GoogleTranslator(source=source, target=target)
        result = translator.translate(text)
        return result if result else ""
    except Exception as e:
        # En cas d'erreur, afficher l'erreur sur stderr et retourner vide
        print(f"Translation error: {e}", file=sys.stderr)
        return ""

def main():
    """Point d'entrée principal."""
    if len(sys.argv) > 1:
        # Texte passé en argument
        text = " ".join(sys.argv[1:])
    else:
        # Texte passé via stdin
        text = sys.stdin.read()
    
    # Traduire et afficher
    translation = translate_text(text.strip())
    print(translation)

if __name__ == "__main__":
    main()
