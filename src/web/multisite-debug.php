<?php

/**
 * @file
 * Standalone multisite debug page (no Drupal bootstrap).
 *
 * Displays sites.php map, request resolution, env source and per-country paths.
 * Accessible at /multisite-debug.php on any local vhost (e.g. fr.localhost:8083).
 *
 * @see sites/load-env.php
 * @see sites/sites.php
 */

declare(strict_types=1);

$appRoot = __DIR__;
require $appRoot . '/sites/load-env.php';
ps_load_env();

header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');

/**
 * Escapes output for HTML.
 */
function ps_debug_h(mixed $value): string {
  return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Renders a key-value table.
 *
 * @param array<string, mixed> $rows
 */
function ps_debug_kv_table(array $rows): void {
  echo '<table><thead><tr><th>Clé</th><th>Valeur</th></tr></thead><tbody>';
  foreach ($rows as $key => $value) {
    if (is_array($value)) {
      $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    elseif (is_bool($value)) {
      $value = $value ? 'true' : 'false';
    }
    echo '<tr><td><code>' . ps_debug_h($key) . '</code></td><td>' . ps_debug_h($value) . '</td></tr>';
  }
  echo '</tbody></table>';
}

$sites = ps_build_multisite_sites_map();
$resolution = ps_find_site_path($_SERVER, $appRoot, $sites);
$drupalSitePath = ps_drupal_kernel_find_site_path($appRoot);
$envMeta = ps_env_debug_meta();
$trustedHosts = ps_build_trusted_host_patterns();

$serverKeys = [
  'HTTP_HOST',
  'SERVER_NAME',
  'SERVER_PORT',
  'REQUEST_SCHEME',
  'HTTPS',
  'REQUEST_URI',
  'SCRIPT_NAME',
  'SCRIPT_FILENAME',
  'DOCUMENT_ROOT',
  'REMOTE_ADDR',
];
$serverVars = [];
foreach ($serverKeys as $key) {
  if (array_key_exists($key, $_SERVER)) {
    $serverVars[$key] = $_SERVER[$key];
  }
}

$pathsMatch = ($drupalSitePath !== NULL && $drupalSitePath === $resolution['site_path']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PS multisite debug</title>
  <style>
    :root { color-scheme: light dark; font-family: system-ui, sans-serif; line-height: 1.45; }
    body { margin: 1.5rem; max-width: 1200px; }
    h1 { font-size: 1.35rem; margin-bottom: 0.25rem; }
    h2 { font-size: 1.1rem; margin-top: 2rem; border-bottom: 1px solid #8884; padding-bottom: 0.25rem; }
    table { border-collapse: collapse; width: 100%; margin: 0.75rem 0; font-size: 0.92rem; }
    th, td { border: 1px solid #8884; padding: 0.35rem 0.55rem; text-align: left; vertical-align: top; }
    th { background: #8882; }
    code { font-size: 0.9em; }
    .ok { color: #0a7; font-weight: 600; }
    .warn { color: #c60; font-weight: 600; }
    .err { color: #c22; font-weight: 600; }
    .meta { color: #666; font-size: 0.9rem; }
    details { margin: 0.5rem 0; }
    tr.match { background: rgba(0, 170, 119, 0.12); }
  </style>
</head>
<body>
  <h1>Property Search — debug multisite</h1>
  <p class="meta">Page standalone (hors bootstrap Drupal). Générée <?php echo ps_debug_h(date('c')); ?>.</p>

  <h2>Environnement</h2>
  <?php ps_debug_kv_table($envMeta); ?>

  <h2>Requête courante — résolution site</h2>
  <?php
  ps_debug_kv_table([
    'HTTP_HOST (résolution)' => $resolution['http_host'],
    'SCRIPT_NAME' => $resolution['script_name'],
    'Clé sites.php matchée' => $resolution['matched_key'] ?? '(aucune — fallback)',
    'Via alias' => $resolution['via_alias'],
    'site_dir' => $resolution['site_dir'],
    'site_path (helper PS)' => $resolution['site_path'],
    'site_path (DrupalKernel)' => $drupalSitePath ?? '(core indisponible)',
    'PS vs Drupal' => $drupalSitePath === NULL ? 'n/a' : ($pathsMatch ? 'identique' : 'DIVERGENCE'),
  ]);
  ?>
  <details>
    <summary>Tentatives findSitePath (<?php echo count($resolution['attempts']); ?>)</summary>
    <table>
      <thead>
        <tr>
          <th>site_id testé</th>
          <th>site_dir</th>
          <th>Alias</th>
          <th>settings.php</th>
          <th>Match</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($resolution['attempts'] as $attempt) : ?>
          <tr class="<?php echo !empty($attempt['match']) ? 'match' : ''; ?>">
            <td><code><?php echo ps_debug_h($attempt['site_id']); ?></code></td>
            <td><code><?php echo ps_debug_h($attempt['resolved_dir']); ?></code></td>
            <td><?php echo $attempt['alias'] ? 'oui' : 'non'; ?></td>
            <td><?php echo $attempt['settings_exists'] ? 'oui' : 'non'; ?></td>
            <td><?php echo $attempt['match'] ? '✓' : ''; ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </details>

  <h2>$_SERVER (extrait)</h2>
  <?php ps_debug_kv_table($serverVars); ?>

  <h2>Carte $sites (sites.php)</h2>
  <p class="meta"><?php echo count($sites); ?> entrée(s) — clé Drupal → dossier sous <code>sites/</code>.</p>
  <table>
    <thead>
      <tr>
        <th>Clé (host ou port.host)</th>
        <th>site_dir</th>
        <th>settings.php</th>
        <th>Cette requête</th>
      </tr>
    </thead>
    <tbody>
      <?php
      ksort($sites, SORT_STRING);
      foreach ($sites as $key => $siteDir) :
        $settingsOk = is_file($appRoot . '/sites/' . $siteDir . '/settings.php');
        $isMatch = ($resolution['matched_key'] === $key);
        ?>
        <tr class="<?php echo $isMatch ? 'match' : ''; ?>">
          <td><code><?php echo ps_debug_h($key); ?></code></td>
          <td><code><?php echo ps_debug_h($siteDir); ?></code></td>
          <td class="<?php echo $settingsOk ? 'ok' : 'err'; ?>"><?php echo $settingsOk ? 'oui' : 'non'; ?></td>
          <td><?php echo $isMatch ? '← match' : ''; ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h2>Détail par pays</h2>
  <table>
    <thead>
      <tr>
        <th>Code</th>
        <th>site_dir</th>
        <th>Port HTTP</th>
        <th>Front hosts</th>
        <th>Admin hosts</th>
        <th>DB</th>
        <th>Solr core</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (ps_country_codes() as $code) :
        $upper = strtoupper($code);
        $frontHosts = ps_env_hosts('APP_DOMAIN_' . $upper);
        if ($frontHosts === []) {
          continue;
        }
        ?>
        <tr>
          <td><code><?php echo ps_debug_h($code); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_country_site_dir($code)); ?></code></td>
          <td><?php echo ps_debug_h((string) ps_country_http_port($code)); ?></td>
          <td><?php echo ps_debug_h(implode(', ', $frontHosts)); ?></td>
          <td><?php echo ps_debug_h(implode(', ', ps_env_hosts('APP_DOMAIN_' . $upper . '_ADMIN')) ?: '—'); ?></td>
          <td><code><?php echo ps_debug_h(ps_env('DB_NAME_' . $upper)); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_env('SOLR_CORE_' . $upper)); ?></code></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php
  $infraHosts = array_values(array_unique(array_merge(
    ps_env_hosts('APP_DOMAIN_SERVICE'),
    ps_env_hosts('APP_DOMAIN_PROBES'),
  )));
  if ($infraHosts !== []) :
    ?>
    <p class="meta">Infra (SERVICE/PROBES, clés port-qualified uniquement) :
      <code><?php echo ps_debug_h(implode(', ', $infraHosts)); ?></code>
    </p>
  <?php endif; ?>

  <h2>Trusted host patterns</h2>
  <?php if ($trustedHosts === []) : ?>
    <p class="warn">Aucun pattern (domaines non configurés dans l’env).</p>
  <?php else : ?>
    <ul>
      <?php foreach ($trustedHosts as $pattern) : ?>
        <li><code><?php echo ps_debug_h($pattern); ?></code></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <h2>Chemins fichiers par pays</h2>
  <table>
    <thead>
      <tr>
        <th>Code</th>
        <th>Public files</th>
        <th>Private files</th>
        <th>Temp</th>
        <th>Assets</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (ps_country_codes() as $code) :
        if (ps_env_hosts('APP_DOMAIN_' . strtoupper($code)) === []) {
          continue;
        }
        ?>
        <tr>
          <td><code><?php echo ps_debug_h($code); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_resolve_public_files_path($code)); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_resolve_private_files_path($code, $appRoot)); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_resolve_temp_files_path($code, $appRoot) ?: '(défaut Drupal)'); ?></code></td>
          <td><code><?php echo ps_debug_h(ps_resolve_assets_files_path($code) ?: '(défaut = public files)'); ?></code></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
