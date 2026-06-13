#!/usr/bin/env python3
"""Add missing admin/UI string translations discovered after YAML extractor fix."""

from __future__ import annotations

import json
from pathlib import Path

CATALOG = Path(__file__).resolve().parent / "translation_catalog"
LANGS = ("de", "es", "fr", "it", "lb", "nl", "pl")

# fmt: off
SUPPLEMENT = {
    "ps_dictionary": {
        "Add dictionary entry": ("Wörterbucheintrag hinzufügen", "Añadir entrada del diccionario", "Ajouter une entrée de dictionnaire", "Aggiungi voce del dizionario", "Dictionnaire-Entrée derbäisetzen", "Woordenboekitem toevoegen", "Dodaj wpis słownika"),
        "Budget periods": ("Budgetperioden", "Períodos de presupuesto", "Périodes de budget", "Periodi di budget", "Budget-Perioden", "Budgetperioden", "Okresy budżetowe"),
        "Budget units": ("Budgeteinheiten", "Unidades de presupuesto", "Unités de budget", "Unità di budget", "Budget-Eenheiten", "Budgeteenheden", "Jednostki budżetowe"),
        "Classes DPE": ("DPE-Klassen", "Clases DPE", "Classes DPE", "Classi DPE", "DPE-Klassen", "DPE-klassen", "Klasy DPE"),
        "Classes GES": ("GES-Klassen", "Clases GES", "Classes GES", "Classi GES", "GES-Klassen", "GES-klassen", "Klasy GES"),
        "Config-first business dictionaries and resolvers.": ("Config-First-Geschäftswörterbücher und Resolver.", "Diccionarios de negocio y resolvedores config-first.", "Dictionnaires métier et résolveurs en approche config-first.", "Dizionari di business e resolver config-first.", "Config-first Geschäftsdictionnaire a Resolver.", "Config-first bedrijfswoordenboeken en resolvers.", "Słowniki biznesowe i resolvery config-first."),
        "Devises": ("Währungen", "Divisas", "Devises", "Valute", "Währungen", "Valuta's", "Waluty"),
        "Dictionary types": ("Wörterbuchtypen", "Tipos de diccionario", "Types de dictionnaire", "Tipi di dizionario", "Dictionnaire-Typen", "Woordenboektypes", "Typy słowników"),
        "Edit": ("Bearbeiten", "Editar", "Modifier", "Modifica", "Änneren", "Bewerken", "Edytuj"),
        "French departments": ("Französische Departements", "Departamentos franceses", "Départements français", "Dipartimenti francesi", "Franséisch Departementer", "Franse departementen", "Departamenty francuskie"),
        "INSEE department codes and names for location search.": ("INSEE-Departementscodes und -namen für die Standortsuche.", "Códigos y nombres de departamentos INSEE para la búsqueda por ubicación.", "Codes et libellés INSEE des départements pour la recherche par localisation.", "Codici e nomi dei dipartimenti INSEE per la ricerca per località.", "INSEE-Departementscode a Nimm fir d'Uertschaftssich.", "INSEE-departementcodes en -namen voor locatiezoeken.", "Kody i nazwy departamentów INSEE do wyszukiwania lokalizacji."),
        "Manage dictionary types.": ("Wörterbuchtypen verwalten.", "Gestionar tipos de diccionario.", "Gérer les types de dictionnaire.", "Gestisci i tipi di dizionario.", "Dictionnaire-Typen geréieren.", "Woordenboektypes beheren.", "Zarządzaj typami słowników."),
        "Surface qualifications": ("Oberflächenqualifikationen", "Calificaciones de superficie", "Qualifications de surface", "Qualifiche di superficie", "Surface-Qualifikatiounen", "Oppervlaktekwalificaties", "Kwalifikacje powierzchni"),
        "Surface units": ("Flächeneinheiten", "Unidades de superficie", "Unités de surface", "Unità di superficie", "Surface-Eenheiten", "Oppervlakte-eenheden", "Jednostki powierzchni"),
        "Types d'actifs": ("Anlagearten", "Tipos de activos", "Types d'actifs", "Tipi di asset", "Asset-Typen", "Activatypen", "Typy aktywów"),
        "Types d'operation": ("Transaktionsarten", "Tipos de operación", "Types d'opération", "Tipi di operazione", "Operatiouns-Typen", "Operatietypen", "Typy operacji"),
        "Types de clients": ("Kundentypen", "Tipos de clientes", "Types de clients", "Tipi di clienti", "Client-Typen", "Klanttypen", "Typy klientów"),
        "Types de mandats": ("Mandatstypen", "Tipos de mandato", "Types de mandats", "Tipi di mandato", "Mandat-Typen", "Mandaattypen", "Typy mandatów"),
        "UI Icon": ("UI-Symbol", "Icono de UI", "Icône UI", "Icona UI", "UI-Icon", "UI-pictogram", "Ikona UI"),
        "ps_dictionary field settings": ("ps_dictionary Feldeinstellungen", "Ajustes de campo ps_dictionary", "Paramètres de champ ps_dictionary", "Impostazioni campo ps_dictionary", "ps_dictionary Felder-Astellungen", "ps_dictionary veldinstellingen", "Ustawienia pola ps_dictionary"),
        "ps_dictionary field storage settings": ("ps_dictionary Feldspeicher-Einstellungen", "Ajustes de almacenamiento de campo ps_dictionary", "Paramètres de stockage de champ ps_dictionary", "Impostazioni storage campo ps_dictionary", "ps_dictionary Felder-Späicher-Astellungen", "ps_dictionary veldopslaginstellingen", "Ustawienia przechowywania pola ps_dictionary"),
    },
    "ps_surface": {
        "+ Add division": ("+ Division hinzufügen", "+ Añadir división", "+ Ajouter une division", "+ Aggiungi divisione", "+ Divisioun derbäisetzen", "+ Divisie toevoegen", "+ Dodaj podział"),
        "Administer PS surface entities": ("PS-Flächenentitäten verwalten", "Administrar entidades de superficie PS", "Administrer les entités de surface PS", "Amministra entità superficie PS", "PS-Surface-Entitéiten geréieren", "PS-oppervlakte-entiteiten beheren", "Zarządzaj encjami powierzchni PS"),
        "Manage surface division entities and configuration.": ("Flächendivisionen und Konfiguration verwalten.", "Gestionar divisiones de superficie y configuración.", "Gérer les divisions de surface et la configuration.", "Gestisci divisioni di superficie e configurazione.", "Surface-Divisiounen a Configuratioun geréieren.", "Oppervlaktedivisies en configuratie beheren.", "Zarządzaj podziałami powierzchni i konfiguracją."),
        "Surface domain foundation for divisions, projections, and governance policies.": ("Flächendomäne für Divisionen, Projektionen und Governance-Richtlinien.", "Fundamento del dominio de superficie para divisiones, proyecciones y políticas de gobernanza.", "Fondation du domaine surface pour les divisions, projections et règles de gouvernance.", "Fondamento del dominio superficie per divisioni, proiezioni e policy di governance.", "Surface-Domain-Fondatioun fir Divisiounen, Projektiounen a Governance-Regelen.", "Oppervlaktedomein voor divisies, projecties en governancebeleid.", "Fundament domeny powierzchni dla podziałów, projekcji i polityk governance."),
        "Table row": ("Tabellenzeile", "Fila de tabla", "Ligne de tableau", "Riga tabella", "Tabellzeil", "Tabelrij", "Wiersz tabeli"),
    },
    "ps_media": {
        "Administer PS Media": ("PS Media verwalten", "Administrar PS Media", "Administrer PS Media", "Amministra PS Media", "PS Media geréieren", "PS Media beheren", "Zarządzaj PS Media"),
        "Configure PS Media gallery and related settings.": ("PS Media-Galerie und zugehörige Einstellungen konfigurieren.", "Configurar la galería PS Media y ajustes relacionados.", "Configurer la galerie PS Media et les paramètres associés.", "Configura la galeria PS Media e le impostazioni correlate.", "PS Media-Galerie a verbonnen Astellungen configuréieren.", "PS Media-galerij en gerelateerde instellingen configureren.", "Skonfiguruj galerię PS Media i powiązane ustawienia."),
        "Configure offer gallery badge icons.": ("Badge-Icons der Angebotsgalerie konfigurieren.", "Configurar iconos de insignias de la galería de ofertas.", "Configurer les icônes de badges de la galerie offre.", "Configura le icone badge della galleria offerta.", "Offer-Galerie-Badge-Iconen configuréieren.", "Badge-iconen van de aanbiedingsgalerij configureren.", "Skonfiguruj ikony odznak galerii ofert."),
        "Minimal media foundation for offer media workflows.": ("Minimale Medienbasis für Angebots-Medien-Workflows.", "Base media mínima para flujos de medios de ofertas.", "Fondation média minimale pour les workflows média des offres.", "Fondamento media minimo per i workflow media delle offerte.", "Minimal Media-Fondatioun fir Offer-Media-Workflows.", "Minimale mediafundering voor aanbiedingsmedia-workflows.", "Minimalna podstawa mediów dla workflow mediów ofert."),
        "Offer gallery": ("Angebotsgalerie", "Galería de oferta", "Galerie offre", "Galleria offerta", "Offer-Galerie", "Aanbiedingsgalerij", "Galeria oferty"),
        "PS Media gallery settings": ("PS Media-Galerieeinstellungen", "Ajustes de galería PS Media", "Paramètres galerie PS Media", "Impostazioni galleria PS Media", "PS Media-Galerie-Astellungen", "PS Media-galerijinstellingen", "Ustawienia galerii PS Media"),
        "PS Offer gallery hero (788×462)": ("PS Angebotsgalerie Hero (788×462)", "Hero galería oferta PS (788×462)", "Hero galerie offre PS (788×462)", "Hero galleria offerta PS (788×462)", "PS Offer-Galerie Hero (788×462)", "PS aanbiedingsgalerij hero (788×462)", "Hero galerii oferty PS (788×462)"),
        "PS Offer gallery thumb (120×70)": ("PS Angebotsgalerie-Thumbnail (120×70)", "Miniatura galería oferta PS (120×70)", "Vignette galerie offre PS (120×70)", "Thumbnail galleria offerta PS (120×70)", "PS Offer-Galerie-Thumbnail (120×70)", "PS aanbiedingsgalerij-thumb (120×70)", "Miniatura galerii oferty PS (120×70)"),
    },
}
# fmt: on

# ps_feature supplement - large set
FEATURE_SUPPLEMENT = {
    "Activities not permitted on the premises.": ("Auf dem Grundstück nicht zulässige Aktivitäten.", "Actividades no permitidas en las instalaciones.", "Activités non autorisées sur le site.", "Attività non consentite nel sito.", "Net erlaabt Aktivitéiten um Site.", "Niet toegestane activiteiten op het terrein.", "Działalność niedozwolona na terenie obiektu."),
    "Add feature": ("Merkmal hinzufügen", "Añadir característica", "Ajouter une caractéristique", "Aggiungi caratteristica", "Feature derbäisetzen", "Kenmerk toevoegen", "Dodaj cechę"),
    "Add group": ("Gruppe hinzufügen", "Añadir grupo", "Ajouter un groupe", "Aggiungi gruppo", "Grupp derbäisetzen", "Groep toevoegen", "Dodaj grupę"),
    "Additional information": ("Zusätzliche Informationen", "Información adicional", "Informations complémentaires", "Informazioni aggiuntive", "Zousätzlech Informatiounen", "Aanvullende informatie", "Informacje uzupełniające"),
    "Administer PS Features": ("PS-Merkmale verwalten", "Administrar características PS", "Administrer les caractéristiques PS", "Amministra caratteristiche PS", "PS-Features geréieren", "PS-kenmerken beheren", "Zarządzaj cechami PS"),
    "Asset type code": ("Anlageart-Code", "Código de tipo de activo", "Code type d'actif", "Codice tipo asset", "Asset-Typ-Code", "Activatypecode", "Kod typu aktywa"),
    "Available data types for features (defined by developers)": ("Verfügbare Datentypen für Merkmale (von Entwicklern definiert)", "Tipos de datos disponibles para características (definidos por desarrolladores)", "Types de données disponibles pour les caractéristiques (définis par les développeurs)", "Tipi di dati disponibili per le caratteristiche (definiti dagli sviluppatori)", "Verfügbar Datentypen fir Features (vun Entwéckler definéiert)", "Beschikbare gegevenstypen voor kenmerken (door ontwikkelaars gedefinieerd)", "Dostępne typy danych cech (zdefiniowane przez deweloperów)"),
    "Building equipment and facilities.": ("Gebäudeausrüstung und Einrichtungen.", "Equipos e instalaciones del edificio.", "Équipements et installations du bâtiment.", "Attrezzature e dotazioni dell'edificio.", "Gebai-Ausrüstung a Facilitéiten.", "Gebouwinstallaties en voorzieningen.", "Wyposażenie i instalacje budynku."),
    "Building structure": ("Gebäudestruktur", "Estructura del edificio", "Structure du bâtiment", "Struttura dell'edificio", "Gebai-Struktur", "Gebouwstructuur", "Struktura budynku"),
    "Building type and condition": ("Gebäudetyp und -zustand", "Tipo y estado del edificio", "Type et état du bâtiment", "Tipo e stato dell'edificio", "Gebai-Typ a Zoustand", "Gebouwtype en -staat", "Typ i stan budynku"),
    "Building type, state and structure.": ("Gebäudetyp, -zustand und -struktur.", "Tipo, estado y estructura del edificio.", "Type, état et structure du bâtiment.", "Tipo, stato e struttura dell'edificio.", "Gebai-Typ, Zoustand a Struktur.", "Gebouwtype, staat en structuur.", "Typ, stan i struktura budynku."),
    "Ceiling heights and clear heights.": ("Deckenhöhen und lichte Höhen.", "Alturas de techo y alturas libres.", "Hauteurs sous plafond et hauteurs libres.", "Altezze dei soffitti e altezze libere.", "Plafonghéichten a fräi Héichten.", "Plafondhoogtes en vrije hoogtes.", "Wysokości sufitów i wysokości użytkowe."),
    "Default icon for feature groups without a custom icon": ("Standardsymbol für Merkmalsgruppen ohne eigenes Symbol", "Icono predeterminado para grupos sin icono personalizado", "Icône par défaut pour les groupes sans icône dédiée", "Icona predefinita per gruppi senza icona personalizzata", "Standard-Icon fir Feature-Gruppe ouni eegent Icon", "Standaardpictogram voor groepen zonder aangepast pictogram", "Domyślna ikona grup bez własnej ikony"),
    "Dictionary ID": ("Wörterbuch-ID", "ID de diccionario", "ID dictionnaire", "ID dizionario", "Dictionnaire-ID", "Woordenboek-ID", "ID słownika"),
    "Edit": ("Bearbeiten", "Editar", "Modifier", "Modifica", "Änneren", "Bewerken", "Edytuj"),
    "Environmental and quality certifications.": ("Umwelt- und Qualitätszertifizierungen.", "Certificaciones medioambientales y de calidad.", "Certifications environnementales et qualité.", "Certificazioni ambientali e di qualità.", "Ëmwelt- a Qualitéitszertifizéierungen.", "Milieu- en kwaliteitscertificeringen.", "Certyfikaty środowiskowe i jakościowe."),
    "Feature Builder - 9 type drivers for business components": ("Feature Builder – 9 Typ-Treiber für Geschäftskomponenten", "Feature Builder: 9 controladores de tipo para componentes de negocio", "Feature Builder — 9 pilotes de type pour les composants métier", "Feature Builder — 9 driver di tipo per componenti business", "Feature Builder — 9 Typ-Driver fir Business-Komponenten", "Feature Builder — 9 typedrivers voor businesscomponenten", "Feature Builder — 9 sterowników typów dla komponentów biznesowych"),
    "Feature Definition": ("Merkmaldefinition", "Definición de característica", "Définition de caractéristique", "Definizione caratteristica", "Feature-Definitioun", "Kenmerkdefinitie", "Definicja cechy"),
    "Feature Group": ("Merkmalsgruppe", "Grupo de características", "Groupe de caractéristiques", "Gruppo caratteristiche", "Feature-Grupp", "Kenmerkgroep", "Grupa cech"),
    "Feature ID": ("Merkmal-ID", "ID de característica", "ID caractéristique", "ID caratteristica", "Feature-ID", "Kenmerk-ID", "ID cechy"),
    "Feature display settings": ("Anzeigeeinstellungen für Merkmale", "Ajustes de visualización de características", "Paramètres d'affichage des caractéristiques", "Impostazioni di visualizzazione caratteristiche", "Feature-Uweisungs-Astellungen", "Weergave-instellingen kenmerken", "Ustawienia wyświetlania cech"),
    "Feature field settings": ("Feldeinstellungen Merkmale", "Ajustes de campo de características", "Paramètres de champ caractéristiques", "Impostazioni campo caratteristiche", "Feature-Feld-Astellungen", "Veldinstellingen kenmerken", "Ustawienia pola cech"),
    "Feature field storage settings": ("Feldspeicher-Einstellungen Merkmale", "Ajustes de almacenamiento de campo", "Paramètres de stockage de champ", "Impostazioni storage campo", "Feature-Feld-Späicher-Astellungen", "Veldopslaginstellingen kenmerken", "Ustawienia przechowywania pola cech"),
    "Feature payload fields used by the ps_feature module.": ("Payload-Felder des ps_feature-Moduls.", "Campos payload usados por el módulo ps_feature.", "Champs payload utilisés par le module ps_feature.", "Campi payload usati dal modulo ps_feature.", "Payload-Felder vum ps_feature-Modul.", "Payload-velden gebruikt door ps_feature.", "Pola payload używane przez moduł ps_feature."),
    "Feature types": ("Merkmalstypen", "Tipos de características", "Types de caractéristiques", "Tipi di caratteristiche", "Feature-Typen", "Kenmerktypes", "Typy cech"),
    "Features": ("Merkmale", "Características", "Caractéristiques", "Caratteristiche", "Features", "Kenmerken", "Cechy"),
    "ID": ("ID", "ID", "ID", "ID", "ID", "ID", "ID"),
    "Interior fittings and layout.": ("Innenausstattung und Raumaufteilung.", "Acondicionamiento y distribución interior.", "Aménagements et agencements intérieurs.", "Allestimenti e layout interni.", "Interieur-Ausstattung a Layout.", "Interieurinrichting en indeling.", "Wyposażenie i układ wnętrza."),
    "LEED certification": ("LEED-Zertifizierung", "Certificación LEED", "Certification LEED", "Certificazione LEED", "LEED-Zertifizéierung", "LEED-certificering", "Certyfikacja LEED"),
    "LEED or equivalent environmental certification level.": ("LEED oder gleichwertiges Umweltzertifikat.", "Certificación LEED o equivalente.", "Certification LEED ou équivalent environnemental.", "Certificazione LEED o equivalente ambientale.", "LEED oder gläichwäerteg Ëmweltzertifizéierung.", "LEED of equivalente milieucertificering.", "Certyfikacja LEED lub równoważna certyfikacja środowiskowa."),
    "Manage feature groups (e.g. Amenities, Technical features)": ("Merkmalsgruppen verwalten (z. B. Ausstattung, Technik)", "Gestionar grupos de características (p. ej. servicios, características técnicas)", "Gérer les groupes de caractéristiques (ex. : aménagements, caractéristiques techniques)", "Gestisci gruppi di caratteristiche (es. servizi, caratteristiche tecniche)", "Feature-Gruppe geréieren (z. B. Ausstattung, Technik)", "Kenmerkgroepen beheren (bijv. voorzieningen, technische kenmerken)", "Zarządzaj grupami cech (np. udogodnienia, cechy techniczne)"),
    "Manage feature groups and definitions": ("Merkmalsgruppen und -definitionen verwalten", "Gestionar grupos y definiciones de características", "Gérer les groupes et définitions de caractéristiques", "Gestisci gruppi e definizioni caratteristiche", "Feature-Gruppe a Definitiounen geréieren", "Kenmerkgroepen en definities beheren", "Zarządzaj grupami i definicjami cech"),
    "Manage the feature catalogue available for offers": ("Merkmalskatalog für Angebote verwalten", "Gestionar el catálogo de características disponible para ofertas", "Gérer le catalogue de caractéristiques disponibles pour les offres", "Gestisci il catalogo caratteristiche disponibile per le offerte", "Feature-Katalog fir Offeren geréieren", "Kenmerkcatalogus voor aanbiedingen beheren", "Zarządzaj katalogiem cech dostępnych dla ofert"),
    "On-site services and amenities.": ("Dienstleistungen und Annehmlichkeiten vor Ort.", "Servicios y prestaciones in situ.", "Prestations et services sur site.", "Servizi e dotazioni in loco.", "Servicer a Annehmlechkeeten vir Ort.", "Diensten en voorzieningen ter plaatse.", "Usługi i udogodnienia na miejscu."),
    "Outdoor areas and surroundings.": ("Außenbereiche und Umgebung.", "Espacios exteriores y entorno.", "Espaces extérieurs et environnement.", "Spazi esterni e dintorni.", "Baussenzonen an Ëmgéigend.", "Buitenruimtes en omgeving.", "Przestrzenie zewnętrzne i otoczenie."),
    "PS Feature": ("PS-Merkmal", "PS Característica", "PS Caractéristique", "PS Caratteristica", "PS Feature", "PS Kenmerk", "PS Cecha"),
    "PS Feature settings": ("PS-Merkmal-Einstellungen", "Ajustes PS Característica", "Paramètres PS Caractéristique", "Impostazioni PS Caratteristica", "PS Feature-Astellungen", "PS Kenmerkinstellingen", "Ustawienia PS Cecha"),
    "Payload defaults": ("Payload-Standardwerte", "Valores predeterminados del payload", "Valeurs par défaut du payload", "Valori predefiniti del payload", "Payload-Default-Wäerter", "Payload-standaardwaarden", "Domyślne wartości payload"),
    "Restricted activities": ("Eingeschränkte Aktivitäten", "Actividades restringidas", "Activités restreintes", "Attività limitate", "Ageschränkt Aktivitéiten", "Beperkte activiteiten", "Ograniczone działania"),
    "Settings": ("Einstellungen", "Ajustes", "Paramètres", "Impostazioni", "Astellungen", "Instellingen", "Ustawienia"),
    "Standards, certifications and labels.": ("Normen, Zertifizierungen und Labels.", "Normas, certificaciones y etiquetas.", "Normes, certifications et labels.", "Standard, certificazioni ed etichette.", "Normen, Zertifizéierungen a Labels.", "Normen, certificeringen en labels.", "Normy, certyfikaty i etykiety."),
    "Standards, certifications and labels": ("Normen, Zertifizierungen und Labels", "Normas, certificaciones y etiquetas", "Normes, certifications et labels", "Standard, certificazioni ed etichette", "Normen, Zertifizéierungen a Labels", "Normen, certificeringen en labels", "Normy, certyfikaty i etykiety"),
    "Status": ("Status", "Estado", "Statut", "Stato", "Status", "Status", "Status"),
    "Structural characteristics of the building.": ("Strukturelle Merkmale des Gebäudes.", "Características estructurales del edificio.", "Caractéristiques structurelles du bâtiment.", "Caratteristiche strutturali dell'edificio.", "Strukturell Charakteristike vum Gebai.", "Structurele kenmerken van het gebouw.", "Cechy konstrukcyjne budynku."),
    "Supplementary offer information.": ("Ergänzende Angebotsinformationen.", "Información complementaria de la oferta.", "Informations complémentaires sur l'offre.", "Informazioni supplementari sull'offerta.", "Zousätzlech Offer-Informatiounen.", "Aanvullende aanbiedingsinformatie.", "Uzupełniające informacje o ofercie."),
    "UI Icon": ("UI-Symbol", "Icono UI", "Icône UI", "Icona UI", "UI-Icon", "UI-pictogram", "Ikona UI"),
    "Vehicle access and public transport.": ("Fahrzeugzugang und öffentlicher Verkehr.", "Acceso vehículos y transporte público.", "Accès véhicules et transports en commun.", "Accesso veicoli e trasporto pubblico.", "Gefier-Zougang a ëffentlechen Transport.", "Voertuigtoegang en openbaar vervoer.", "Dostęp pojazdów i transport publiczny."),
    "Vocabulary ID": ("Vokabular-ID", "ID de vocabulario", "ID vocabulaire", "ID vocabolario", "Vocabulary-ID", "Vocabulaire-ID", "ID słownika taksonomii"),
}

SUPPLEMENT["ps_feature"] = {k: v for k, v in FEATURE_SUPPLEMENT.items()}

SUPPLEMENT["bnp_editor"] = {
    "Access to Basic HTML format with standard editing tools.": ("Zugriff auf Basic HTML mit Standard-Editorwerkzeugen.", "Acceso al formato Basic HTML con herramientas de edición estándar.", "Accès au format Basic HTML avec les outils d'édition standard.", "Accesso al formato Basic HTML con strumenti di editing standard.", "Zougang op Basic HTML mat Standard-Editor-Tools.", "Toegang tot Basic HTML-formaat met standaard bewerkingstools.", "Dostęp do formatu Basic HTML ze standardowymi narzędziami edycji."),
    "Access to Full HTML format with complete CKEditor capabilities.": ("Zugriff auf Full HTML mit vollständigen CKEditor-Funktionen.", "Acceso al formato Full HTML con capacidades completas de CKEditor.", "Accès au format Full HTML avec toutes les capacités CKEditor.", "Accesso al formato Full HTML con tutte le funzionalità CKEditor.", "Zougang op Full HTML mat kompletten CKEditor-Funktiounen.", "Toegang tot Full HTML-formaat met volledige CKEditor-mogelijkheden.", "Dostęp do formatu Full HTML z pełnymi możliwościami CKEditor."),
    "Access to Plain text format (no HTML).": ("Zugriff auf Nur-Text-Format (ohne HTML).", "Acceso al formato de texto plano (sin HTML).", "Accès au format texte brut (sans HTML).", "Accesso al formato testo semplice (senza HTML).", "Zougang op Plain-Text-Format (ouni HTML).", "Toegang tot platte tekst (geen HTML).", "Dostęp do formatu zwykłego tekstu (bez HTML)."),
    "Access to Restricted HTML format (limited tags).": ("Zugriff auf eingeschränktes HTML (begrenzte Tags).", "Acceso al formato HTML restringido (etiquetas limitadas).", "Accès au format HTML restreint (balises limitées).", "Accesso al formato HTML limitato (tag limitati).", "Zougang op Restricted HTML (limitéiert Tags).", "Toegang tot beperkt HTML-formaat (beperkte tags).", "Dostęp do formatu Restricted HTML (ograniczone tagi)."),
    "Administer BNP Editor": ("BNP Editor verwalten", "Administrar BNP Editor", "Administrer BNP Editor", "Amministra BNP Editor", "BNP Editor geréieren", "BNP Editor beheren", "Zarządzaj BNP Editor"),
    "At least one protocol must be allowed.": ("Mindestens ein Protokoll muss erlaubt sein.", "Debe permitirse al menos un protocolo.", "Au moins un protocole doit être autorisé.", "Deve essere consentito almeno un protocollo.", "Mindestens ee Protokoll muss erlaabt sinn.", "Ten minste één protocol moet toegestaan zijn.", "Co najmniej jeden protokół musi być dozwolony."),
    "Configure CKEditor settings and manage editor configurations.": ("CKEditor-Einstellungen konfigurieren und Editor-Konfigurationen verwalten.", "Configurar ajustes de CKEditor y gestionar configuraciones del editor.", "Configurer CKEditor et gérer les configurations de l'éditeur.", "Configura CKEditor e gestisci le configurazioni dell'editor.", "CKEditor-Astellungen configuréieren an Editor-Configuratiounen geréieren.", "CKEditor-instellingen configureren en editorconfiguraties beheren.", "Skonfiguruj CKEditor i zarządzaj konfiguracjami edytora."),
    "Custom Plugin": ("Benutzerdefiniertes Plugin", "Plugin personalizado", "Plugin personnalisé", "Plugin personalizzato", "Custom Plugin", "Aangepaste plug-in", "Wtyczka niestandardowa"),
    "Invalid protocol format: @protocol": ("Ungültiges Protokollformat: @protocol", "Formato de protocolo no válido: @protocol", "Format de protocole invalide : @protocol", "Formato protocollo non valido: @protocol", "Ongültegt Protokoll-Format: @protocol", "Ongeldig protocolformaat: @protocol", "Nieprawidłowy format protokołu: @protocol"),
    "Use Basic HTML text format": ("Basic HTML-Textformat verwenden", "Usar formato de texto Basic HTML", "Utiliser le format texte Basic HTML", "Usa formato testo Basic HTML", "Basic HTML-Textformat benotzen", "Basic HTML-tekstformaat gebruiken", "Użyj formatu tekstu Basic HTML"),
    "Use Full HTML text format": ("Full HTML-Textformat verwenden", "Usar formato de texto Full HTML", "Utiliser le format texte Full HTML", "Usa formato testo Full HTML", "Full HTML-Textformat benotzen", "Full HTML-tekstformaat gebruiken", "Użyj formatu tekstu Full HTML"),
    "Use Plain text format": ("Nur-Text-Format verwenden", "Usar formato de texto plano", "Utiliser le format texte brut", "Usa formato testo semplice", "Plain-Text-Format benotzen", "Platte tekst gebruiken", "Użyj formatu zwykłego tekstu"),
    "Use Restricted HTML text format": ("Eingeschränktes HTML-Format verwenden", "Usar formato HTML restringido", "Utiliser le format HTML restreint", "Usa formato HTML limitato", "Restricted HTML-Format benotzen", "Beperkt HTML-formaat gebruiken", "Użyj formatu Restricted HTML"),
}


def apply_supplement() -> None:
    for module, entries in SUPPLEMENT.items():
        path = CATALOG / f"{module}.json"
        catalog = json.loads(path.read_text(encoding="utf-8"))
        for msgid, values in entries.items():
            catalog[msgid] = {
                lang: values[i] for i, lang in enumerate(LANGS)
            }
        path.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
        print(f"{module}: supplemented {len(entries)} entries")


if __name__ == "__main__":
    apply_supplement()
