<?php namespace Wordplan\Settings;

defined('ABSPATH') || exit;

use Wordplan\Abstracts\SettingsAbstract;

/**
 * ConnectionSettings
 *
 * @package Wordplan\Settings
 */
class ConnectionSettings extends SettingsAbstract
{
	/**
	 * ConnectionSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('connection');
	}
}