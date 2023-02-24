<?php
/**
 * Plugin Name: WordPlan
 * Plugin URI: https://wordpress.org/plugins/wordplan
 * Description: Implementation of a mechanism for flexible exchange of various data between Megaplan and a site running WordPress.
 * Version: 0.1.0
 * Requires at least: 5.2
 * Requires PHP: 7.0
 * Text Domain: wordplan
 * Domain Path: /assets/languages
 * Copyright: WordPlan team © 2023
 * Author: WordPlan team
 * Author URI: https://github.com/wordplan
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wordplan
 **/
namespace
{
	defined('ABSPATH') || exit;

	if(version_compare(PHP_VERSION, '7.0') < 0)
	{
		return false;
	}

	if(false === defined('WORDPLAN_PLUGIN_FILE'))
	{
		define('WORDPLAN_PLUGIN_FILE', __FILE__);

		include_once __DIR__ . '/vendor/autoload.php';

		/**
		 * Main instance of WordPlan
		 *
		 * @return Wordplan\Core
		 */
		function wordplan(): Wordplan\Core
		{
			return Wordplan\Core::instance();
		}
	}
}

/**
 * @package Wordplan
 */
namespace Wordplan
{
	$loader = new \Digiom\Woplucore\Loader();

	try
	{
		$loader->addNamespace(__NAMESPACE__, plugin_dir_path(__FILE__) . 'src');

		$loader->register(__FILE__);

		$loader->registerActivation([Activation::class, 'instance']);
		$loader->registerDeactivation([Deactivation::class, 'instance']);
		$loader->registerUninstall([Uninstall::class, 'instance']);
	}
	catch(\Throwable $e)
	{
		trigger_error($e->getMessage());
		return false;
	}

    wordplan()->register(new Context(), $loader);
}