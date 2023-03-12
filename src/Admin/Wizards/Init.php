<?php namespace Wordplan\Admin\Wizards;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Init
 *
 * @package Wordplan\Admin\Wizards
 */
final class Init
{
	use SingletonTrait;

	/**
	 * Init constructor.
	 */
	public function __construct()
	{
		/**
		 * Setup
		 */
		if('setup' === get_option('wordplan_wizard', false))
		{
			SetupWizard::instance();
		}

		/**
		 * Update
		 */
		if('update' === get_option('wordplan_wizard', false))
		{
			UpdateWizard::instance();
		}
	}
}