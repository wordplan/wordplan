<?php defined('ABSPATH') || exit;?>

<div class="row g-0">
    <div class="col-24">
        <div class="p-2 pt-3 pb-3">
            <a href="<?php echo esc_url_raw(wordplan()->admin()->utilityAdminConnsGetUrl('dashboard', $_GET['conn_id'])); ?>" class="wp-heading-inline text-decoration-none fs-5"><?php _e('Dashboard', 'wordplan'); ?></a>
        </div>
    </div>
</div>

<div class="row g-0">
    <div class="col-24 col-lg-17">
        <div class="pe-0 pe-lg-2">
            <?php do_action('wordplan_admin_before_conns_dashboard_show'); ?>

            <?php do_action('wordplan_admin_conns_dashboard_show'); ?>

            <?php do_action('wordplan_admin_after_conns_dashboard_show'); ?>
        </div>
    </div>
    <div class="col-24 col-lg-7">
        <?php do_action('wordplan_admin_conns_dashboard_sidebar_show'); ?>
    </div>
</div>