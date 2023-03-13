<?php namespace Wordplan;

defined('ABSPATH') || exit;

/**
 * Activation
 *
 * @package Wordplan
 */
final class Activation extends \Digiom\Woplucore\Activation
{
	public function __construct()
	{
		if(false === get_option('wordplan_version', false))
		{
			update_option('wordplan_wizard', 'setup');

            wordplan()->admin()->notices()->create
			(
				[
					'id' => 'activation_welcome',
					'dismissible' => false,
					'type' => 'info',
					'data' => __('WordPlan successfully activated. You have made the right choice to integrate the site with Megaplan (plugin number one)!', 'wordplan'),
					'extra_data' => sprintf
					(
						'<p>%s <a href="%s">%s</a></p>',
						__('The basic plugin setup has not been done yet, so you can proceed to the setup, which takes no more than 5 minutes.', 'wordplan'),
						admin_url('admin.php?page=wordplan'),
						__('Go to setting.', 'wordplan')
					)
				]
			);
		}

		if(false === get_option('wordplan_version_init', false))
		{
			update_option('wordplan_version_init', wordplan()->environment()->get('wordplan_version'));
		}
	}
}