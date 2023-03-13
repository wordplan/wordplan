<?php defined('ABSPATH') || exit;?>

<div class="conns-empty">
	<h2>
	<?php
		if(!empty($_REQUEST['s']))
		{
			$search_text = sanitize_text_field(wp_unslash($_REQUEST['s']));
			printf('%s <b>%s</b>', __('Conns by query is not found, query:', 'wordplan'), $search_text);
		}
		else
		{
			esc_html_e('Conns not found.', 'wordplan');
		}
	?>
	</h2>

	<p>
		<?php esc_html_e( 'To continue working, you must add at least one conn from Megaplan.', 'wordplan' ); ?>
	</p>

	<a href="<?php echo esc_url_raw(add_query_arg(['page' => 'wordplan_add'])); ?>" class="mt-2 btn-lg d-inline-block page-title-action">
		<?php _e('Add conns', 'wordplan'); ?>
	</a>

</div>