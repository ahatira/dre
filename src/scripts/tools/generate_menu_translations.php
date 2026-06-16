<?php

/**
 * @file
 * Generates menu_link_translations.yml from ps_demo menu exports.
 *
 * Usage: drush php:script scripts/tools/generate_menu_translations.php
 */

use Drupal\Component\Serialization\Yaml;

$dir = DRUPAL_ROOT . '/modules/custom/ps_demo/content/menu_link_content';
$overlay = [];

$es = menu_overlay_map_es();
$it = menu_overlay_map_it();
$nl = menu_overlay_map_nl();
$pl = menu_overlay_map_pl();

foreach (glob($dir . '/*.yml') as $file) {
  $parsed = Yaml::decode((string) file_get_contents($file));
  if (!is_array($parsed)) {
    continue;
  }
  $uuid = (string) ($parsed['_meta']['uuid'] ?? '');
  if ($uuid === '') {
    continue;
  }
  $en = (string) ($parsed['default']['title'][0]['value'] ?? '');
  $fr = (string) ($parsed['translations']['fr']['title'][0]['value'] ?? $en);

  $overlay[$uuid] = [];
  foreach (['es' => $es, 'it' => $it, 'nl' => $nl, 'pl' => $pl] as $lang => $map) {
    $title = menu_overlay_title($map, $fr, $en);
    if ($title !== '') {
      $overlay[$uuid][$lang] = ['title' => $title];
    }
  }
}

$out = DRUPAL_ROOT . '/modules/custom/ps_demo/data/menu_link_translations.yml';
$yaml = "# Demo menu link overlays (UUID → langcode → title). EN/FR from default_content export.\n";
$yaml .= Yaml::encode($overlay);
file_put_contents($out, $yaml);
echo "Written " . count($overlay) . " menu overlays to {$out}\n";

/**
 * Resolves overlay title from FR/EN source using lang map.
 */
function menu_overlay_title(array $map, string $fr, string $en): string {
  if (isset($map[$fr])) {
    return $map[$fr];
  }
  if (isset($map[$en])) {
    return $map[$en];
  }
  if ($fr !== '') {
    return $fr;
  }
  return $en;
}

/**
 * @return array<string, string>
 */
function menu_overlay_map_es(): array {
  return [
    'Trouver un bien' => 'Buscar un inmueble',
    'Find a property' => 'Buscar un inmueble',
    'Tous les biens' => 'Todos los inmuebles',
    'All properties' => 'Todos los inmuebles',
    'À propos de nous' => 'Sobre nosotros',
    'About us' => 'Sobre nosotros',
    'Nous contacter' => 'Contáctenos',
    'Contact us' => 'Contáctenos',
    'Solutions' => 'Soluciones',
    'Notre expertise' => 'Nuestra experiencia',
    'Our expertise' => 'Nuestra experiencia',
    'Actualités' => 'Últimas noticias',
    'Latest News' => 'Últimas noticias',
    'Conseil' => 'Consejo',
    'Advisory' => 'Consejo',
    'Toutes les actualités' => 'Todas las noticias',
    'All news' => 'Todas las noticias',
    'Corporate' => 'Corporativo',
    'Location' => 'Alquilar',
    'Rent' => 'Alquilar',
    'Bureaux' => 'Oficinas',
    'Offices' => 'Oficinas',
    'Logistique' => 'Logística',
    'Logistics' => 'Logística',
    'Commerce' => 'Comercio',
    'Retail' => 'Comercio',
    'Achat' => 'Comprar',
    'Buy' => 'Comprar',
    'À propos de BNP Paribas Real Estate' => 'Sobre BNP Paribas Real Estate',
    'About BNP Paribas Real Estate' => 'Sobre BNP Paribas Real Estate',
    'Notre groupe' => 'Nuestro grupo',
    'Our group' => 'Nuestro grupo',
    'Services de conseil' => 'Servicios de consultoría',
    'Advisory services' => 'Servicios de consultoría',
    'Gestion immobilière' => 'Gestión inmobiliaria',
    'Property management' => 'Gestión inmobiliaria',
    'Thématiques' => 'Temas',
    'Topics' => 'Temas',
    'Marché immobilier' => 'Noticias del mercado',
    'Market news' => 'Noticias del mercado',
    'Tendances' => 'Tendencias',
    'Trends' => 'Tendencias',
    'Coworking' => 'Coworking',
    'Tous les espaces coworking' => 'Todos los espacios coworking',
    'All coworking spaces' => 'Todos los espacios coworking',
    'Acheter un coworking' => 'Comprar coworking',
    'Buy coworking' => 'Comprar coworking',
    'Locaux d\'activité' => 'Locales de actividad',
    'Business premises' => 'Locales de actividad',
    'Terrain' => 'Terrenos',
    'Land' => 'Terrenos',
    'Louer' => 'Alquilar',
    'Acheter' => 'Comprar',
    'Principales villes' => 'Ciudades principales',
    'Top cities' => 'Ciudades principales',
    'Sites métier' => 'Sitios de negocio',
    'Business websites' => 'Sitios de negocio',
    'Nous écrire' => 'Escríbanos',
    'Email us' => 'Escríbanos',
    'Formulaire de contact' => 'Formulario de contacto',
    'Contact form' => 'Formulario de contacto',
    'Développement durable' => 'Sostenibilidad',
    'Sustainability' => 'Sostenibilidad',
    'Presse' => 'Prensa',
    'Press' => 'Prensa',
    'Carrières' => 'Carreras',
    'Careers' => 'Carreras',
    'Données personnelles' => 'Protección de datos',
    'Data protection' => 'Protección de datos',
    'Politique cookies' => 'Política de cookies',
    'Cookie policy' => 'Política de cookies',
    'Disclaimer' => 'Aviso legal',
    'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs' => 'Proveedores: BNP Paribas comprometido con sus socios y proveedores',
    'Suppliers: BNP Paribas is committed to its partners and suppliers' => 'Proveedores: BNP Paribas comprometido con sus socios y proveedores',
    'Plan du site' => 'Mapa del sitio',
    'Sitemap' => 'Mapa del sitio',
    'Réclamations Service Client' => 'Reclamaciones Servicio al Cliente',
    'Complaints Customer Service' => 'Reclamaciones Servicio al Cliente',
    'Canal de dénonciation éthique' => 'Canal de denuncias éticas',
    'Canal de denuncias éticas' => 'Canal de denuncias éticas',
    'Se connecter / S\'inscrire' => 'Iniciar sesión / Registrarse',
    'Log in / Sign up' => 'Iniciar sesión / Registrarse',
    'Que recherchez-vous ?' => '¿Qué está buscando?',
    'What are you looking for?' => '¿Qué está buscando?',
    'Research' => 'Investigación',
  ];
}

/**
 * @return array<string, string>
 */
function menu_overlay_map_it(): array {
  return [
    'Trouver un bien' => 'Trova un immobile',
    'Find a property' => 'Trova un immobile',
    'Tous les biens' => 'Tutti gli immobili',
    'All properties' => 'Tutti gli immobili',
    'À propos de nous' => 'Chi siamo',
    'About us' => 'Chi siamo',
    'Nous contacter' => 'Contattaci',
    'Contact us' => 'Contattaci',
    'Solutions' => 'Soluzioni',
    'Notre expertise' => 'La nostra expertise',
    'Our expertise' => 'La nostra expertise',
    'Actualités' => 'Ultime notizie',
    'Latest News' => 'Ultime notizie',
    'Conseil' => 'Consulenza',
    'Advisory' => 'Consulenza',
    'Toutes les actualités' => 'Tutte le notizie',
    'All news' => 'Tutte le notizie',
    'Corporate' => 'Corporate',
    'Location' => 'Affitto',
    'Rent' => 'Affitto',
    'Bureaux' => 'Uffici',
    'Offices' => 'Uffici',
    'Logistique' => 'Logistica',
    'Logistics' => 'Logistica',
    'Commerce' => 'Retail',
    'Retail' => 'Retail',
    'Achat' => 'Acquisto',
    'Buy' => 'Acquisto',
    'À propos de BNP Paribas Real Estate' => 'BNP Paribas Real Estate',
    'About BNP Paribas Real Estate' => 'BNP Paribas Real Estate',
    'Notre groupe' => 'Il nostro gruppo',
    'Our group' => 'Il nostro gruppo',
    'Services de conseil' => 'Servizi di consulenza',
    'Advisory services' => 'Servizi di consulenza',
    'Gestion immobilière' => 'Gestione immobiliare',
    'Property management' => 'Gestione immobiliare',
    'Thématiques' => 'Temi',
    'Topics' => 'Temi',
    'Marché immobilier' => 'Mercato immobiliare',
    'Market news' => 'Mercato immobiliare',
    'Tendances' => 'Tendenze',
    'Trends' => 'Tendenze',
    'Coworking' => 'Coworking',
    'Tous les espaces coworking' => 'Tutti gli spazi coworking',
    'All coworking spaces' => 'Tutti gli spazi coworking',
    'Acheter un coworking' => 'Acquista coworking',
    'Buy coworking' => 'Acquista coworking',
    'Locaux d\'activité' => 'Locali commerciali',
    'Business premises' => 'Locali commerciali',
    'Terrain' => 'Terreni',
    'Land' => 'Terreni',
    'Louer' => 'Affitta',
    'Acheter' => 'Acquista',
    'Principales villes' => 'Città principali',
    'Top cities' => 'Città principali',
    'Sites métier' => 'Siti business',
    'Business websites' => 'Siti business',
    'Nous écrire' => 'Scrivici',
    'Email us' => 'Scrivici',
    'Formulaire de contact' => 'Modulo di contatto',
    'Contact form' => 'Modulo di contatto',
    'Développement durable' => 'Sostenibilità',
    'Sustainability' => 'Sostenibilità',
    'Presse' => 'Stampa',
    'Press' => 'Stampa',
    'Carrières' => 'Carriere',
    'Careers' => 'Carriere',
    'Données personnelles' => 'Protezione dei dati',
    'Data protection' => 'Protezione dei dati',
    'Politique cookies' => 'Politica sui cookie',
    'Cookie policy' => 'Politica sui cookie',
    'Disclaimer' => 'Note legali',
    'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs' => 'Fornitori: BNP Paribas impegnata con partner e fornitori',
    'Suppliers: BNP Paribas is committed to its partners and suppliers' => 'Fornitori: BNP Paribas impegnata con partner e fornitori',
    'Plan du site' => 'Mappa del sito',
    'Sitemap' => 'Mappa del sito',
    'Réclamations Service Client' => 'Reclami Servizio Clienti',
    'Complaints Customer Service' => 'Reclami Servizio Clienti',
    'Canal de dénonciation éthique' => 'Canale di segnalazione etica',
    'Canal de denuncias éticas' => 'Canale di segnalazione etica',
    'Se connecter / S\'inscrire' => 'Accedi / Registrati',
    'Log in / Sign up' => 'Accedi / Registrati',
    'Que recherchez-vous ?' => 'Cosa state cercando?',
    'What are you looking for?' => 'Cosa state cercando?',
    'Research' => 'Ricerca',
  ];
}

/**
 * @return array<string, string>
 */
function menu_overlay_map_nl(): array {
  return [
    'Trouver un bien' => 'Vind een pand',
    'Find a property' => 'Vind een pand',
    'Tous les biens' => 'Alle panden',
    'All properties' => 'Alle panden',
    'À propos de nous' => 'Over ons',
    'About us' => 'Over ons',
    'Nous contacter' => 'Neem contact op',
    'Contact us' => 'Neem contact op',
    'Solutions' => 'Oplossingen',
    'Notre expertise' => 'Onze expertise',
    'Our expertise' => 'Onze expertise',
    'Actualités' => 'Laatste nieuws',
    'Latest News' => 'Laatste nieuws',
    'Conseil' => 'Advies',
    'Advisory' => 'Advies',
    'Toutes les actualités' => 'Alle nieuwsberichten',
    'All news' => 'Alle nieuwsberichten',
    'Corporate' => 'Corporate',
    'Location' => 'Huren',
    'Rent' => 'Huren',
    'Bureaux' => 'Kantoren',
    'Offices' => 'Kantoren',
    'Logistique' => 'Logistiek',
    'Logistics' => 'Logistiek',
    'Commerce' => 'Retail',
    'Retail' => 'Retail',
    'Achat' => 'Kopen',
    'Buy' => 'Kopen',
    'À propos de BNP Paribas Real Estate' => 'Over BNP Paribas Real Estate',
    'About BNP Paribas Real Estate' => 'Over BNP Paribas Real Estate',
    'Notre groupe' => 'Onze groep',
    'Our group' => 'Onze groep',
    'Services de conseil' => 'Adviesdiensten',
    'Advisory services' => 'Adviesdiensten',
    'Gestion immobilière' => 'Vastgoedbeheer',
    'Property management' => 'Vastgoedbeheer',
    'Thématiques' => 'Thema\'s',
    'Topics' => 'Thema\'s',
    'Marché immobilier' => 'Marktnieuws',
    'Market news' => 'Marktnieuws',
    'Tendances' => 'Trends',
    'Trends' => 'Trends',
    'Coworking' => 'Coworking',
    'Tous les espaces coworking' => 'Alle coworkingruimtes',
    'All coworking spaces' => 'Alle coworkingruimtes',
    'Acheter un coworking' => 'Coworking kopen',
    'Buy coworking' => 'Coworking kopen',
    'Locaux d\'activité' => 'Bedrijfsruimtes',
    'Business premises' => 'Bedrijfsruimtes',
    'Terrain' => 'Terreinen',
    'Land' => 'Terreinen',
    'Louer' => 'Huren',
    'Acheter' => 'Kopen',
    'Principales villes' => 'Belangrijkste steden',
    'Top cities' => 'Belangrijkste steden',
    'Sites métier' => 'Business websites',
    'Business websites' => 'Business websites',
    'Nous écrire' => 'Schrijf ons',
    'Email us' => 'Schrijf ons',
    'Formulaire de contact' => 'Contactformulier',
    'Contact form' => 'Contactformulier',
    'Développement durable' => 'Duurzaamheid',
    'Sustainability' => 'Duurzaamheid',
    'Presse' => 'Pers',
    'Press' => 'Pers',
    'Carrières' => 'Carrières',
    'Careers' => 'Carrières',
    'Données personnelles' => 'Gegevensbescherming',
    'Data protection' => 'Gegevensbescherming',
    'Politique cookies' => 'Cookiebeleid',
    'Cookie policy' => 'Cookiebeleid',
    'Disclaimer' => 'Disclaimer',
    'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs' => 'Leveranciers: BNP Paribas zet zich in voor partners en leveranciers',
    'Suppliers: BNP Paribas is committed to its partners and suppliers' => 'Leveranciers: BNP Paribas zet zich in voor partners en leveranciers',
    'Plan du site' => 'Sitemap',
    'Sitemap' => 'Sitemap',
    'Réclamations Service Client' => 'Klachten Klantenservice',
    'Complaints Customer Service' => 'Klachten Klantenservice',
    'Canal de dénonciation éthique' => 'Ethisch meldkanaal',
    'Canal de denuncias éticas' => 'Ethisch meldkanaal',
    'Se connecter / S\'inscrire' => 'Inloggen / Registreren',
    'Log in / Sign up' => 'Inloggen / Registreren',
    'Que recherchez-vous ?' => 'Wat zoekt u?',
    'What are you looking for?' => 'Wat zoekt u?',
    'Research' => 'Onderzoek',
  ];
}

/**
 * @return array<string, string>
 */
function menu_overlay_map_pl(): array {
  return [
    'Trouver un bien' => 'Znajdź nieruchomość',
    'Find a property' => 'Znajdź nieruchomość',
    'Tous les biens' => 'Wszystkie nieruchomości',
    'All properties' => 'Wszystkie nieruchomości',
    'À propos de nous' => 'O nas',
    'About us' => 'O nas',
    'Nous contacter' => 'Skontaktuj się z nami',
    'Contact us' => 'Skontaktuj się z nami',
    'Solutions' => 'Rozwiązania',
    'Notre expertise' => 'Nasza ekspertyza',
    'Our expertise' => 'Nasza ekspertyza',
    'Actualités' => 'Aktualności',
    'Latest News' => 'Aktualności',
    'Conseil' => 'Doradztwo',
    'Advisory' => 'Doradztwo',
    'Toutes les actualités' => 'Wszystkie aktualności',
    'All news' => 'Wszystkie aktualności',
    'Corporate' => 'Corporate',
    'Location' => 'Wynajem',
    'Rent' => 'Wynajem',
    'Bureaux' => 'Biura',
    'Offices' => 'Biura',
    'Logistique' => 'Logistyka',
    'Logistics' => 'Logistyka',
    'Commerce' => 'Handel',
    'Retail' => 'Handel',
    'Achat' => 'Kupno',
    'Buy' => 'Kupno',
    'À propos de BNP Paribas Real Estate' => 'BNP Paribas Real Estate',
    'About BNP Paribas Real Estate' => 'BNP Paribas Real Estate',
    'Notre groupe' => 'Nasza grupa',
    'Our group' => 'Nasza grupa',
    'Services de conseil' => 'Usługi doradcze',
    'Advisory services' => 'Usługi doradcze',
    'Gestion immobilière' => 'Zarządzanie nieruchomościami',
    'Property management' => 'Zarządzanie nieruchomościami',
    'Thématiques' => 'Tematy',
    'Topics' => 'Tematy',
    'Marché immobilier' => 'Wiadomości rynkowe',
    'Market news' => 'Wiadomości rynkowe',
    'Tendances' => 'Trendy',
    'Trends' => 'Trendy',
    'Coworking' => 'Coworking',
    'Tous les espaces coworking' => 'Wszystkie przestrzenie coworkingowe',
    'All coworking spaces' => 'Wszystkie przestrzenie coworkingowe',
    'Acheter un coworking' => 'Kup coworking',
    'Buy coworking' => 'Kup coworking',
    'Locaux d\'activité' => 'Lokale użytkowe',
    'Business premises' => 'Lokale użytkowe',
    'Terrain' => 'Działki',
    'Land' => 'Działki',
    'Louer' => 'Wynajmij',
    'Acheter' => 'Kup',
    'Principales villes' => 'Główne miasta',
    'Top cities' => 'Główne miasta',
    'Sites métier' => 'Strony biznesowe',
    'Business websites' => 'Strony biznesowe',
    'Nous écrire' => 'Napisz do nas',
    'Email us' => 'Napisz do nas',
    'Formulaire de contact' => 'Formularz kontaktowy',
    'Contact form' => 'Formularz kontaktowy',
    'Développement durable' => 'Zrównoważony rozwój',
    'Sustainability' => 'Zrównoważony rozwój',
    'Presse' => 'Prasa',
    'Press' => 'Prasa',
    'Carrières' => 'Kariera',
    'Careers' => 'Kariera',
    'Données personnelles' => 'Ochrona danych',
    'Data protection' => 'Ochrona danych',
    'Politique cookies' => 'Polityka cookies',
    'Cookie policy' => 'Polityka cookies',
    'Disclaimer' => 'Informacje prawne',
    'Fournisseurs : BNP Paribas s\'engage envers ses partenaires et fournisseurs' => 'Dostawcy: BNP Paribas wspiera partnerów i dostawców',
    'Suppliers: BNP Paribas is committed to its partners and suppliers' => 'Dostawcy: BNP Paribas wspiera partnerów i dostawców',
    'Plan du site' => 'Mapa witryny',
    'Sitemap' => 'Mapa witryny',
    'Réclamations Service Client' => 'Reklamacje Obsługi Klienta',
    'Complaints Customer Service' => 'Reklamacje Obsługi Klienta',
    'Canal de dénonciation éthique' => 'Kanał zgłoszeń etycznych',
    'Canal de denuncias éticas' => 'Kanał zgłoszeń etycznych',
    'Se connecter / S\'inscrire' => 'Zaloguj się / Zarejestruj się',
    'Log in / Sign up' => 'Zaloguj się / Zarejestruj się',
    'Que recherchez-vous ?' => 'Czego Pan/Pani szuka?',
    'What are you looking for?' => 'Czego Pan/Pani szuka?',
    'Research' => 'Badania',
  ];
}
