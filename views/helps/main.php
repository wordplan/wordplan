<?php defined('ABSPATH') || exit; ?>

<?php
printf
(
    '<p>%s %s</p>',
    __('If no understand how Integration with Megaplan works, how to use and supplement it, can view the documentation.', 'wordplan'),
    __('Documentation contains all kinds of resources such as code snippets, user guides and more.', 'wordplan')
);
?>

<a href="//wordplan.ru/docs" target="_blank" class="button button-primary">
    <?php _e('Documentation', 'wordplan'); ?>
</a>

<?php
    if(has_action('wordplan_admin_help_main_show'))
    {
        echo '<hr>';
        do_action('wordplan_admin_help_main_show');
    }
?>