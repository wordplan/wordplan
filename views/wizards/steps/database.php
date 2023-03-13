<?php defined('ABSPATH') || exit;

use Wordplan\Admin\Wizards\Setup\Database;

if(!isset($args['step']))
{
    return;
}

/** @var Database $wizard */
$step = $args['step'];

?>

<h1><?php _e('Creating tables in the database', 'wordplan'); ?></h1>
<p><?php _e('If continue, the required tables will be created in the database.', 'wordplan'); ?></p>

<form method="post" action="">
<p class="mt-4 actions step">
    <?php wp_nonce_field('wordplan-admin-wizard-database', '_wordplan-admin-nonce'); ?>
    <input type="submit" name="submit" id="submit" class="button button-primary button-large button-next" value="<?php _e('Lets Go!', 'wordplan'); ?>">
</p>
</form>