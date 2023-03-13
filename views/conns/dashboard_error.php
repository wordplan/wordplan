<?php defined('ABSPATH') || exit;

$label = __('Back to conns list', 'wordplan');
wordplan()->views()->adminBackLink($label, $args['back_url']);

?>

<?php
$title = __('Error', 'wordplan');
$title = apply_filters('wordplan_admin_conns_update_error_title', $title);
$text = __('Update is not available. Conn not found or unavailable.', 'wordplan');
$text = apply_filters('wordplan_admin_conns_update_error_text', $text);
?>

<div class="wordplan-conns-alert mb-2 mt-2">
    <h3><?php esc_html_e($title); ?></h3>
    <p><?php esc_html_e($text); ?></p>
</div>