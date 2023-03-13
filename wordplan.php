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
 * Author URI: https://wordplan.ru
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WordPress\Plugins
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

		$autoloader = __DIR__ . '/vendor/autoload.php';

		if(!is_readable($autoloader))
		{
			trigger_error('File is not found: ' . $autoloader);
			return false;
		}

		require_once $autoloader;

		/**
		 * For external use
		 *
		 * @return Wordplan\Core Main instance of core
		 */
		function wordplan(): Wordplan\Core
		{
			return Wordplan\Core();
		}
	}
}

/**
 * @package Wordplan
 */
namespace Wordplan
{
	/**
	 * For internal use
	 *
	 * @return Core Main instance of plugin core
	 */
	function core(): Core
	{
		return Core::instance();
	}

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

	$context = new Context(__FILE__, 'wordplan', $loader);

	core()->register($context);
}