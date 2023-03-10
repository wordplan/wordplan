<?php namespace Wordplan\Settings;

defined('ABSPATH') || exit;

use Wordplan\Abstracts\SettingsAbstract;

/**
 * InterfaceSettings
 *
 * @package Wsklad\Settings
 */
class InterfaceSettings extends SettingsAbstract
{
	/**
	 * InterfaceSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('interface');
	}
}