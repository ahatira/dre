#!/bin/bash

# Add feature fields to Solr managed schema via REST API

SOLR_URL="http://localhost:8983/solr/ps_project"

# Boolean feature fields
curl -X POST -H 'Content-type:application/json' --data-binary '{
  "add-field":{
    "name":"feature_amenagements_tec_hall_daccueil",
    "type":"boolean",
    "stored":true,
    "indexed":true
  }
}' "$SOLR_URL/schema"

curl -X POST -H 'Content-type:application/json' --data-binary '{
  "add-field":{
    "name":"feature_equipements_tec_cblage_informatique",
    "type":"boolean",
    "stored":true,
    "indexed":true
  }
}' "$SOLR_URL/schema"

# Numeric feature field (use pfloat for decimal)
curl -X POST -H 'Content-type:application/json' --data-binary '{
  "add-field":{
    "name":"feature_hauteurs_tec_hauteur_libre",
    "type":"pfloat",
    "stored":true,
    "indexed":true
  }
}' "$SOLR_URL/schema"

echo "Schema fields added. Reloading core..."

curl "http://localhost:8983/solr/admin/cores?action=RELOAD&core=ps_project"

echo "Done."
