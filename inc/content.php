<?php
$post_id = get_the_ID();
$rating = get_post_meta($post_id, 'rating', true);
$external_link = get_post_meta($post_id, 'site_link', true);
$bgImage = get_the_post_thumbnail_url() ? 'style="background-image: url(' . get_the_post_thumbnail_url() . ');"' : '';
$category = get_the_category();
$link = get_category_link($category[0]->term_id);
?>

<div class="articles__item">
    <div class="articles__image" <?php echo $bgImage; ?>></div>
    <div class="articles__content">
        <a href="<?php echo get_category_link($category[0]->term_id); ?>"
           class="articles__category"><?php echo $category[0]->name; ?></a>
        <h3 class="articles__title"><?php the_title(); ?></h3>
        <div class="articles__footer">
            <a href="<?php the_permalink(); ?>" class="articles__link">Read More</a>

            <?php if ($rating || $external_link) : ?>
                <div class="articles__meta">
                    <?php if ($rating) : ?>
                        <span class="star">⭐</span>
                        <span class="rating"><?php printf('%0.1f', $rating); ?></span>
                    <?php endif; ?>

                    <?php if ($external_link) : ?>
                        <a href="<?php echo esc_url($external_link); ?>" target="_blank" class="external-link">Visit
                            Site</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>