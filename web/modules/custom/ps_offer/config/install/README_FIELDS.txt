# Configuration pour les champs du node type "offer"
# Ce fichier sera synchronisé après installation du module

# CHOIX TECHNIQUES ACTÉS
#
# field_surfaces — translatable
#   - field.storage.node.field_surfaces : translatable = true
#     Le storage autorise la traduction pour garder la porte ouverte à une
#     version multilingue future.
#   - field.field.node.offer.field_surfaces : translatable = false
#     L'instance désactive la traduction par champ sur le bundle offer.
#     Décision métier : une surface est une donnée physique indépendante de la
#     langue de l'offre (pas de surface FR vs surface EN). Cette valeur ne doit
#     pas être dupliquée par langue.
#   => Ne pas activer `translatable: true` sur l'instance sans révision
#      explicite de l'impact sur les imports CRM et l'indexation Search API.

# Base field definitions sont gérées via Drupal node module
# Les champs personnalisés seront ajoutés via hook_node_type_presave ou config

# TODO: After module installation, create field instances via UI or programmatically:

# 1. Custom fields to create:
# - external_id (string, max_length: 255)
# - reference (string, max_length: 100)
# - property_type_code (string, max_length: 50)
# - transaction_type_codes (text)
# - address (string, max_length: 500)
# - postal_code (string, max_length: 20)
# - city_label (string, max_length: 255)
# - description (text_long)
# - prices (ps_price, cardinality: -1)
# - features (ps_feature_value, cardinality: -1)
# - diagnostics (ps_diagnostic, cardinality: -1)
# - divisions (entity_reference to ps_division, cardinality: -1)
# - media_photos (media_reference, cardinality: -1)
# - media_plans (media_reference, cardinality: -1)
# - media_videos (media_reference, cardinality: -1)
# - agents (entity_reference to ps_agent, cardinality: -1)
# - geofield (geofield, cardinality: 1)

# View modes:
# - default
# - teaser
# - search_result

# Form modes:
# - default
# - inline
