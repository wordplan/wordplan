<?php defined('ABSPATH') || exit;?>

<div class="bg-white p-2 pt-3 pb-3 mt-2 rounded-3">
	<?php
	printf('%s <b>%s</b>', __('ID of the conn to be deleted:', 'wordplan'), $args['conn']->getId());
	?>
	<br/>
	<?php
	printf('%s <b>%s</b>', __('Name of the conn to be deleted:', 'wordplan'), $args['conn']->getName());
	?>
	<br/>
	<?php
	printf('%s <b>%s</b>', __('Path of the conn directory to be deleted:', 'wordplan'), $args['conn']->getUploadDirectory());
	?>
</div>

<div class="">
	<?php do_action('wordplan_admin_conns_form_delete_show'); ?>
</div>
