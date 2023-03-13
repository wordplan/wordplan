<?php defined('ABSPATH') || exit;?>

<form method="post" action="">
	<?php wp_nonce_field('wordplan-admin-conns-delete-save', '_wordplan-admin-nonce-conns-delete'); ?>
    <div class="mt-2 bg-white p-2 pt-1">
        <table class="form-table wordplan-admin-form-table">
            <?php
                if(isset($args) && is_array($args))
                {
                    $args['object']->generateHtml($args['object']->getFields(), true);
                }
            ?>
        </table>
    </div>
    <p class="submit">
	    <input type="submit" name="submit" id="submit" class="button button-danger" value="<?php _e('Delete', 'wordplan'); ?>">
    </p>
</form>