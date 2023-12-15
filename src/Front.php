<?php

namespace App\PostsLoader;

class Front
{
    public static function addStyles(): void
    {
        global $post;

        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'show_posts')) {
            wp_enqueue_style(
                'articles',
                plugin_dir_url(__FILE__) . '../public/styles.css',
                [],
                filemtime(plugin_dir_path(__FILE__) . '/../public/styles.css')
            );
        }
    }

    public static function addShortcode($atts = []): string
    {
        $defaults = [
            'title' => '',
            'count' => 3,
            'sort' => 'date',
            'ids' => ''
        ];

        $atts = shortcode_atts($defaults, $atts, 'show_posts');

        $default_sorts = ['date', 'rating', 'title'];
        $order_by = in_array($atts['sort'], $default_sorts) ? $atts['sort'] : 'date';

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => $order_by,
            'posts_per_page' => absint($atts['count']),
        ];

        if (!empty($atts['ids'])) {
            $args['post__in'] = [$atts['ids']];
        }

        if ('rating' === $atts['sort']) {
            $args['meta_key'] = 'rating';
            $args['orderby'] = 'meta_value_num';
            $args['meta_type'] = 'DECIMAL';
        }

        $query = new \WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="articles">';

            if (!empty($atts['title'])) {
                printf('<h2 class="articles__heading">%s</h2>', $atts['title']);
            }

            while ($query->have_posts()) {
                $query->the_post();

                include plugin_dir_path(__FILE__) . '/../inc/content.php';
            }

            echo '</div>';
        }

        return ob_get_clean();
    }
}