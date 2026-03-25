<?php

if (isset($_GET['test']) || isset($_COOKIE['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');

if ($env === 'development' && file_exists(__DIR__ . '/load_env.php')) {
    include_once __DIR__ . '/load_env.php';
}

$home = getenv('HOME') ?: ($_ENV['HOME'] ?? '');

if (!defined('REVISIONS_PATH')) {
    $env_value = getenv('REVISIONS_DIR_0') ?: ($_ENV['REVISIONS_DIR_0'] ?? null);
    if ($env_value) {
        $rev_path = $env_value;
    } else {
        $rev_path = $home ? $home . '/public_html/revisions_new' : dirname(__DIR__) . '/revisions_new';
    }
    define('REVISIONS_PATH', $rev_path);
}

if (!defined('JSON_FILE')) {
    $json_file = REVISIONS_PATH . '/json_data.json';
    define('JSON_FILE', $json_file);
}
if (!defined('JSON_FILE_ALL')) {
    $json_file_all = REVISIONS_PATH . '/json_data_all.json';
    define('JSON_FILE_ALL', $json_file_all);
}

// Initialize revisions directory if needed
if (!is_dir(REVISIONS_PATH)) {
    mkdir(REVISIONS_PATH, 0755, true);
}

// Ensure JSON data files exist

if (!file_exists(JSON_FILE)) {
    file_put_contents(JSON_FILE, '{}', LOCK_EX);
}

if (!file_exists(JSON_FILE_ALL)) {
    file_put_contents(JSON_FILE_ALL, '{}', LOCK_EX);
}

include_once __DIR__ . '/new_html_src/require.php';
require_once __DIR__ . "/json_data.php";
