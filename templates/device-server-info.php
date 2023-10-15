<?php

/**
 * @var $post WP_Post
 */

$parent = get_post($post->post_parent);

?>

<a href="<?php echo get_edit_post_link($parent->ID) ?>" target="_blank" style="font-size: 1.25rem; text-decoration: none;">
    <?php echo $parent->post_title ?>
</a>