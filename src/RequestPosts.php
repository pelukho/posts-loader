<?php

namespace App\PostsLoader;

class RequestPosts
{
    protected string $apiUrl = 'https://my.api.mockaroo.com/posts.json';

    public array $args = [
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ];

    public function makeRequest(): array
    {
        if (empty($_ENV['X_Api_Key'])) {
            return [];
        }

        $this->args['headers']['X-Api-Key'] = $_ENV['X_Api_Key'];

        $response = wp_remote_get($this->apiUrl, $this->args);

        if (is_array($response) && !is_wp_error($response)) {
            return json_decode($response['body'], true);
        }

        return [];
    }
}