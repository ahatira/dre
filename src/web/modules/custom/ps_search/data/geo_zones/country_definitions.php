<?php

declare(strict_types=1);

/**
 * Admin level 1 definitions per PS country (source for geo zone YAML generation).
 *
 * Loaded by GeoZoneDefinitionProvider. FR is derived from dictionary + centroids.
 *
 * Zone IDs: {zone_type}.{country}.{lowercase code}
 *
 * @return array<string, array{
 *   zone_type: string,
 *   default_code: string,
 *   divisions: list<array{
 *     code: string,
 *     label: string,
 *     slug: string,
 *     lat: float,
 *     lng: float,
 *     postal_prefixes: list<string>,
 *     radius_km: float
 *   }>
 * }>
 */
function ps_geo_div(
  string $code,
  string $label,
  string $slug,
  float $lat,
  float $lng,
  array $postal_prefixes,
  float $radius_km,
): array {
  return [
    'code' => $code,
    'label' => $label,
    'slug' => $slug,
    'lat' => $lat,
    'lng' => $lng,
    'postal_prefixes' => $postal_prefixes,
    'radius_km' => $radius_km,
  ];
}

function ps_geo_country_definitions(): array {
  return [
    'be' => [
      'zone_type' => 'region',
      'default_code' => 'BRU',
      'divisions' => [
        ps_geo_div('BRU', 'Brussels-Capital Region', 'brussels', 50.8503, 4.3517, ['10', '11', '12'], 20),
        ps_geo_div('VAN', 'Antwerp', 'antwerp', 51.2194, 4.4025, ['20', '21', '22', '23', '24', '25', '26', '27', '28', '29'], 45),
        ps_geo_div('VLI', 'Limburg', 'limburg', 50.9307, 5.3378, ['36', '37', '38', '39'], 40),
        ps_geo_div('VOV', 'East Flanders', 'east-flanders', 51.0543, 3.7174, ['90', '91', '92', '93', '94', '95', '96', '97', '98', '99'], 45),
        ps_geo_div('VWV', 'West Flanders', 'west-flanders', 51.2093, 3.2247, ['80', '81', '82', '83', '84', '85', '86', '87', '88', '89'], 45),
        ps_geo_div('VBR', 'Flemish Brabant', 'flemish-brabant', 50.8798, 4.7005, ['15', '16', '17', '18', '19', '30', '31', '32', '33', '34'], 35),
        ps_geo_div('WBR', 'Walloon Brabant', 'walloon-brabant', 50.7175, 4.6108, ['13', '14'], 30),
        ps_geo_div('WHT', 'Hainaut', 'hainaut', 50.4542, 3.9564, ['60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79'], 50),
        ps_geo_div('WLG', 'Liège', 'liege', 50.6337, 5.5675, ['40', '41', '42', '43', '44', '45', '46', '47', '48', '49'], 45),
        ps_geo_div('WLX', 'Luxembourg', 'luxembourg-be', 49.6833, 5.8167, ['66', '67', '68', '69'], 45),
        ps_geo_div('WNA', 'Namur', 'namur', 50.4674, 4.8719, ['50', '51', '52', '53', '54', '55', '56', '57', '58', '59'], 45),
      ],
    ],
    'nl' => [
      'zone_type' => 'region',
      'default_code' => 'NH',
      'divisions' => [
        ps_geo_div('DR', 'Drenthe', 'drenthe', 52.9068, 6.6368, ['78', '79'], 45),
        ps_geo_div('FL', 'Flevoland', 'flevoland', 52.4484, 5.4235, ['13', '82'], 35),
        ps_geo_div('FR', 'Friesland', 'friesland', 53.0924, 5.7770, ['84', '85', '86', '87', '88', '89', '90', '91', '92'], 45),
        ps_geo_div('GE', 'Gelderland', 'gelderland', 52.1014, 5.9516, ['38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59', '60', '61', '62', '63', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74'], 55),
        ps_geo_div('GR', 'Groningen', 'groningen', 53.2194, 6.5665, ['93', '94', '95', '96', '97', '98', '99'], 45),
        ps_geo_div('LI', 'Limburg', 'limburg-nl', 50.8514, 5.6910, ['60', '61', '62', '63', '64'], 40),
        ps_geo_div('NB', 'North Brabant', 'north-brabant', 51.6978, 5.3037, ['46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59'], 50),
        ps_geo_div('NH', 'North Holland', 'north-holland', 52.3874, 4.6462, ['10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29'], 45),
        ps_geo_div('OV', 'Overijssel', 'overijssel', 52.5168, 6.0830, ['75', '76', '77', '80', '81'], 45),
        ps_geo_div('ZH', 'South Holland', 'south-holland', 52.0705, 4.3007, ['22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35'], 45),
        ps_geo_div('UT', 'Utrecht', 'utrecht', 52.0907, 5.1214, ['34', '35', '36', '37', '38', '39'], 35),
        ps_geo_div('ZE', 'Zeeland', 'zeeland', 51.4988, 3.6136, ['43', '44', '45', '46'], 40),
      ],
    ],
    'es' => [
      'zone_type' => 'region',
      'default_code' => 'MD',
      'divisions' => [
        ps_geo_div('AN', 'Andalusia', 'andalusia', 37.3400, -4.5812, ['04', '11', '14', '18', '21', '23', '29', '41'], 120),
        ps_geo_div('AR', 'Aragon', 'aragon', 41.3787, -0.7639, ['22', '44', '50'], 90),
        ps_geo_div('AS', 'Asturias', 'asturias', 43.3134, -5.9419, ['33'], 70),
        ps_geo_div('IB', 'Balearic Islands', 'balearic-islands', 39.6134, 2.8829, ['07'], 80),
        ps_geo_div('PV', 'Basque Country', 'basque-country', 42.9912, -2.5543, ['01', '20', '48'], 70),
        ps_geo_div('CN', 'Canary Islands', 'canary-islands', 28.2936, -16.6214, ['35', '38'], 150),
        ps_geo_div('CB', 'Cantabria', 'cantabria', 43.1596, -4.0878, ['39'], 60),
        ps_geo_div('CL', 'Castile and León', 'castile-and-leon', 41.5501, -5.1387, ['05', '09', '24', '34', '37', '40', '42', '47', '49'], 130),
        ps_geo_div('CM', 'Castilla-La Mancha', 'castilla-la-mancha', 39.4178, -2.6232, ['02', '13', '16', '19', '45'], 110),
        ps_geo_div('CT', 'Catalonia', 'catalonia', 41.8523, 1.5745, ['08', '17', '25', '43'], 100),
        ps_geo_div('EX', 'Extremadura', 'extremadura', 39.1748, -6.1530, ['06', '10'], 100),
        ps_geo_div('GA', 'Galicia', 'galicia', 42.6195, -7.8631, ['15', '27', '32', '36'], 100),
        ps_geo_div('RI', 'La Rioja', 'la-rioja', 42.4627, -2.4449, ['26'], 55),
        ps_geo_div('MD', 'Community of Madrid', 'madrid', 40.4168, -3.7038, ['28'], 70),
        ps_geo_div('MC', 'Region of Murcia', 'murcia', 37.9922, -1.1307, ['30'], 70),
        ps_geo_div('NC', 'Navarre', 'navarre', 42.8125, -1.6458, ['31'], 65),
        ps_geo_div('VC', 'Valencian Community', 'valencian-community', 39.4699, -0.3763, ['03', '12', '46'], 90),
      ],
    ],
    'it' => [
      'zone_type' => 'region',
      'default_code' => 'LAZ',
      'divisions' => [
        ps_geo_div('ABR', 'Abruzzo', 'abruzzo', 42.2277, 13.8550, ['65', '66', '67'], 70),
        ps_geo_div('VDA', 'Aosta Valley', 'aosta-valley', 45.7301, 7.3874, ['11'], 50),
        ps_geo_div('PUG', 'Apulia', 'apulia', 40.9843, 16.6210, ['70', '71', '72', '73', '74', '75', '76'], 90),
        ps_geo_div('BAS', 'Basilicata', 'basilicata', 40.5006, 16.0820, ['85'], 70),
        ps_geo_div('CAL', 'Calabria', 'calabria', 39.0566, 16.5250, ['87', '88', '89'], 80),
        ps_geo_div('CAM', 'Campania', 'campania', 40.8607, 14.8440, ['80', '81', '82', '83', '84'], 80),
        ps_geo_div('EMR', 'Emilia-Romagna', 'emilia-romagna', 44.5257, 11.0394, ['40', '41', '42', '43', '44', '47', '48'], 90),
        ps_geo_div('FVG', 'Friuli-Venezia Giulia', 'friuli-venezia-giulia', 46.1510, 13.0559, ['33', '34'], 70),
        ps_geo_div('LAZ', 'Lazio', 'lazio', 41.9802, 12.7668, ['00', '01', '02', '03', '04'], 80),
        ps_geo_div('LIG', 'Liguria', 'liguria', 44.4778, 8.7026, ['16', '17', '18', '19'], 60),
        ps_geo_div('LOM', 'Lombardy', 'lombardy', 45.5704, 9.7733, ['20', '21', '22', '23', '24', '25', '26', '27', '28', '29'], 90),
        ps_geo_div('MAR', 'Marche', 'marche', 43.3458, 13.1416, ['60', '61', '62', '63'], 70),
        ps_geo_div('MOL', 'Molise', 'molise', 41.6847, 14.5956, ['86'], 50),
        ps_geo_div('PMN', 'Piedmont', 'piedmont', 45.0607, 7.9235, ['10', '12', '13', '14', '15'], 90),
        ps_geo_div('SAR', 'Sardinia', 'sardinia', 40.0913, 9.0306, ['07', '08', '09'], 90),
        ps_geo_div('SIC', 'Sicily', 'sicily', 37.5878, 14.1550, ['90', '91', '92', '93', '94', '95', '96', '97', '98'], 110),
        ps_geo_div('TOS', 'Tuscany', 'tuscany', 43.4587, 11.1389, ['50', '51', '52', '53', '54', '55', '56', '57', '58', '59'], 90),
        ps_geo_div('TAA', 'Trentino-Alto Adige', 'trentino-alto-adige', 46.4415, 11.2821, ['38', '39'], 70),
        ps_geo_div('UMB', 'Umbria', 'umbria', 42.9659, 12.4902, ['06'], 60),
        ps_geo_div('VEN', 'Veneto', 'veneto', 45.6477, 11.8665, ['30', '31', '32', '35', '36', '37'], 90),
      ],
    ],
    'pl' => [
      'zone_type' => 'region',
      'default_code' => 'MZ',
      'divisions' => [
        ps_geo_div('DS', 'Lower Silesian', 'lower-silesian', 50.9206, 16.4946, ['50', '51', '52', '53', '54', '55', '56', '57', '58', '59'], 90),
        ps_geo_div('KP', 'Kuyavian-Pomeranian', 'kuyavian-pomeranian', 53.3140, 18.3798, ['85', '86', '87', '88', '89'], 80),
        ps_geo_div('LU', 'Lublin', 'lublin', 50.9359, 22.8308, ['20', '21', '22', '23', '24'], 90),
        ps_geo_div('LB', 'Lubusz', 'lubusz', 52.1002, 15.3605, ['65', '66', '67', '68', '69'], 70),
        ps_geo_div('LD', 'Łódź', 'lodz', 51.4722, 19.3461, ['90', '91', '92', '93', '94', '95', '96', '97', '98', '99'], 80),
        ps_geo_div('MA', 'Lesser Poland', 'lesser-poland', 49.7910, 20.3794, ['30', '31', '32', '33', '34'], 90),
        ps_geo_div('MZ', 'Masovian', 'masovian', 52.5462, 21.2073, ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '26'], 100),
        ps_geo_div('OP', 'Opole', 'opole', 50.8919, 17.9321, ['45', '46', '47', '48', '49'], 60),
        ps_geo_div('PK', 'Subcarpathian', 'subcarpathian', 49.9927, 22.1771, ['35', '36', '37', '38', '39'], 90),
        ps_geo_div('PD', 'Podlaskie', 'podlaskie', 53.2668, 22.8526, ['15', '16', '17', '18', '19'], 90),
        ps_geo_div('PM', 'Pomeranian', 'pomeranian', 54.2456, 18.1099, ['80', '81', '82', '83', '84'], 90),
        ps_geo_div('SL', 'Silesian', 'silesian', 50.5687, 19.2344, ['40', '41', '42', '43', '44'], 80),
        ps_geo_div('SK', 'Holy Cross', 'holy-cross', 50.7505, 20.7829, ['25', '26', '27', '28', '29'], 70),
        ps_geo_div('WN', 'Warmian-Masurian', 'warmian-masurian', 53.9312, 21.1261, ['10', '11', '12', '13', '14'], 90),
        ps_geo_div('WP', 'Greater Poland', 'greater-poland', 52.1459, 17.3977, ['60', '61', '62', '63', '64'], 90),
        ps_geo_div('ZP', 'West Pomeranian', 'west-pomeranian', 53.5451, 15.5662, ['70', '71', '72', '73', '74', '75', '76', '77', '78', '79'], 90),
      ],
    ],
    'ie' => [
      'zone_type' => 'region',
      'default_code' => 'D',
      'divisions' => [
        ps_geo_div('CW', 'Carlow', 'carlow', 52.8365, -6.9264, ['R93'], 40),
        ps_geo_div('CN', 'Cavan', 'cavan', 53.9908, -7.3601, ['H12'], 45),
        ps_geo_div('CE', 'Clare', 'clare', 52.8436, -8.9864, ['V95'], 55),
        ps_geo_div('CO', 'Cork', 'cork', 51.8985, -8.4756, ['P12', 'P14', 'P17', 'P24', 'P25', 'P31', 'P32', 'P36', 'P43', 'P47', 'P48', 'P51', 'P56', 'P61', 'P67', 'P72', 'P75', 'P81', 'P85', 'P86'], 70),
        ps_geo_div('DL', 'Donegal', 'donegal', 54.6538, -8.1098, ['F92', 'F93', 'F94'], 70),
        ps_geo_div('D', 'Dublin', 'dublin', 53.3498, -6.2603, ['D01', 'D02', 'D03', 'D04', 'D05', 'D06', 'D07', 'D08', 'D09', 'D10', 'D11', 'D12', 'D13', 'D14', 'D15', 'D16', 'D17', 'D18', 'D20', 'D22', 'D24'], 35),
        ps_geo_div('G', 'Galway', 'galway', 53.2707, -9.0568, ['H54', 'H62', 'H65', 'H71', 'H91'], 70),
        ps_geo_div('KY', 'Kerry', 'kerry', 52.0599, -9.5044, ['V23', 'V31', 'V92', 'V93'], 65),
        ps_geo_div('KE', 'Kildare', 'kildare', 53.1579, -6.9090, ['R14', 'R51', 'R56', 'W12', 'W23', 'W34'], 45),
        ps_geo_div('KK', 'Kilkenny', 'kilkenny', 52.6541, -7.2448, ['R95'], 45),
        ps_geo_div('LS', 'Laois', 'laois', 53.0345, -7.3042, ['R32'], 45),
        ps_geo_div('LM', 'Leitrim', 'leitrim', 54.0020, -8.0581, ['N41'], 50),
        ps_geo_div('LK', 'Limerick', 'limerick', 52.6638, -8.6267, ['V94'], 55),
        ps_geo_div('LD', 'Longford', 'longford', 53.7275, -7.7986, ['N39'], 40),
        ps_geo_div('LH', 'Louth', 'louth', 53.9920, -6.5413, ['A91', 'A92'], 40),
        ps_geo_div('MO', 'Mayo', 'mayo', 53.8555, -9.3042, ['F23', 'F26', 'F28', 'F35', 'F45'], 70),
        ps_geo_div('MH', 'Meath', 'meath', 53.6539, -6.6873, ['A82', 'A83', 'A85', 'C15'], 45),
        ps_geo_div('MN', 'Monaghan', 'monaghan', 54.2479, -6.9719, ['H18'], 45),
        ps_geo_div('OY', 'Offaly', 'offaly', 53.2754, -7.4944, ['R35', 'R42'], 45),
        ps_geo_div('RN', 'Roscommon', 'roscommon', 53.6340, -8.1890, ['F42'], 50),
        ps_geo_div('SO', 'Sligo', 'sligo', 54.2695, -8.4696, ['F91'], 50),
        ps_geo_div('TA', 'Tipperary', 'tipperary', 52.4731, -8.1619, ['E21', 'E25', 'E32', 'E34', 'E41', 'E45', 'E91'], 55),
        ps_geo_div('WD', 'Waterford', 'waterford', 52.2593, -7.1101, ['X35', 'X42', 'X91'], 50),
        ps_geo_div('WH', 'Westmeath', 'westmeath', 53.5256, -7.3386, ['N37', 'N91'], 45),
        ps_geo_div('WX', 'Wexford', 'wexford', 52.3369, -6.4633, ['Y21', 'Y25', 'Y34', 'Y35'], 55),
        ps_geo_div('WW', 'Wicklow', 'wicklow', 52.9808, -6.0441, ['A63', 'A67', 'A98'], 45),
      ],
    ],
    'lu' => [
      'zone_type' => 'region',
      'default_code' => 'LU',
      'divisions' => [
        ps_geo_div('CA', 'Capellen', 'capellen', 49.6411, 5.9994, ['83'], 20),
        ps_geo_div('CL', 'Clervaux', 'clervaux', 50.0547, 6.0289, ['97'], 25),
        ps_geo_div('DI', 'Diekirch', 'diekirch', 49.8678, 6.1556, ['92'], 20),
        ps_geo_div('EC', 'Echternach', 'echternach', 49.8125, 6.4219, ['64'], 20),
        ps_geo_div('ES', 'Esch-sur-Alzette', 'esch-sur-alzette', 49.4958, 5.9806, ['43'], 20),
        ps_geo_div('GR', 'Grevenmacher', 'grevenmacher', 49.6808, 6.4408, ['67'], 20),
        ps_geo_div('LU', 'Luxembourg', 'luxembourg', 49.6116, 6.1319, ['12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28'], 20),
        ps_geo_div('ME', 'Mersch', 'mersch', 49.7489, 6.1061, ['75'], 20),
        ps_geo_div('RD', 'Redange', 'redange', 49.7656, 5.8903, ['85'], 20),
        ps_geo_div('RM', 'Remich', 'remich', 49.5447, 6.3669, ['55'], 20),
        ps_geo_div('VD', 'Vianden', 'vianden', 49.9347, 6.2078, ['94'], 20),
        ps_geo_div('WI', 'Wiltz', 'wiltz', 49.9669, 5.9319, ['95'], 25),
      ],
    ],
  ];
}
