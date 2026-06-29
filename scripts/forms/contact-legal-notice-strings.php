<?php

declare(strict_types=1);

/**
 * Contact wizard legal notices — aligned with bnppre.fr / realestate.bnpparibas.fr.
 *
 * @see https://www.realestate.bnpparibas.fr/fr/nous-contacter
 */

/**
 * Builds the legal footer markup (title + 2 paragraphs).
 *
 * @param array<string, string> $strings
 *   Language-specific legal string set.
 * @param 'transaction'|'advisory' $variant
 *   Entity variant.
 */
function ps_form_build_contact_legal_notice(array $strings, string $variant): string {
  $entityFull = $strings['entity_full_' . $variant];
  $entityName = $strings['entity_name_' . $variant];

  $paragraphOne = str_replace(
    ['{entity_full}', '{entity_name}'],
    [$entityFull, $entityName],
    $strings['paragraph_one'],
  );
  $paragraphTwo = str_replace(
    ['{entity_full}', '{entity_name}', '{privacy_url}', '{privacy_link_label}'],
    [$entityFull, $entityName, $strings['privacy_url'], $strings['privacy_link_label']],
    $strings['paragraph_two'],
  );

  return '<p class="ps-form-legal-notice__title"><strong>'
    . $strings['heading']
    . '</strong></p><p class="ps-form-legal-notice">'
    . $paragraphOne
    . '</p><p class="ps-form-legal-notice">'
    . $paragraphTwo
    . '</p>';
}

/**
 * @return array<string, array<string, string>>
 *   Legal strings keyed by language code.
 */
function ps_form_contact_legal_notice_strings(): array {
  return [
    'fr' => [
      'heading' => 'Mentions sur la protection des données personnelles',
      'paragraph_one' => 'Il vous est possible de vous opposer à recevoir de la prospection commerciale par voie téléphonique en vous inscrivant gratuitement sur la liste d\'opposition au démarchage téléphonique sur le site internet <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> ou par courrier postal à Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes. Cette inscription interdit à un professionnel de vous démarcher téléphoniquement, sauf si cette sollicitation intervient dans le cadre de l\'exécution d\'un contrat en cours et qu\'elle a un rapport avec l\'objet de ce contrat. {entity_full}, en tant que responsable de traitement, traite des informations vous concernant pour répondre à votre demande transmise via ce formulaire et, le cas échéant, à des fins de prospection commerciale (si vous avez donné votre accord ou que la législation applicable nous y autorise).',
      'paragraph_two' => 'Pour plus d\'informations concernant la façon dont {entity_name} traite vos données personnelles dans ce contexte ainsi que sur vos droits (notamment vos droits d\'accès, de rectification, d\'opposition au traitement à des fins de prospection et votre droit de donner des directives relatives à l\'utilisation de vos données à caractère personnel après votre décès ainsi que, dans certaines circonstances, un droit à l\'effacement, à la limitation du traitement, à la portabilité des données et un droit de s\'opposer à d\'autres formes de traitement) et les obligations de {entity_name} à cet égard, nous vous invitons à consulter notre <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, société anonyme située 50, cours de l&rsquo;Île Seguin, 92100 Boulogne-Billancourt',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, société par actions simplifiée située 50, cours de l&rsquo;Île Seguin, 92100 Boulogne-Billancourt',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'notice Protection des données',
    ],
    'en' => [
      'heading' => 'Personal data protection information',
      'paragraph_one' => 'You may object to receiving commercial prospecting by telephone by registering free of charge on the telemarketing opposition list at <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> or by post to Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, France. This registration prohibits a professional from contacting you by telephone, except where the call relates to an ongoing contract. {entity_full}, as data controller, processes information about you to respond to your request submitted via this form and, where applicable, for commercial prospecting purposes (if you have given your consent or applicable law allows us to do so).',
      'paragraph_two' => 'For more information on how {entity_name} processes your personal data in this context and on your rights (including access, rectification, objection to processing for prospecting purposes, and the right to give instructions regarding your personal data after your death, as well as, in certain circumstances, erasure, restriction of processing, data portability and objection to other forms of processing) and {entity_name}\'s obligations in this regard, please see our <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, a public limited company located at 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, France',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, a simplified joint stock company located at 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, France',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Data Protection Notice',
    ],
    'de' => [
      'heading' => 'Hinweise zum Schutz personenbezogener Daten',
      'paragraph_one' => 'Sie können dem Erhalt werblicher Telefonanrufe widersprechen, indem Sie sich kostenlos auf der Widerspruchsliste unter <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> registrieren oder per Post an Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Frankreich, wenden. Dieses Register untersagt es Unternehmen, Sie telefonisch zu kontaktieren, es sei denn, der Anruf steht im Zusammenhang mit einem laufenden Vertrag. {entity_full} verarbeitet als Verantwortlicher Informationen über Sie, um auf Ihre über dieses Formular übermittelte Anfrage zu antworten und gegebenenfalls zu werblichen Zwecken (sofern Sie eingewilligt haben oder dies gesetzlich zulässig ist).',
      'paragraph_two' => 'Weitere Informationen darüber, wie {entity_name} Ihre personenbezogenen Daten in diesem Zusammenhang verarbeitet, sowie zu Ihren Rechten (einschließlich Auskunft, Berichtigung, Widerspruch gegen die Verarbeitung zu Werbezwecken und das Recht, Anweisungen zur Verwendung Ihrer personenbezogenen Daten nach Ihrem Tod zu erteilen, sowie unter bestimmten Umständen Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit und Widerspruch gegen andere Formen der Verarbeitung) und zu den Pflichten von {entity_name} finden Sie in unserer <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, eine Aktiengesellschaft mit Sitz in 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankreich',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, eine vereinfachte Aktiengesellschaft mit Sitz in 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankreich',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Datenschutzhinweis',
    ],
    'es' => [
      'heading' => 'Información sobre la protección de datos personales',
      'paragraph_one' => 'Puede oponerse a recibir prospección comercial por teléfono inscribiéndose gratuitamente en la lista de oposición al telemarketing en <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> o por correo postal a Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Francia. Esta inscripción prohíbe que un profesional le contacte telefónicamente, salvo si la llamada está relacionada con un contrato en curso. {entity_full}, como responsable del tratamiento, trata la información que le concierne para responder a su solicitud enviada a través de este formulario y, en su caso, con fines de prospección comercial (si ha dado su consentimiento o la legislación aplicable nos lo permite).',
      'paragraph_two' => 'Para más información sobre cómo {entity_name} trata sus datos personales en este contexto y sobre sus derechos (incluidos los derechos de acceso, rectificación, oposición al tratamiento con fines de prospección y el derecho a dar instrucciones relativas al uso de sus datos personales después de su fallecimiento, así como, en determinadas circunstancias, supresión, limitación del tratamiento, portabilidad de los datos y oposición a otras formas de tratamiento) y las obligaciones de {entity_name} al respecto, consulte nuestra <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, sociedad anónima con domicilio en 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francia',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, sociedad por acciones simplificada con domicilio en 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francia',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Aviso de protección de datos',
    ],
    'it' => [
      'heading' => 'Informazioni sulla protezione dei dati personali',
      'paragraph_one' => 'Può opporsi a ricevere prospezioni commerciali per telefono iscrivendosi gratuitamente all\'elenco di opposizione al telemarketing su <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> o per posta a Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Francia. Questa iscrizione vieta a un professionista di contattarLa telefonicamente, salvo se la chiamata riguarda un contratto in corso. {entity_full}, in qualità di titolare del trattamento, tratta le informazioni che La riguardano per rispondere alla Sua richiesta inviata tramite questo modulo e, ove applicabile, per finalità di prospezione commerciale (se ha prestato il consenso o la legge applicabile ce lo consente).',
      'paragraph_two' => 'Per maggiori informazioni su come {entity_name} tratta i Suoi dati personali in questo contesto e sui Suoi diritti (inclusi accesso, rettifica, opposizione al trattamento per finalità di prospezione e diritto di fornire istruzioni relative all\'utilizzo dei Suoi dati personali dopo la Sua morte, nonché, in determinate circostanze, cancellazione, limitazione del trattamento, portabilità dei dati e opposizione ad altre forme di trattamento) e sugli obblighi di {entity_name} al riguardo, consulti la nostra <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, società per azioni con sede in 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francia',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, società per azioni semplificata con sede in 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francia',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Informativa sulla protezione dei dati',
    ],
    'nl' => [
      'heading' => 'Informatie over de bescherming van persoonsgegevens',
      'paragraph_one' => 'U kunt zich verzetten tegen commerciële telefonische prospectie door zich kosteloos in te schrijven op de oppositionlijst via <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> of per post aan Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Frankrijk. Deze inschrijving verbiedt een professional u telefonisch te benaderen, behalve wanneer het gesprek betrekking heeft op een lopend contract. {entity_full} verwerkt als verwerkingsverantwoordelijke informatie over u om te reageren op uw via dit formulier ingediende aanvraag en, indien van toepassing, voor commerciële prospectiedoeleinden (indien u toestemming heeft gegeven of de toepasselijke wetgeving dit toestaat).',
      'paragraph_two' => 'Voor meer informatie over de wijze waarop {entity_name} uw persoonsgegevens in deze context verwerkt en over uw rechten (met inbegrip van inzage, rectificatie, bezwaar tegen verwerking voor prospectiedoeleinden en het recht om instructies te geven over het gebruik van uw persoonsgegevens na uw overlijden, alsmede, onder bepaalde omstandigheden, wissing, beperking van de verwerking, gegevensoverdraagbaarheid en bezwaar tegen andere vormen van verwerking) en de verplichtingen van {entity_name} in dit verband, raadpleeg onze <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, een naamloze vennootschap gevestigd aan 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankrijk',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, een vereenvoudigde naamloze vennootschap gevestigd aan 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankrijk',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Kennisgeving gegevensbescherming',
    ],
    'pl' => [
      'heading' => 'Informacje o ochronie danych osobowych',
      'paragraph_one' => 'Może Pan/Pani sprzeciwić się otrzymywaniu telefonicznych ofert handlowych, rejestrując się bezpłatnie na liście sprzeciwu na stronie <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> lub listownie na adres Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Francja. Rejestracja ta zabrania profesjonalistom kontaktowania się telefonicznie, chyba że rozmowa dotyczy trwającej umowy. {entity_full}, jako administrator danych, przetwarza informacje dotyczące Pana/Pani w celu udzielenia odpowiedzi na zapytanie przesłane za pośrednictwem tego formularza oraz, w stosownych przypadkach, w celach prospectingowych (jeśli wyraził/a Pan/Pani zgodę lub pozwala na to obowiązujące prawo).',
      'paragraph_two' => 'Więcej informacji o tym, w jaki sposób {entity_name} przetwarza Pana/Pani dane osobowe w tym kontekście, oraz o Pana/Pani prawach (w tym prawie dostępu, sprostowania, sprzeciwu wobec przetwarzania w celach prospectingowych i prawie do wydania dyspozycji dotyczących wykorzystania danych osobowych po śmierci, a także — w określonych okolicznościach — prawie do usunięcia, ograniczenia przetwarzania, przenoszenia danych i sprzeciwu wobec innych form przetwarzania) oraz obowiązkach {entity_name} w tym zakresie znajduje się w naszym <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, spółka akcyjna z siedzibą przy 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francja',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, spółka z ograniczoną odpowiedzialnością uproszczona z siedzibą przy 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Francja',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Komunikat o ochronie danych',
    ],
    'lb' => [
      'heading' => 'Mentionen iwwer de Schutz vu perséinlechen Donnéeën',
      'paragraph_one' => 'Dir kënnt Iech géint kommerziell Telefonsufroen wierderloen, andeems Dir Iech gratis op der Oppositiounslëscht op <a href="https://www.bloctel.gouv.fr">www.bloctel.gouv.fr</a> registréiert oder per Post un Société Opposetel, Service Bloctel, 6 rue Nicolas Siret, 10000 Troyes, Frankräich schreift. Dës Aschreiwung verbitt engem Professionell Iech telefonesch ze kontaktéieren, ausser wann den Uruff mat engem lafende Kontrakt ze dinn huet. {entity_full}, als verantwortlech fir d\'Traitement, veraarbecht Informatiounen iwwer Iech fir op Är Demande iwwer dëse Formulaire ze reagéieren an, wou et ubruecht ass, fir kommerziell Prospection (wann Dir Är Zoustëmmung ginn hutt oder d\'uwendbar Gesetzgebung et erlaabt).',
      'paragraph_two' => 'Fir méi Informatiounen iwwer d\'Art a Weis, wéi {entity_name} Är perséinlech Donnéeën an dësem Kontext veraarbecht, an iwwer Är Rechter (dont Zougang, Berichtigung, Wierderloen géint d\'Veraarbechtung fir Prospection an d\'Recht, Uweisungen iwwer d\'Benotzung vun Ären Donnéeën no Ärem Doud ze ginn, an och — a bestëmmte Fäll — d\'Recht op Läschen, d\'Aschränkung vun der Veraarbechtung, d\'Portabilitéit an d\'Wierderloen géint aner Forme vun der Veraarbechtung) an d\'Verpflichtunge vun {entity_name} an dësem Zesummenhang, kuckt w.e.g. eis <a href="{privacy_url}">{privacy_link_label}</a>.',
      'entity_full_transaction' => 'BNP Paribas Real Estate Transaction France, eng Aktiengesellschaft mat Sëtz op 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankräich',
      'entity_full_advisory' => 'BNP Paribas Real Estate Advisory France, eng vereinfacht Aktiengesellschaft mat Sëtz op 50, cours de l&rsquo;Ile Seguin, 92100 Boulogne-Billancourt, Frankräich',
      'entity_name_transaction' => 'BNP Paribas Real Estate Transaction France',
      'entity_name_advisory' => 'BNP Paribas Real Estate Advisory France',
      'privacy_url' => 'https://data-privacy.realestate.bnpparibas/',
      'privacy_link_label' => 'Notice de protection des données',
    ],
  ];
}
