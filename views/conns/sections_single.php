<?php defined('ABSPATH') || exit;?>

<div class="row g-0">
    <div class="col-24">
        <div class="p-2 pt-3 pb-3">
            <a href="<?php echo esc_url_raw(wordplan()->admin()->utilityAdminConnsGetUrl('dashboard', $_GET['conn_id'])); ?>" class="wp-heading-inline text-decoration-none fs-5"><?php _e('Dashboard', 'wordplan'); ?></a>
            >
            <span class="wp-heading-inline fs-5"><?php echo esc_html($args['name']); ?></span>
        </div>
    </div>
</div>

<?php do_action('wordplan_admin_before_conns_sections_single_show'); ?>

<?php do_action('wordplan_admin_conns_sections_single_show'); ?>

<?php do_action('wordplan_admin_after_conns_sections_single_show'); ?>