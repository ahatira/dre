#!/usr/bin/env python3
"""Build governance_po_data.py with manual governance translations."""

from __future__ import annotations

import json
from pathlib import Path

# fmt: off
MANUAL: dict[str, dict[str, str]] = {
"- @group (fallback) -": {
    "fr": "- @group (repli) -",
    "de": "- @group (Fallback) -",
    "es": "- @group (reserva) -",
    "it": "- @group (fallback) -",
    "nl": "- @group (fallback) -",
    "pl": "- @group (zapasowa) -",
    "lb": "- @group (Fallback) -",
},
"Allow CRM to overwrite display fields": {
    "fr": "Autoriser le CRM à écraser les champs d'affichage",
    "de": "CRM darf Anzeigefelder überschreiben",
    "es": "Permitir que el CRM sobrescriba los campos de visualización",
    "it": "Consenti al CRM di sovrascrivere i campi di visualizzazione",
    "nl": "CRM mag weergavevelden overschrijven",
    "pl": "Zezwól CRM na nadpisywanie pól wyświetlania",
    "lb": "CRM däerf Uweis-Felder iwwerschreiwen",
},
"Applies to agent entities during migrate row saves when internal_lock is enabled. Inherit uses the global CRM pipeline default.": {
    "fr": "S'applique aux entités agent lors des sauvegardes migrate si internal_lock est activé. Hériter utilise la valeur par défaut du pipeline CRM global.",
    "de": "Gilt bei Migrate-Zeilenspeicherungen für Agenten-Entitäten, wenn internal_lock aktiviert ist. Erben verwendet die globale CRM-Pipeline-Standardeinstellung.",
    "es": "Se aplica al guardar filas migrate de agentes cuando internal_lock está activado. Heredar usa el valor predeterminado del pipeline CRM global.",
    "it": "Si applica al salvataggio delle righe migrate delle entità agente quando internal_lock è abilitato. Eredita usa l'impostazione predefinita della pipeline CRM globale.",
    "nl": "Van toepassing bij migrate-rijopslag voor agent-entiteiten wanneer internal_lock is ingeschakeld. Overnemen gebruikt de globale CRM-pipelinestandaard.",
    "pl": "Dotyczy zapisu wierszy migrate encji agenta, gdy internal_lock jest włączony. Dziedzicz używa globalnej domyślnej strategii pipeline CRM.",
    "lb": "Gëllt bei Migrate-Zeile fir Agenten-Entitéiten wann internal_lock aktiv ass. Iwwerhuelen benotzt de globalen CRM-Pipeline-Default.",
},
"Applies to feature groups and definitions during migrate row saves. Inherit uses the global CRM pipeline default.": {
    "fr": "S'applique aux groupes et définitions lors des sauvegardes migrate. Hériter utilise la valeur par défaut du pipeline CRM global.",
    "de": "Gilt für Feature-Gruppen und -Definitionen bei Migrate-Zeilenspeicherungen. Erben verwendet die globale CRM-Pipeline-Standardeinstellung.",
    "es": "Se aplica a grupos y definiciones al guardar filas migrate. Heredar usa el valor predeterminado del pipeline CRM global.",
    "it": "Si applica a gruppi e definizioni durante il salvataggio delle righe migrate. Eredita usa l'impostazione predefinita della pipeline CRM globale.",
    "nl": "Van toepassing op groepen en definities bij migrate-rijopslag. Overnemen gebruikt de globale CRM-pipelinestandaard.",
    "pl": "Dotyczy grup i definicji podczas zapisu wierszy migrate. Dziedzicz używa globalnej domyślnej strategii pipeline CRM.",
    "lb": "Gëllt fir Feature-Gruppen a Definitiounen bei Migrate-Zeile. Iwwerhuelen benotzt de globalen CRM-Pipeline-Default.",
},
"Applies to media entities during migrate row saves when field_internal_lock is enabled. Inherit uses the global CRM pipeline default.": {
    "fr": "S'applique aux entités média lors des sauvegardes migrate si field_internal_lock est activé. Hériter utilise la valeur par défaut du pipeline CRM global.",
    "de": "Gilt bei Migrate-Zeilenspeicherungen für Medien-Entitäten, wenn field_internal_lock aktiviert ist. Erben verwendet die globale CRM-Pipeline-Standardeinstellung.",
    "es": "Se aplica al guardar filas migrate de medios cuando field_internal_lock está activado. Heredar usa el valor predeterminado del pipeline CRM global.",
    "it": "Si applica al salvataggio delle righe migrate dei media quando field_internal_lock è abilitato. Eredita usa l'impostazione predefinita della pipeline CRM globale.",
    "nl": "Van toepassing bij migrate-rijopslag voor media-entiteiten wanneer field_internal_lock is ingeschakeld. Overnemen gebruikt de globale CRM-pipelinestandaard.",
    "pl": "Dotyczy zapisu wierszy migrate encji mediów, gdy field_internal_lock jest włączony. Dziedzicz używa globalnej domyślnej strategii pipeline CRM.",
    "lb": "Gëllt bei Migrate-Zeile fir Medien-Entitéiten wann field_internal_lock aktiv ass. Iwwerhuelen benotzt de globalen CRM-Pipeline-Default.",
},
"Applies to offer nodes during migrate row saves when field_internal_lock is enabled. Inherit uses the global CRM pipeline default.": {
    "fr": "S'applique aux nœuds offre lors des sauvegardes migrate si field_internal_lock est activé. Hériter utilise la valeur par défaut du pipeline CRM global.",
    "de": "Gilt bei Migrate-Zeilenspeicherungen für Angebotsknoten, wenn field_internal_lock aktiviert ist. Erben verwendet die globale CRM-Pipeline-Standardeinstellung.",
    "es": "Se aplica al guardar filas migrate de ofertas cuando field_internal_lock está activado. Heredar usa el valor predeterminado del pipeline CRM global.",
    "it": "Si applica al salvataggio delle righe migrate delle offerte quando field_internal_lock è abilitato. Eredita usa l'impostazione predefinita della pipeline CRM globale.",
    "nl": "Van toepassing bij migrate-rijopslag voor aanbiedingsnodes wanneer field_internal_lock is ingeschakeld. Overnemen gebruikt de globale CRM-pipelinestandaard.",
    "pl": "Dotyczy zapisu wierszy migrate węzłów ofert, gdy field_internal_lock jest włączony. Dziedzicz używa globalnej domyślnej strategii pipeline CRM.",
    "lb": "Gëllt bei Migrate-Zeile fir Offer-Noden wann field_internal_lock aktiv ass. Iwwerhuelen benotzt de globalen CRM-Pipeline-Default.",
},
"Applies when an offer technical element references a feature definition that is not in the catalogue.": {
    "fr": "S'applique lorsqu'un élément technique d'offre référence une définition absente du catalogue.",
    "de": "Gilt, wenn ein technisches Angebotselement eine Feature-Definition referenziert, die nicht im Katalog ist.",
    "es": "Se aplica cuando un elemento técnico de oferta referencia una definición que no está en el catálogo.",
    "it": "Si applica quando un elemento tecnico dell'offerta fa riferimento a una definizione non presente nel catalogo.",
    "nl": "Van toepassing wanneer een technisch offerelement verwijst naar een definitie die niet in de catalogus staat.",
    "pl": "Dotyczy sytuacji, gdy element techniczny oferty odnosi się do definicji nieobecnej w katalogu.",
    "lb": "Gëllt wann en technescht Offer-Element eng Definitioun referenzéiert déi net am Katalog ass.",
},
"Applies when field_internal_lock is enabled on the media entity.": {
    "fr": "S'applique lorsque field_internal_lock est activé sur l'entité média.",
    "de": "Gilt, wenn field_internal_lock für die Medien-Entität aktiviert ist.",
    "es": "Se aplica cuando field_internal_lock está activado en la entidad de medios.",
    "it": "Si applica quando field_internal_lock è abilitato sull'entità media.",
    "nl": "Van toepassing wanneer field_internal_lock is ingeschakeld op de media-entiteit.",
    "pl": "Dotyczy sytuacji, gdy field_internal_lock jest włączony na encji mediów.",
    "lb": "Gëllt wann field_internal_lock op der Medien-Entitéit aktiv ass.",
},
"Applies when field_internal_lock is enabled on the offer.": {
    "fr": "S'applique lorsque field_internal_lock est activé sur l'offre.",
    "de": "Gilt, wenn field_internal_lock für das Angebot aktiviert ist.",
    "es": "Se aplica cuando field_internal_lock está activado en la oferta.",
    "it": "Si applica quando field_internal_lock è abilitato sull'offerta.",
    "nl": "Van toepassing wanneer field_internal_lock is ingeschakeld op de aanbieding.",
    "pl": "Dotyczy sytuacji, gdy field_internal_lock jest włączony na ofercie.",
    "lb": "Gëllt wann field_internal_lock op der Offer aktiv ass.",
},
"Applies when internal_lock is enabled on the agent.": {
    "fr": "S'applique lorsque internal_lock est activé sur l'agent.",
    "de": "Gilt, wenn internal_lock für den Agenten aktiviert ist.",
    "es": "Se aplica cuando internal_lock está activado en el agente.",
    "it": "Si applica quando internal_lock è abilitato sull'agente.",
    "nl": "Van toepassing wanneer internal_lock is ingeschakeld op de agent.",
    "pl": "Dotyczy sytuacji, gdy internal_lock jest włączony na agencie.",
    "lb": "Gëllt wann internal_lock op dem Agent aktiv ass.",
},
}
# fmt: on

out = Path(__file__).with_name("governance_po_data.py")
out.write_text("# Generated by build_governance_po_data.py\nMANUAL = " + json.dumps(MANUAL, ensure_ascii=False, indent=4) + "\n", encoding="utf-8")
print(f"wrote {out} with {len(MANUAL)} entries (partial)")
