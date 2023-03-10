<?php namespace Wordplan\Abstracts;

defined('ABSPATH') || exit;

/**
 * SettingsAbstract
 *
 * @package Wordplan\Settings
 */
abstract class SettingsAbstract extends \Digiom\Woplucore\Abstracts\SettingsAbstract
{
	/**
	 * @var string Name option prefix in wp_options
	 */
	protected $option_name_prefix = 'wordplan_settings_';
}