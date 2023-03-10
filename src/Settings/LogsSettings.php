<?php namespace Wordplan\Settings;

defined('ABSPATH') || exit;

use Wordplan\Settings\Abstracts\SettingsAbstract;

/**
 * LogsSettings
 *
 * @package Wordplan\Settings
 */
class LogsSettings extends SettingsAbstract
{
	/**
	 * LogsSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('logs');
	}
}