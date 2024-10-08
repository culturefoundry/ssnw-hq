<?php

// Important constants :)
$pantheon_yellow = '#EFD01B';

$defaults = [];

// Load our hidden credentials.
// See the README.md for instructions on storing secrets.
$secrets = _get_secrets(['endpoint', 'token', 'room'], $defaults);

$site = $_ENV['PANTHEON_SITE_NAME'];
$env_link = '<a href="https://' . $_ENV['PANTHEON_ENVIRONMENT'] . '-' . $_ENV['PANTHEON_SITE_NAME'] . '.pantheonsite.io">' . $_ENV['PANTHEON_ENVIRONMENT'] . '</a>';
$user_email = $_POST['user_email'];
$workflow = ucfirst($_POST['stage']) . ' ' . str_replace('_', ' ',  $_POST['wf_type']);
$dashboard_link = '<a href="https://dashboard.pantheon.io/sites/'. $_ENV['PANTHEON_SITE_NAME'] .'#'. $_ENV['PANTHEON_ENVIRONMENT'] .'/deploys">View Dashboard</a>';

$data = [
  'site' => $site,
  'env' => $_ENV['PANTHEON_ENVIRONMENT'],
  'user' => $user_email,
  'workflow' => $workflow,
  'dashboard' => $dashboard_link,
];
// Customize the message based on the workflow type.  Note that notify_matrix.php
// must appear in your pantheon.yml for each workflow type you wish to send notifications on.
switch($_POST['wf_type']) {
  case 'deploy':
    // Find out what tag we are on and get the annotation.
    $deploy_tag = `git describe --tags`;
    $deploy_message = $_POST['deploy_message'];

    // Prepare the slack payload as per:
    // https://api.slack.com/incoming-webhooks
    $text = 'Deploy to the '. $env_link;
    $text .= ' environment of '. $site .' by '. $user_email .' complete!';
    $text .= ' ' . $dashboard_link;
    $text .= ' ' . $deploy_message;
    break;

  case 'sync_code':
    // Get the committer, hash, and message for the most recent commit.
    $committer = `git log -1 --pretty=%cn`;
    $email = `git log -1 --pretty=%ce`;
    $message = `git log -1 --pretty=%B`;
    $hash = `git log -1 --pretty=%h`;

    $text = 'Code sync to the ' . $env_link . ' environment of ' . $site . ' by ' . $user_email . "<br/>\n";
    $text .= 'Most recent commit: ' . rtrim($hash) . ' by ' . rtrim($committer) . ': ' . $message;
    break;

  case 'clear_cache':
    $text = 'Cleared caches on the ' . $env_link . ' environment of ' . $site . "<br/>\n";
    break;

  default:
    $text = $_POST['qs_description'];
    break;
}

_matrix_notification($secrets['endpoint'], $secrets['room'], $secrets['token'], $text, $data);

/**
 * Get secrets from secrets file.
 *
 * @param array $requiredKeys  List of keys in secrets file that must exist.
 */
function _get_secrets($requiredKeys, $defaults)
{
  $secretsFile = $_SERVER['HOME'] . '/files/private/secrets.json';
  if (!file_exists($secretsFile)) {
    $secretsFile = __DIR__ . '/secrets.json';
    if (!file_exists($secretsFile)) {
      die('No secrets file found. Aborting!');
    }
  }
  $secretsContents = file_get_contents($secretsFile);
  $all_secrets = json_decode($secretsContents, 1);
  if ($all_secrets == false) {
    die('Could not parse json in secrets file. Aborting!');
  }
  $secrets = $all_secrets['matrix'] + $defaults;
  $missing = array_diff($requiredKeys, array_keys($secrets));
  if (!empty($missing)) {
    die('Missing required keys in json secrets file: ' . implode(',', $missing) . '. Aborting!');
  }
  return $secrets;
}

/**
 * Send a notification to matrix
 */
function _matrix_notification($endpoint, $room, $token, $body, $data = false)
{
  $url = 'https://' . $endpoint . '/_matrix/client/r0/rooms/' . $room . '/send/m.room.message';
  $plain = strip_tags($body);

  $post = [
    'msgtype' => 'm.text',
    'body' => $plain,
    'formatted_body' => $body,
    'format' => "org.matrix.custom.html",
  ];
  if ($data) {
    $post['data'] = $data;
  }
  $headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
  ];
  $payload = json_encode($post);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  // Watch for messages with `terminus workflows watch --site=SITENAME`
  print("\n==== Posting to Matrix ====\n");
  $result = curl_exec($ch);
  print("RESULT: $result");
  // $payload_pretty = json_encode($post,JSON_PRETTY_PRINT); // Uncomment to debug JSON
  // print("JSON: $payload_pretty"); // Uncomment to Debug JSON
  print("\n===== Post Complete! =====\n");
  curl_close($ch);
}
