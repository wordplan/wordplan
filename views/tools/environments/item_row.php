<?php defined('ABSPATH') || exit; ?>

<tr class="">
    <td class="" style="width: 40%;">
        <b><?php esc_html_e($args['title']); ?></b>
    </td>
    <td class="">
	    <?php
	        esc_html_e(apply_filters('wordplan_admin_report_data_row_print', $args['data']));
	    ?>
    </td>
</tr>