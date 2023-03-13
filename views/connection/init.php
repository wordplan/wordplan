<?php defined('ABSPATH') || exit; ?>

<?php

$title = __('Connection', 'wordplan');

if(has_filter('wordplan_admin_settings_connect_title'))
{
    $title = apply_filters('wordplan_admin_settings_connect_title', $title);
}

$text = sprintf
(
    '<p>%s</p> %s',
    __('To receive support and official services, need to go through the authorization of external applications.', 'wordplan'),
    __('Authorization of an external app occurs by going to the official WORDPLAN and returning to the current site.', 'wordplan')
);

if(has_filter('wordplan_admin_settings_connect_text'))
{
    $text = apply_filters('wordplan_admin_settings_connect_text', $text);
}

?>

<div class="wordplan-conns-alert mb-2 mt-2">
    <h3><?php esc_html_e($title); ?></h3>
    <p><?php echo wp_kses_post($text); ?></p>
</div>