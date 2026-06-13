#!/usr/bin/env python3
"""Patch remaining FR gaps in partially translated module catalogs."""

from __future__ import annotations

import json
from pathlib import Path

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"
LANGS = ["de", "es", "fr", "it", "lb", "nl", "pl"]

# msgid -> {lang: translation}
PATCHES: dict[str, dict[str, dict[str, str]]] = {
    "ps_compare": {
        "Compare column": {
            "fr": "Colonne de comparaison", "de": "Vergleichsspalte", "es": "Columna de comparación",
            "it": "Colonna di confronto", "lb": "Vergläichssail", "nl": "Vergelijkingskolom", "pl": "Kolumna porównania",
        },
        "DPE": {"fr": "DPE", "de": "DPE", "es": "DPE", "it": "DPE", "lb": "DPE", "nl": "DPE", "pl": "DPE"},
        "GES": {"fr": "GES", "de": "GES", "es": "GES", "it": "GES", "lb": "GES", "nl": "GES", "pl": "GES"},
        "Deprecated — use display_share_button": {
            "fr": "Obsolète — utiliser display_share_button",
            "de": "Veraltet — display_share_button verwenden", "es": "Obsoleto — usar display_share_button",
            "it": "Deprecato — usare display_share_button", "lb": "Veraltet — display_share_button benotzen",
            "nl": "Verouderd — gebruik display_share_button", "pl": "Przestarzałe — użyj display_share_button",
        },
        "Divisible": {
            "fr": "Divisible", "de": "Teilbar", "es": "Divisible", "it": "Divisibile",
            "lb": "Deelbar", "nl": "Deelbaar", "pl": "Podzielny",
        },
        "Enabled entity targets": {
            "fr": "Cibles d'entité activées", "de": "Aktivierte Entitätsziele", "es": "Objetivos de entidad habilitados",
            "it": "Target entità abilitati", "lb": "Aktivéiert Entitéitsziler", "nl": "Ingeschakelde entiteitsdoelen", "pl": "Włączone cele encji",
        },
        "Feature definition IDs or codes shown in comparison table (empty = all applicable features)": {
            "fr": "IDs ou codes de caractéristiques affichés dans le tableau de comparaison (vide = toutes les caractéristiques applicables)",
            "de": "Merkmal-IDs oder -Codes in der Vergleichstabelle (leer = alle anwendbaren Merkmale)",
            "es": "IDs o códigos de características en la tabla de comparación (vacío = todas las aplicables)",
            "it": "ID o codici caratteristiche nella tabella di confronto (vuoto = tutte le applicabili)",
            "lb": "Feature-IDs oder -Coden an der Vergläichstabelle (eidel = all applicabel Features)",
            "nl": "Kenmerkdefinitie-IDs of -codes in vergelijkingstabel (leeg = alle toepasselijke kenmerken)",
            "pl": "ID lub kody cech w tabeli porównania (puste = wszystkie mające zastosowanie)",
        },
        "Minimum items required to open comparison page": {
            "fr": "Nombre minimum d'éléments pour ouvrir la page de comparaison",
            "de": "Mindestanzahl Elemente zum Öffnen der Vergleichsseite",
            "es": "Mínimo de elementos para abrir la página de comparación",
            "it": "Numero minimo di elementi per aprire la pagina di confronto",
            "lb": "Minimum Unzuel fir d'Vergläichs-Säit opzemaachen",
            "nl": "Minimum aantal items om vergelijkingspagina te openen",
            "pl": "Minimalna liczba elementów do otwarcia strony porównania",
        },
        "PS Compare SEO URL mappings": {
            "fr": "Mappings URL SEO PS Compare",
            "de": "PS Compare SEO-URL-Zuordnungen", "es": "Mapeos URL SEO de PS Compare",
            "it": "Mapping URL SEO PS Compare", "lb": "PS Compare SEO-URL-Mappings", "nl": "PS Compare SEO-URL-toewijzingen", "pl": "Mapowania URL SEO PS Compare",
        },
        "PS Compare column (320×220)": {
            "fr": "Colonne PS Compare (320×220)", "de": "PS Compare-Spalte (320×220)", "es": "Columna PS Compare (320×220)",
            "it": "Colonna PS Compare (320×220)", "lb": "PS Compare-Sail (320×220)", "nl": "PS Compare-kolom (320×220)", "pl": "Kolumna PS Compare (320×220)",
        },
        "PS Compare settings": {
            "fr": "Paramètres PS Compare", "de": "PS Compare-Einstellungen", "es": "Ajustes de PS Compare",
            "it": "Impostazioni PS Compare", "lb": "PS Compare-Astellungen", "nl": "PS Compare-instellingen", "pl": "Ustawienia PS Compare",
        },
        "Photos": {
            "fr": "Photos", "de": "Fotos", "es": "Fotos", "it": "Foto", "lb": "Fotoen", "nl": "Foto's", "pl": "Zdjęcia",
        },
        "Property offer comparison list with anonymous and authenticated persistence.": {
            "fr": "Liste de comparaison d'offres avec persistance anonyme et authentifiée.",
            "de": "Vergleichsliste für Immobilienangebote mit anonymer und authentifizierter Persistenz.",
            "es": "Lista de comparación de ofertas con persistencia anónima y autenticada.",
            "it": "Elenco confronto offerte con persistenza anonima e autenticata.",
            "lb": "Offer-Vergläichslëscht mat anonymer an authentifizéierter Persistenz.",
            "nl": "Vergelijkingslijst voor aanbiedingen met anonieme en geauthenticeerde persistentie.",
            "pl": "Lista porównania ofert z trwałością anonimową i uwierzytelnioną.",
        },
        "Public compare page slug": {
            "fr": "Slug de la page publique de comparaison",
            "de": "Slug der öffentlichen Vergleichsseite", "es": "Slug de la página pública de comparación",
            "it": "Slug pagina pubblica di confronto", "lb": "Slug vun der ëffentlecher Vergläichs-Säit",
            "nl": "Slug van openbare vergelijkingspagina", "pl": "Slug publicznej strony porównania",
        },
        "Show comparison summary banner": {
            "fr": "Afficher la bannière récapitulative de comparaison",
            "de": "Vergleichs-Zusammenfassungsbanner anzeigen", "es": "Mostrar banner resumen de comparación",
            "it": "Mostra banner riepilogo confronto", "lb": "Vergläichs-Zesummefaassungs-Banner weisen",
            "nl": "Vergelijkingssamenvattingsbanner tonen", "pl": "Pokaż baner podsumowania porównania",
        },
        "Show price info popover in compare table": {
            "fr": "Afficher l'infobulle prix dans le tableau de comparaison",
            "de": "Preis-Infopopover in Vergleichstabelle anzeigen", "es": "Mostrar popover de precio en tabla de comparación",
            "it": "Mostra popover prezzo nella tabella di confronto", "lb": "Präis-Infopopover an der Vergläichstabelle weisen",
            "nl": "Prijsinfopopover in vergelijkingstabel tonen", "pl": "Pokaż dymek informacji o cenie w tabeli porównania",
        },
        "Show share comparison button": {
            "fr": "Afficher le bouton de partage de comparaison",
            "de": "Vergleich-teilen-Schaltfläche anzeigen", "es": "Mostrar botón compartir comparación",
            "it": "Mostra pulsante condivisione confronto", "lb": "Vergläich-deelen-Knäppchen weisen",
            "nl": "Knop vergelijking delen tonen", "pl": "Pokaż przycisk udostępniania porównania",
        },
        "mo": {"fr": "mois", "de": "Mon.", "es": "mes", "it": "mese", "lb": "Mount", "nl": "mnd", "pl": "mies."},
        "—": {"fr": "—", "de": "—", "es": "—", "it": "—", "lb": "—", "nl": "—", "pl": "—"},
    },
    "ps_favorite": {
        "@entity_type: @bundle": {
            "fr": "@entity_type : @bundle", "de": "@entity_type: @bundle", "es": "@entity_type: @bundle",
            "it": "@entity_type: @bundle", "lb": "@entity_type: @bundle", "nl": "@entity_type: @bundle", "pl": "@entity_type: @bundle",
        },
        "Bundle": {"fr": "Type de contenu", "de": "Bundle", "es": "Bundle", "it": "Bundle", "lb": "Bundle", "nl": "Bundle", "pl": "Bundle"},
        "Card favorite view mode when available.": {
            "fr": "Mode d'affichage carte favori lorsque disponible.",
            "de": "Favoriten-Karten-Ansichtsmodus wenn verfügbar.",
            "es": "Modo de vista tarjeta favorito cuando esté disponible.",
            "it": "Modalità visualizzazione scheda preferito quando disponibile.",
            "lb": "Favoritten-Kaart-Uweisungsmodus wann disponibel.",
            "nl": "Favorietkaart-weergavemodus indien beschikbaar.",
            "pl": "Tryb widoku karty ulubionych, gdy dostępny.",
        },
        "Entity type ID": {
            "fr": "ID du type d'entité", "de": "Entitätstyp-ID", "es": "ID de tipo de entidad",
            "it": "ID tipo entità", "lb": "Entitéitstyp-ID", "nl": "Entiteitstype-ID", "pl": "ID typu encji",
        },
        "Favorite card view mode mapping": {
            "fr": "Mapping du mode d'affichage carte favori",
            "de": "Zuordnung Favoriten-Karten-Ansichtsmodus", "es": "Mapeo de modo de vista tarjeta favorito",
            "it": "Mapping modalità visualizzazione scheda preferito", "lb": "Favoritten-Kaart-Uweisungsmodus-Mapping",
            "nl": "Toewijzing favorietkaart-weergavemodus", "pl": "Mapowanie trybu widoku karty ulubionych",
        },
        "Favorite system for offers with authenticated and anonymous persistence.": {
            "fr": "Système de favoris pour les offres avec persistance authentifiée et anonyme.",
            "de": "Favoritensystem für Angebote mit authentifizierter und anonymer Persistenz.",
            "es": "Sistema de favoritos para ofertas con persistencia autenticada y anónima.",
            "it": "Sistema preferiti per offerte con persistenza autenticata e anonima.",
            "lb": "Favorittensystem fir Offeren mat authentifizéierter an anonymer Persistenz.",
            "nl": "Favorietensysteem voor aanbiedingen met geauthenticeerde en anonieme persistentie.",
            "pl": "System ulubionych dla ofert z trwałością uwierzytelnioną i anonimową.",
        },
        "Favorite target": {
            "fr": "Cible favori", "de": "Favoritenziel", "es": "Objetivo favorito",
            "it": "Target preferito", "lb": "Favoritten-Zil", "nl": "Favorietdoel", "pl": "Cel ulubionych",
        },
        "ID": {"fr": "ID", "de": "ID", "es": "ID", "it": "ID", "lb": "ID", "nl": "ID", "pl": "ID"},
        "Manage Property Search favorites": {
            "fr": "Gérer les favoris Property Search",
            "de": "Property Search-Favoriten verwalten", "es": "Gestionar favoritos de Property Search",
            "it": "Gestisci preferiti Property Search", "lb": "Property Search-Favoritten verwalten",
            "nl": "Property Search-favorieten beheren", "pl": "Zarządzaj ulubionymi Property Search",
        },
        "Manage favorite system integrations and settings.": {
            "fr": "Gérer les intégrations et paramètres du système de favoris.",
            "de": "Favoritensystem-Integrationen und -Einstellungen verwalten.",
            "es": "Gestionar integraciones y ajustes del sistema de favoritos.",
            "it": "Gestisci integrazioni e impostazioni del sistema preferiti.",
            "lb": "Favorittensystem-Integratiounen an -Astellungen verwalten.",
            "nl": "Favorietensysteemintegraties en -instellingen beheren.",
            "pl": "Zarządzaj integracjami i ustawieniami systemu ulubionych.",
        },
        "Maximum favorites": {
            "fr": "Nombre maximum de favoris", "de": "Maximale Favoriten", "es": "Máximo de favoritos",
            "it": "Massimo preferiti", "lb": "Maximum Favoritten", "nl": "Maximum favorieten", "pl": "Maksymalna liczba ulubionych",
        },
        "Maximum favorites mapping": {
            "fr": "Mapping du nombre maximum de favoris",
            "de": "Zuordnung maximale Favoriten", "es": "Mapeo de máximo de favoritos",
            "it": "Mapping massimo preferiti", "lb": "Maximum-Favoritten-Mapping",
            "nl": "Toewijzing maximum favorieten", "pl": "Mapowanie maksymalnej liczby ulubionych",
        },
        "PS Favorite settings": {
            "fr": "Paramètres PS Favorite", "de": "PS Favorite-Einstellungen", "es": "Ajustes de PS Favorite",
            "it": "Impostazioni PS Favorite", "lb": "PS Favorite-Astellungen", "nl": "PS Favorite-instellingen", "pl": "Ustawienia PS Favorite",
        },
        "UUID": {"fr": "UUID", "de": "UUID", "es": "UUID", "it": "UUID", "lb": "UUID", "nl": "UUID", "pl": "UUID"},
        "Unable to update favorite.": {
            "fr": "Impossible de mettre à jour le favori.", "de": "Favorit konnte nicht aktualisiert werden.",
            "es": "No se pudo actualizar el favorito.", "it": "Impossibile aggiornare il preferito.",
            "lb": "Favorit konnt net aktualiséiert ginn.", "nl": "Kan favoriet niet bijwerken.", "pl": "Nie można zaktualizować ulubionego.",
        },
        "favorite target": {
            "fr": "cible favori", "de": "Favoritenziel", "es": "objetivo favorito",
            "it": "target preferito", "lb": "Favoritten-Zil", "nl": "favorietdoel", "pl": "cel ulubionych",
        },
        "favorite targets": {
            "fr": "cibles favori", "de": "Favoritenziele", "es": "objetivos favorito",
            "it": "target preferiti", "lb": "Favoritten-Ziler", "nl": "favorietdoelen", "pl": "cele ulubionych",
        },
    },
    "ps_context": {
        "Action": {"fr": "Action", "de": "Aktion", "es": "Acción", "it": "Azione", "lb": "Aktioun", "nl": "Actie", "pl": "Akcja"},
        "Actions": {"fr": "Actions", "de": "Aktionen", "es": "Acciones", "it": "Azioni", "lb": "Aktiounen", "nl": "Acties", "pl": "Akcje"},
        "Condition": {"fr": "Condition", "de": "Bedingung", "es": "Condición", "it": "Condizione", "lb": "Konditioun", "nl": "Voorwaarde", "pl": "Warunek"},
        "Conditions": {"fr": "Conditions", "de": "Bedingungen", "es": "Condiciones", "it": "Condizioni", "lb": "Konditiounen", "nl": "Voorwaarden", "pl": "Warunki"},
        "UUID": {"fr": "UUID", "de": "UUID", "es": "UUID", "it": "UUID", "lb": "UUID", "nl": "UUID", "pl": "UUID"},
    },
    "ps_core": {
        "@description": {"fr": "@description", "de": "@description", "es": "@description", "it": "@description", "lb": "@description", "nl": "@description", "pl": "@description"},
        "Configuration": {"fr": "Configuration", "de": "Konfiguration", "es": "Configuración", "it": "Configurazione", "lb": "Konfiguratioun", "nl": "Configuratie", "pl": "Konfiguracja"},
        "Configuration transverse Property Search": {
            "fr": "Configuration transverse Property Search", "de": "Übergreifende Property Search-Konfiguration",
            "es": "Configuración transversal de Property Search", "it": "Configurazione trasversale Property Search",
            "lb": "Transversal Property Search-Konfiguratioun", "nl": "Transversale Property Search-configuratie", "pl": "Konfiguracja poprzeczna Property Search",
        },
        "Contenu": {"fr": "Contenu", "de": "Inhalt", "es": "Contenido", "it": "Contenuto", "lb": "Inhalt", "nl": "Inhoud", "pl": "Treść"},
        "Description": {"fr": "Description", "de": "Beschreibung", "es": "Descripción", "it": "Descrizione", "lb": "Beschreiwung", "nl": "Beschrijving", "pl": "Opis"},
        "Gestion du contenu Property Search": {
            "fr": "Gestion du contenu Property Search", "de": "Property Search-Inhaltsverwaltung",
            "es": "Gestión de contenido de Property Search", "it": "Gestione contenuti Property Search",
            "lb": "Property Search-Inhaltsverwaltung", "nl": "Property Search-inhoudsbeheer", "pl": "Zarządzanie treścią Property Search",
        },
        "Section": {"fr": "Section", "de": "Abschnitt", "es": "Sección", "it": "Sezione", "lb": "Sektioun", "nl": "Sectie", "pl": "Sekcja"},
        "Structure": {"fr": "Structure", "de": "Struktur", "es": "Estructura", "it": "Struttura", "lb": "Struktur", "nl": "Structuur", "pl": "Struktura"},
    },
    "ps_dictionary": {
        "Action": {"fr": "Action", "de": "Aktion", "es": "Acción", "it": "Azione", "lb": "Aktioun", "nl": "Actie", "pl": "Akcja"},
        "Code": {"fr": "Code", "de": "Code", "es": "Código", "it": "Codice", "lb": "Code", "nl": "Code", "pl": "Kod"},
        "Description": {"fr": "Description", "de": "Beschreibung", "es": "Descripción", "it": "Descrizione", "lb": "Beschreiwung", "nl": "Beschrijving", "pl": "Opis"},
        "ID": {"fr": "ID", "de": "ID", "es": "ID", "it": "ID", "lb": "ID", "nl": "ID", "pl": "ID"},
        "Type": {"fr": "Type", "de": "Typ", "es": "Tipo", "it": "Tipo", "lb": "Typ", "nl": "Type", "pl": "Typ"},
        "description": {"fr": "description", "de": "Beschreibung", "es": "descripción", "it": "descrizione", "lb": "Beschreiwung", "nl": "beschrijving", "pl": "opis"},
        "Classes DPE": {"fr": "Classes DPE", "de": "DPE-Klassen", "es": "Clases DPE", "it": "Classi DPE", "lb": "DPE-Klassen", "nl": "DPE-klassen", "pl": "Klasy DPE"},
        "Classes GES": {"fr": "Classes GES", "de": "GES-Klassen", "es": "Clases GES", "it": "Classi GES", "lb": "GES-Klassen", "nl": "GES-klassen", "pl": "Klasy GES"},
        "Devises": {"fr": "Devises", "de": "Währungen", "es": "Divisas", "it": "Valute", "lb": "Währungen", "nl": "Valuta's", "pl": "Waluty"},
        "Types d'actifs": {"fr": "Types d'actifs", "de": "Vermögensarten", "es": "Tipos de activos", "it": "Tipi di attività", "lb": "Verméigensarten", "nl": "Activatypen", "pl": "Typy aktywów"},
        "Types de clients": {"fr": "Types de clients", "de": "Kundentypen", "es": "Tipos de clientes", "it": "Tipi di clienti", "lb": "Cliententypen", "nl": "Klanttypen", "pl": "Typy klientów"},
        "Types de mandats": {"fr": "Types de mandats", "de": "Mandatstypen", "es": "Tipos de mandatos", "it": "Tipi di mandato", "lb": "Mandatstypen", "nl": "Mandaattypen", "pl": "Typy mandatów"},
    },
    "ps_feature": {
        "Absent": {"fr": "Absent", "de": "Abwesend", "es": "Ausente", "it": "Assente", "lb": "Absent", "nl": "Afwezig", "pl": "Brak"},
        "Code": {"fr": "Code", "de": "Code", "es": "Código", "it": "Codice", "lb": "Code", "nl": "Code", "pl": "Kod"},
        "Compact": {"fr": "Compact", "de": "Kompakt", "es": "Compacto", "it": "Compatto", "lb": "Kompakt", "nl": "Compact", "pl": "Kompaktowy"},
        "Coworking": {"fr": "Coworking", "de": "Coworking", "es": "Coworking", "it": "Coworking", "lb": "Coworking", "nl": "Coworking", "pl": "Coworking"},
        "Date": {"fr": "Date", "de": "Datum", "es": "Fecha", "it": "Data", "lb": "Datum", "nl": "Datum", "pl": "Data"},
        "Description": {"fr": "Description", "de": "Beschreibung", "es": "Descripción", "it": "Descrizione", "lb": "Beschreiwung", "nl": "Beschrijving", "pl": "Opis"},
        "Maximum": {"fr": "Maximum", "de": "Maximum", "es": "Máximo", "it": "Massimo", "lb": "Maximum", "nl": "Maximum", "pl": "Maksimum"},
        "Minimum": {"fr": "Minimum", "de": "Minimum", "es": "Mínimo", "it": "Minimo", "lb": "Minimum", "nl": "Minimum", "pl": "Minimum"},
        "Original: @value": {"fr": "Original : @value", "de": "Original: @value", "es": "Original: @value", "it": "Originale: @value", "lb": "Original: @value", "nl": "Origineel: @value", "pl": "Oryginał: @value"},
        "Type": {"fr": "Type", "de": "Typ", "es": "Tipo", "it": "Tipo", "lb": "Typ", "nl": "Type", "pl": "Typ"},
        "description": {"fr": "description", "de": "Beschreibung", "es": "descripción", "it": "descrizione", "lb": "Beschreiwung", "nl": "beschrijving", "pl": "opis"},
        "max": {"fr": "max", "de": "max", "es": "máx", "it": "max", "lb": "max", "nl": "max", "pl": "maks"},
        "min": {"fr": "min", "de": "min", "es": "mín", "it": "min", "lb": "min", "nl": "min", "pl": "min"},
        "ID": {"fr": "ID", "de": "ID", "es": "ID", "it": "ID", "lb": "ID", "nl": "ID", "pl": "ID"},
    },
    "ps_seo": {
        "@city (@postal_code)": {"fr": "@city (@postal_code)", "de": "@city (@postal_code)", "es": "@city (@postal_code)", "it": "@city (@postal_code)", "lb": "@city (@postal_code)", "nl": "@city (@postal_code)", "pl": "@city (@postal_code)"},
        "@lead. Ref. @reference.": {"fr": "@lead. Réf. @reference.", "de": "@lead. Ref. @reference.", "es": "@lead. Ref. @reference.", "it": "@lead. Rif. @reference.", "lb": "@lead. Ref. @reference.", "nl": "@lead. Ref. @reference.", "pl": "@lead. Ref. @reference."},
        "@location, @region": {"fr": "@location, @region", "de": "@location, @region", "es": "@location, @region", "it": "@location, @region", "lb": "@location, @region", "nl": "@location, @region", "pl": "@location, @region"},
        "Configuration": {"fr": "Configuration", "de": "Konfiguration", "es": "Configuración", "it": "Configurazione", "lb": "Konfiguratioun", "nl": "Configuratie", "pl": "Konfiguracja"},
        "SEO": {"fr": "SEO", "de": "SEO", "es": "SEO", "it": "SEO", "lb": "SEO", "nl": "SEO", "pl": "SEO"},
        "[current-page:title] | [site:name]": {"fr": "[current-page:title] | [site:name]", "de": "[current-page:title] | [site:name]", "es": "[current-page:title] | [site:name]", "it": "[current-page:title] | [site:name]", "lb": "[current-page:title] | [site:name]", "nl": "[current-page:title] | [site:name]", "pl": "[current-page:title] | [site:name]"},
        "[node:field_commercial_title] | [site:name]": {"fr": "[node:field_commercial_title] | [site:name]", "de": "[node:field_commercial_title] | [site:name]", "es": "[node:field_commercial_title] | [site:name]", "it": "[node:field_commercial_title] | [site:name]", "lb": "[node:field_commercial_title] | [site:name]", "nl": "[node:field_commercial_title] | [site:name]", "pl": "[node:field_commercial_title] | [site:name]"},
        "[node:summary]": {"fr": "[node:summary]", "de": "[node:summary]", "es": "[node:summary]", "it": "[node:summary]", "lb": "[node:summary]", "nl": "[node:summary]", "pl": "[node:summary]"},
        "[site:name]": {"fr": "[site:name]", "de": "[site:name]", "es": "[site:name]", "it": "[site:name]", "lb": "[site:name]", "nl": "[site:name]", "pl": "[site:name]"},
        "[site:slogan]": {"fr": "[site:slogan]", "de": "[site:slogan]", "es": "[site:slogan]", "it": "[site:slogan]", "lb": "[site:slogan]", "nl": "[site:slogan]", "pl": "[site:slogan]"},
    },
    "ps_surface": {
        "Lot": {"fr": "Lot", "de": "Los", "es": "Lote", "it": "Lotto", "lb": "Lot", "nl": "Perceel", "pl": "Lokal"},
        "Nature": {"fr": "Nature", "de": "Art", "es": "Naturaleza", "it": "Natura", "lb": "Natur", "nl": "Aard", "pl": "Rodzaj"},
        "Qualification": {"fr": "Qualification", "de": "Qualifikation", "es": "Calificación", "it": "Qualificazione", "lb": "Qualifikatioun", "nl": "Kwalificatie", "pl": "Kwalifikacja"},
        "Surface": {"fr": "Surface", "de": "Fläche", "es": "Superficie", "it": "Superficie", "lb": "Fläch", "nl": "Oppervlakte", "pl": "Powierzchnia"},
        "Surfaces": {"fr": "Surfaces", "de": "Flächen", "es": "Superficies", "it": "Superfici", "lb": "Flächen", "nl": "Oppervlakten", "pl": "Powierzchnie"},
        "description": {"fr": "description", "de": "Beschreibung", "es": "descripción", "it": "descrizione", "lb": "Beschreiwung", "nl": "beschrijving", "pl": "opis"},
    },
    "ps_media": {
        "Documents (PS Media)": {
            "fr": "Documents (PS Media)", "de": "Dokumente (PS Media)", "es": "Documentos (PS Media)",
            "it": "Documenti (PS Media)", "lb": "Dokumenter (PS Media)", "nl": "Documenten (PS Media)", "pl": "Dokumenty (PS Media)",
        },
        "description": {"fr": "description", "de": "Beschreibung", "es": "descripción", "it": "descrizione", "lb": "Beschreiwung", "nl": "beschrijving", "pl": "opis"},
    },
    "ps_diagnostic": {
        "DPE": {"fr": "DPE", "de": "DPE", "es": "DPE", "it": "DPE", "lb": "DPE", "nl": "DPE", "pl": "DPE"},
        "GES": {"fr": "GES", "de": "GES", "es": "GES", "it": "GES", "lb": "GES", "nl": "GES", "pl": "GES"},
    },
}


def apply_patches() -> None:
    for module, patches in PATCHES.items():
        path = CATALOG_DIR / f"{module}.json"
        if not path.exists():
            print(f"skip {module}: no catalog")
            continue
        catalog = json.loads(path.read_text(encoding="utf-8"))
        applied = 0
        for msgid, langs in patches.items():
            if msgid not in catalog:
                continue
            for lang, trans in langs.items():
                catalog[msgid][lang] = trans
            applied += 1
        path.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
        fr_same = sum(1 for k, v in catalog.items() if v.get("fr") == k)
        print(f"{module}: patched {applied}, FR=EN remaining {fr_same}")


if __name__ == "__main__":
    apply_patches()
