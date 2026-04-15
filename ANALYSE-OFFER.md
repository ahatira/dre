# ANALYSE OFFER

## Contexte
Analyse de 3 offres BNP Paribas Real Estate (Paris 19) pour deduire une structure d'offre simple et coherente avec notre modele Drupal `ps_offer`.

Sources analysees :
- https://www.bnppre.fr/a-louer/bureau/paris-75/paris-19-75019/location-bureau-2082-m2-non-divisible-OLBUR2311214.html
- https://www.bnppre.fr/a-louer/bureau/paris-75/paris-19-75019/location-bureau-1068-m2-divisible-OLBUR2107076.html
- https://www.bnppre.fr/a-louer/bureau/paris-75/paris-19-75019/location-bureau-902-m2-divisible-OLBUR2427085.html

## 1) Ce qui ressort des 3 offres
Elements communs observes :
- Identite: titre commercial, reference (ex: OLBURxxxxxxx), type de bien (bureau), transaction (location).
- Localisation: adresse ou secteur, code postal, ville, carte, transports.
- Surfaces: surface totale, parfois surface mini divisible, tableau detaille par lot/niveau.
- Prix: valeur au m2/an, loyer annuel, ou "loyer sur demande".
- Disponibilite: immediate ou "nous consulter".
- Description marketing: contexte du bien, points forts, conditions commerciales.
- Services et prestations: amenagements, equipements techniques, services, exterieurs.
- Contact: consultant, telephone, CTA de contact/visite.
- Medias: photos, parfois visite 360, plans implicites selon cas.

Lecture rapide des 3 refs :
- OLBUR2311214: 2 083 m2, non divisible, loyer sur demande, disponibilite immediate, argumentaire campus + services.
- OLBUR2107076: 1 068 m2 (slug "divisible"), prix affiche (290 EUR/m2/an), disponibilite immediate, fiche prestations tres detaillee.
- OLBUR2427085: 902 m2, divisible des 258 m2, prix "a partir de 390 EUR/m2/an", tableau multi-niveaux, infos bail/garantie/honoraires.

## 2) Structure essentielle d'une offre (version cible simple)
Pour rester lisible et evolutif, une offre peut etre geree en 8 blocs :

1. Identification
- Reference interne/externe
- Type de bien
- Type de transaction
- Titre editorial

2. Localisation
- Adresse, CP, ville
- Coordonnees geographiques
- Desserte transports (metro, rer, tram, route)

3. Surfaces
- Surface totale
- Divisible (oui/non)
- Surface minimale divisible
- Tableau des lots (niveau, surface, prix)

4. Conditions financieres
- Prix principal (EUR/m2/an ou autre unite)
- Loyer annuel (si calcule)
- Mention "sur demande" possible
- Conditions juridiques/commerciales (bail, depot, honoraires)

5. Disponibilite
- Immediate / date / a confirmer

6. Contenu marketing
- Description longue
- Labels/pastilles (exclusivite, 360, etc.)

7. Prestations
- Amenagements
- Equipements
- Services
- Exterieurs
- Diagnostics

8. Medias et contact
- Photos, plans, videos, visite 3D
- Agent(s) commercial(aux), CTA contact/visite

## 3) Comment la gerer dans Drupal
Modele recommande :
- Entite de contenu `node` bundle `offer`.
- Champs structures pour les donnees filtrables (prix, surfaces, divisible, localisation, type, transaction, disponibilite).
- Champs texte pour le marketing (description, highlights).
- Champs media pour photos/plans/videos/visites 3D.
- Reference vers agent(s) pour le contact.
- Bloc "prestations" en donnees structurees (pas uniquement texte libre) pour permettre facettes et filtres.
- Affichage en sections: hero + resume cle + details (prestations/surfaces/localisation).

## 4) Comparatif rapide avec notre offre (`ps_offer`)
Couvert aujourd'hui (bon alignement) :
- Identification: `field_reference`, `external_id`, `field_property_type`, `field_transaction_types`.
- Localisation: `field_address`, `field_geofield`.
- Surfaces/prix: `field_surfaces`, `field_prices`, `field_is_divisible`, `field_divisions`.
- Disponibilite: `field_availability`.
- Contenu: `body`, `field_labels`.
- Prestations/diagnostics: `field_features`, `field_diagnostics`.
- Medias/contact: `field_media_photos`, `field_media_plans`, `field_media_videos`, `field_media_virtual_tours`, `field_media_brochures`, `field_agents`.
- Meta business interne: `field_client_type`, `field_mandate_type`.

Points potentiellement manquants ou a clarifier :
- Gestion explicite du "loyer sur demande" (etat metier, pas seulement valeur vide).
- Surface minimale divisible explicite (si non derivee de `field_divisions`).
- Conditions commerciales detaillees (bail, depot de garantie, honoraires) en champs structures.
- Desserte transport normalisee (actuellement surtout en description/features selon les cas).
- Distinction claire entre "disponibilite immediate" et "nous consulter" avec statut enumere.

Ce qu'on a deja en plus vs pages analysees :
- Structuration metier plus riche (`client type`, `mandate type`, labels taxonomy).
- Couverture media complete (brochures + visite 3D nativement).
- Reference auto-generee et integration CRM (`external_id`) deja prevues.

## 5) Recommandation courte
Notre socle `ps_offer` est globalement coherent avec les 3 offres du site principal.
Priorite conseillee: renforcer 3 axes de normalisation pour fiabiliser l'import et le front.
- Axe 1: prix "sur demande" et statuts de disponibilite explicites.
- Axe 2: transport et conditions commerciales en donnees structurees.
- Axe 3: regles de mapping import -> champs Drupal (surtout surfaces/divisibilite/tableau des lots).
