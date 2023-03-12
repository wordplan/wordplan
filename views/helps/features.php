<?php defined('ABSPATH') || exit; ?>

<h2><?php _e( 'Not a feature?', 'wordplan' ); ?></h2>

<p>
	<?php _e('First of all, you need to make sure - whether the necessary opportunity is really missing.', 'wordplan'); ?>
	<?php _e('It may be worth looking at the available settings or reading the documentation.', 'wordplan'); ?>
</p>

<p>
	<?php _e('Also, before requesting an opportunity, you need to make sure that:', 'wordplan'); ?>
</p>

<ul>
    <li><?php _e('Is the required feature added in WORDPLAN updates.', 'wordplan'); ?></li>
    <li><?php _e('Whether the possibility is implemented by an additional extension to WORDPLAN.', 'wordplan'); ?></li>
    <li><?php _e('Whether the desired opportunity is waiting for its implementation.', 'wordplan'); ?></li>
</ul>

<p>
	<?php _e('If the feature is added in WORDPLAN updates, you just need to install the updated version.', 'wordplan'); ?>
</p>

<p>
	<?php _e('But if the feature is implemented in an extension to WORDPLAN, then this feature should not be expected as part of WORDPLAN and you need to install the extension.', 'wordplan'); ?>
	<?php _e('Because the feature implemented in the extension is so significant that it needed to create an extension for it.', 'wordplan'); ?>
</p>

<p>
	<a href="//wordplan.ru/features" class="button" target="_blank">
		<?php _e('Features', 'wordplan'); ?>
	</a>
    <a href="//wordplan.ru/extensions" class="button" target="_blank">
		<?php _e('Extensions', 'wordplan'); ?>
    </a>
</p>