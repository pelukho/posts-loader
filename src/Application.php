<?php

namespace App\PostsLoader;

use Symfony\Component\Dotenv\Dotenv;

class Application
{
    protected static string $rootFilePath = '';

    public static function start(string $rootFilePath): void
    {
        self::$rootFilePath = $rootFilePath;
        self::loadDotenv();
        self::activate();
        self::deactivate();
        self::loadStyles();
        self::makeShortcode();
    }

    public static function activationHandler(): void
    {
        wp_clear_scheduled_hook('pl_run_daily_request');
        wp_schedule_event(time(), 'daily', 'pl_run_daily_request');
    }

    public static function deactivationHandler(): void
    {
        wp_clear_scheduled_hook('pl_run_daily_request');
    }

    public static function addEvent(): void
    {
        $mockaroo = new RequestPosts();
        $addPosts = new AddPosts();

        $addPosts->insertPosts($mockaroo->makeRequest());
    }

    protected static function loadDotenv(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');
    }

    public static function activate(): void
    {
        add_action('pl_run_daily_request', ['App\PostsLoader\Application', 'addEvent']);
        register_activation_hook(self::$rootFilePath, ['App\PostsLoader\Application', 'activationHandler']);
    }

    public static function deactivate(): void
    {
        register_deactivation_hook(self::$rootFilePath, ['App\PostsLoader\Application', 'deactivationHandler']);
    }

    public static function loadStyles(): void
    {
        add_action('wp_enqueue_scripts', ['App\PostsLoader\Front', 'addStyles']);
    }

    public static function makeShortcode(): void
    {
        add_shortcode('show_posts', ['App\PostsLoader\Front', 'addShortcode']);
    }
}