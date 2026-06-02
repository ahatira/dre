<?php

declare(strict_types=1);

$index = \Drupal\search_api\Entity\Index::load('offers');
if (!$index) {
  echo "Index not found\n";
  return;
}

$query = $index->query();
$query->addCondition('feature_amenagements__tec_hall_daccueil', 1);
$query->range(0, 10);

$results = $query->execute();

echo 'total_results=' . $results->getResultCount() . "\n";

foreach ($results->getResultItems() as $item) {
  $nid = (int) str_replace('entity:node/', '', $item->getId());
  echo 'nid=' . $nid . "\n";
}

// Now test the Solr query built
$backend = $index->getServerInstance()->getBackend();
$solrQuery = $backend->getSolrConnector()->getSelectQuery();
$solrQuery->setQuery('feature_amenagements_tec_hall_daccueil:true');
$solrQuery->setRows(5);

try {
  $solrResult = $backend->getSolrConnector()->execute($solrQuery);
  echo "\nsolr_direct_numFound=" . $solrResult->getNumFound() . "\n";
} catch (\Exception $e) {
  echo "\nsolr_error=" . $e->getMessage() . "\n";
}
