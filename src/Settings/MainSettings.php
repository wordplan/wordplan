<?php namespace Wordplan\Settings;

defined('ABSPATH') || exit;

use Wordplan\Settings\Abstracts\SettingsAbstract;

/**
 * Class MainSettings
 *
 * @package Wordplan\Settings
 */
class MainSettings extends SettingsAbstract
{
	/**
	 * Main constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('main');
	}
}