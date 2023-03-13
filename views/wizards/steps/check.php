<?php defined('ABSPATH') || exit;

use Wordplan\Admin\Wizards\Setup\Check;

if(!isset($args['step']))
{
    return;
}

/** @var Check $wizard */
$step = $args['step'];
$available = true;
?>

<h1><?php _e('Welcome to WORDPLAN!', 'wordplan'); ?></h1>
<p><?php _e('Thank you for choosing WORDPLAN to website! This is only complete solution for integrating WordPress with Megaplan.', 'wordplan'); ?></p>

<p><?php _e('This quick setup wizard will help you configure the basic settings.', 'wordplan'); ?></p>

<?php if(10 > wordplan()->environment()->get('php_max_execution_time')) : ?>
<?php $available = false; ?>
<p><?php _e('PHP scripts execution time is less than 10 seconds. WC1C requires at least 20. Set php_max_execution_time to more than 20 seconds.', 'wordplan'); ?></p>
<?php endif; ?>

<?php if($available) : ?>
<p><strong><?php _e('Its should not take longer than five minutes.', 'wordplan'); ?></strong></p>
<p class="mt-4 actions step">
    <a href="<?php echo esc_url($step->wizard()->getNextStepLink()); ?>" class="button button-primary button-large button-next">
        <?php _e('Lets Go!', 'wordplan'); ?>
    </a>
</p>
<?php endif; ?>

<?php if(!$available) : ?>
    <p><strong><?php _e('Need to fix the compatibility errors and return to the setup wizard.', 'wordplan'); ?></strong></p>
<?php endif;
