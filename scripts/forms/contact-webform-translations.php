<?php

declare(strict_types=1);

/**
 * Contact-family webform config translations (7 langs × 6 forms).
 *
 * Consumed by scripts/forms/generate-contact-webform-translations.php.
 */

require __DIR__ . '/contact-legal-notice-strings.php';
require __DIR__ . '/contact-job-title-strings.php';

// ---------------------------------------------------------------------------
// Shared translation strings per language.
// ---------------------------------------------------------------------------

$common = [
  'fr' => [
    'category' => 'Recherche immobilière',
    'wizard_need' => 'Besoin',
    'wizard_project' => 'Projet',
    'wizard_contact' => 'Coordonnées',
    'wizard_message' => 'Message',
    'need_intro' => '<p class="h4">Pour commencer, quel est votre besoin ?</p>',
    'need_title' => 'Besoin',
    'need_rent' => 'Trouver un bien à acheter ou à louer',
    'need_delegate' => 'Confier ma recherche',
    'need_advise' => 'Être conseillé',
    'need_market' => 'Confier un bien',
    'need_sell' => 'Investir ou vendre un bien',
    'need_other' => 'Autre demande',
    'project_fieldset' => 'Type de bien et critères',
    'transaction_type' => 'Quel type de transaction ?',
    'opt_rent' => 'Location',
    'opt_buy' => 'Achat',
    'opt_sell' => 'Vente',
    'opt_either' => 'Sans préférence',
    'search_type' => 'Quel type de bien recherchez-vous ?',
    'search_type_entrust' => 'Quel type de bien confiez-vous ?',
    'search_type_generic' => 'Quel type de bien ?',
    'opt_bur' => 'Bureaux',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Entrepôt / Logistique',
    'opt_act' => 'Locaux d\'activité',
    'opt_com' => 'Commerce',
    'opt_ter' => 'Terrain',
    'search_criteria' => 'Quels sont vos critères de recherche ?',
    'search_criteria_short' => 'Quels sont vos critères ?',
    'budget' => 'Budget',
    'min_price' => 'Prix min.',
    'max_price' => 'Prix max.',
    'rent_fieldset' => 'Loyer',
    'min_rent' => 'Loyer min. (HT/HC/m²/an)',
    'max_rent' => 'Loyer max. (HT/HC/m²/an)',
    'surface' => 'Surface',
    'min_surface' => 'Surface min.',
    'max_surface' => 'Surface max.',
    'workstations' => 'Postes de travail',
    'min_post' => 'Postes min.',
    'max_post' => 'Postes max.',
    'location' => 'Localisation',
    'location_placeholder' => 'Ville, quartier, code postal, nom du bâtiment…',
    'firstname' => 'Prénom',
    'lastname' => 'Nom',
    'company' => 'Société',
    'job_title' => 'Fonction',
    'empty_job_title' => 'Sélectionnez une fonction',
    'prof_phone' => 'Téléphone professionnel',
    'prof_email' => 'E-mail professionnel',
    'optout_intro' => '<p>Je m\'oppose à recevoir des communications commerciales de BNP Paribas Real Estate Advisory France :</p>',
    'opt_email' => 'E-mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Téléphone',
    'message' => 'Message',
    'describe_project' => 'Précisez ici votre projet',
    'describe_request' => 'Décrivez votre demande',
    'submit' => 'Envoyer le message',
    'wizard_back' => 'Retour',
    'wizard_next' => 'Continuer',
    'confirmation' => '<p>Merci. Votre message a été envoyé.</p>',
    'handler_notification' => 'Notification du site',
    'handler_confirmation' => 'Confirmation visiteur',
    'email_thanks_subject' => 'Merci de nous avoir contactés',
    'email_thanks_body' => '<p>Merci pour votre message.</p><p>Nous avons bien reçu votre demande et reviendrons vers vous rapidement.</p>',
    'property_entrust' => 'Bien à confier',
    'property_postcode' => 'Code postal du bien',
    'postcode_placeholder' => 'ex. 75002',
    'total_surface' => 'Surface totale du bien (m²)',
    'invest_project' => 'Projet d\'investissement ou de vente',
    'advice_type' => 'Quel type de conseil recherchez-vous ?',
    'advice_strategy' => 'Stratégie immobilière',
    'advice_workplace' => 'Workplace et environnement de travail',
    'advice_building' => 'Bâtiment et aménagement',
    'advice_supply' => 'Supply chain',
    'other_need_title' => 'Objet de votre demande',
    'other_services' => 'Proposer des services',
    'other_application' => 'Déposer une candidature',
    'other_press' => 'Contacter le service presse',
    'other_complaint' => 'Déposer une réclamation',
    'other_account' => 'Autre (compte, connexion…)',
  ],
  'de' => [
    'category' => 'Immobiliensuche',
    'wizard_need' => 'Bedarf',
    'wizard_project' => 'Projekt',
    'wizard_contact' => 'Ihre Kontaktdaten',
    'wizard_message' => 'Nachricht',
    'need_intro' => '<p class="h4">Um zu beginnen: Was ist Ihr Anliegen?</p>',
    'need_title' => 'Bedarf',
    'need_rent' => 'Eine Immobilie zum Kauf oder zur Miete finden',
    'need_delegate' => 'Meine Suche beauftragen',
    'need_advise' => 'Beratung erhalten',
    'need_market' => 'Eine Immobilie anvertrauen',
    'need_sell' => 'Immobilie investieren oder verkaufen',
    'need_other' => 'Sonstige Anfrage',
    'project_fieldset' => 'Immobilientyp und Anforderungen',
    'transaction_type' => 'Welche Art von Transaktion?',
    'opt_rent' => 'Miete',
    'opt_buy' => 'Kauf',
    'opt_sell' => 'Verkauf',
    'opt_either' => 'Keine Präferenz',
    'search_type' => 'Welche Art von Immobilie suchen Sie?',
    'search_type_entrust' => 'Welche Art von Immobilie beauftragen Sie?',
    'search_type_generic' => 'Welche Art von Immobilie?',
    'opt_bur' => 'Büro',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Lager / Logistik',
    'opt_act' => 'Gewerbefläche',
    'opt_com' => 'Einzelhandel',
    'opt_ter' => 'Grundstück',
    'search_criteria' => 'Was sind Ihre Suchkriterien?',
    'search_criteria_short' => 'Was sind Ihre Kriterien?',
    'budget' => 'Budget',
    'min_price' => 'Mindestpreis',
    'max_price' => 'Höchstpreis',
    'rent_fieldset' => 'Miete',
    'min_rent' => 'Mindestmiete',
    'max_rent' => 'Höchstmiete',
    'surface' => 'Fläche',
    'min_surface' => 'Mindestfläche (m²)',
    'max_surface' => 'Höchstfläche (m²)',
    'workstations' => 'Arbeitsplätze',
    'min_post' => 'Min. Arbeitsplätze',
    'max_post' => 'Max. Arbeitsplätze',
    'location' => 'Standort',
    'location_placeholder' => 'Stadt, Stadtteil, Postleitzahl, Gebäudename…',
    'firstname' => 'Vorname',
    'lastname' => 'Nachname',
    'company' => 'Unternehmen',
    'job_title' => 'Position',
    'empty_job_title' => 'Position auswählen',
    'prof_phone' => 'Geschäftstelefon',
    'prof_email' => 'Geschäftliche E-Mail',
    'optout_intro' => '<p>Ich widerspreche dem Erhalt werblicher Mitteilungen von BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'E-Mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Telefon',
    'message' => 'Nachricht',
    'describe_project' => 'Beschreiben Sie Ihr Projekt',
    'describe_request' => 'Beschreiben Sie Ihre Anfrage',
    'submit' => 'Nachricht senden',
    'wizard_back' => 'Zurück',
    'wizard_next' => 'Weiter',
    'confirmation' => '<p>Vielen Dank. Ihre Nachricht wurde gesendet.</p>',
    'handler_notification' => 'Website-Benachrichtigung',
    'handler_confirmation' => 'Besucherbestätigung',
    'email_thanks_subject' => 'Vielen Dank für Ihre Kontaktaufnahme',
    'email_thanks_body' => '<p>Vielen Dank für Ihre Nachricht.</p><p>Wir haben Ihre Anfrage erhalten und werden uns in Kürze bei Ihnen melden.</p>',
    'property_entrust' => 'Anzuvertrauende Immobilie',
    'property_postcode' => 'Postleitzahl der Immobilie',
    'postcode_placeholder' => 'z. B. 75002',
    'total_surface' => 'Gesamtfläche der Immobilie (m²)',
    'invest_project' => 'Investitions- oder Verkaufsprojekt',
    'advice_type' => 'Welche Art von Beratung suchen Sie?',
    'advice_strategy' => 'Immobilienstrategie',
    'advice_workplace' => 'Workplace & Arbeitsumfeld',
    'advice_building' => 'Gebäude & Ausstattung',
    'advice_supply' => 'Supply Chain',
    'other_need_title' => 'Worum geht es bei Ihrer Anfrage?',
    'other_services' => 'Dienstleistungen anbieten',
    'other_application' => 'Bewerbung einreichen',
    'other_press' => 'Pressestelle kontaktieren',
    'other_complaint' => 'Beschwerde einreichen',
    'other_account' => 'Sonstiges (Konto, Anmeldung…)',
  ],
  'es' => [
    'category' => 'Búsqueda inmobiliaria',
    'wizard_need' => 'Necesidad',
    'wizard_project' => 'Proyecto',
    'wizard_contact' => 'Sus datos de contacto',
    'wizard_message' => 'Mensaje',
    'need_intro' => '<p class="h4">Para empezar, ¿cuál es su necesidad?</p>',
    'need_title' => 'Necesidad',
    'need_rent' => 'Encontrar un inmueble en venta o alquiler',
    'need_delegate' => 'Encargar mi búsqueda',
    'need_advise' => 'Recibir asesoramiento',
    'need_market' => 'Encargar un inmueble',
    'need_sell' => 'Invertir o vender un inmueble',
    'need_other' => 'Otra solicitud',
    'project_fieldset' => 'Tipo de inmueble y requisitos',
    'transaction_type' => '¿Qué tipo de transacción?',
    'opt_rent' => 'Alquiler',
    'opt_buy' => 'Compra',
    'opt_sell' => 'Venta',
    'opt_either' => 'Sin preferencia',
    'search_type' => '¿Qué tipo de inmueble busca?',
    'search_type_entrust' => '¿Qué tipo de inmueble confía?',
    'search_type_generic' => '¿Qué tipo de inmueble?',
    'opt_bur' => 'Oficinas',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Almacén / Logística',
    'opt_act' => 'Local comercial / actividad',
    'opt_com' => 'Comercio',
    'opt_ter' => 'Terreno',
    'search_criteria' => '¿Cuáles son sus criterios de búsqueda?',
    'search_criteria_short' => '¿Cuáles son sus criterios?',
    'budget' => 'Presupuesto',
    'min_price' => 'Precio mín.',
    'max_price' => 'Precio máx.',
    'rent_fieldset' => 'Alquiler',
    'min_rent' => 'Alquiler mín.',
    'max_rent' => 'Alquiler máx.',
    'surface' => 'Superficie',
    'min_surface' => 'Superficie mín. (m²)',
    'max_surface' => 'Superficie máx. (m²)',
    'workstations' => 'Puestos de trabajo',
    'min_post' => 'Puestos mín.',
    'max_post' => 'Puestos máx.',
    'location' => 'Ubicación',
    'location_placeholder' => 'Ciudad, barrio, código postal, nombre del edificio…',
    'firstname' => 'Nombre',
    'lastname' => 'Apellidos',
    'company' => 'Empresa',
    'job_title' => 'Cargo',
    'empty_job_title' => 'Seleccione un cargo',
    'prof_phone' => 'Teléfono profesional',
    'prof_email' => 'Correo electrónico profesional',
    'optout_intro' => '<p>Me opongo a recibir comunicaciones comerciales de BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'Correo electrónico',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Teléfono',
    'message' => 'Mensaje',
    'describe_project' => 'Describa su proyecto',
    'describe_request' => 'Describa su solicitud',
    'submit' => 'Enviar mensaje',
    'wizard_back' => 'Volver',
    'wizard_next' => 'Continuar',
    'confirmation' => '<p>Gracias. Su mensaje ha sido enviado.</p>',
    'handler_notification' => 'Notificación del sitio',
    'handler_confirmation' => 'Confirmación al visitante',
    'email_thanks_subject' => 'Gracias por contactarnos',
    'email_thanks_body' => '<p>Gracias por su mensaje.</p><p>Hemos recibido su solicitud y nos pondremos en contacto con usted en breve.</p>',
    'property_entrust' => 'Inmueble a encargar',
    'property_postcode' => 'Código postal del inmueble',
    'postcode_placeholder' => 'p. ej. 75002',
    'total_surface' => 'Superficie total del inmueble (m²)',
    'invest_project' => 'Proyecto de inversión o venta',
    'advice_type' => '¿Qué tipo de asesoramiento busca?',
    'advice_strategy' => 'Estrategia inmobiliaria',
    'advice_workplace' => 'Workplace y entorno de trabajo',
    'advice_building' => 'Edificio y adecuación',
    'advice_supply' => 'Cadena de suministro',
    'other_need_title' => '¿De qué trata su solicitud?',
    'other_services' => 'Proponer servicios',
    'other_application' => 'Enviar una candidatura',
    'other_press' => 'Contactar con prensa',
    'other_complaint' => 'Presentar una reclamación',
    'other_account' => 'Otro (cuenta, acceso…)',
  ],
  'it' => [
    'category' => 'Ricerca immobiliare',
    'wizard_need' => 'Esigenza',
    'wizard_project' => 'Progetto',
    'wizard_contact' => 'I suoi dati',
    'wizard_message' => 'Messaggio',
    'need_intro' => '<p class="h4">Per iniziare, qual è la sua esigenza?</p>',
    'need_title' => 'Esigenza',
    'need_rent' => 'Trovare un immobile in vendita o in affitto',
    'need_delegate' => 'Affidare la mia ricerca',
    'need_advise' => 'Ricevere consulenza',
    'need_market' => 'Affidare un immobile',
    'need_sell' => 'Investire o vendere un immobile',
    'need_other' => 'Altra richiesta',
    'project_fieldset' => 'Tipologia di immobile e requisiti',
    'transaction_type' => 'Che tipo di transazione?',
    'opt_rent' => 'Affitto',
    'opt_buy' => 'Acquisto',
    'opt_sell' => 'Vendita',
    'opt_either' => 'Nessuna preferenza',
    'search_type' => 'Che tipo di immobile sta cercando?',
    'search_type_entrust' => 'Che tipo di immobile affida?',
    'search_type_generic' => 'Che tipo di immobile?',
    'opt_bur' => 'Uffici',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Magazzino / Logistica',
    'opt_act' => 'Locali commerciali',
    'opt_com' => 'Retail',
    'opt_ter' => 'Terreno',
    'search_criteria' => 'Quali sono i suoi criteri di ricerca?',
    'search_criteria_short' => 'Quali sono i suoi criteri?',
    'budget' => 'Budget',
    'min_price' => 'Prezzo min.',
    'max_price' => 'Prezzo max.',
    'rent_fieldset' => 'Affitto',
    'min_rent' => 'Affitto min.',
    'max_rent' => 'Affitto max.',
    'surface' => 'Superficie',
    'min_surface' => 'Superficie min. (m²)',
    'max_surface' => 'Superficie max. (m²)',
    'workstations' => 'Postazioni di lavoro',
    'min_post' => 'Postazioni min.',
    'max_post' => 'Postazioni max.',
    'location' => 'Località',
    'location_placeholder' => 'Città, quartiere, CAP, nome dell\'edificio…',
    'firstname' => 'Nome',
    'lastname' => 'Cognome',
    'company' => 'Società',
    'job_title' => 'Qualifica',
    'empty_job_title' => 'Selezioni una qualifica',
    'prof_phone' => 'Telefono professionale',
    'prof_email' => 'E-mail professionale',
    'optout_intro' => '<p>Mi oppongo a ricevere comunicazioni commerciali da BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'E-mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Telefono',
    'message' => 'Messaggio',
    'describe_project' => 'Descriva il suo progetto',
    'describe_request' => 'Descriva la sua richiesta',
    'submit' => 'Invia messaggio',
    'wizard_back' => 'Indietro',
    'wizard_next' => 'Continua',
    'confirmation' => '<p>Grazie. Il suo messaggio è stato inviato.</p>',
    'handler_notification' => 'Notifica del sito',
    'handler_confirmation' => 'Conferma visitatore',
    'email_thanks_subject' => 'Grazie per averci contattato',
    'email_thanks_body' => '<p>Grazie per il suo messaggio.</p><p>Abbiamo ricevuto la sua richiesta e la contatteremo a breve.</p>',
    'property_entrust' => 'Immobile da affidare',
    'property_postcode' => 'CAP dell\'immobile',
    'postcode_placeholder' => 'es. 75002',
    'total_surface' => 'Superficie totale dell\'immobile (m²)',
    'invest_project' => 'Progetto di investimento o vendita',
    'advice_type' => 'Che tipo di consulenza sta cercando?',
    'advice_strategy' => 'Strategia immobiliare',
    'advice_workplace' => 'Workplace e ambiente di lavoro',
    'advice_building' => 'Edificio e fit-out',
    'advice_supply' => 'Supply chain',
    'other_need_title' => 'Oggetto della sua richiesta',
    'other_services' => 'Proporre servizi',
    'other_application' => 'Inviare una candidatura',
    'other_press' => 'Contattare l\'ufficio stampa',
    'other_complaint' => 'Presentare un reclamo',
    'other_account' => 'Altro (account, accesso…)',
  ],
  'nl' => [
    'category' => 'Vastgoedzoektocht',
    'wizard_need' => 'Behoefte',
    'wizard_project' => 'Project',
    'wizard_contact' => 'Uw gegevens',
    'wizard_message' => 'Bericht',
    'need_intro' => '<p class="h4">Om te beginnen: wat is uw behoefte?</p>',
    'need_title' => 'Behoefte',
    'need_rent' => 'Een pand vinden om te kopen of te huren',
    'need_delegate' => 'Mijn zoekopdracht toevertrouwen',
    'need_advise' => 'Advies ontvangen',
    'need_market' => 'Een pand toevertrouwen',
    'need_sell' => 'Investeren of een pand verkopen',
    'need_other' => 'Overige aanvraag',
    'project_fieldset' => 'Type pand en vereisten',
    'transaction_type' => 'Welk type transactie?',
    'opt_rent' => 'Huur',
    'opt_buy' => 'Koop',
    'opt_sell' => 'Verkoop',
    'opt_either' => 'Geen voorkeur',
    'search_type' => 'Welk type pand zoekt u?',
    'search_type_entrust' => 'Welk type pand vertrouwt u toe?',
    'search_type_generic' => 'Welk type pand?',
    'opt_bur' => 'Kantoor',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Magazijn / Logistiek',
    'opt_act' => 'Bedrijfsruimte',
    'opt_com' => 'Retail',
    'opt_ter' => 'Grond',
    'search_criteria' => 'Wat zijn uw zoekcriteria?',
    'search_criteria_short' => 'Wat zijn uw criteria?',
    'budget' => 'Budget',
    'min_price' => 'Min. prijs',
    'max_price' => 'Max. prijs',
    'rent_fieldset' => 'Huur',
    'min_rent' => 'Min. huur',
    'max_rent' => 'Max. huur',
    'surface' => 'Oppervlakte',
    'min_surface' => 'Min. oppervlakte (m²)',
    'max_surface' => 'Max. oppervlakte (m²)',
    'workstations' => 'Werkplekken',
    'min_post' => 'Min. werkplekken',
    'max_post' => 'Max. werkplekken',
    'location' => 'Locatie',
    'location_placeholder' => 'Stad, wijk, postcode, gebouwnaam…',
    'firstname' => 'Voornaam',
    'lastname' => 'Achternaam',
    'company' => 'Bedrijf',
    'job_title' => 'Functie',
    'empty_job_title' => 'Selecteer een functie',
    'prof_phone' => 'Zakelijke telefoon',
    'prof_email' => 'Zakelijke e-mail',
    'optout_intro' => '<p>Ik verzet me tegen het ontvangen van commerciële communicatie van BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'E-mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Telefoon',
    'message' => 'Bericht',
    'describe_project' => 'Beschrijf uw project',
    'describe_request' => 'Beschrijf uw aanvraag',
    'submit' => 'Bericht verzenden',
    'wizard_back' => 'Terug',
    'wizard_next' => 'Doorgaan',
    'confirmation' => '<p>Dank u. Uw bericht is verzonden.</p>',
    'handler_notification' => 'Sitewaarschuwing',
    'handler_confirmation' => 'Bevestiging bezoeker',
    'email_thanks_subject' => 'Bedankt voor uw contact',
    'email_thanks_body' => '<p>Bedankt voor uw bericht.</p><p>Wij hebben uw aanvraag ontvangen en nemen spoedig contact met u op.</p>',
    'property_entrust' => 'Toe te vertrouwen pand',
    'property_postcode' => 'Postcode van het pand',
    'postcode_placeholder' => 'bijv. 75002',
    'total_surface' => 'Totale oppervlakte van het pand (m²)',
    'invest_project' => 'Investerings- of verkoopproject',
    'advice_type' => 'Welk type advies zoekt u?',
    'advice_strategy' => 'Vastgoedstrategie',
    'advice_workplace' => 'Workplace & werkomgeving',
    'advice_building' => 'Gebouw & inrichting',
    'advice_supply' => 'Supply chain',
    'other_need_title' => 'Waar gaat uw aanvraag over?',
    'other_services' => 'Diensten voorstellen',
    'other_application' => 'Sollicitatie indienen',
    'other_press' => 'Contact opnemen met de persdienst',
    'other_complaint' => 'Klacht indienen',
    'other_account' => 'Overig (account, login…)',
  ],
  'pl' => [
    'category' => 'Wyszukiwanie nieruchomości',
    'wizard_need' => 'Potrzeba',
    'wizard_project' => 'Projekt',
    'wizard_contact' => 'Dane kontaktowe',
    'wizard_message' => 'Wiadomość',
    'need_intro' => '<p class="h4">Na początek: jakie są Państwa potrzeby?</p>',
    'need_title' => 'Potrzeba',
    'need_rent' => 'Znaleźć nieruchomość na sprzedaż lub wynajem',
    'need_delegate' => 'Powierzyć poszukiwania',
    'need_advise' => 'Uzyskać doradztwo',
    'need_market' => 'Powierzyć nieruchomość',
    'need_sell' => 'Zainwestować lub sprzedać nieruchomość',
    'need_other' => 'Inne zapytanie',
    'project_fieldset' => 'Typ nieruchomości i wymagania',
    'transaction_type' => 'Jaki rodzaj transakcji?',
    'opt_rent' => 'Wynajem',
    'opt_buy' => 'Zakup',
    'opt_sell' => 'Sprzedaż',
    'opt_either' => 'Bez preferencji',
    'search_type' => 'Jakiego typu nieruchomości Państwo szukają?',
    'search_type_entrust' => 'Jakiego typu nieruchomość powierzają Państwo?',
    'search_type_generic' => 'Jakiego typu nieruchomość?',
    'opt_bur' => 'Biura',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Magazyn / Logistyka',
    'opt_act' => 'Lokal użytkowy',
    'opt_com' => 'Handel detaliczny',
    'opt_ter' => 'Działka',
    'search_criteria' => 'Jakie są Państwa kryteria wyszukiwania?',
    'search_criteria_short' => 'Jakie są Państwa kryteria?',
    'budget' => 'Budżet',
    'min_price' => 'Cena min.',
    'max_price' => 'Cena max.',
    'rent_fieldset' => 'Czynsz',
    'min_rent' => 'Czynsz min.',
    'max_rent' => 'Czynsz max.',
    'surface' => 'Powierzchnia',
    'min_surface' => 'Powierzchnia min. (m²)',
    'max_surface' => 'Powierzchnia max. (m²)',
    'workstations' => 'Stanowiska pracy',
    'min_post' => 'Stanowiska min.',
    'max_post' => 'Stanowiska max.',
    'location' => 'Lokalizacja',
    'location_placeholder' => 'Miasto, dzielnica, kod pocztowy, nazwa budynku…',
    'firstname' => 'Imię',
    'lastname' => 'Nazwisko',
    'company' => 'Firma',
    'job_title' => 'Stanowisko',
    'empty_job_title' => 'Wybierz stanowisko',
    'prof_phone' => 'Telefon służbowy',
    'prof_email' => 'Służbowy e-mail',
    'optout_intro' => '<p>Sprzeciwiam się otrzymywaniu komunikacji handlowej od BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'E-mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Telefon',
    'message' => 'Wiadomość',
    'describe_project' => 'Opisz projekt',
    'describe_request' => 'Opisz zapytanie',
    'submit' => 'Wyślij wiadomość',
    'wizard_back' => 'Wstecz',
    'wizard_next' => 'Dalej',
    'confirmation' => '<p>Dziękujemy. Państwa wiadomość została wysłana.</p>',
    'handler_notification' => 'Powiadomienie witryny',
    'handler_confirmation' => 'Potwierdzenie dla odwiedzającego',
    'email_thanks_subject' => 'Dziękujemy za kontakt',
    'email_thanks_body' => '<p>Dziękujemy za wiadomość.</p><p>Otrzymaliśmy Państwa zapytanie i wkrótce się z Państwem skontaktujemy.</p>',
    'property_entrust' => 'Nieruchomość do powierzenia',
    'property_postcode' => 'Kod pocztowy nieruchomości',
    'postcode_placeholder' => 'np. 75002',
    'total_surface' => 'Całkowita powierzchnia nieruchomości (m²)',
    'invest_project' => 'Projekt inwestycyjny lub sprzedaż',
    'advice_type' => 'Jakiego rodzaju doradztwa Państwo szukają?',
    'advice_strategy' => 'Strategia nieruchomościowa',
    'advice_workplace' => 'Workplace i środowisko pracy',
    'advice_building' => 'Budynek i wyposażenie',
    'advice_supply' => 'Supply chain',
    'other_need_title' => 'Czego dotyczy Państwa zapytanie?',
    'other_services' => 'Zaproponować usługi',
    'other_application' => 'Złożyć aplikację',
    'other_press' => 'Skontaktować się z biurem prasowym',
    'other_complaint' => 'Złożyć reklamację',
    'other_account' => 'Inne (konto, logowanie…)',
  ],
  'lb' => [
    'category' => 'Immobiliensich',
    'wizard_need' => 'Besoin',
    'wizard_project' => 'Projet',
    'wizard_contact' => 'Vir Kontaktdaten',
    'wizard_message' => 'Message',
    'need_intro' => '<p class="h4">Fir un ufzemaachen: wat ass Äre Besoin?</p>',
    'need_title' => 'Besoin',
    'need_rent' => 'Eng Immobilie fënzen zum Kaf oder zur Locatioun',
    'need_delegate' => 'Meng Sich uvertrauen',
    'need_advise' => 'Berodung kréien',
    'need_market' => 'Eng Immobilie uvertrauen',
    'need_sell' => 'An eng Immobilie investéieren oder se verkafen',
    'need_other' => 'Anere Demande',
    'project_fieldset' => 'Typ vun der Immobilie a Critèren',
    'transaction_type' => 'Wéi eng Zort Transaktioun?',
    'opt_rent' => 'Locatioun',
    'opt_buy' => 'Kaf',
    'opt_sell' => 'Verkaf',
    'opt_either' => 'Keng Preferenz',
    'search_type' => 'Wéi eng Zort Immobilie sicht Dir?',
    'search_type_entrust' => 'Wéi eng Zort Immobilie vertraut Dir un?',
    'search_type_generic' => 'Wéi eng Zort Immobilie?',
    'opt_bur' => 'Büroen',
    'opt_cow' => 'Coworking',
    'opt_ent' => 'Lager / Logistik',
    'opt_act' => 'Gewerbeflächen',
    'opt_com' => 'Commerce',
    'opt_ter' => 'Terrain',
    'search_criteria' => 'Wat sinn Är Sichcritèren?',
    'search_criteria_short' => 'Wat sinn Är Critèren?',
    'budget' => 'Budget',
    'min_price' => 'Min. Präis',
    'max_price' => 'Max. Präis',
    'rent_fieldset' => 'Locatioun',
    'min_rent' => 'Min. Locatioun',
    'max_rent' => 'Max. Locatioun',
    'surface' => 'Surface',
    'min_surface' => 'Min. Surface (m²)',
    'max_surface' => 'Max. Surface (m²)',
    'workstations' => 'Aarbechtsplazen',
    'min_post' => 'Min. Aarbechtsplazen',
    'max_post' => 'Max. Aarbechtsplazen',
    'location' => 'Standuert',
    'location_placeholder' => 'Stad, Quartier, Postcode, Gebaunumm…',
    'firstname' => 'Virnumm',
    'lastname' => 'Familljennumm',
    'company' => 'Entreprise',
    'job_title' => 'Funktioun',
    'empty_job_title' => 'Wielt eng Funktioun',
    'prof_phone' => 'Professionellt Telefon',
    'prof_email' => 'Professionell E-Mail',
    'optout_intro' => '<p>Ech widerspréch dem Empfang vu kommerziellen Kommunikatioune vu BNP Paribas Real Estate Advisory France:</p>',
    'opt_email' => 'E-Mail',
    'opt_sms' => 'SMS',
    'opt_tel' => 'Telefon',
    'message' => 'Message',
    'describe_project' => 'Beschreift Äre Projet',
    'describe_request' => 'Beschreift Är Demande',
    'submit' => 'Message schécken',
    'wizard_back' => 'Zréck',
    'wizard_next' => 'Weider',
    'confirmation' => '<p>Merci. Äre Message gouf geschéckt.</p>',
    'handler_notification' => 'Notifikatioun vum Site',
    'handler_confirmation' => 'Confirmatioun fir de Visiteur',
    'email_thanks_subject' => 'Merci fir Äre Kontakt',
    'email_thanks_body' => '<p>Merci fir Äre Message.</p><p>Mir hunn Är Demande kritt a kommen geschwënn op Iech zréck.</p>',
    'property_entrust' => 'Immobilie fir unzetraue',
    'property_postcode' => 'Postcode vun der Immobilie',
    'postcode_placeholder' => 'z. B. 75002',
    'total_surface' => 'Total Surface vun der Immobilie (m²)',
    'invest_project' => 'Investitiouns- oder Verkafsprojet',
    'advice_type' => 'Wéi eng Zort Berodung sicht Dir?',
    'advice_strategy' => 'Immobilienstrategie',
    'advice_workplace' => 'Workplace an Aarbechtsëmfeld',
    'advice_building' => 'Gebai an Amenagement',
    'advice_supply' => 'Supply chain',
    'other_need_title' => 'Woumi geet Är Demande?',
    'other_services' => 'Servicer proposéieren',
    'other_application' => 'Eng Candidature ofginn',
    'other_press' => 'D\'Presse kontaktéieren',
    'other_complaint' => 'Eng Plainte ofginn',
    'other_account' => 'Aner (Kont, Login…)',
  ],
];

// ---------------------------------------------------------------------------
// Per-form metadata (title, description, notification subject, legal type).
// ---------------------------------------------------------------------------

$formMeta = [
  'contact' => [
    'title' => [
      'fr' => 'Contactez-nous',
      'de' => 'Kontaktieren Sie uns',
      'es' => 'Contáctenos',
      'it' => 'Contattaci',
      'nl' => 'Neem contact op',
      'pl' => 'Skontaktuj się z nami',
      'lb' => 'Kontaktéiert eis',
    ],
    'description' => [
      'fr' => 'Hub contact — choix du besoin puis formulaire dédié.',
      'de' => 'Site-weites Kontaktformular für das Header-Offcanvas.',
      'es' => 'Formulario de contacto global para el offcanvas del encabezado.',
      'it' => 'Modulo di contatto globale per l\'offcanvas dell\'header.',
      'nl' => 'Sitebreed contactformulier voor de header-offcanvas.',
      'pl' => 'Ogólny formularz kontaktowy dla offcanvas nagłówka.',
      'lb' => 'Site-breet Kontaktformulaire fir den Header-Offcanvas.',
    ],
    'notification_subject' => [
      'fr' => 'Formulaire de contact du site',
      'de' => 'Kontaktformular der Website',
      'es' => 'Formulario de contacto del sitio',
      'it' => 'Modulo di contatto del sito',
      'nl' => 'Contactformulier website',
      'pl' => 'Formularz kontaktowy witryny',
      'lb' => 'Kontaktformulaire vum Site',
    ],
    'legal' => 'transaction',
    'job_required' => FALSE,
  ],
  'find_property' => [
    'title' => [
      'fr' => 'Trouver un bien à acheter ou à louer',
      'de' => 'Eine Immobilie zum Kauf oder zur Miete finden',
      'es' => 'Encontrar un inmueble en venta o alquiler',
      'it' => 'Trovare un immobile in vendita o in affitto',
      'nl' => 'Een pand te koop of te huur vinden',
      'pl' => 'Znaleźć nieruchomość na sprzedaż lub wynajem',
      'lb' => 'Eng Immobilie fënnen fir ze kafen oder ze lounen',
    ],
    'description' => [
      'fr' => 'Accès direct — recherche d\'un bien à acheter ou à louer.',
      'de' => 'Direktzugang — Suche nach einer Immobilie zum Kauf oder zur Miete.',
      'es' => 'Acceso directo — búsqueda de un inmueble en venta o alquiler.',
      'it' => 'Accesso diretto — ricerca di un immobile in vendita o in affitto.',
      'nl' => 'Directe toegang — zoeken naar een pand te koop of te huur.',
      'pl' => 'Bezpośredni dostęp — wyszukiwanie nieruchomości na sprzedaż lub wynajem.',
      'lb' => 'Direkten Zougang — Sich no enger Immobilie fir ze kafen oder ze lounen.',
    ],
    'notification_subject' => [
      'fr' => 'Demande de recherche de bien',
      'de' => 'Anfrage: Immobilie finden',
      'es' => 'Solicitud de búsqueda de inmueble',
      'it' => 'Richiesta di ricerca immobile',
      'nl' => 'Aanvraag pand zoeken',
      'pl' => 'Zapytanie: znalezienie nieruchomości',
      'lb' => 'Demande: Immobilie fannen',
    ],
    'legal' => 'transaction',
    'job_required' => FALSE,
  ],
  'entrust_search' => [
    'title' => [
      'fr' => 'Confier ma recherche',
      'de' => 'Meine Suche beauftragen',
      'es' => 'Encargar mi búsqueda',
      'it' => 'Affidare la mia ricerca',
      'nl' => 'Mijn zoekopdracht toevertrouwen',
      'pl' => 'Powierzyć poszukiwania',
      'lb' => 'Meng Sich uvertrauen',
    ],
    'description' => [
      'fr' => 'Accès direct — confiez votre recherche immobilière à BNP Paribas Real Estate.',
      'de' => 'Direktzugang — beauftragen Sie BNP Paribas Real Estate mit Ihrer Immobiliensuche.',
      'es' => 'Acceso directo — encargue su búsqueda inmobiliaria a BNP Paribas Real Estate.',
      'it' => 'Accesso diretto — affidi la sua ricerca immobiliare a BNP Paribas Real Estate.',
      'nl' => 'Directe toegang — vertrouw uw vastgoedzoektocht toe aan BNP Paribas Real Estate.',
      'pl' => 'Bezpośredni dostęp — powierz wyszukiwanie nieruchomości BNP Paribas Real Estate.',
      'lb' => 'Direkten Zougang — vertraut Är Immobiliensich BNP Paribas Real Estate un.',
    ],
    'notification_subject' => [
      'fr' => 'Demande de recherche confiée',
      'de' => 'Anfrage: Suche beauftragen',
      'es' => 'Solicitud de búsqueda encargada',
      'it' => 'Richiesta di ricerca affidata',
      'nl' => 'Aanvraag zoekopdracht toevertrouwen',
      'pl' => 'Zapytanie: powierzenie poszukiwań',
      'lb' => 'Demande: Sich uvertrauen',
    ],
    'legal' => 'transaction',
    'job_required' => TRUE,
  ],
  'get_advice' => [
    'title' => [
      'fr' => 'Être conseillé',
      'de' => 'Beratung erhalten',
      'es' => 'Recibir asesoramiento',
      'it' => 'Ricevere consulenza',
      'nl' => 'Advies ontvangen',
      'pl' => 'Uzyskać doradztwo',
      'lb' => 'Berodung kréien',
    ],
    'description' => [
      'fr' => 'Accès direct — demande de conseil immobilier.',
      'de' => 'Direktzugang — Anfrage für Immobilienberatung.',
      'es' => 'Acceso directo — solicitud de consultoría inmobiliaria.',
      'it' => 'Accesso diretto — richiesta di consulenza immobiliare.',
      'nl' => 'Directe toegang — aanvraag voor vastgoedadvies.',
      'pl' => 'Bezpośredni dostęp — zapytanie o doradztwo nieruchomościowe.',
      'lb' => 'Direkten Zougang — Demande fir Immobilienberodung.',
    ],
    'notification_subject' => [
      'fr' => 'Demande de conseil',
      'de' => 'Beratungsanfrage',
      'es' => 'Solicitud de asesoramiento',
      'it' => 'Richiesta di consulenza',
      'nl' => 'Adviesaanvraag',
      'pl' => 'Zapytanie o doradztwo',
      'lb' => 'Berodungsdemande',
    ],
    'legal' => 'advisory',
    'job_required' => TRUE,
  ],
  'entrust_property' => [
    'title' => [
      'fr' => 'Confier un bien',
      'de' => 'Eine Immobilie anvertrauen',
      'es' => 'Encargar un inmueble',
      'it' => 'Affidare un immobile',
      'nl' => 'Een pand toevertrouwen',
      'pl' => 'Powierzyć nieruchomość',
      'lb' => 'Eng Immobilie uvertrauen',
    ],
    'description' => [
      'fr' => 'Accès direct — confiez la commercialisation de votre bien à BNP Paribas Real Estate.',
      'de' => 'Direktzugang — beauftragen Sie BNP Paribas Real Estate mit der Vermarktung Ihrer Immobilie.',
      'es' => 'Acceso directo — encargue la comercialización de su inmueble a BNP Paribas Real Estate.',
      'it' => 'Accesso diretto — affidi la commercializzazione del suo immobile a BNP Paribas Real Estate.',
      'nl' => 'Directe toegang — vertrouw de commercialisatie van uw pand toe aan BNP Paribas Real Estate.',
      'pl' => 'Bezpośredni dostęp — powierz komercjalizację nieruchomości BNP Paribas Real Estate.',
      'lb' => 'Direkten Zougang — vertraut d\'Kommerzialisatioun vun Ärer Immobilie BNP Paribas Real Estate un.',
    ],
    'notification_subject' => [
      'fr' => 'Demande de confiance de bien',
      'de' => 'Anfrage: Immobilie anvertrauen',
      'es' => 'Solicitud de encargo de inmueble',
      'it' => 'Richiesta di immobile affidato',
      'nl' => 'Aanvraag pand toevertrouwen',
      'pl' => 'Zapytanie: powierzenie nieruchomości',
      'lb' => 'Demande: Immobilie uvertrauen',
    ],
    'legal' => 'transaction',
    'job_required' => TRUE,
  ],
  'invest_sell' => [
    'title' => [
      'fr' => 'Investir ou vendre un bien',
      'de' => 'Immobilie investieren oder verkaufen',
      'es' => 'Invertir o vender un inmueble',
      'it' => 'Investire o vendere un immobile',
      'nl' => 'Investeren of een pand verkopen',
      'pl' => 'Zainwestować lub sprzedać nieruchomość',
      'lb' => 'An eng Immobilie investéieren oder se verkafen',
    ],
    'description' => [
      'fr' => 'Accès direct — demande d\'investissement ou de vente immobilière.',
      'de' => 'Direktzugang — Anfrage zum Investieren oder Verkaufen von Gewerbeimmobilien.',
      'es' => 'Acceso directo — solicitud de inversión o venta de inmuebles comerciales.',
      'it' => 'Accesso diretto — richiesta di investimento o vendita immobiliare commerciale.',
      'nl' => 'Directe toegang — aanvraag voor investeren of verkopen van commercieel vastgoed.',
      'pl' => 'Bezpośredni dostęp — zapytanie o inwestycję lub sprzedaż nieruchomości komercyjnej.',
      'lb' => 'Direkten Zougang — Demande fir Investitioun oder Verkaf vu Gewerbeimmobilien.',
    ],
    'notification_subject' => [
      'fr' => 'Demande d\'investissement ou de vente',
      'de' => 'Anfrage: Investieren oder verkaufen',
      'es' => 'Solicitud de inversión o venta',
      'it' => 'Richiesta di investimento o vendita',
      'nl' => 'Aanvraag investeren of verkopen',
      'pl' => 'Zapytanie: inwestycja lub sprzedaż',
      'lb' => 'Demande: Investitioun oder Verkaf',
    ],
    'legal' => 'transaction',
    'job_required' => TRUE,
  ],
  'other_request' => [
    'title' => [
      'fr' => 'Autre demande',
      'de' => 'Sonstige Anfrage',
      'es' => 'Otra solicitud',
      'it' => 'Altra richiesta',
      'nl' => 'Overige aanvraag',
      'pl' => 'Inne zapytanie',
      'lb' => 'Anere Demande',
    ],
    'description' => [
      'fr' => 'Accès direct — demande de contact hors standard.',
      'de' => 'Direktzugang — nicht standardmäßige Kontaktanfrage.',
      'es' => 'Acceso directo — solicitud de contacto no estándar.',
      'it' => 'Accesso diretto — richiesta di contatto non standard.',
      'nl' => 'Directe toegang — niet-standaard contactaanvraag.',
      'pl' => 'Bezpośredni dostęp — niestandardowe zapytanie kontaktowe.',
      'lb' => 'Direkten Zougang — net-standard Kontaktdemande.',
    ],
    'notification_subject' => [
      'fr' => 'Autre demande de contact',
      'de' => 'Sonstige Kontaktanfrage',
      'es' => 'Otra solicitud de contacto',
      'it' => 'Altra richiesta di contatto',
      'nl' => 'Overige contactaanvraag',
      'pl' => 'Inne zapytanie kontaktowe',
      'lb' => 'Anere Kontaktdemande',
    ],
    'legal' => 'advisory',
    'job_required' => FALSE,
  ],
];

// ---------------------------------------------------------------------------
// Element YAML builders.
// ---------------------------------------------------------------------------

/**
 * Double-quote a value for safe YAML embedding.
 */
function yq(string $value): string {
  return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
}

/**
 * Quote all common strings for YAML element blocks.
 *
 * @return array<string, string>
 */
function yamlize(array $c): array {
  $y = [];
  foreach ($c as $key => $value) {
    $y[$key] = yq($value);
  }
  return $y;
}

/**
 * Shared search project fieldset (contact + entrust_search).
 */
function buildSearchProjectElements(array $c, bool $includeNeedStep = FALSE): string {
  $y = yamlize($c);
  $needStep = '';
  if ($includeNeedStep) {
    $needStep = <<<YAML
step_need:
  '#type': webform_wizard_page
  '#title': {$y['wizard_need']}
  '#attributes':
    class:
      - type_need
  need_title:
    '#type': webform_markup
    '#markup': {$y['need_intro']}
  need:
    '#type': radios
    '#title': {$y['need_title']}
    '#title_display': invisible
    '#required': true
    '#options':
      rent: {$y['need_rent']}
      delegate: {$y['need_delegate']}
      advise: {$y['need_advise']}
      market: {$y['need_market']}
      sell: {$y['need_sell']}
      other: {$y['need_other']}
    '#default_value': rent

YAML;
  }

  return $needStep . <<<YAML
step_project:
  '#type': webform_wizard_page
  '#title': {$y['wizard_project']}
  project:
    '#type': fieldset
    '#title': {$y['project_fieldset']}
    transaction_type:
      '#type': radios
      '#title': {$y['transaction_type']}
      '#required': true
      '#options':
        LOC: {$y['opt_rent']}
        VEN: {$y['opt_buy']}
        EITHER: {$y['opt_either']}
      '#default_value': LOC
    search_type:
      '#type': radios
      '#title': {$y['search_type']}
      '#required': true
      '#options':
        BUR: {$y['opt_bur']}
        COW: {$y['opt_cow']}
        ENT: {$y['opt_ent']}
        ACT: {$y['opt_act']}
        COM: {$y['opt_com']}
        TER: {$y['opt_ter']}
      '#default_value': BUR
    search_criteria:
      '#type': fieldset
      '#title': {$y['search_criteria']}
      budget:
        '#type': fieldset
        '#title': {$y['budget']}
        '#states':
          visible:
            - ':input[name="transaction_type"]':
                value: VEN
            - ':input[name="transaction_type"]':
                value: EITHER
        min_budget:
          '#type': number
          '#title': {$y['min_price']}
          '#min': 0
        max_budget:
          '#type': number
          '#title': {$y['max_price']}
          '#min': 0
      rent:
        '#type': fieldset
        '#title': {$y['rent_fieldset']}
        '#states':
          visible:
            - ':input[name="transaction_type"]':
                value: LOC
            - ':input[name="transaction_type"]':
                value: EITHER
        min_rent:
          '#type': number
          '#title': {$y['min_rent']}
          '#min': 0
        max_rent:
          '#type': number
          '#title': {$y['max_rent']}
          '#min': 0
      surface:
        '#type': fieldset
        '#title': {$y['surface']}
        '#states':
          invisible:
            - ':input[name="search_type"]':
                value: COW
        min_surface:
          '#type': number
          '#title': {$y['min_surface']}
          '#min': 20
        max_surface:
          '#type': number
          '#title': {$y['max_surface']}
          '#min': 20
      post:
        '#type': fieldset
        '#title': {$y['workstations']}
        '#states':
          visible:
            - ':input[name="search_type"]':
                value: COW
        min_post:
          '#type': number
          '#title': {$y['min_post']}
          '#min': 1
        max_post:
          '#type': number
          '#title': {$y['max_post']}
          '#min': 1
      search_territory:
        '#type': textfield
        '#title': {$y['location']}
        '#required': true
        '#placeholder': {$y['location_placeholder']}

YAML;
}

/**
 * Contact + message steps shared across most forms.
 */
function buildContactAndMessageSteps(array $c, string $legalKey, bool $jobRequired = TRUE, string $messagePlaceholder = ''): string {
  $y = yamlize($c);
  $legal = $legalKey === 'advisory' ? $y['legal_advisory'] : $y['legal_transaction'];
  $jobRequiredLine = $jobRequired ? "    '#required': true\n" : '';
  $placeholderKey = $messagePlaceholder !== '' ? yq($messagePlaceholder) : $y['describe_project'];
  $lang = $c['_lang'] ?? 'fr';
  $jobOptions = ps_form_build_contact_job_title_options_yaml($lang);

  return <<<YAML
step_contact:
  '#type': webform_wizard_page
  '#title': {$y['wizard_contact']}
  firstname:
    '#type': textfield
    '#title': {$y['firstname']}
    '#required': true
  lastname:
    '#type': textfield
    '#title': {$y['lastname']}
    '#required': true
  company_name:
    '#type': textfield
    '#title': {$y['company']}
    '#required': true
  job_title:
    '#type': select
    '#title': {$y['job_title']}
{$jobRequiredLine}    '#empty_option': {$y['empty_job_title']}
    '#options':
{$jobOptions}
  prof_phone:
    '#type': tel
    '#title': {$y['prof_phone']}
    '#required': true
  prof_email_address:
    '#type': email
    '#title': {$y['prof_email']}
    '#required': true
  optout_intro:
    '#type': webform_markup
    '#markup': {$y['optout_intro']}
  optout_email_transaction:
    '#type': checkbox
    '#title': {$y['opt_email']}
  optout_sms_transaction:
    '#type': checkbox
    '#title': {$y['opt_sms']}
  optout_tel_transaction:
    '#type': checkbox
    '#title': {$y['opt_tel']}
  captcha:
    '#type': captcha
    '#captcha_type': altcha/ALTCHA
  legal_notice:
    '#type': webform_markup
    '#markup': {$legal}
    '#wrapper_attributes':
      class:
        - ps-contact-form__legal
        - ps-form-legal-notice
step_message:
  '#type': webform_wizard_page
  '#title': {$y['wizard_message']}
  qualification_comment:
    '#type': textarea
    '#title': {$y['message']}
    '#placeholder': {$placeholderKey}
    '#maxlength': 2000

YAML;
}

/**
 * Contact step without job_title (other_request).
 */
function buildContactWithoutJobStep(array $c, string $legalKey): string {
  $y = yamlize($c);
  $legal = $legalKey === 'advisory' ? $y['legal_advisory'] : $y['legal_transaction'];

  return <<<YAML
step_contact:
  '#type': webform_wizard_page
  '#title': {$y['wizard_contact']}
  firstname:
    '#type': textfield
    '#title': {$y['firstname']}
    '#required': true
  lastname:
    '#type': textfield
    '#title': {$y['lastname']}
    '#required': true
  company_name:
    '#type': textfield
    '#title': {$y['company']}
    '#required': true
  prof_phone:
    '#type': tel
    '#title': {$y['prof_phone']}
    '#required': true
  prof_email_address:
    '#type': email
    '#title': {$y['prof_email']}
    '#required': true
  optout_intro:
    '#type': webform_markup
    '#markup': {$y['optout_intro']}
  optout_email_transaction:
    '#type': checkbox
    '#title': {$y['opt_email']}
  optout_sms_transaction:
    '#type': checkbox
    '#title': {$y['opt_sms']}
  optout_tel_transaction:
    '#type': checkbox
    '#title': {$y['opt_tel']}
  captcha:
    '#type': captcha
    '#captcha_type': altcha/ALTCHA
  legal_notice:
    '#type': webform_markup
    '#markup': {$legal}
    '#wrapper_attributes':
      class:
        - ps-contact-form__legal
        - ps-form-legal-notice
step_message:
  '#type': webform_wizard_page
  '#title': {$y['wizard_message']}
  qualification_comment:
    '#type': textarea
    '#title': {$y['message']}
    '#placeholder': {$y['describe_request']}
    '#maxlength': 2000

YAML;
}

/**
 * Hub need step only (contact router webform).
 */
function buildHubNeedStep(array $c): string {
  $y = yamlize($c);

  return <<<YAML
step_need:
  '#type': webform_wizard_page
  '#title': {$y['wizard_need']}
  '#attributes':
    class:
      - type_need
  need_title:
    '#type': webform_markup
    '#markup': {$y['need_intro']}
  need:
    '#type': radios
    '#title': {$y['need_title']}
    '#title_display': invisible
    '#required': true
    '#options':
      rent: {$y['need_rent']}
      delegate: {$y['need_delegate']}
      advise: {$y['need_advise']}
      market: {$y['need_market']}
      sell: {$y['need_sell']}
      other: {$y['need_other']}
    '#default_value': rent

YAML;
}

function buildContactElements(array $c, string $legalKey, bool $jobRequired): string {
  return buildHubNeedStep($c);
}

function buildFindPropertyElements(array $c, string $legalKey): string {
  return <<<YAML
need:
  '#type': hidden
  '#default_value': rent

YAML
    . buildSearchProjectElements($c, FALSE)
    . buildContactAndMessageSteps($c, $legalKey, FALSE);
}

function buildEntrustSearchElements(array $c, string $legalKey): string {
  return <<<YAML
need:
  '#type': hidden
  '#default_value': delegate

YAML
    . buildSearchProjectElements($c, FALSE)
    . buildContactAndMessageSteps($c, $legalKey, TRUE);
}

function buildGetAdviceElements(array $c, string $legalKey): string {
  $y = yamlize($c);

  return <<<YAML
need:
  '#type': hidden
  '#default_value': advise
step_project:
  '#type': webform_wizard_page
  '#title': {$y['wizard_project']}
  consulting_type:
    '#type': checkboxes
    '#title': {$y['advice_type']}
    '#required': true
    '#options':
      strategy: {$y['advice_strategy']}
      workplace: {$y['advice_workplace']}
      building: {$y['advice_building']}
      supply_chain: {$y['advice_supply']}

YAML
    . buildContactAndMessageSteps($c, $legalKey, TRUE);
}

function buildEntrustPropertyElements(array $c, string $legalKey): string {
  $y = yamlize($c);

  return <<<YAML
need:
  '#type': hidden
  '#default_value': market
step_project:
  '#type': webform_wizard_page
  '#title': {$y['wizard_project']}
  project:
    '#type': fieldset
    '#title': {$y['property_entrust']}
    tf_assetpostalcode:
      '#type': textfield
      '#title': {$y['property_postcode']}
      '#required': true
      '#placeholder': {$y['postcode_placeholder']}
    totale_surface:
      '#type': number
      '#title': {$y['total_surface']}
      '#required': true
      '#min': 0
    transaction_type:
      '#type': radios
      '#title': {$y['transaction_type']}
      '#required': true
      '#options':
        LOC: {$y['opt_rent']}
        VEN: {$y['opt_buy']}
      '#default_value': LOC
    search_type:
      '#type': radios
      '#title': {$y['search_type_entrust']}
      '#required': true
      '#options':
        BUR: {$y['opt_bur']}
        COW: {$y['opt_cow']}
        ENT: {$y['opt_ent']}
        ACT: {$y['opt_act']}
        COM: {$y['opt_com']}
        TER: {$y['opt_ter']}
      '#default_value': BUR
    search_territory:
      '#type': textfield
      '#title': {$y['location']}
      '#placeholder': {$y['location_placeholder']}

YAML
    . buildContactAndMessageSteps($c, $legalKey, TRUE);
}

function buildInvestSellElements(array $c, string $legalKey): string {
  $y = yamlize($c);

  return <<<YAML
need:
  '#type': hidden
  '#default_value': sell
step_project:
  '#type': webform_wizard_page
  '#title': {$y['wizard_project']}
  project:
    '#type': fieldset
    '#title': {$y['invest_project']}
    transaction_type:
      '#type': radios
      '#title': {$y['transaction_type']}
      '#required': true
      '#options':
        VEN: {$y['opt_sell']}
        LOC: {$y['opt_buy']}
        EITHER: {$y['opt_either']}
      '#default_value': VEN
    search_type:
      '#type': radios
      '#title': {$y['search_type_generic']}
      '#required': true
      '#options':
        BUR: {$y['opt_bur']}
        COW: {$y['opt_cow']}
        ENT: {$y['opt_ent']}
        ACT: {$y['opt_act']}
        COM: {$y['opt_com']}
        TER: {$y['opt_ter']}
      '#default_value': BUR
    search_criteria:
      '#type': fieldset
      '#title': {$y['search_criteria_short']}
      budget:
        '#type': fieldset
        '#title': {$y['budget']}
        '#states':
          visible:
            - ':input[name="transaction_type"]':
                value: LOC
            - ':input[name="transaction_type"]':
                value: EITHER
        min_budget:
          '#type': number
          '#title': {$y['min_price']}
          '#min': 0
        max_budget:
          '#type': number
          '#title': {$y['max_price']}
          '#min': 0
      surface:
        '#type': fieldset
        '#title': {$y['surface']}
        '#states':
          invisible:
            - ':input[name="search_type"]':
                value: COW
        min_surface:
          '#type': number
          '#title': {$y['min_surface']}
          '#min': 20
        max_surface:
          '#type': number
          '#title': {$y['max_surface']}
          '#min': 20
    totale_surface:
      '#type': number
      '#title': {$y['total_surface']}
      '#required': true
      '#min': 0
    search_territory:
      '#type': textfield
      '#title': {$y['location']}
      '#required': true
      '#placeholder': {$y['location_placeholder']}

YAML
    . buildContactAndMessageSteps($c, $legalKey, TRUE);
}

function buildOtherRequestElements(array $c, string $legalKey): string {
  $y = yamlize($c);

  return <<<YAML
need:
  '#type': hidden
  '#default_value': other
step_project:
  '#type': webform_wizard_page
  '#title': {$y['wizard_project']}
  other_need:
    '#type': checkboxes
    '#title': {$y['other_need_title']}
    '#required': true
    '#options':
      services: {$y['other_services']}
      application: {$y['other_application']}
      press: {$y['other_press']}
      complaint: {$y['other_complaint']}
      account: {$y['other_account']}

YAML
    . buildContactWithoutJobStep($c, $legalKey);
}

// ---------------------------------------------------------------------------
// Assemble per-form translation entry.
// ---------------------------------------------------------------------------

function buildFormTranslation(array $c, array $meta, string $lang, string $formId, string $elements): array {
  $translation = [
    'title' => $meta['title'][$lang],
    'description' => $meta['description'][$lang],
    'category' => $c['category'],
    'elements' => $elements,
    'settings' => [
      'form_submit_label' => $formId === 'contact' ? $c['wizard_next'] : $c['submit'],
      'wizard_prev_button_label' => $c['wizard_back'],
      'wizard_next_button_label' => $c['wizard_next'],
      'confirmation_message' => $formId === 'contact' ? '' : $c['confirmation'],
    ],
  ];

  if ($formId === 'contact') {
    return $translation;
  }

  $translation['handlers'] = [
    'email_notification' => [
      'label' => $c['handler_notification'],
      'settings' => [
        'subject' => $meta['notification_subject'][$lang],
        'body' => '_default',
      ],
    ],
    'email_confirmation' => [
      'label' => $c['handler_confirmation'],
      'settings' => [
        'subject' => $c['email_thanks_subject'],
        'body' => $c['email_thanks_body'],
      ],
    ],
  ];

  return $translation;
}

// ---------------------------------------------------------------------------
// Build and return the full dictionary.
// ---------------------------------------------------------------------------

$langs = ['fr', 'de', 'es', 'it', 'nl', 'pl', 'lb'];
$result = [];
$legalNoticeStrings = ps_form_contact_legal_notice_strings();

foreach ($langs as $lang) {
  $c = $common[$lang];
  $c['_lang'] = $lang;
  $c['legal_transaction'] = ps_form_build_contact_legal_notice($legalNoticeStrings[$lang], 'transaction');
  $c['legal_advisory'] = ps_form_build_contact_legal_notice($legalNoticeStrings[$lang], 'advisory');
  $result[$lang] = [];

  foreach ($formMeta as $formId => $meta) {
    $legal = $meta['legal'];
    $elements = match ($formId) {
      'contact' => buildContactElements($c, $legal, $meta['job_required']),
      'find_property' => buildFindPropertyElements($c, $legal),
      'entrust_search' => buildEntrustSearchElements($c, $legal),
      'get_advice' => buildGetAdviceElements($c, $legal),
      'entrust_property' => buildEntrustPropertyElements($c, $legal),
      'invest_sell' => buildInvestSellElements($c, $legal),
      'other_request' => buildOtherRequestElements($c, $legal),
      default => throw new InvalidArgumentException("Unknown form: {$formId}"),
    };

    $result[$lang][$formId] = buildFormTranslation($c, $meta, $lang, $formId, $elements);
  }
}

return $result;
