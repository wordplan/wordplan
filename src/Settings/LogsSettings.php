<?php namespace Wordplan\Settings;

defined('ABSPATH') || exit;

use Wordplan\Abstracts\SettingsAbstract;

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