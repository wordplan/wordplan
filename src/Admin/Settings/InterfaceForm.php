<?php namespace Wordplan\Admin\Settings;

defined('ABSPATH') || exit;

use Wordplan\Exceptions\Exception;
use Wordplan\Settings\InterfaceSettings;

/**
 *  InterfaceForm
 *
 * @package Wordplan\Admin
 */
class InterfaceForm extends Form
{
	/**
	 * InterfaceForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('settings-interface');
		$this->setSettings(new InterfaceSettings());

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_interface'], 10);

		$this->init();
	}
	/**
	 * Add for Interface
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_interface($fields): array
	{
		$fields['admin_interface'] =
		[
			'title' => __('Changing the interface', 'wordplan'),
			'type' => 'checkbox',
			'label' => __('Allow changes to WordPress dashboard interface?', 'wordplan'),
			'description' => sprintf
			(
				'%s <hr>%s',
				__('If enabled, new features will appear in the WordPress interface according to the interface change settings.', 'wordplan'),
				__('If interface modification is enabled, it is possible to change settings for individual features, users, and roles. If disabled, features will be disabled globally for everyone and everything.', 'wordplan')
			),
			'default' => 'yes'
		];

		return $fields;
	}
}