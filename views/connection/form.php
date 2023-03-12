<?php defined('ABSPATH') || exit;

use Wordplan\Admin\Settings\ConnectionForm;

/** @var ConnectionForm $object */
$object = $args['object'];

?>

<form method="post" action="">
	<?php wp_nonce_field('wordplan-admin-settings-save', '_wordplan-admin-nonce'); ?>
    <?php if($object->status) : ?>
    <div class="wordplan-admin-settings section-border wordplan-admin-connection bg-white rounded-3 mt-2 mb-2 px-2">
        <table class="form-table wordplan-admin-form-table wordplan-admin-settings-form-table">
		    <?php $object->generateHtml($object->getFields(), true); ?>
        </table>
    </div>
    <?php endif; ?>
    <div class="submit p-0 mt-3">
	    <?php
	        $button = __('Connect by WORDPLAN site', 'wordplan');
            if($object->status)
            {
                $button = __('Disconnect', 'wordplan');
            }
        ?>

	    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e($button); ?>">
    </div>
</form>