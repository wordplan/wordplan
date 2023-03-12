<?php defined('ABSPATH') || exit;

use Wordplan\Admin\Settings\ConnectionForm;

/** @var ConnectionForm $object */
$object = $args['object'];

?>

<form method="post" action="">
    <div class="row g-0">
        <div class="col-24 col-lg-17">
            <div class="pe-0 pe-lg-2">
	            <?php wp_nonce_field('wordplan-admin-settings-save', '_wordplan-admin-nonce'); ?>
                <div class="section-border wordplan-admin-settings wordplan-admin-connection bg-white rounded-3 mt-2 mb-2 px-2">
                    <table class="form-table wordplan-admin-form-table wordplan-admin-settings-form-table">
						<?php
						if(isset($args) && is_array($args))
						{
							$args['object']->generateHtml($args['object']->getFields(), true);
						}
						?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-24 col-lg-7">
			<?php do_action('wordplan_admin_settings_activation_sidebar_before_show'); ?>

            <div class="alert alert-warning border-0 mb-4 mt-2 w-100">
                <h4 class="alert-heading mt-0 mb-1"><?php _e('Get code', 'wordplan'); ?></h4>
				<?php _e('The code can be obtained from the plugin website.', 'wordplan'); ?>
                <hr>
				<?php _e('Site:', 'wordplan'); ?> <a target="_blank" href="//wordplan.ru/market/code">wordplan.ru/market/code</a>
            </div>

			<?php do_action('wordplan_admin_settings_activation_sidebar_after_show'); ?>
        </div>
    </div>
</form>