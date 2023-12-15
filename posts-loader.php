<?php
/**
 * Plugin Name:       Posts Loader
 * Description:       Load posts once per day using CRON
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    return;
}

require_once __DIR__ . '/vendor/autoload.php';

$rootFile = __FILE__;

\App\PostsLoader\Application::start($rootFile);
