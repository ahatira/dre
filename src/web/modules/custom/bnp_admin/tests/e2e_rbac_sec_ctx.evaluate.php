<?php

/**
 * @file
 * Drush evaluation for e2e_rbac_sec_ctx.sh.
 */

declare(strict_types=1);

use Drupal\node\Entity\Node;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;

$results = [];

$requiredRoles = ['content_editor', 'content_admin', 'site_admin'];
$legacyRoles = ['ps_admin', 'ps_content_editor'];
$missingRoles = array_filter($requiredRoles, static fn(string $rid): bool => Role::load($rid) === NULL);
$legacyPresent = array_filter($legacyRoles, static fn(string $rid): bool => Role::load($rid) !== NULL);
$results['roles_ok'] = $missingRoles === [] && $legacyPresent === [] ? 'yes' : 'no';
$results['roles_detail'] = $missingRoles === [] ? '' : 'missing:' . implode(',', $missingRoles);
if ($legacyPresent !== []) {
  $results['roles_detail'] .= ' legacy:' . implode(',', $legacyPresent);
}

$requiredUsers = ['content.editor', 'content.admin', 'site.admin'];
$missingUsers = [];
foreach ($requiredUsers as $name) {
  $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $name]);
  if ($users === []) {
    $missingUsers[] = $name;
  }
}
$results['users_ok'] = $missingUsers === [] ? 'yes' : 'no';
$results['users_detail'] = implode(',', $missingUsers);

$access = \Drupal::service('access_manager');
$loadUser = static function (string $name): ?User {
  $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => $name]);
  $user = reset($users);
  return $user instanceof User ? $user : NULL;
};

$editor = $loadUser('content.editor');
$siteAdmin = $loadUser('site.admin');
$contentAdmin = $loadUser('content.admin');

$results['sec01'] = ($editor && $access->checkNamedRoute('node.add', ['node_type' => 'offer'], $editor)) ? 'allowed' : 'denied';
$results['sec02'] = ($editor && $access->checkNamedRoute('entity.ps_context_rule.collection', [], $editor)) ? 'allowed' : 'denied';
$results['sec03'] = ($siteAdmin && $access->checkNamedRoute('entity.ps_context_rule.collection', [], $siteAdmin)) ? 'allowed' : 'denied';
$results['sec05_add'] = ($contentAdmin && $access->checkNamedRoute('node.add', ['node_type' => 'offer'], $contentAdmin)) ? 'allowed' : 'denied';

$readonlyRole = Role::create(['id' => 'rbac_e2e_readonly', 'label' => 'RBAC E2E readonly']);
$readonlyRole->save();
$readonlyUser = User::create([
  'name' => 'rbac_e2e_readonly',
  'mail' => 'rbac_e2e_readonly@test.ps.local',
  'status' => 1,
]);
$readonlyUser->addRole('rbac_e2e_readonly');
$readonlyUser->save();
$results['sec04'] = $access->checkNamedRoute('node.add', ['node_type' => 'offer'], $readonlyUser) ? 'allowed' : 'denied';
$readonlyUser->delete();
$readonlyRole->delete();

if ($contentAdmin && $editor) {
  $offer = Node::create([
    'type' => 'offer',
    'title' => 'RBAC E2E foreign offer',
    'uid' => $contentAdmin->id(),
    'status' => 0,
    'langcode' => 'en',
  ]);
  $offer->save();
  $results['sec05_edit'] = $offer->access('update', $contentAdmin) ? 'allowed' : 'denied';
  $results['sec06'] = $offer->access('update', $editor) ? 'allowed' : 'denied';
  $offer->delete();
}
else {
  $results['sec05_edit'] = 'missing_users';
  $results['sec06'] = 'missing_users';
}

$ruleCount = (int) \Drupal::entityTypeManager()->getStorage('ps_context_rule')
  ->getQuery()
  ->accessCheck(FALSE)
  ->count()
  ->execute();
$results['ctx_adm01_rules'] = (string) $ruleCount;

foreach ($results as $key => $value) {
  print $key . '=' . $value . PHP_EOL;
}
