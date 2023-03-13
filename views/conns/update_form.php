<?php defined('ABSPATH') || exit;?>

<?php do_action('wordplan_admin_conns_update_form_before_show'); ?>

<div class="row g-0">
    <div class="col-24 col-lg-17 p-0">
        <div class="pe-0 pe-lg-2">
            <form method="post" action="<?php echo esc_url(add_query_arg('form', $args['object']->getId())); ?>">
                <?php wp_nonce_field('wordplan-admin-conns-update-save', '_wordplan-admin-nonce'); ?>
                <div class="bg-white p-2 rounded-3 wordplan-toc-container section-border">
                    <table class="form-table wordplan-admin-form-table">
                        <?php $args['object']->generateHtml($args['object']->getFields(), true); ?>
                    </table>
                </div>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save conn', 'wordplan'); ?>">
                </p>
            </form>
        </div>
    </div>
    <div class="col-24 col-lg-7 p-0">
        <?php do_action('wordplan_admin_conns_update_sidebar_show'); ?>
    </div>
</div>

<?php do_action('wordplan_admin_conns_update_form_after_show'); ?>
