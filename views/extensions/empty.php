<?php defined('ABSPATH') || exit; ?>

<div class="extensions-alert mb-2 mt-2">
    <h3><?php _e('Extensions not found.', 'wordplan'); ?></h3>
    <p><?php _e('As soon as the extensions are installed, they will appear in this section.', 'wordplan'); ?></p>

	<?php
	printf
	(
		'<p>%s %s</p>',
		__('Information about all available official extensions is available on the website:', 'wordplan'),
		'<a href="https://wordplan.ru/extensions" target=_blank>https://wordplan.ru/extensions</a>'
	);
	?>
</div>