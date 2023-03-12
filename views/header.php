<?php defined('ABSPATH') || exit; ?>

<div class="header bg-white rounded-bottom border-top border-light border-5 p-2 pb-3 fs-5">
    <a href="<?php echo esc_url_raw(wordplan()->admin()->utilityAdminConnsGetUrl('all')); ?>" class="wp-heading-inline text-decoration-none"><?php _e('Megaplan', 'wordplan'); ?></a>
    <?php do_action('wordplan_admin_header_items_show'); ?>
</div>