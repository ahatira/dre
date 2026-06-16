#!/usr/bin/env python3
"""Complete ps_theme translation catalog from cross-module reuse and UI patches."""

from __future__ import annotations

import json
from pathlib import Path

CATALOG_DIR = Path(__file__).resolve().parent / "translation_catalog"
LANGS = ("de", "es", "fr", "it", "lb", "nl", "pl")
OTHER_MODULES = ("ps_search", "ps_compare", "ps_favorite", "ps_core", "ps_form")

UI_PATCHES: dict[str, dict[str, str]] = {
    "3D visit": {
        "fr": "Visite 3D", "de": "3D-Tour", "es": "Visita 3D", "it": "Visita 3D",
        "lb": "3D-Tour", "nl": "3D-rondleiding", "pl": "Wizyta 3D",
    },
    "@current / @total": {
        "fr": "@current / @total", "de": "@current / @total", "es": "@current / @total",
        "it": "@current / @total", "lb": "@current / @total", "nl": "@current / @total", "pl": "@current / @total",
    },
    "@current of @total": {
        "fr": "@current sur @total", "de": "@current von @total", "es": "@current de @total",
        "it": "@current di @total", "lb": "@current vun @total", "nl": "@current van @total", "pl": "@current z @total",
    },
    "About menu": {
        "fr": "Menu À propos", "de": "Menü Über uns", "es": "Menú Sobre nosotros", "it": "Menu Chi siamo",
        "lb": "Menu Iwwer eis", "nl": "Menu Over ons", "pl": "Menu O nas",
    },
    "Account navigation": {
        "fr": "Navigation du compte", "de": "Kontonavigation", "es": "Navegación de cuenta", "it": "Navigazione account",
        "lb": "Kont-Navigatioun", "nl": "Accountnavigatie", "pl": "Nawigacja konta",
    },
    "BNP Paribas Real Estate": {
        "fr": "BNP Paribas Real Estate", "de": "BNP Paribas Real Estate", "es": "BNP Paribas Real Estate",
        "it": "BNP Paribas Real Estate", "lb": "BNP Paribas Real Estate", "nl": "BNP Paribas Real Estate", "pl": "BNP Paribas Real Estate",
    },
    "Buy": {
        "fr": "Acheter", "de": "Kaufen", "es": "Comprar", "it": "Acquista",
        "lb": "Kafen", "nl": "Kopen", "pl": "Kup",
    },
    "Calculate your target office space": {
        "fr": "Calculez votre surface de bureau cible", "de": "Berechnen Sie Ihre Ziel-Bürofläche",
        "es": "Calcule su superficie de oficina objetivo", "it": "Calcola la superficie ufficio target",
        "lb": "Berechnen Är Zil-Bürofläch", "nl": "Bereken uw doelkantoorruimte", "pl": "Oblicz docelową powierzchnię biura",
    },
    "Calculate your travel time :": {
        "fr": "Calculez votre temps de trajet :", "de": "Berechnen Sie Ihre Fahrzeit:",
        "es": "Calcule su tiempo de viaje:", "it": "Calcola il tempo di percorrenza:",
        "lb": "Berechnen Är Fahrzäit:", "nl": "Bereken uw reistijd:", "pl": "Oblicz czas podróży:",
    },
    "Clear departure address": {
        "fr": "Effacer l'adresse de départ", "de": "Abfahrtsadresse löschen", "es": "Borrar dirección de salida",
        "it": "Cancella indirizzo di partenza", "lb": "Startadress läschen", "nl": "Vertrekadres wissen", "pl": "Wyczyść adres startowy",
    },
    "Clear location search": {
        "fr": "Effacer la recherche de localisation", "de": "Standortsuche löschen", "es": "Borrar búsqueda de ubicación",
        "it": "Cancella ricerca località", "lb": "Standuertsich läschen", "nl": "Locatiezoekopdracht wissen", "pl": "Wyczyść wyszukiwanie lokalizacji",
    },
    "Customize area": {
        "fr": "Personnaliser la zone", "de": "Bereich anpassen", "es": "Personalizar zona", "it": "Personalizza area",
        "lb": "Beräich personaliséieren", "nl": "Gebied aanpassen", "pl": "Dostosuj obszar",
    },
    "Departure address :": {
        "fr": "Adresse de départ :", "de": "Abfahrtsadresse:", "es": "Dirección de salida:",
        "it": "Indirizzo di partenza:", "lb": "Startadress:", "nl": "Vertrekadres:", "pl": "Adres startowy:",
    },
    "Departure or destination address could not be found.": {
        "fr": "L'adresse de départ ou de destination n'a pas pu être trouvée.", "de": "Abfahrts- oder Zieladresse konnte nicht gefunden werden.",
        "es": "No se encontró la dirección de salida o destino.", "it": "Indirizzo di partenza o destinazione non trovato.",
        "lb": "Start- oder Zieladress konnt net fonnt ginn.", "nl": "Vertrek- of bestemmingsadres niet gevonden.", "pl": "Nie znaleziono adresu startowego lub docelowego.",
    },
    "Discover your tools:": {
        "fr": "Découvrez vos outils :", "de": "Entdecken Sie Ihre Tools:", "es": "Descubra sus herramientas:",
        "it": "Scopri i tuoi strumenti:", "lb": "Entdeckt Är Tools:", "nl": "Ontdek uw tools:", "pl": "Odkryj swoje narzędzia:",
    },
    "Enter an address": {
        "fr": "Saisissez une adresse", "de": "Adresse eingeben", "es": "Introduzca una dirección", "it": "Inserisci un indirizzo",
        "lb": "Adress aginn", "nl": "Voer een adres in", "pl": "Wpisz adres",
    },
    "Expert journey steps": {
        "fr": "Étapes du parcours expert", "de": "Expert-Schritte", "es": "Pasos del recorrido experto", "it": "Passi del percorso esperto",
        "lb": "Expert-Schrëtt", "nl": "Experttraject stappen", "pl": "Kroki ścieżki eksperta",
    },
    "Favorites": {
        "fr": "Favoris", "de": "Favoriten", "es": "Favoritos", "it": "Preferiti",
        "lb": "Favoritten", "nl": "Favorieten", "pl": "Ulubione",
    },
    "Follow us": {
        "fr": "Suivez-nous", "de": "Folgen Sie uns", "es": "Síguenos", "it": "Seguici",
        "lb": "Follegt eis", "nl": "Volg ons", "pl": "Obserwuj nas",
    },
    "Google Maps API key is not configured.": {
        "fr": "La clé API Google Maps n'est pas configurée.", "de": "Google Maps API-Schlüssel ist nicht konfiguriert.",
        "es": "La clave API de Google Maps no está configurada.", "it": "La chiave API Google Maps non è configurata.",
        "lb": "Google Maps API-Schlëssel ass net konfiguréiert.", "nl": "Google Maps API-sleutel is niet geconfigureerd.", "pl": "Klucz API Google Maps nie jest skonfigurowany.",
    },
    "Home": {
        "fr": "Accueil", "de": "Startseite", "es": "Inicio", "it": "Home",
        "lb": "Start", "nl": "Home", "pl": "Strona główna",
    },
    "Main navigation": {
        "fr": "Navigation principale", "de": "Hauptnavigation", "es": "Navegación principal", "it": "Navigazione principale",
        "lb": "Haaptnavigatioun", "nl": "Hoofdnavigatie", "pl": "Nawigacja główna",
    },
    "Max surface (m²)": {
        "fr": "Surface max. (m²)", "de": "Max. Fläche (m²)", "es": "Superficie máx. (m²)", "it": "Superficie max (m²)",
        "lb": "Max. Fläch (m²)", "nl": "Max. oppervlakte (m²)", "pl": "Maks. powierzchnia (m²)",
    },
    "Min surface (m²)": {
        "fr": "Surface min. (m²)", "de": "Min. Fläche (m²)", "es": "Superficie mín. (m²)", "it": "Superficie min (m²)",
        "lb": "Min. Fläch (m²)", "nl": "Min. oppervlakte (m²)", "pl": "Min. powierzchnia (m²)",
    },
    "More information": {
        "fr": "Plus d'informations", "de": "Weitere Informationen", "es": "Más información", "it": "Ulteriori informazioni",
        "lb": "Méi Informatiounen", "nl": "Meer informatie", "pl": "Więcej informacji",
    },
    "My location": {
        "fr": "Ma position", "de": "Mein Standort", "es": "Mi ubicación", "it": "La mia posizione",
        "lb": "Meng Positioun", "nl": "Mijn locatie", "pl": "Moja lokalizacja",
    },
    "No media available": {
        "fr": "Aucun média disponible", "de": "Keine Medien verfügbar", "es": "No hay medios disponibles", "it": "Nessun media disponibile",
        "lb": "Keng Medien disponibel", "nl": "Geen media beschikbaar", "pl": "Brak dostępnych mediów",
    },
    "No properties in this map area": {
        "fr": "Aucun bien dans cette zone de la carte", "de": "Keine Immobilien in diesem Kartenbereich",
        "es": "No hay inmuebles en esta zona del mapa", "it": "Nessun immobile in quest'area della mappa",
        "lb": "Keng Immobilie an dësem Kaartebereich", "nl": "Geen objecten in dit kaartgebied", "pl": "Brak nieruchomości w tym obszarze mapy",
    },
    "No cycling route found for this journey.": {
        "fr": "Aucun itinéraire cyclable trouvé pour ce trajet.", "de": "Keine Radroute für diese Strecke gefunden.",
        "es": "No se encontró ruta en bicicleta para este trayecto.", "it": "Nessun percorso in bici trovato per questo tragitto.",
        "lb": "Keng Vëloroute fir dëse Wee fonnt.", "nl": "Geen fietsroute gevonden voor deze route.", "pl": "Nie znaleziono trasy rowerowej dla tej podróży.",
    },
    "No public transport route found for this journey.": {
        "fr": "Aucun itinéraire en transports publics trouvé pour ce trajet.", "de": "Keine ÖPNV-Route für diese Strecke gefunden.",
        "es": "No se encontró ruta de transporte público para este trayecto.", "it": "Nessun percorso di trasporto pubblico trovato.",
        "lb": "Keng ëffentlech Transportroute fonnt.", "nl": "Geen openbaarvervoerroute gevonden.", "pl": "Nie znaleziono trasy transportu publicznego.",
    },
    "No route found for this journey.": {
        "fr": "Aucun itinéraire trouvé pour ce trajet.", "de": "Keine Route für diese Strecke gefunden.",
        "es": "No se encontró ruta para este trayecto.", "it": "Nessun percorso trovato per questo tragitto.",
        "lb": "Keng Route fir dëse Wee fonnt.", "nl": "Geen route gevonden voor deze route.", "pl": "Nie znaleziono trasy dla tej podróży.",
    },
    "No walking route found for this journey.": {
        "fr": "Aucun itinéraire piéton trouvé pour ce trajet.", "de": "Keine Fußroute für diese Strecke gefunden.",
        "es": "No se encontró ruta a pie para este trayecto.", "it": "Nessun percorso a piedi trovato.",
        "lb": "Keng Foussroute fonnt.", "nl": "Geen wandelroute gevonden.", "pl": "Nie znaleziono trasy pieszej.",
    },
    "Office or coworking rental? Take the test!": {
        "fr": "Location de bureau ou coworking ? Faites le test !", "de": "Büro- oder Coworking-Miete? Machen Sie den Test!",
        "es": "¿Alquiler de oficina o coworking? ¡Haga la prueba!", "it": "Affitto ufficio o coworking? Fai il test!",
        "lb": "Büro- oder Coworking-Miet? Maacht de Test!", "nl": "Kantoor of coworking huren? Doe de test!", "pl": "Wynajem biura lub coworking? Zrób test!",
    },
    "Open 3D visit in a new tab": {
        "fr": "Ouvrir la visite 3D dans un nouvel onglet", "de": "3D-Tour in neuem Tab öffnen", "es": "Abrir visita 3D en una nueva pestaña",
        "it": "Apri visita 3D in una nuova scheda", "lb": "3D-Tour an neiem Tab opmaachen", "nl": "3D-rondleiding openen in nieuw tabblad", "pl": "Otwórz wizytę 3D w nowej karcie",
    },
    "Open favorites panel": {
        "fr": "Ouvrir le panneau favoris", "de": "Favoritenbereich öffnen", "es": "Abrir panel de favoritos",
        "it": "Apri panel preferiti", "lb": "Favoritten-Panell opmaachen", "nl": "Favorietenpaneel openen", "pl": "Otwórz panel ulubionych",
    },
    "Open gallery": {
        "fr": "Ouvrir la galerie", "de": "Galerie öffnen", "es": "Abrir galería", "it": "Apri galleria",
        "lb": "Galerie opmaachen", "nl": "Galerie openen", "pl": "Otwórz galerię",
    },
    "Our internal tools:": {
        "fr": "Nos outils internes :", "de": "Unsere internen Tools:", "es": "Nuestras herramientas internas:",
        "it": "I nostri strumenti interni:", "lb": "Eis intern Tools:", "nl": "Onze interne tools:", "pl": "Nasze narzędzia wewnętrzne:",
    },
    "Plan": {
        "fr": "Plan", "de": "Grundriss", "es": "Plano", "it": "Planimetria",
        "lb": "Plang", "nl": "Plattegrond", "pl": "Plan",
    },
    "Please select an address from the suggestions.": {
        "fr": "Veuillez sélectionner une adresse dans les suggestions.", "de": "Bitte wählen Sie eine Adresse aus den Vorschlägen.",
        "es": "Seleccione una dirección de las sugerencias.", "it": "Seleziona un indirizzo dai suggerimenti.",
        "lb": "Wielt w.e.g. eng Adress aus de Virschléi.", "nl": "Selecteer een adres uit de suggesties.", "pl": "Wybierz adres z podpowiedzi.",
    },
    "Property types": {
        "fr": "Types de biens", "de": "Immobilientypen", "es": "Tipos de inmuebles", "it": "Tipi di immobili",
        "lb": "Immobilientypen", "nl": "Type objecten", "pl": "Typy nieruchomości",
    },
    "Rechercher": {
        "fr": "Rechercher", "de": "Suchen", "es": "Buscar", "it": "Cerca",
        "lb": "Sichen", "nl": "Zoeken", "pl": "Szukaj",
    },
    "Reset": {
        "fr": "Réinitialiser", "de": "Zurücksetzen", "es": "Restablecer", "it": "Reimposta",
        "lb": "Zrécksetzen", "nl": "Resetten", "pl": "Resetuj",
    },
    "Search": {
        "fr": "Rechercher", "de": "Suchen", "es": "Buscar", "it": "Cerca",
        "lb": "Sichen", "nl": "Zoeken", "pl": "Szukaj",
    },
    "See you soon!": {
        "fr": "À bientôt !", "de": "Bis bald!", "es": "¡Hasta pronto!", "it": "A presto!",
        "lb": "Bis geschwënn!", "nl": "Tot ziens!", "pl": "Do zobaczenia!",
    },
    "Select language": {
        "fr": "Choisir la langue", "de": "Sprache auswählen", "es": "Seleccionar idioma", "it": "Seleziona lingua",
        "lb": "Sprooch wielen", "nl": "Taal selecteren", "pl": "Wybierz język",
    },
    "Show points of interest :": {
        "fr": "Afficher les points d'intérêt :", "de": "Sehenswürdigkeiten anzeigen:", "es": "Mostrar puntos de interés:",
        "it": "Mostra punti di interesse:", "lb": "Interessi-Punkten weisen:", "nl": "Bezienswaardigheden tonen:", "pl": "Pokaż punkty zainteresowania:",
    },
    "Skip to main content": {
        "fr": "Aller au contenu principal", "de": "Zum Hauptinhalt springen", "es": "Pasar al contenido principal",
        "it": "Vai al contenuto principale", "lb": "Op de Haaptinhalt sprangen", "nl": "Naar hoofdinhoud", "pl": "Przejdź do treści głównej",
    },
    "This message was sent by": {
        "fr": "Ce message a été envoyé par", "de": "Diese Nachricht wurde gesendet von", "es": "Este mensaje fue enviado por",
        "it": "Questo messaggio è stato inviato da", "lb": "Dëse Message gouf geschéckt vun", "nl": "Dit bericht is verzonden door", "pl": "Ta wiadomość została wysłana przez",
    },
    "This route may include several connections.": {
        "fr": "Cet itinéraire peut inclure plusieurs correspondances.", "de": "Diese Route kann mehrere Umstiege enthalten.",
        "es": "Esta ruta puede incluir varias conexiones.", "it": "Questo percorso può includere diversi collegamenti.",
        "lb": "Dës Route kann e puer Ëmstieger enthalen.", "nl": "Deze route kan meerdere overstappen bevatten.", "pl": "Ta trasa może zawierać kilka połączeń.",
    },
    "Toggle navigation": {
        "fr": "Basculer la navigation", "de": "Navigation umschalten", "es": "Alternar navegación", "it": "Attiva/disattiva navigazione",
        "lb": "Navigatioun wiesselen", "nl": "Navigatie wisselen", "pl": "Przełącz nawigację",
    },
    "Transaction type": {
        "fr": "Type de transaction", "de": "Transaktionstyp", "es": "Tipo de transacción", "it": "Tipo di transazione",
        "lb": "Transaktiounstyp", "nl": "Transactietype", "pl": "Typ transakcji",
    },
    "Transports": {
        "fr": "Transports", "de": "Verkehrsmittel", "es": "Transportes", "it": "Trasporti",
        "lb": "Transporten", "nl": "Vervoer", "pl": "Transport",
    },
    "Travel mode": {
        "fr": "Mode de transport", "de": "Fortbewegungsart", "es": "Modo de transporte", "it": "Modalità di trasporto",
        "lb": "Fortbewegungsmodus", "nl": "Vervoermiddel", "pl": "Tryb transportu",
    },
    "Travel mode :": {
        "fr": "Mode de transport :", "de": "Fortbewegungsart:", "es": "Modo de transporte:", "it": "Modalità di trasporto:",
        "lb": "Fortbewegungsmodus:", "nl": "Vervoermiddel:", "pl": "Tryb transportu:",
    },
    "Unable to calculate travel time.": {
        "fr": "Impossible de calculer le temps de trajet.", "de": "Fahrzeit konnte nicht berechnet werden.",
        "es": "No se puede calcular el tiempo de viaje.", "it": "Impossibile calcolare il tempo di percorrenza.",
        "lb": "Fahrzäit konnt net berechent ginn.", "nl": "Kan reistijd niet berekenen.", "pl": "Nie można obliczyć czasu podróży.",
    },
    "Via": {
        "fr": "Via", "de": "Via", "es": "Vía", "it": "Via",
        "lb": "Via", "nl": "Via", "pl": "Via",
    },
    "eCommute: employee commute time calculator": {
        "fr": "eCommute : calculateur de temps de trajet domicile-travail", "de": "eCommute: Pendlerzeit-Rechner",
        "es": "eCommute: calculadora de tiempo de desplazamiento", "it": "eCommute: calcolatore tempo di pendolarismo",
        "lb": "eCommute: Pendlerzäit-Rechner", "nl": "eCommute: reistijd calculator", "pl": "eCommute: kalkulator czasu dojazdu",
    },
    "eSelect: personalised listing matching tool": {
        "fr": "eSelect : outil de matching d'annonces personnalisé", "de": "eSelect: personalisiertes Anzeigen-Matching",
        "es": "eSelect: herramienta de emparejamiento de anuncios", "it": "eSelect: strumento di matching annunci personalizzato",
        "lb": "eSelect: personaliséiert Annonce-Matching", "nl": "eSelect: gepersonaliseerde matchingtool", "pl": "eSelect: spersonalizowane dopasowanie ofert",
    },
    "mo": {
        "fr": "mois", "de": "Mon.", "es": "mes", "it": "mese", "lb": "Mount", "nl": "mnd", "pl": "mies.",
    },
    "photos": {
        "fr": "photos", "de": "Fotos", "es": "fotos", "it": "foto", "lb": "Fotoen", "nl": "foto's", "pl": "zdjęcia",
    },
    "seat": {
        "fr": "poste", "de": "Platz", "es": "plaza", "it": "posto", "lb": "Plaz", "nl": "plek", "pl": "miejsce",
    },
    "yr": {
        "fr": "an", "de": "J.", "es": "año", "it": "anno", "lb": "Joer", "nl": "jr", "pl": "rok",
    },
    "Offer actions": {
        "fr": "Actions sur l'offre", "de": "Angebotsaktionen", "es": "Acciones de la oferta", "it": "Azioni offerta",
        "lb": "Offer-Aktiounen", "nl": "Aanbiedingsacties", "pl": "Akcje oferty",
    },
    "Capacity": {
        "fr": "Capacité", "de": "Kapazität", "es": "Capacidad", "it": "Capacità",
        "lb": "Kapazitéit", "nl": "Capaciteit", "pl": "Pojemność",
    },
    "Max @unit": {
        "fr": "Max @unit", "de": "Max @unit", "es": "Máx @unit", "it": "Max @unit",
        "lb": "Max @unit", "nl": "Max @unit", "pl": "Maks @unit",
    },
    "Min @unit": {
        "fr": "Min @unit", "de": "Min @unit", "es": "Mín @unit", "it": "Min @unit",
        "lb": "Min @unit", "nl": "Min @unit", "pl": "Min @unit",
    },
    "Next media": {
        "fr": "Média suivant", "de": "Nächstes Medium", "es": "Medio siguiente", "it": "Media successivo",
        "lb": "Nächst Medium", "nl": "Volgende media", "pl": "Następne media",
    },
    "Previous media": {
        "fr": "Média précédent", "de": "Vorheriges Medium", "es": "Medio anterior", "it": "Media precedente",
        "lb": "Viregt Medium", "nl": "Vorige media", "pl": "Poprzednie media",
    },
}

SDC_FR_TO_LANG: dict[str, dict[str, str]] = {
    "Carte offre horizontale pour les résultats de recherche (Stellar Product card).": {
        "fr": "Carte offre horizontale pour les résultats de recherche (Stellar Product card).",
        "de": "Horizontale Angebotskarte für Suchergebnisse (Stellar Product card).",
        "es": "Tarjeta horizontal de oferta para resultados de búsqueda (Stellar Product card).",
        "it": "Scheda offerta orizzontale per risultati di ricerca (Stellar Product card).",
        "lb": "Horizontal Offer-Kaart fir Sichresultater.", "nl": "Horizontale aanbiedingskaart voor zoekresultaten.", "pl": "Pozioma karta oferty dla wyników wyszukiwania.",
    },
    "Carte offre verticale pour carrousels homepage et grilles teaser (Stellar).": {
        "fr": "Carte offre verticale pour carrousels homepage et grilles teaser (Stellar).",
        "de": "Vertikale Angebotskarte für Homepage-Karussells und Teaser-Grids (Stellar).",
        "es": "Tarjeta vertical de oferta para carruseles de homepage y rejillas teaser (Stellar).",
        "it": "Scheda offerta verticale per carrelli homepage e griglie teaser (Stellar).",
        "lb": "Vertikal Offer-Kaart fir Homepage-Karusseller.", "nl": "Verticale aanbiedingskaart voor homepage-carrousels.", "pl": "Pionowa karta oferty dla karuzel strony głównej.",
    },
    "Consultant card for offer detail with contact and visit CTAs.": {
        "fr": "Carte consultant pour fiche offre avec CTAs contact et visite.",
        "de": "Beraterkarte für Angebotsdetail mit Kontakt- und Besichtigungs-CTAs.",
        "es": "Tarjeta de consultor para ficha de oferta con CTAs de contacto y visita.",
        "it": "Scheda consulente per dettaglio offerta con CTA contatto e visita.",
        "lb": "Berater-Kaart fir Offerdetail.", "nl": "Consultantkaart voor aanbiedingsdetail.", "pl": "Karta konsultanta dla szczegółów oferty.",
    },
    "Full-width homepage band shell — background, Bootstrap spacing, container and body slots.": {
        "fr": "Bandeau homepage pleine largeur — arrière-plan, espacements Bootstrap, conteneur et emplacements body.",
        "de": "Homepage-Vollbreiten-Band — Hintergrund, Bootstrap-Abstände, Container und Body-Slots.",
        "es": "Franja homepage a ancho completo — fondo, espaciado Bootstrap, contenedor y slots body.",
        "it": "Fascia homepage a tutta larghezza — sfondo, spaziatura Bootstrap, container e slot body.",
        "lb": "Homepage-Vollbreet-Band.", "nl": "Homepage volledige breedte band.", "pl": "Pełnoszerokościowy pas strony głównej.",
    },
    "Homepage expert journey — desktop stepper with per-step images and mobile stacked cards.": {
        "fr": "Parcours expert homepage — stepper desktop avec images par étape et cartes empilées mobile.",
        "de": "Homepage-Expert Journey — Desktop-Stepper mit Bildern pro Schritt und gestapelten Mobile-Karten.",
        "es": "Recorrido experto homepage — stepper escritorio con imágenes por paso y tarjetas apiladas móvil.",
        "it": "Percorso esperto homepage — stepper desktop con immagini per passo e card impilate mobile.",
        "lb": "Homepage-Expert Journey.", "nl": "Homepage experttraject.", "pl": "Ścieżka eksperta na stronie głównej.",
    },
    "Homepage hero delegate search bar below the search form.": {
        "fr": "Barre de recherche déléguée sous le formulaire de recherche du hero.",
        "de": "Delegierte Suchleiste unter dem Hero-Suchformular.",
        "es": "Barra de búsqueda delegada bajo el formulario de búsqueda del hero.",
        "it": "Barra di ricerca delegata sotto il form di ricerca hero.",
        "lb": "Delegéiert Sichbar ënner dem Hero-Form.", "nl": "Gedelegeerde zoekbalk onder hero-zoekformulier.", "pl": "Delegowana wyszukiwarka pod formularzem hero.",
    },
    "Homepage hero promotional panel (right column).": {
        "fr": "Panneau promotionnel hero homepage (colonne droite).",
        "de": "Homepage-Hero-Werbebereich (rechte Spalte).",
        "es": "Panel promocional hero homepage (columna derecha).",
        "it": "Panel promozionale hero homepage (colonna destra).",
        "lb": "Homepage-Hero-Werbebereich.", "nl": "Homepage hero promotiepaneel.", "pl": "Panel promocyjny hero strony głównej.",
    },
    "Homepage hero shell — background, title, search, delegate and promo slots.": {
        "fr": "Shell hero homepage — arrière-plan, titre, recherche, délégué et emplacements promo.",
        "de": "Homepage-Hero-Shell — Hintergrund, Titel, Suche, Delegate und Promo-Slots.",
        "es": "Shell hero homepage — fondo, título, búsqueda, delegado y slots promo.",
        "it": "Shell hero homepage — sfondo, titolo, ricerca, delegato e slot promo.",
        "lb": "Homepage-Hero-Shell.", "nl": "Homepage hero shell.", "pl": "Shell hero strony głównej.",
    },
    "Homepage section footer CTA button.": {
        "fr": "Bouton CTA pied de section homepage.",
        "de": "Homepage-Sektions-Footer-CTA-Button.",
        "es": "Botón CTA pie de sección homepage.",
        "it": "Pulsante CTA footer sezione homepage.",
        "lb": "Homepage-Sektioun-Footer-CTA.", "nl": "Homepage sectie footer CTA-knop.", "pl": "Przycisk CTA stopki sekcji strony głównej.",
    },
    "Homepage section title, optional subtitle and alignment variants.": {
        "fr": "Titre de section homepage, sous-titre optionnel et variantes d'alignement.",
        "de": "Homepage-Sektionstitel, optionaler Untertitel und Ausrichtungsvarianten.",
        "es": "Título de sección homepage, subtítulo opcional y variantes de alineación.",
        "it": "Titolo sezione homepage, sottotitolo opzionale e varianti allineamento.",
        "lb": "Homepage-Sektiounstitel.", "nl": "Homepage sectietitel en uitlijning.", "pl": "Tytuł sekcji strony głównej i warianty wyrównania.",
    },
    "Homepage service card with icon, title, body and bottom-aligned CTA.": {
        "fr": "Carte service homepage avec icône, titre, corps et CTA aligné en bas.",
        "de": "Homepage-Servicekarte mit Icon, Titel, Text und unten ausgerichtetem CTA.",
        "es": "Tarjeta de servicio homepage con icono, título, cuerpo y CTA alineado abajo.",
        "it": "Scheda servizio homepage con icona, titolo, corpo e CTA in basso.",
        "lb": "Homepage-Servicekaart.", "nl": "Homepage servicekaart.", "pl": "Karta usługi strony głównej.",
    },
    "Interactive offer map with POI filters and travel time.": {
        "fr": "Carte interactive d'offre avec filtres POI et temps de trajet.",
        "de": "Interaktive Angebotskarte mit POI-Filtern und Fahrzeit.",
        "es": "Mapa interactivo de oferta con filtros POI y tiempo de viaje.",
        "it": "Mappa interattiva offerta con filtri POI e tempo di percorrenza.",
        "lb": "Interaktiv Offer-Kaart.", "nl": "Interactieve aanbiedingskaart.", "pl": "Interaktywna mapa oferty.",
    },
    "Interstitial promotional card inserted between search result offer cards.": {
        "fr": "Carte promotionnelle interstitielle insérée entre les cartes offre des résultats.",
        "de": "Interstitiale Werbekarte zwischen Suchergebnis-Angebotskarten.",
        "es": "Tarjeta promocional intersticial entre tarjetas de oferta en resultados.",
        "it": "Card promozionale interstiziale tra le card offerta nei risultati.",
        "lb": "Interstitial Promo-Kaart.", "nl": "Interstitiale promotiekaart.", "pl": "Karta promocyjna między wynikami.",
    },
    "Location editor row (chips + autocomplete input) for the BNPPRE search filter bar.": {
        "fr": "Ligne éditeur de localisation (puces + saisie autocomplete) pour le filtre BNPPRE.",
        "de": "Standort-Editorzeile (Chips + Autocomplete) für BNPPRE-Suchfilter.",
        "es": "Fila editor de ubicación (chips + autocompletar) para filtro BNPPRE.",
        "it": "Riga editor località (chip + autocomplete) per filtro BNPPRE.",
        "lb": "Standuert-Editorzeil.", "nl": "Locatie-editorrij voor BNPPRE-filter.", "pl": "Wiersz edytora lokalizacji dla filtra BNPPRE.",
    },
    "Louer / Acheter toggle for Property Search (SEO URLs via ps_search).": {
        "fr": "Toggle Louer / Acheter pour Property Search (URLs SEO via ps_search).",
        "de": "Mieten/Kaufen-Umschalter für Property Search (SEO-URLs via ps_search).",
        "es": "Toggle Alquilar/Comprar para Property Search (URLs SEO vía ps_search).",
        "it": "Toggle Affitta/Acquista per Property Search (URL SEO via ps_search).",
        "lb": "Mieten/Kafen-Toggle.", "nl": "Huur/koop schakelaar Property Search.", "pl": "Przełącznik wynajem/kupno Property Search.",
    },
    "Min/max range inputs (surface, capacity, budget) for the BNPPRE search filter bar.": {
        "fr": "Champs min/max (surface, capacité, budget) pour le filtre BNPPRE.",
        "de": "Min/Max-Eingaben (Fläche, Kapazität, Budget) für BNPPRE-Suchfilter.",
        "es": "Campos min/max (superficie, capacidad, presupuesto) para filtro BNPPRE.",
        "it": "Input min/max (superficie, capacità, budget) per filtro BNPPRE.",
        "lb": "Min/Max-Felder.", "nl": "Min/max velden BNPPRE-filter.", "pl": "Pola min/max filtra BNPPRE.",
    },
    "Minimal homepage search shortcut with icon, title and text link.": {
        "fr": "Raccourci de recherche homepage minimal avec icône, titre et lien texte.",
        "de": "Minimaler Homepage-Suchshortcut mit Icon, Titel und Textlink.",
        "es": "Atajo de búsqueda homepage mínimo con icono, título y enlace.",
        "it": "Shortcut ricerca homepage minimale con icona, titolo e link.",
        "lb": "Minimalen Homepage-Sichshortcut.", "nl": "Minimale homepage zoeksnelkoppeling.", "pl": "Minimalny skrót wyszukiwania strony głównej.",
    },
    "Modal media gallery with thumbnails for offer pages.": {
        "fr": "Galerie média modale avec vignettes pour pages offre.",
        "de": "Modale Mediagalerie mit Vorschaubildern für Angebotsseiten.",
        "es": "Galería modal con miniaturas para páginas de oferta.",
        "it": "Galleria media modale con miniature per pagine offerta.",
        "lb": "Modal Media-Galerie.", "nl": "Modale mediagalerie.", "pl": "Modalna galeria mediów oferty.",
    },
    "News article teaser for homepage grid and news listing.": {
        "fr": "Teaser article pour grille homepage et liste actualités.",
        "de": "News-Artikel-Teaser für Homepage-Grid und Newsliste.",
        "es": "Teaser de artículo para rejilla homepage y listado de noticias.",
        "it": "Teaser articolo per griglia homepage e lista news.",
        "lb": "News-Artikel-Teaser.", "nl": "Nieuwsartikel teaser.", "pl": "Zajawka artykułu news.",
    },
    "Property Search navbar — Stellar header shell with BNP custom icons.": {
        "fr": "Navbar Property Search — shell header Stellar avec icônes BNP.",
        "de": "Property Search Navbar — Stellar-Header-Shell mit BNP-Icons.",
        "es": "Navbar Property Search — shell header Stellar con iconos BNP.",
        "it": "Navbar Property Search — shell header Stellar con icone BNP.",
        "lb": "Property Search Navbar.", "nl": "Property Search navbar.", "pl": "Navbar Property Search.",
    },
    "Property type asset grid and transaction buttons for the BNPPRE search filter bar.": {
        "fr": "Grille types de biens et boutons transaction pour filtre BNPPRE.",
        "de": "Vermögensarten-Grid und Transaktionsbuttons für BNPPRE-Filter.",
        "es": "Rejilla tipos de inmueble y botones de transacción para filtro BNPPRE.",
        "it": "Griglia tipi immobile e pulsanti transazione per filtro BNPPRE.",
        "lb": "Verméigensarten-Grid.", "nl": "Activagrid en transactieknoppen BNPPRE.", "pl": "Siatka typów nieruchomości BNPPRE.",
    },
    "Simple card with title and action button.": {
        "fr": "Carte simple avec titre et bouton d'action.",
        "de": "Einfache Karte mit Titel und Aktionsbutton.",
        "es": "Tarjeta simple con título y botón de acción.",
        "it": "Scheda semplice con titolo e pulsante azione.",
        "lb": "Einfach Kaart.", "nl": "Eenvoudige kaart met actieknop.", "pl": "Prosta karta z przyciskiem akcji.",
    },
    "Single hero slide preview for offer gallery.": {
        "fr": "Aperçu slide hero unique pour galerie offre.",
        "de": "Einzelne Hero-Slide-Vorschau für Angebotsgalerie.",
        "es": "Vista previa slide hero única para galería de oferta.",
        "it": "Anteprima slide hero singola per galleria offerta.",
        "lb": "Hero-Slide-Virschau.", "nl": "Enkele hero-slide preview.", "pl": "Podgląd pojedynczego slajdu hero.",
    },
    "Single lightbox thumbnail for offer gallery.": {
        "fr": "Vignette lightbox unique pour galerie offre.",
        "de": "Einzelnes Lightbox-Thumbnail für Angebotsgalerie.",
        "es": "Miniatura lightbox única para galería de oferta.",
        "it": "Miniatura lightbox singola per galleria offerta.",
        "lb": "Lightbox-Thumbnail.", "nl": "Enkele lightbox-miniatuur.", "pl": "Pojedyncza miniatura lightbox.",
    },
    "Stacked filter section shell for the mobile filters offcanvas (Phase 5A.6).": {
        "fr": "Shell section filtre empilée pour offcanvas filtres mobile.",
        "de": "Gestapelte Filter-Sektion-Shell für mobiles Filter-Offcanvas.",
        "es": "Shell sección filtro apilada para offcanvas filtros móvil.",
        "it": "Shell sezione filtro impilata per offcanvas filtri mobile.",
        "lb": "Gestapelte Filter-Sektioun.", "nl": "Gestapelde filtersectie shell.", "pl": "Shell sekcji filtrów mobilnych.",
    },
    "Stellar footer contact column — phones and email with icons.": {
        "fr": "Colonne contact footer Stellar — téléphones et e-mail avec icônes.",
        "de": "Stellar-Footer-Kontaktspalte — Telefon und E-Mail mit Icons.",
        "es": "Columna contacto footer Stellar — teléfonos y email con iconos.",
        "it": "Colonna contatto footer Stellar — telefoni e email con icone.",
        "lb": "Stellar-Footer-Kontaktspalte.", "nl": "Stellar footer contactkolom.", "pl": "Kolumna kontaktu stopki Stellar.",
    },
    "Stellar footer copyright line.": {
        "fr": "Ligne copyright footer Stellar.",
        "de": "Stellar-Footer-Copyright-Zeile.",
        "es": "Línea copyright footer Stellar.",
        "it": "Riga copyright footer Stellar.",
        "lb": "Stellar-Footer-Copyright.", "nl": "Stellar footer copyrightregel.", "pl": "Linia copyright stopki Stellar.",
    },
    "Stellar footer follow-us social links.": {
        "fr": "Liens sociaux Suivez-nous footer Stellar.",
        "de": "Stellar-Footer Social-Follow-us-Links.",
        "es": "Enlaces sociales Síguenos footer Stellar.",
        "it": "Link social Seguici footer Stellar.",
        "lb": "Stellar-Footer Social-Links.", "nl": "Stellar footer social links.", "pl": "Linki społecznościowe stopki Stellar.",
    },
    "Stellar footer menu column — accordion on mobile, static column on desktop.": {
        "fr": "Colonne menu footer Stellar — accordéon mobile, colonne statique desktop.",
        "de": "Stellar-Footer-Menüspalte — Akkordeon mobil, statische Spalte desktop.",
        "es": "Columna menú footer Stellar — acordeón móvil, columna estática escritorio.",
        "it": "Colonna menu footer Stellar — accordion mobile, colonna statica desktop.",
        "lb": "Stellar-Footer-Menüspalte.", "nl": "Stellar footer menukolom.", "pl": "Kolumna menu stopki Stellar.",
    },
    "Stellar footer — prefooter, three columns, legal bottom bar.": {
        "fr": "Footer Stellar — préfooter, trois colonnes, barre légale.",
        "de": "Stellar-Footer — Prefooter, drei Spalten, rechtliche Leiste.",
        "es": "Footer Stellar — prefooter, tres columnas, barra legal.",
        "it": "Footer Stellar — prefooter, tre colonne, barra legale.",
        "lb": "Stellar-Footer.", "nl": "Stellar footer.", "pl": "Stopka Stellar.",
    },
    "Stellar header — branding/shortcut row + navigation/actions row.": {
        "fr": "Header Stellar — ligne branding/raccourcis + ligne navigation/actions.",
        "de": "Stellar-Header — Branding/Shortcut-Zeile + Navigation/Actions-Zeile.",
        "es": "Header Stellar — fila branding/atajos + fila navegación/acciones.",
        "it": "Header Stellar — riga branding/shortcut + riga navigazione/azioni.",
        "lb": "Stellar-Header.", "nl": "Stellar header.", "pl": "Nagłówek Stellar.",
    },
    "Vertical offer card for homepage carousel with CTA link and actions.": {
        "fr": "Carte offre verticale pour carrousel homepage avec lien CTA et actions.",
        "de": "Vertikale Angebotskarte für Homepage-Karussell mit CTA-Link und Aktionen.",
        "es": "Tarjeta vertical de oferta para carrusel homepage con enlace CTA y acciones.",
        "it": "Scheda offerta verticale per carosello homepage con link CTA e azioni.",
        "lb": "Vertikal Offer-Kaart.", "nl": "Verticale aanbiedingskaart carousel.", "pl": "Pionowa karta oferty karuzeli.",
    },
    "Hero gallery for offer detail pages with overlays and lightbox trigger.": {
        "fr": "Galerie hero pour pages détail offre avec overlays et lightbox.",
        "de": "Hero-Galerie für Angebotsdetailseiten mit Overlays und Lightbox.",
        "es": "Galería hero para páginas detalle oferta con overlays y lightbox.",
        "it": "Galleria hero per pagine dettaglio offerta con overlay e lightbox.",
        "lb": "Hero-Galerie.", "nl": "Hero-galerie aanbiedingsdetail.", "pl": "Galeria hero strony szczegółów oferty.",
    },
}

ADMIN_LABELS: dict[str, dict[str, str]] = {
    "Background": {"fr": "Arrière-plan", "de": "Hintergrund", "es": "Fondo", "it": "Sfondo", "lb": "Hannergrond", "nl": "Achtergrond", "pl": "Tło"},
    "Background gradient": {"fr": "Dégradé d'arrière-plan", "de": "Hintergrundverlauf", "es": "Degradado de fondo", "it": "Gradiente sfondo", "lb": "Hannergrond-Gradient", "nl": "Achtergrondgradiënt", "pl": "Gradient tła"},
    "Bootstrap container class": {"fr": "Classe conteneur Bootstrap", "de": "Bootstrap-Container-Klasse", "es": "Clase contenedor Bootstrap", "it": "Classe container Bootstrap", "lb": "Bootstrap-Container-Klass", "nl": "Bootstrap-containerklasse", "pl": "Klasa kontenera Bootstrap"},
    "Bottom bar container class": {"fr": "Classe conteneur barre inférieure", "de": "Untere Leisten-Container-Klasse", "es": "Clase contenedor barra inferior", "it": "Classe container barra inferiore", "lb": "Ënnes Bar-Container-Klass", "nl": "Onderbalk containerklasse", "pl": "Klasa kontenera dolnej belki"},
    "Business menu": {"fr": "Menu business", "de": "Business-Menü", "es": "Menú business", "it": "Menu business", "lb": "Business-Menu", "nl": "Businessmenu", "pl": "Menu biznesowe"},
    "Footer menu": {"fr": "Menu pied de page", "de": "Footer-Menü", "es": "Menú pie de página", "it": "Menu footer", "lb": "Footer-Menu", "nl": "Footermenu", "pl": "Menu stopki"},
    "Header actions menu": {"fr": "Menu actions header", "de": "Header-Aktionen-Menü", "es": "Menú acciones cabecera", "it": "Menu azioni header", "lb": "Header-Aktiounen-Menu", "nl": "Headeractiesmenu", "pl": "Menu akcji nagłówka"},
    "Header favorites block": {"fr": "Bloc favoris header", "de": "Header-Favoriten-Block", "es": "Bloque favoritos cabecera", "it": "Blocco preferiti header", "lb": "Header-Favoritten-Block", "nl": "Header favorietenblok", "pl": "Blok ulubionych nagłówka"},
    "Header navigation (desktop bar)": {"fr": "Navigation header (barre desktop)", "de": "Header-Navigation (Desktop-Leiste)", "es": "Navegación cabecera (barra escritorio)", "it": "Navigazione header (barra desktop)", "lb": "Header-Navigatioun (Desktop)", "nl": "Headernavigatie (desktopbalk)", "pl": "Nawigacja nagłówka (desktop)"},
    "Header navigation (mobile panel)": {"fr": "Navigation header (panneau mobile)", "de": "Header-Navigation (Mobile-Panel)", "es": "Navegación cabecera (panel móvil)", "it": "Navigazione header (panel mobile)", "lb": "Header-Navigatioun (Mobile)", "nl": "Headernavigatie (mobiel paneel)", "pl": "Nawigacja nagłówka (mobilny panel)"},
    "Header shortcut (language switcher)": {"fr": "Raccourci header (sélecteur de langue)", "de": "Header-Shortcut (Sprachumschalter)", "es": "Atajo cabecera (selector idioma)", "it": "Shortcut header (selettore lingua)", "lb": "Header-Shortcut (Sprooch)", "nl": "Headersnelkoppeling (taalschakelaar)", "pl": "Skrót nagłówka (przełącznik języka)"},
    "Heading level": {"fr": "Niveau de titre", "de": "Überschriftenebene", "es": "Nivel de encabezado", "it": "Livello intestazione", "lb": "Iwwerschrëft-Niveau", "nl": "Kopniveau", "pl": "Poziom nagłówka"},
    "Main footer (3 columns) container class": {"fr": "Classe conteneur footer principal (3 colonnes)", "de": "Hauptfooter-Container-Klasse (3 Spalten)", "es": "Clase contenedor footer principal (3 columnas)", "it": "Classe container footer principale (3 colonne)", "lb": "Haaptfooter-Container", "nl": "Hoofdfooter containerklasse", "pl": "Klasa kontenera głównej stopki"},
    "Margin bottom": {"fr": "Marge inférieure", "de": "Abstand unten", "es": "Margen inferior", "it": "Margine inferiore", "lb": "Margin ënnen", "nl": "Marge onder", "pl": "Margines dolny"},
    "Margin end": {"fr": "Marge fin", "de": "Abstand Ende", "es": "Margen final", "it": "Margine finale", "lb": "Margin Enn", "nl": "Marge einde", "pl": "Margines końcowy"},
    "Margin start": {"fr": "Marge début", "de": "Abstand Anfang", "es": "Margen inicial", "it": "Margine iniziale", "lb": "Margin Ufank", "nl": "Marge start", "pl": "Margines początkowy"},
    "Margin top": {"fr": "Marge supérieure", "de": "Abstand oben", "es": "Margen superior", "it": "Margine superiore", "lb": "Margin uewen", "nl": "Marge boven", "pl": "Margines górny"},
    "Navigation aria-label": {"fr": "Aria-label navigation", "de": "Navigation Aria-Label", "es": "Aria-label navegación", "it": "Aria-label navigazione", "lb": "Navigatioun Aria-Label", "nl": "Navigatie aria-label", "pl": "Aria-label nawigacji"},
    "Navigation collapsible": {"fr": "Navigation repliable", "de": "Navigation einklappbar", "es": "Navegación plegable", "it": "Navigazione comprimibile", "lb": "Navigatioun zesummeklappbar", "nl": "Inklapbare navigatie", "pl": "Nawigacja zwijana"},
    "Offcanvas label": {"fr": "Libellé offcanvas", "de": "Offcanvas-Bezeichnung", "es": "Etiqueta offcanvas", "it": "Etichetta offcanvas", "lb": "Offcanvas-Bezeechnung", "nl": "Offcanvas-label", "pl": "Etykieta offcanvas"},
    "PreFooter container class": {"fr": "Classe conteneur préfooter", "de": "PreFooter-Container-Klasse", "es": "Clase contenedor prefooter", "it": "Classe container prefooter", "lb": "PreFooter-Container-Klass", "nl": "Prefooter containerklasse", "pl": "Klasa kontenera prefooter"},
    "Property Search Theme settings": {"fr": "Paramètres thème Property Search", "de": "Property Search Theme-Einstellungen", "es": "Ajustes del tema Property Search", "it": "Impostazioni tema Property Search", "lb": "Property Search Theme-Astellungen", "nl": "Property Search thema-instellingen", "pl": "Ustawienia motywu Property Search"},
    "PS overrides (legacy split build)": {"fr": "Overrides PS (build split legacy)", "de": "PS-Overrides (Legacy-Split-Build)", "es": "Overrides PS (build split legacy)", "it": "Override PS (build split legacy)", "lb": "PS-Overrides", "nl": "PS-overrides (legacy split build)", "pl": "Overrides PS (legacy split build)"},
    "Search desktop trigger": {"fr": "Trigger recherche desktop", "de": "Desktop-Such-Trigger", "es": "Trigger búsqueda escritorio", "it": "Trigger ricerca desktop", "lb": "Desktop-Sich-Trigger", "nl": "Desktop zoektrigger", "pl": "Trigger wyszukiwania desktop"},
    "Search dropdown panel": {"fr": "Panneau déroulant recherche", "de": "Such-Dropdown-Panel", "es": "Panel desplegable búsqueda", "it": "Panel dropdown ricerca", "lb": "Sich-Dropdown-Panell", "nl": "Zoek dropdownpaneel", "pl": "Panel rozwijany wyszukiwania"},
    "Search mobile trigger": {"fr": "Trigger recherche mobile", "de": "Mobile-Such-Trigger", "es": "Trigger búsqueda móvil", "it": "Trigger ricerca mobile", "lb": "Mobil-Sich-Trigger", "nl": "Mobiele zoektrigger", "pl": "Trigger wyszukiwania mobilnego"},
    "Site branding": {"fr": "Identité du site", "de": "Site-Branding", "es": "Marca del sitio", "it": "Branding sito", "lb": "Site-Branding", "nl": "Sitebranding", "pl": "Branding witryny"},
    "Spacing": {"fr": "Espacement", "de": "Abstand", "es": "Espaciado", "it": "Spaziatura", "lb": "Abstand", "nl": "Spacing", "pl": "Odstępy"},
    "Suffix such as HT/HC/m²/an.": {"fr": "Suffixe tel que HT/HC/m²/an.", "de": "Suffix wie HT/HC/m²/Jahr.", "es": "Sufijo como HT/HC/m²/año.", "it": "Suffisso come HT/HC/m²/anno.", "lb": "Suffix wéi HT/HC/m²/Joer.", "nl": "Suffix zoals HT/HC/m²/jaar.", "pl": "Sufiks jak HT/HC/m²/rok."},
    "Theme front Property Search (BNPPRE)": {"fr": "Thème front Property Search (BNPPRE)", "de": "Front-Theme Property Search (BNPPRE)", "es": "Tema front Property Search (BNPPRE)", "it": "Tema front Property Search (BNPPRE)", "lb": "Front-Theme Property Search", "nl": "Frontthema Property Search (BNPPRE)", "pl": "Motyw front Property Search (BNPPRE)"},
    "Unique section ID (accordion)": {"fr": "ID section unique (accordéon)", "de": "Eindeutige Sektions-ID (Akkordeon)", "es": "ID sección única (acordeón)", "it": "ID sezione univoco (accordion)", "lb": "Eenzeg Sektioun-ID", "nl": "Unieke sectie-ID (accordion)", "pl": "Unikalny ID sekcji (accordion)"},
    "User account menu (authenticated)": {"fr": "Menu compte utilisateur (authentifié)", "de": "Benutzerkontomenü (authentifiziert)", "es": "Menú cuenta usuario (autenticado)", "it": "Menu account utente (autenticato)", "lb": "Benotzerkont-Menu", "nl": "Gebruikersaccountmenu", "pl": "Menu konta użytkownika"},
    "Internal path (e.g. /node/1) or absolute URL.": {"fr": "Chemin interne (ex. /node/1) ou URL absolue.", "de": "Interner Pfad (z. B. /node/1) oder absolute URL.", "es": "Ruta interna (ej. /node/1) o URL absoluta.", "it": "Percorso interno (es. /node/1) o URL assoluto.", "lb": "Internen Wee oder absolut URL.", "nl": "Intern pad (bijv. /node/1) of absolute URL.", "pl": "Ścieżka wewnętrzna (np. /node/1) lub absolutny URL."},
    "Primary image URL fallback.": {"fr": "URL image principale de repli.", "de": "Primäre Bild-URL-Fallback.", "es": "URL imagen principal alternativa.", "it": "URL immagine principale fallback.", "lb": "Primär Bild-URL-Fallback.", "nl": "Primaire afbeelding-URL fallback.", "pl": "Zapasowy URL głównego obrazu."},
    "Expand small": {"fr": "Étendre petit", "de": "Klein erweitern", "es": "Expandir pequeño", "it": "Espandi piccolo", "lb": "Kleng erweideren", "nl": "Klein uitbreiden", "pl": "Rozszerz mały"},
    "Expand medium": {"fr": "Étendre moyen", "de": "Mittel erweitern", "es": "Expandir mediano", "it": "Espandi medio", "lb": "Mëttel erweideren", "nl": "Middel uitbreiden", "pl": "Rozszerz średni"},
    "Expand large": {"fr": "Étendre grand", "de": "Groß erweitern", "es": "Expandir grande", "it": "Espandi grande", "lb": "Grouss erweideren", "nl": "Groot uitbreiden", "pl": "Rozszerz duży"},
    "Expand extra large": {"fr": "Étendre très grand", "de": "Extra groß erweitern", "es": "Expandir extra grande", "it": "Espandi extra large", "lb": "Extra grouss erweideren", "nl": "Extra groot uitbreiden", "pl": "Rozszerz extra large"},
    "Expand extra extra large": {"fr": "Étendre extra extra grand", "de": "Extra extra groß erweitern", "es": "Expandir extra extra grande", "it": "Espandi extra extra large", "lb": "Extra extra grouss", "nl": "Extra extra groot uitbreiden", "pl": "Rozszerz extra extra large"},
    "Add a linear gradient as background image to the backgrounds. This gradient starts with a semi-transparent white which fades out to the bottom.": {
        "fr": "Ajouter un dégradé linéaire comme image d'arrière-plan. Ce dégradé commence par un blanc semi-transparent qui s'estompe vers le bas.",
        "de": "Linearen Verlauf als Hintergrundbild hinzufügen, von semi-transparentem Weiß nach unten verblassend.",
        "es": "Añadir degradado lineal como imagen de fondo, de blanco semitransparente que se desvanece hacia abajo.",
        "it": "Aggiungi gradiente lineare come sfondo, da bianco semi-trasparente che sfuma in basso.",
        "lb": "Lineare Gradient als Hannergrondbild.", "nl": "Lineair gradiënt als achtergrondafbeelding.", "pl": "Dodaj gradient liniowy jako tło.",
    },
}


def load_other_catalogs() -> dict[str, dict[str, dict[str, str]]]:
    catalogs: dict[str, dict[str, dict[str, str]]] = {}
    for mod in OTHER_MODULES:
        path = CATALOG_DIR / f"{mod}.json"
        if path.exists():
            catalogs[mod] = json.loads(path.read_text(encoding="utf-8"))
    return catalogs


def merge_patch(catalog: dict, msgid: str, patch: dict[str, str]) -> None:
    if msgid not in catalog:
        catalog[msgid] = {}
    for lang, text in patch.items():
        catalog[msgid][lang] = text


def fill_from_other(catalog: dict, others: dict[str, dict]) -> None:
    for msgid, entry in catalog.items():
        for other in others.values():
            if msgid not in other:
                continue
            for lang in LANGS:
                if lang not in entry and lang in other[msgid]:
                    entry[lang] = other[msgid][lang]


def ensure_all_langs(catalog: dict) -> None:
    for msgid, entry in catalog.items():
        if not entry.get("fr"):
            entry["fr"] = msgid
        for lang in LANGS:
            if lang not in entry:
                entry[lang] = entry.get("es") or entry.get("fr") or msgid


def main() -> None:
    path = CATALOG_DIR / "ps_theme.json"
    catalog = json.loads(path.read_text(encoding="utf-8"))
    others = load_other_catalogs()

    for msgid, patch in UI_PATCHES.items():
        merge_patch(catalog, msgid, patch)
    for msgid, patch in SDC_FR_TO_LANG.items():
        merge_patch(catalog, msgid, patch)
    for msgid, patch in ADMIN_LABELS.items():
        merge_patch(catalog, msgid, patch)

    fill_from_other(catalog, others)
    ensure_all_langs(catalog)

    path.write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding="utf-8")
    incomplete = sum(1 for k, v in catalog.items() if any(v.get(l) == k for l in LANGS))
    print(f"ps_theme catalog: {len(catalog)} entries, still EN-equals: {incomplete}")


if __name__ == "__main__":
    main()
