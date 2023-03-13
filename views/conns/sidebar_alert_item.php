<?php defined('ABSPATH') || exit; ?>

<?php do_action('wordplan_admin_conns_before_sidebar_alert_item_show'); ?>

<div class="border-0 p-2 alert alert-<?php if(isset($args['type'])) printf('%s', wp_kses_post($args['type'])); ?>" style="max-width: 100%; <?php if(isset($args['css'])) printf('%s', wp_kses_post($args['css'])); ?>">
    <?php if(isset($args['header'])): ?>
        <?php printf('%s', wp_kses_post($args['header'])); ?>
    <?php endif; ?>
    <?php if(isset($args['body'])): ?>
        <?php printf('%s', wp_kses_post($args['body'])); ?>
    <?php endif; ?>
</div>

<?php do_action('wordplan_admin_conns_after_sidebar_alert_item_show'); ?>