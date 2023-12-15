<?php

namespace App\PostsLoader;

use WP_Error;
use WP_Query;

class AddPosts
{
    public function insertPosts(array $posts): void
    {
        if (empty($posts)) {
            return;
        }

        $date = new \DateTime();
        $date->modify('-1 month');

        foreach ($posts as $post) {
            if ($this->isPostExist($post['title'])) {
                continue;
            }

            // Creating category
            $categoryId = $this->insertCategory($post['category']);

            $postArgs = [
                'post_title' => wp_strip_all_tags($post['title']),
                'post_content' => $post['content'],
                'post_status' => 'publish',
                'post_author' => 1,
                'post_category' => !is_wp_error($categoryId) ? [$categoryId] : [],
                'post_type' => 'post',
                'post_date' => $date->format('Y-m-d h:i:s'),
            ];

            // Insert the post into the database
            $postId = wp_insert_post($postArgs);

            update_post_meta($postId, 'site_link', $post['site_link']);
            update_post_meta($postId, 'rating', $post['rating']);

            // Adding attach
            $this->insertAttachment($postId, $post['image']);
        }
    }

    protected function insertCategory(string $categoryName): int|WP_Error
    {
        include_once ABSPATH . '/wp-admin/includes/taxonomy.php';

        // Hope that every post has category name
        return wp_create_category($categoryName);
    }

    protected function insertAttachment(int $postId, string $image): void
    {
        $uploadDir = wp_upload_dir();
        $imageData = file_get_contents($image);
        $filename = basename($image);
        $filename = !preg_match('~\.(jpg|jpeg|gif|png)(\?.*)?$~', $filename) ? $filename . '.jpg' : $filename;

        if (wp_mkdir_p($uploadDir['path'])) {
            $file = $uploadDir['path'] . '/' . $filename;
        } else {
            $file = $uploadDir['basedir'] . '/' . $filename;
        }

        file_put_contents($file, $imageData);

        $filetype = wp_check_filetype($filename);

        $attachment = [
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'guid' => $uploadDir['url'] . '/' . basename($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        ];

        $attachId = wp_insert_attachment($attachment, $file, $postId);

        include_once ABSPATH . 'wp-admin/includes/image.php';

        $attachData = wp_generate_attachment_metadata($attachId, $file);

        wp_update_attachment_metadata($attachId, $attachData);
        set_post_thumbnail($postId, $attachId);
    }

    protected function isPostExist(string $title): bool
    {
        $args = [
            'post_type' => 'post',
            'title' => $title,
            'post_status' => 'all',
            'posts_per_page' => 1,
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'orderby' => 'post_date ID',
            'order' => 'ASC',
        ];

        $query = new WP_Query($args);

        return !empty($query->post);
    }
}