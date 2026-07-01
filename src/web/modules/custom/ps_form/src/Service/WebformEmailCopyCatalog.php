<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\ps_offer_webform\Service\OfferContactSnapshotFields;

/**
 * Canonical copy for webform email confirmation handlers.
 *
 * Source of truth for install YAML generation via sync-webform-email-copy.php.
 */
final class WebformEmailCopyCatalog {

  public const HUB_WEBFORM_IDS = [
    'find_property',
    'entrust_search',
    'get_advice',
    'entrust_property',
    'invest_sell',
    'other_request',
  ];

  public const OFFER_WEBFORM_IDS = [
    'offer_contact',
    'schedule_visit',
  ];

  public const LANGUAGE_CODES = [
    'en',
    'de',
    'es',
    'fr',
    'it',
    'lb',
    'nl',
    'pl',
  ];

  /**
   * Shared offer email strings per language (snapshot + agent intro).
   *
   * @var array<string, array<string, string>>
   */
  private const OFFER_SHARED = [
    'en' => [
      'snapshot_intro' => 'The property details below were recorded at the time of the request:',
      'agent_intro' => 'You have received a new contact request. The offer snapshot and visitor details are below.',
    ],
    'fr' => [
      'snapshot_intro' => 'Les caractéristiques de l\'offre ci-dessous ont été enregistrées au moment de la demande :',
      'agent_intro' => 'Vous avez reçu une nouvelle demande. L\'instantané de l\'offre et les coordonnées du visiteur figurent ci-dessous.',
    ],
    'de' => [
      'snapshot_intro' => 'Die untenstehenden Objektdaten wurden zum Zeitpunkt der Anfrage erfasst:',
      'agent_intro' => 'Sie haben eine neue Kontaktanfrage erhalten. Objekt-Snapshot und Besucherdaten finden Sie unten.',
    ],
    'es' => [
      'snapshot_intro' => 'Los datos del inmueble que figuran a continuación se registraron en el momento de la solicitud:',
      'agent_intro' => 'Ha recibido una nueva solicitud de contacto. A continuación, la instantánea del inmueble y los datos del visitante.',
    ],
    'it' => [
      'snapshot_intro' => 'I dettagli dell\'immobile riportati di seguito sono stati registrati al momento della richiesta:',
      'agent_intro' => 'Ha ricevuto una nuova richiesta di contatto. Di seguito lo snapshot dell\'offerta e i dati del visitatore.',
    ],
    'nl' => [
      'snapshot_intro' => 'De onderstaande objectgegevens zijn vastgelegd op het moment van de aanvraag:',
      'agent_intro' => 'U hebt een nieuw contactverzoek ontvangen. Hieronder de snapshot van het aanbod en de gegevens van de bezoeker.',
    ],
    'pl' => [
      'snapshot_intro' => 'Poniższe dane nieruchomości zostały zapisane w momencie złożenia zapytania:',
      'agent_intro' => 'Otrzymali Państwo nowe zapytanie kontaktowe. Poniżej snapshot oferty i dane odwiedzającego.',
    ],
    'lb' => [
      'snapshot_intro' => 'D\'Objektdetailer hei drënner goufen zum Zäitpunkt vun der Demande erfasst:',
      'agent_intro' => 'Dir hutt eng nei Kontaktufro kritt. Hei drënner de Snapshot vun der Offer an d\'Donnéeë vum Visiteur.',
    ],
  ];

  /**
   * Shared strings per language (hub webforms).
   *
   * @var array<string, array<string, string>>
   */
  private const HUB_SHARED = [
    'en' => [
      'subject' => 'Thank you for contacting us',
      'greeting' => 'Hello [webform_submission:values:firstname:raw],',
      'recap_intro' => 'For reference, your request is as follows:',
      'closing' => 'Thank you for your interest in BNP Paribas Real Estate.',
      'signoff' => 'Best regards,<br>Your Customer Service team, BNP Paribas Real Estate.',
    ],
    'fr' => [
      'subject' => 'Merci de nous avoir contactés',
      'greeting' => 'Bonjour [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Pour référence, voici le récapitulatif de votre demande :',
      'closing' => 'Merci de l\'intérêt que vous portez à BNP Paribas Real Estate.',
      'signoff' => 'Cordialement,<br>Votre équipe Service Client, BNP Paribas Real Estate.',
    ],
    'de' => [
      'subject' => 'Vielen Dank für Ihre Kontaktaufnahme',
      'greeting' => 'Guten Tag [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Zur Referenz finden Sie Ihre Anfrage im Folgenden:',
      'closing' => 'Vielen Dank für Ihr Interesse an BNP Paribas Real Estate.',
      'signoff' => 'Mit freundlichen Grüßen,<br>Ihr Kundenservice-Team, BNP Paribas Real Estate.',
    ],
    'es' => [
      'subject' => 'Gracias por contactarnos',
      'greeting' => 'Hola [webform_submission:values:firstname:raw],',
      'recap_intro' => 'A título informativo, su solicitud es la siguiente:',
      'closing' => 'Gracias por su interés en BNP Paribas Real Estate.',
      'signoff' => 'Atentamente,<br>Su equipo de Atención al Cliente, BNP Paribas Real Estate.',
    ],
    'it' => [
      'subject' => 'Grazie per averci contattato',
      'greeting' => 'Gentile [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Di seguito il riepilogo della sua richiesta:',
      'closing' => 'La ringraziamo per l\'interesse verso BNP Paribas Real Estate.',
      'signoff' => 'Cordiali saluti,<br>Il team Servizio Clienti, BNP Paribas Real Estate.',
    ],
    'nl' => [
      'subject' => 'Bedankt voor uw contact',
      'greeting' => 'Beste [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Ter referentie vindt u hieronder uw aanvraag:',
      'closing' => 'Bedankt voor uw interesse in BNP Paribas Real Estate.',
      'signoff' => 'Met vriendelijke groet,<br>Uw klantenserviceteam, BNP Paribas Real Estate.',
    ],
    'pl' => [
      'subject' => 'Dziękujemy za kontakt',
      'greeting' => 'Dzień dobry [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Poniżej przedstawiamy podsumowanie Państwa zapytania:',
      'closing' => 'Dziękujemy za zainteresowanie BNP Paribas Real Estate.',
      'signoff' => 'Z poważaniem,<br>Zespół Obsługi Klienta, BNP Paribas Real Estate.',
    ],
    'lb' => [
      'subject' => 'Merci fir Äre Kontakt',
      'greeting' => 'Moien [webform_submission:values:firstname:raw],',
      'recap_intro' => 'Fir Är Informatioun, hei d\'Recapitulatioun vun Ärer Demande:',
      'closing' => 'Merci fir Ären Interessi un BNP Paribas Real Estate.',
      'signoff' => 'Mat beschte Gréiss,<br>Äert Clientsservice-Team, BNP Paribas Real Estate.',
    ],
  ];

  /**
   * Contextual hub titles and intros per webform and language.
   *
   * @var array<string, array<string, array{title: string, intro: string}>>
   */
  private const HUB_CONTEXT = [
    'find_property' => [
      'en' => [
        'title' => 'Your property search request has been sent',
        'intro' => 'We have received your search criteria. One of our consultants will contact you to help you find the right property.',
      ],
      'fr' => [
        'title' => 'Votre demande de recherche de bien a été envoyée',
        'intro' => 'Nous avons bien reçu vos critères de recherche. Un de nos consultants vous contactera pour vous aider à trouver le bien adapté à votre projet.',
      ],
      'de' => [
        'title' => 'Ihre Immobiliensuche wurde übermittelt',
        'intro' => 'Wir haben Ihre Suchkriterien erhalten. Einer unserer Berater wird sich mit Ihnen in Verbindung setzen, um Ihnen bei der Suche nach der passenden Immobilie zu helfen.',
      ],
      'es' => [
        'title' => 'Su solicitud de búsqueda de inmueble ha sido enviada',
        'intro' => 'Hemos recibido sus criterios de búsqueda. Uno de nuestros consultores se pondrá en contacto con usted para ayudarle a encontrar el inmueble adecuado.',
      ],
      'it' => [
        'title' => 'La sua richiesta di ricerca immobiliare è stata inviata',
        'intro' => 'Abbiamo ricevuto i suoi criteri di ricerca. Un nostro consulente la contatterà per aiutarla a trovare l\'immobile più adatto.',
      ],
      'nl' => [
        'title' => 'Uw zoekopdracht is verzonden',
        'intro' => 'Wij hebben uw zoekcriteria ontvangen. Een van onze adviseurs neemt contact met u op om u te helpen het juiste pand te vinden.',
      ],
      'pl' => [
        'title' => 'Państwa zapytanie dotyczące wyszukiwania nieruchomości zostało wysłane',
        'intro' => 'Otrzymaliśmy Państwa kryteria wyszukiwania. Jeden z naszych doradców skontaktuje się z Państwem, aby pomóc znaleźć odpowiednią nieruchomość.',
      ],
      'lb' => [
        'title' => 'Är Demande no Immobiliesich ass geschéckt ginn',
        'intro' => 'Mir hunn Är Sichkriterien kritt. Ee vun eise Beroder kontaktéiert Iech fir Iech beim Fannen vun der passender Immobilie ze hëllefen.',
      ],
    ],
    'entrust_search' => [
      'en' => [
        'title' => 'Your search brief has been sent',
        'intro' => 'We have received your brief. Our team will review your requirements and get back to you shortly.',
      ],
      'fr' => [
        'title' => 'Votre demande de recherche confiée a été envoyée',
        'intro' => 'Nous avons bien reçu votre cahier des charges. Notre équipe étudiera vos besoins et reviendra vers vous rapidement.',
      ],
      'de' => [
        'title' => 'Ihr Suchauftrag wurde übermittelt',
        'intro' => 'Wir haben Ihren Suchauftrag erhalten. Unser Team prüft Ihre Anforderungen und meldet sich in Kürze bei Ihnen.',
      ],
      'es' => [
        'title' => 'Su encargo de búsqueda ha sido enviado',
        'intro' => 'Hemos recibido su encargo de búsqueda. Nuestro equipo revisará sus necesidades y se pondrá en contacto con usted en breve.',
      ],
      'it' => [
        'title' => 'Il suo incarico di ricerca è stato inviato',
        'intro' => 'Abbiamo ricevuto il suo incarico di ricerca. Il nostro team esaminerà le sue esigenze e la contatterà a breve.',
      ],
      'nl' => [
        'title' => 'Uw zoekopdracht is verzonden',
        'intro' => 'Wij hebben uw zoekopdracht ontvangen. Ons team bekijkt uw wensen en neemt spoedig contact met u op.',
      ],
      'pl' => [
        'title' => 'Państwa zlecenie poszukiwania zostało wysłane',
        'intro' => 'Otrzymaliśmy Państwa zlecenie poszukiwania. Nasz zespół przeanalizuje Państwa wymagania i wkrótce się z Państwem skontaktuje.',
      ],
      'lb' => [
        'title' => 'Är uvertraut Sich ass geschéckt ginn',
        'intro' => 'Mir hunn Är Sich kritt. Eist Team iwwerpréift Är Ufuerderungen a kontaktéiert Iech geschwënn.',
      ],
    ],
    'get_advice' => [
      'en' => [
        'title' => 'Your advisory request has been sent',
        'intro' => 'We have received your request for advice. A consultant will contact you to discuss your needs.',
      ],
      'fr' => [
        'title' => 'Votre demande de conseil a été envoyée',
        'intro' => 'Nous avons bien reçu votre demande de conseil. Un consultant vous contactera pour échanger sur vos besoins.',
      ],
      'de' => [
        'title' => 'Ihre Beratungsanfrage wurde übermittelt',
        'intro' => 'Wir haben Ihre Beratungsanfrage erhalten. Ein Berater wird sich mit Ihnen in Verbindung setzen, um Ihre Bedürfnisse zu besprechen.',
      ],
      'es' => [
        'title' => 'Su solicitud de asesoramiento ha sido enviada',
        'intro' => 'Hemos recibido su solicitud de asesoramiento. Un consultor se pondrá en contacto con usted para hablar de sus necesidades.',
      ],
      'it' => [
        'title' => 'La sua richiesta di consulenza è stata inviata',
        'intro' => 'Abbiamo ricevuto la sua richiesta di consulenza. Un consulente la contatterà per discutere le sue esigenze.',
      ],
      'nl' => [
        'title' => 'Uw adviesaanvraag is verzonden',
        'intro' => 'Wij hebben uw adviesaanvraag ontvangen. Een adviseur neemt contact met u op om uw behoeften te bespreken.',
      ],
      'pl' => [
        'title' => 'Państwa zapytanie o doradztwo zostało wysłane',
        'intro' => 'Otrzymaliśmy Państwa zapytanie o doradztwo. Doradca skontaktuje się z Państwem, aby omówić Państwa potrzeby.',
      ],
      'lb' => [
        'title' => 'Är Berodungsufro ass geschéckt ginn',
        'intro' => 'Mir hunn Är Berodungsufro kritt. E Beroder kontaktéiert Iech fir iwwer Är Besoinen ze schwätzen.',
      ],
    ],
    'entrust_property' => [
      'en' => [
        'title' => 'Your property details have been sent',
        'intro' => 'We have received the details of your property. Our team will review them and get back to you shortly.',
      ],
      'fr' => [
        'title' => 'Les informations sur votre bien ont été envoyées',
        'intro' => 'Nous avons bien reçu les informations relatives à votre bien. Notre équipe les étudiera et reviendra vers vous rapidement.',
      ],
      'de' => [
        'title' => 'Ihre Immobiliendaten wurden übermittelt',
        'intro' => 'Wir haben die Angaben zu Ihrer Immobilie erhalten. Unser Team prüft diese und meldet sich in Kürze bei Ihnen.',
      ],
      'es' => [
        'title' => 'Los datos de su inmueble han sido enviados',
        'intro' => 'Hemos recibido la información de su inmueble. Nuestro equipo la revisará y se pondrá en contacto con usted en breve.',
      ],
      'it' => [
        'title' => 'I dati del suo immobile sono stati inviati',
        'intro' => 'Abbiamo ricevuto le informazioni sul suo immobile. Il nostro team le esaminerà e la contatterà a breve.',
      ],
      'nl' => [
        'title' => 'Uw pandgegevens zijn verzonden',
        'intro' => 'Wij hebben de gegevens van uw pand ontvangen. Ons team bekijkt deze en neemt spoedig contact met u op.',
      ],
      'pl' => [
        'title' => 'Dane Państwa nieruchomości zostały wysłane',
        'intro' => 'Otrzymaliśmy informacje o Państwa nieruchomości. Nasz zespół je przeanalizuje i wkrótce się z Państwem skontaktuje.',
      ],
      'lb' => [
        'title' => 'Är Immobiliendetailer sinn geschéckt ginn',
        'intro' => 'Mir hunn d\'Informatiounen iwwer Är Immobilie kritt. Eist Team iwwerpréift se a kontaktéiert Iech geschwënn.',
      ],
    ],
    'invest_sell' => [
      'en' => [
        'title' => 'Your investment request has been sent',
        'intro' => 'We have received your investment or sale project. A consultant will contact you to discuss the next steps.',
      ],
      'fr' => [
        'title' => 'Votre demande d\'investissement a été envoyée',
        'intro' => 'Nous avons bien reçu votre projet d\'investissement ou de cession. Un consultant vous contactera pour en discuter.',
      ],
      'de' => [
        'title' => 'Ihre Investitionsanfrage wurde übermittelt',
        'intro' => 'Wir haben Ihr Investitions- oder Verkaufsprojekt erhalten. Ein Berater wird sich mit Ihnen in Verbindung setzen, um die nächsten Schritte zu besprechen.',
      ],
      'es' => [
        'title' => 'Su solicitud de inversión ha sido enviada',
        'intro' => 'Hemos recibido su proyecto de inversión o venta. Un consultor se pondrá en contacto con usted para hablar de los próximos pasos.',
      ],
      'it' => [
        'title' => 'La sua richiesta di investimento è stata inviata',
        'intro' => 'Abbiamo ricevuto il suo progetto di investimento o vendita. Un consulente la contatterà per discutere i prossimi passi.',
      ],
      'nl' => [
        'title' => 'Uw investeringsaanvraag is verzonden',
        'intro' => 'Wij hebben uw investerings- of verkoopproject ontvangen. Een adviseur neemt contact met u op om de volgende stappen te bespreken.',
      ],
      'pl' => [
        'title' => 'Państwa zapytanie inwestycyjne zostało wysłane',
        'intro' => 'Otrzymaliśmy Państwa projekt inwestycyjny lub sprzedażowy. Doradca skontaktuje się z Państwem, aby omówić kolejne kroki.',
      ],
      'lb' => [
        'title' => 'Är Investitiounsufro ass geschéckt ginn',
        'intro' => 'Mir hunn Äert Investitiouns- oder Verkafprojet kritt. E Beroder kontaktéiert Iech fir déi nächst Schrëtt ze beschwätzen.',
      ],
    ],
    'other_request' => [
      'en' => [
        'title' => 'Your request has been sent',
        'intro' => 'We have received your message and will get back to you as soon as possible.',
      ],
      'fr' => [
        'title' => 'Votre demande a été envoyée',
        'intro' => 'Nous avons bien reçu votre message et reviendrons vers vous dans les meilleurs délais.',
      ],
      'de' => [
        'title' => 'Ihre Anfrage wurde übermittelt',
        'intro' => 'Wir haben Ihre Nachricht erhalten und melden uns so schnell wie möglich bei Ihnen.',
      ],
      'es' => [
        'title' => 'Su solicitud ha sido enviada',
        'intro' => 'Hemos recibido su mensaje y nos pondremos en contacto con usted lo antes posible.',
      ],
      'it' => [
        'title' => 'La sua richiesta è stata inviata',
        'intro' => 'Abbiamo ricevuto il suo messaggio e la contatteremo il prima possibile.',
      ],
      'nl' => [
        'title' => 'Uw aanvraag is verzonden',
        'intro' => 'Wij hebben uw bericht ontvangen en nemen zo snel mogelijk contact met u op.',
      ],
      'pl' => [
        'title' => 'Państwa zapytanie zostało wysłane',
        'intro' => 'Otrzymaliśmy Państwa wiadomość i skontaktujemy się z Państwem tak szybko, jak to możliwe.',
      ],
      'lb' => [
        'title' => 'Är Demande ass geschéckt ginn',
        'intro' => 'Mir hunn Äre Message kritt a kommen esou séier wéi méiglech op Iech zréck.',
      ],
    ],
  ];

  /**
   * Offer webform confirmation copy per language.
   *
   * @var array<string, array<string, array<string, string>>>
   */
  private const OFFER_COPY = [
    'offer_contact' => [
      'en' => [
        'subject' => 'Your contact request — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Hello [webform_submission:values:first_name:raw],',
        'intro' => 'Thank you for your interest in offer <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Your message has been forwarded to our consultant, who will get back to you shortly.',
        'recap_intro' => 'For reference, your request is as follows:',
        'closing' => 'Thank you for your interest in BNP Paribas Real Estate.',
        'signoff' => 'Best regards,<br>Your Customer Service team, BNP Paribas Real Estate.',
      ],
      'fr' => [
        'subject' => 'Votre demande de contact — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Bonjour [webform_submission:values:first_name:raw],',
        'intro' => 'Merci de l\'intérêt que vous portez à l\'offre <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Votre message a été transmis à notre consultant, qui reviendra vers vous rapidement.',
        'recap_intro' => 'Pour référence, voici le récapitulatif de votre demande :',
        'closing' => 'Merci de l\'intérêt que vous portez à BNP Paribas Real Estate.',
        'signoff' => 'Cordialement,<br>Votre équipe Service Client, BNP Paribas Real Estate.',
      ],
      'de' => [
        'subject' => 'Ihre Kontaktanfrage — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Guten Tag [webform_submission:values:first_name:raw],',
        'intro' => 'Vielen Dank für Ihr Interesse am Angebot <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Ihre Nachricht wurde an unseren Berater weitergeleitet, der sich in Kürze bei Ihnen melden wird.',
        'recap_intro' => 'Zur Referenz finden Sie Ihre Anfrage im Folgenden:',
        'closing' => 'Vielen Dank für Ihr Interesse an BNP Paribas Real Estate.',
        'signoff' => 'Mit freundlichen Grüßen,<br>Ihr Kundenservice-Team, BNP Paribas Real Estate.',
      ],
      'es' => [
        'subject' => 'Su solicitud de contacto — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Hola [webform_submission:values:first_name:raw],',
        'intro' => 'Gracias por su interés en la oferta <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Su mensaje ha sido remitido a nuestro consultor, que se pondrá en contacto con usted en breve.',
        'recap_intro' => 'A título informativo, su solicitud es la siguiente:',
        'closing' => 'Gracias por su interés en BNP Paribas Real Estate.',
        'signoff' => 'Atentamente,<br>Su equipo de Atención al Cliente, BNP Paribas Real Estate.',
      ],
      'it' => [
        'subject' => 'La sua richiesta di contatto — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Gentile [webform_submission:values:first_name:raw],',
        'intro' => 'La ringraziamo per l\'interesse verso l\'offerta <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Il suo messaggio è stato inoltrato al nostro consulente, che la contatterà a breve.',
        'recap_intro' => 'Di seguito il riepilogo della sua richiesta:',
        'closing' => 'La ringraziamo per l\'interesse verso BNP Paribas Real Estate.',
        'signoff' => 'Cordiali saluti,<br>Il team Servizio Clienti, BNP Paribas Real Estate.',
      ],
      'nl' => [
        'subject' => 'Uw contactverzoek — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Beste [webform_submission:values:first_name:raw],',
        'intro' => 'Bedankt voor uw interesse in het aanbod <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Uw bericht is doorgestuurd naar onze adviseur, die spoedig contact met u opneemt.',
        'recap_intro' => 'Ter referentie vindt u hieronder uw aanvraag:',
        'closing' => 'Bedankt voor uw interesse in BNP Paribas Real Estate.',
        'signoff' => 'Met vriendelijke groet,<br>Uw klantenserviceteam, BNP Paribas Real Estate.',
      ],
      'pl' => [
        'subject' => 'Państwa zapytanie kontaktowe — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Dzień dobry [webform_submission:values:first_name:raw],',
        'intro' => 'Dziękujemy za zainteresowanie ofertą <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Państwa wiadomość została przekazana naszemu doradcy, który wkrótce się z Państwem skontaktuje.',
        'recap_intro' => 'Poniżej przedstawiamy podsumowanie Państwa zapytania:',
        'closing' => 'Dziękujemy za zainteresowanie BNP Paribas Real Estate.',
        'signoff' => 'Z poważaniem,<br>Zespół Obsługi Klienta, BNP Paribas Real Estate.',
      ],
      'lb' => [
        'subject' => 'Är Kontaktufro — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Moien [webform_submission:values:first_name:raw],',
        'intro' => 'Merci fir Ären Interessi un der Offer <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Är Noriicht gouf un eise Beroder weidergeleet, deen Iech geschwënn kontaktéiert.',
        'recap_intro' => 'Fir Är Informatioun, hei d\'Recapitulatioun vun Ärer Demande:',
        'closing' => 'Merci fir Ären Interessi un BNP Paribas Real Estate.',
        'signoff' => 'Mat beschte Gréiss,<br>Äert Clientsservice-Team, BNP Paribas Real Estate.',
      ],
    ],
    'schedule_visit' => [
      'en' => [
        'subject' => 'Your visit request — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Hello [webform_submission:values:first_name:raw],',
        'intro' => 'Thank you for your visit request regarding offer <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Our consultant will contact you shortly to arrange a visit.',
        'recap_intro' => 'For reference, your request is as follows:',
        'closing' => 'Thank you for your interest in BNP Paribas Real Estate.',
        'signoff' => 'Best regards,<br>Your Customer Service team, BNP Paribas Real Estate.',
      ],
      'fr' => [
        'subject' => 'Votre demande de visite — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Bonjour [webform_submission:values:first_name:raw],',
        'intro' => 'Merci pour votre demande de visite concernant l\'offre <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Notre consultant vous contactera rapidement pour organiser la visite.',
        'recap_intro' => 'Pour référence, voici le récapitulatif de votre demande :',
        'closing' => 'Merci de l\'intérêt que vous portez à BNP Paribas Real Estate.',
        'signoff' => 'Cordialement,<br>Votre équipe Service Client, BNP Paribas Real Estate.',
      ],
      'de' => [
        'subject' => 'Ihre Besichtigungsanfrage — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Guten Tag [webform_submission:values:first_name:raw],',
        'intro' => 'Vielen Dank für Ihre Besichtigungsanfrage zum Angebot <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Unser Berater wird Sie in Kürze kontaktieren, um einen Termin zu vereinbaren.',
        'recap_intro' => 'Zur Referenz finden Sie Ihre Anfrage im Folgenden:',
        'closing' => 'Vielen Dank für Ihr Interesse an BNP Paribas Real Estate.',
        'signoff' => 'Mit freundlichen Grüßen,<br>Ihr Kundenservice-Team, BNP Paribas Real Estate.',
      ],
      'es' => [
        'subject' => 'Su solicitud de visita — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Hola [webform_submission:values:first_name:raw],',
        'intro' => 'Gracias por su solicitud de visita relativa a la oferta <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Nuestro consultor se pondrá en contacto con usted en breve para organizar la visita.',
        'recap_intro' => 'A título informativo, su solicitud es la siguiente:',
        'closing' => 'Gracias por su interés en BNP Paribas Real Estate.',
        'signoff' => 'Atentamente,<br>Su equipo de Atención al Cliente, BNP Paribas Real Estate.',
      ],
      'it' => [
        'subject' => 'La tua richiesta visita — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Gentile [webform_submission:values:first_name:raw],',
        'intro' => 'Grazie per la sua richiesta di visita relativa all\'offerta <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Il nostro consulente la contatterà a breve per organizzare la visita.',
        'recap_intro' => 'Di seguito il riepilogo della sua richiesta:',
        'closing' => 'La ringraziamo per l\'interesse verso BNP Paribas Real Estate.',
        'signoff' => 'Cordiali saluti,<br>Il team Servizio Clienti, BNP Paribas Real Estate.',
      ],
      'nl' => [
        'subject' => 'Uw bezoekaanvraag — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Beste [webform_submission:values:first_name:raw],',
        'intro' => 'Bedankt voor uw bezoekaanvraag over het aanbod <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Onze adviseur neemt spoedig contact met u op om het bezoek te plannen.',
        'recap_intro' => 'Ter referentie vindt u hieronder uw aanvraag:',
        'closing' => 'Bedankt voor uw interesse in BNP Paribas Real Estate.',
        'signoff' => 'Met vriendelijke groet,<br>Uw klantenserviceteam, BNP Paribas Real Estate.',
      ],
      'pl' => [
        'subject' => 'Państwa prośba o wizytę — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Dzień dobry [webform_submission:values:first_name:raw],',
        'intro' => 'Dziękujemy za prośbę o wizytę dotyczącą oferty <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Nasz doradca wkrótce skontaktuje się z Państwem, aby ustalić termin wizyty.',
        'recap_intro' => 'Poniżej przedstawiamy podsumowanie Państwa zapytania:',
        'closing' => 'Dziękujemy za zainteresowanie BNP Paribas Real Estate.',
        'signoff' => 'Z poważaniem,<br>Zespół Obsługi Klienta, BNP Paribas Real Estate.',
      ],
      'lb' => [
        'subject' => 'Är Ufro fir eng Visitt — [webform_submission:values:offer_reference:raw]',
        'greeting' => 'Moien [webform_submission:values:first_name:raw],',
        'intro' => 'Merci fir Är Ufro fir eng Visitt betreffend d\'Offer <strong>[webform_submission:values:offer_reference:raw]</strong>.',
        'follow_up' => 'Eise Beroder kontaktéiert Iech geschwënn fir de Rendez-vous ze organiséieren.',
        'recap_intro' => 'Fir Är Informatioun, hei d\'Recapitulatioun vun Ärer Demande:',
        'closing' => 'Merci fir Ären Interessi un BNP Paribas Real Estate.',
        'signoff' => 'Mat beschte Gréiss,<br>Äert Clientsservice-Team, BNP Paribas Real Estate.',
      ],
    ],
  ];

  /**
   * Hub site notification email subjects per webform and language.
   *
   * @var array<string, array<string, string>>
   */
  private const HUB_NOTIFICATION_SUBJECTS = [
    'find_property' => [
      'en' => 'Find property request',
      'de' => 'Anfrage: Immobilie finden',
      'es' => 'Solicitud de búsqueda de inmueble',
      'fr' => 'Demande de recherche de bien',
      'it' => 'Richiesta di ricerca immobile',
      'lb' => 'Demande: Immobilie fannen',
      'nl' => 'Aanvraag pand zoeken',
      'pl' => 'Zapytanie: znalezienie nieruchomości',
    ],
    'entrust_search' => [
      'en' => 'Entrust search request',
      'de' => 'Anfrage: Suche beauftragen',
      'es' => 'Solicitud de búsqueda encargada',
      'fr' => 'Demande de recherche confiée',
      'it' => 'Richiesta di ricerca affidata',
      'lb' => 'Demande: Sich uvertrauen',
      'nl' => 'Aanvraag zoekopdracht toevertrouwen',
      'pl' => 'Zapytanie: powierzenie poszukiwań',
    ],
    'get_advice' => [
      'en' => 'Consulting request',
      'de' => 'Beratungsanfrage',
      'es' => 'Solicitud de asesoramiento',
      'fr' => 'Demande de conseil',
      'it' => 'Richiesta di consulenza',
      'lb' => 'Berodungsdemande',
      'nl' => 'Adviesaanvraag',
      'pl' => 'Zapytanie o doradztwo',
    ],
    'entrust_property' => [
      'en' => 'Entrust property request',
      'de' => 'Anfrage: Immobilie anvertrauen',
      'es' => 'Solicitud de encargo de inmueble',
      'fr' => 'Demande de confiance de bien',
      'it' => 'Richiesta di immobile affidato',
      'lb' => 'Demande: Immobilie uvertrauen',
      'nl' => 'Aanvraag pand toevertrouwen',
      'pl' => 'Zapytanie: powierzenie nieruchomości',
    ],
    'invest_sell' => [
      'en' => 'Invest or sell request',
      'de' => 'Anfrage: Investieren oder verkaufen',
      'es' => 'Solicitud de inversión o venta',
      'fr' => "Demande d'investissement ou de vente",
      'it' => 'Richiesta di investimento o vendita',
      'lb' => 'Demande: Investitioun oder Verkaf',
      'nl' => 'Aanvraag investeren of verkopen',
      'pl' => 'Zapytanie: inwestycja lub sprzedaż',
    ],
    'other_request' => [
      'en' => 'Other contact request',
      'de' => 'Sonstige Kontaktanfrage',
      'es' => 'Otra solicitud de contacto',
      'fr' => 'Autre demande de contact',
      'it' => 'Altra richiesta di contatto',
      'lb' => 'Anere Kontaktdemande',
      'nl' => 'Overige contactaanvraag',
      'pl' => 'Inne zapytanie kontaktowe',
    ],
  ];

  /**
   * Offer agent notification subjects per webform and language.
   *
   * @var array<string, array<string, string>>
   */
  private const OFFER_AGENT_SUBJECTS = [
    'offer_contact' => [
      'en' => 'Contact request — Offer [webform_submission:values:offer_reference:raw]',
      'de' => 'Kontaktanfrage — Angebot [webform_submission:values:offer_reference:raw]',
      'es' => 'Solicitud de contacto — Oferta [webform_submission:values:offer_reference:raw]',
      'fr' => 'Demande de contact — Offre [webform_submission:values:offer_reference:raw]',
      'it' => 'Richiesta di contatto — Offerta [webform_submission:values:offer_reference:raw]',
      'lb' => 'Kontaktufro — Offer [webform_submission:values:offer_reference:raw]',
      'nl' => 'Contactverzoek — Aanbod [webform_submission:values:offer_reference:raw]',
      'pl' => 'Zapytanie kontaktowe — Oferta [webform_submission:values:offer_reference:raw]',
    ],
    'schedule_visit' => [
      'en' => 'Visit request — Offer [webform_submission:values:offer_reference:raw]',
      'de' => 'Besichtigungsanfrage — Angebot [webform_submission:values:offer_reference:raw]',
      'es' => 'Solicitud de visita — Oferta [webform_submission:values:offer_reference:raw]',
      'fr' => 'Demande de visite — Offre [webform_submission:values:offer_reference:raw]',
      'it' => 'Richiesta visita — Offerta [webform_submission:values:offer_reference:raw]',
      'lb' => 'Ufro fir eng Visitt — Offer [webform_submission:values:offer_reference:raw]',
      'nl' => 'Bezoekaanvraag — Aanbod [webform_submission:values:offer_reference:raw]',
      'pl' => 'Prośba o wizytę — Oferta [webform_submission:values:offer_reference:raw]',
    ],
  ];

  /**
   * Handler labels for language overrides.
   *
   * @var array<string, array<string, array<string, string>>>
   */
  private const HANDLER_LABELS = [
    'hub' => [
      'email_notification' => [
        'en' => 'Site notification',
        'fr' => 'Notification du site',
        'de' => 'Website-Benachrichtigung',
        'es' => 'Notificación del sitio',
        'it' => 'Notifica del sito',
        'nl' => 'Sitewaarschuwing',
        'pl' => 'Powiadomienie witryny',
        'lb' => 'Notifikatioun vum Site',
      ],
      'email_confirmation' => [
        'en' => 'Visitor confirmation',
        'fr' => 'Confirmation visiteur',
        'de' => 'Besucherbestätigung',
        'es' => 'Confirmación al visitante',
        'it' => 'Conferma visitatore',
        'nl' => 'Bevestiging bezoeker',
        'pl' => 'Potwierdzenie dla odwiedzającego',
        'lb' => 'Confirmatioun fir de Visiteur',
      ],
    ],
    'offer' => [
      'email_agent' => [
        'en' => 'Agent notification',
        'fr' => 'Notification consultant',
        'de' => 'Maklerbenachrichtigung',
        'es' => 'Notificación al consultor',
        'it' => 'Notifica consulente',
        'nl' => 'Adviseur notificatie',
        'pl' => 'Powiadomienie doradcy',
        'lb' => 'Beroder-Benoriichtigung',
      ],
      'email_confirmation' => [
        'en' => 'Visitor confirmation',
        'fr' => 'Confirmation visiteur',
        'de' => 'Besucherbestätigung',
        'es' => 'Confirmación al visitante',
        'it' => 'Conferma visitatore',
        'nl' => 'Bevestiging bezoeker',
        'pl' => 'Potwierdzenie dla odwiedzającego',
        'lb' => 'Besicherbestätegung',
      ],
    ],
  ];

  /**
   * Returns hidden offer snapshot keys excluded from visitor email recap.
   *
   * @return array<string, string>
   */
  public static function getOfferSnapshotExcludedElements(): array {
    return OfferContactSnapshotFields::excludedElements();
  }

  /**
   * Returns hub confirmation subject for a webform and language.
   */
  public static function getHubConfirmationSubject(string $langcode): string {
    return self::HUB_SHARED[$langcode]['subject'] ?? self::HUB_SHARED['en']['subject'];
  }

  /**
   * Returns hub confirmation HTML body.
   */
  public static function buildHubConfirmationBody(string $webformId, string $langcode): string {
    $shared = self::HUB_SHARED[$langcode] ?? self::HUB_SHARED['en'];
    $context = self::HUB_CONTEXT[$webformId][$langcode]
      ?? self::HUB_CONTEXT[$webformId]['en']
      ?? self::HUB_CONTEXT['other_request']['en'];

    return self::renderHubBody(
      $context['title'],
      $shared['greeting'],
      $context['intro'],
      $shared['recap_intro'],
      $shared['closing'],
      $shared['signoff'],
    );
  }

  /**
   * Returns offer confirmation subject.
   */
  public static function getOfferConfirmationSubject(string $webformId, string $langcode): string {
    return self::OFFER_COPY[$webformId][$langcode]['subject']
      ?? self::OFFER_COPY[$webformId]['en']['subject'];
  }

  /**
   * Returns offer confirmation HTML body.
   */
  public static function buildOfferConfirmationBody(string $webformId, string $langcode): string {
    $copy = self::getOfferCopy($webformId, $langcode);

    return self::renderOfferVisitorBody(
      $copy['greeting'],
      $copy['intro'],
      $copy['snapshot_intro'],
      $copy['follow_up'],
      $copy['recap_intro'],
      $copy['closing'],
      $copy['signoff'],
    );
  }

  /**
   * Returns offer agent notification HTML body (same offer + visitor data).
   */
  public static function buildOfferAgentBody(string $webformId, string $langcode): string {
    $copy = self::getOfferCopy($webformId, $langcode);

    return self::renderOfferAgentBody(
      $copy['agent_intro'],
      $copy['intro'],
      $copy['snapshot_intro'],
      $copy['follow_up'],
      $copy['recap_intro'],
      $copy['closing'],
      $copy['signoff'],
    );
  }

  /**
   * @return array<string, string>
   */
  private static function getOfferCopy(string $webformId, string $langcode): array {
    $shared = self::OFFER_SHARED[$langcode] ?? self::OFFER_SHARED['en'];
    $copy = self::OFFER_COPY[$webformId][$langcode]
      ?? self::OFFER_COPY[$webformId]['en'];

    return $copy + $shared;
  }

  /**
   * Returns the H1 title for a hub webform (used in tests).
   */
  public static function getHubConfirmationTitle(string $webformId, string $langcode = 'en'): string {
    $context = self::HUB_CONTEXT[$webformId][$langcode]
      ?? self::HUB_CONTEXT[$webformId]['en']
      ?? self::HUB_CONTEXT['other_request']['en'];
    return $context['title'];
  }

  /**
   * Returns hub notification subject for a webform and language.
   */
  public static function getHubNotificationSubject(string $webformId, string $langcode): string {
    return self::HUB_NOTIFICATION_SUBJECTS[$webformId][$langcode]
      ?? self::HUB_NOTIFICATION_SUBJECTS[$webformId]['en']
      ?? 'Contact request';
  }

  /**
   * Returns offer agent notification subject.
   */
  public static function getOfferAgentSubject(string $webformId, string $langcode): string {
    return self::OFFER_AGENT_SUBJECTS[$webformId][$langcode]
      ?? self::OFFER_AGENT_SUBJECTS[$webformId]['en'];
  }

  /**
   * Returns handler label for language override files.
   */
  public static function getHandlerLabel(string $type, string $handlerId, string $langcode): string {
    return self::HANDLER_LABELS[$type][$handlerId][$langcode]
      ?? self::HANDLER_LABELS[$type][$handlerId]['en'];
  }

  /**
   * Returns confirmation handler label for language override files.
   */
  public static function getConfirmationHandlerLabel(string $type, string $langcode): string {
    return self::getHandlerLabel($type, 'email_confirmation', $langcode);
  }

  /**
   * Builds hub confirmation HTML.
   */
  private static function renderHubBody(
    string $title,
    string $greeting,
    string $intro,
    string $recapIntro,
    string $closing,
    string $signoff,
  ): string {
    $p = 'margin:0 0 16px;font-size:14px;line-height:1.6;color:#333333;';
    $h1 = 'margin:0 0 24px;font-size:22px;font-weight:700;line-height:1.3;color:#333333;text-align:center;';
    $recap = 'margin:0 0 12px;font-size:14px;line-height:1.6;color:#333333;font-weight:700;';

    return <<<HTML
<h1 style="{$h1}">{$title}</h1>
<p style="{$p}">{$greeting}</p>
<p style="{$p}">{$intro}</p>
<p style="{$recap}">{$recapIntro}</p>
[webform_submission:values]
<p style="{$p}">{$closing}</p>
<p style="margin:0;font-size:14px;line-height:1.6;color:#333333;">{$signoff}</p>
HTML;
  }

  /**
   * Builds offer visitor confirmation HTML (no H1 — shell uses subject).
   */
  private static function renderOfferVisitorBody(
    string $greeting,
    string $intro,
    string $snapshotIntro,
    string $followUp,
    string $recapIntro,
    string $closing,
    string $signoff,
  ): string {
    $p = 'margin:0 0 16px;font-size:14px;line-height:1.6;color:#333333;';
    $shared = self::renderOfferSharedContent($snapshotIntro, $followUp, $recapIntro, $p);

    return <<<HTML
<p style="{$p}">{$greeting}</p>
<p style="{$p}">{$intro}</p>
{$shared}
<p style="{$p}">{$closing}</p>
<p style="margin:0;font-size:14px;line-height:1.6;color:#333333;">{$signoff}</p>
HTML;
  }

  /**
   * Builds offer agent notification HTML with the same offer and visitor blocks.
   */
  private static function renderOfferAgentBody(
    string $agentIntro,
    string $intro,
    string $snapshotIntro,
    string $followUp,
    string $recapIntro,
    string $closing,
    string $signoff,
  ): string {
    $p = 'margin:0 0 16px;font-size:14px;line-height:1.6;color:#333333;';
    $shared = self::renderOfferSharedContent($snapshotIntro, $followUp, $recapIntro, $p);

    return <<<HTML
<p style="{$p}">{$agentIntro}</p>
<p style="{$p}">{$intro}</p>
{$shared}
<p style="{$p}">{$closing}</p>
<p style="margin:0;font-size:14px;line-height:1.6;color:#333333;">{$signoff}</p>
HTML;
  }

  /**
   * Shared offer card, snapshot table and visitor recap for both email handlers.
   */
  private static function renderOfferSharedContent(
    string $snapshotIntro,
    string $followUp,
    string $recapIntro,
    string $p,
  ): string {
    $section = 'margin:0 0 12px;font-size:14px;line-height:1.6;color:#333333;font-weight:700;';
    $recap = 'margin:0 0 12px;font-size:14px;line-height:1.6;color:#333333;font-weight:700;';

    return <<<HTML
[webform_submission:ps-offer-email-block]
<p style="{$section}">{$snapshotIntro}</p>
[webform_submission:ps-offer-snapshot-recap]
<p style="{$p}">{$followUp}</p>
<p style="{$recap}">{$recapIntro}</p>
[webform_submission:values]
HTML;
  }

}
