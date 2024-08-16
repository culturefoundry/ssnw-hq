<?php

$databases = [];

$settings['hash_salt'] = 'uDCnqQUVOD-oY0ukzB_4YAfo49LLT0HyyytN-TKssAvG0gWcYrf7_pD0FC2dFZdioJcTW94Qkw';

$settings['update_free_access'] = FALSE;

$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

/*$settings['trusted_host_patterns'] = [
  '^programminglibrarian\.org$',
  '^.*\.programminglibrarian\.org$',
  '^.*\.plib\.freelock\.net$',
  '^plib\.freelock\.com$',
  'plib\.ddev\.site$',
];*/
$settings['file_private_path'] = '/var/www/conf/ssnw/private';

$settings['file_scan_ignore_directories'] = [
  'node_modules',
  'bower_components',
];

$settings['entity_update_batch_size'] = 50;

$settings['entity_update_backup'] = TRUE;

$settings['migrate_node_migrate_type_classic'] = FALSE;
$settings['config_sync_directory'] = '../config/sync';
$settings['skip_permissions_hardening'] = TRUE;


if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
} elseif (file_exists(__DIR__ . '/settings.production.php')) {
   include __DIR__ . '/settings.production.php';
}


// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}
