<?php defined('ABSPATH') || exit; ?>

<div class="wrap mt-0 me-0">
    <?php do_action('wordplan_admin_header_show'); ?>
</div>

<div class="wrap mt-0">
    <?php
        if(wordplan()->context()->isAdmin())
        {
            wordplan()->admin()->notices()->output();
        }
    ?>
    <?php do_action('wordplan_admin_show'); ?>
</div>