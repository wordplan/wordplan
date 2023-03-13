<?php namespace Wordplan\Admin\Settings;

defined('ABSPATH') || exit;

use Exception;
use Wordplan\Settings\MainSettings;

/**
 * Class MainForm
 *
 * @package Wordplan\Admin\Settings
 */
class MainForm extends Form
{
	/**
	 * MainForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('settings-main');
		$this->setSettings(new MainSettings());

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_conns'], 10);
		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_technical'], 10);
		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_api_megaplan'], 10);

		$this->init();
	}

	/**
	 * Add fields for Megaplan
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_api_megaplan($fields)
	{
		$fields['api_megaplan_title'] =
		[
			'title' => __('API Megaplan', 'wordplan'),
			'type' => 'title',
			'description' => __('Used for API connections.', 'wordplan'),
		];

		$fields['api_megaplan_host'] =
		[
			'title' => __('Default host', 'wordplan'),
			'type' => 'text',
			'description' => __('This host is used for API connection. Example: {hostname}.megaplan.ru', 'wordplan'),
			'default' => '',
			'css' => 'min-width: 255px;',
		];

		$fields['api_megaplan_force_https'] =
		[
			'title' => __('Force requests over HTTPS', 'wordplan'),
			'type' => 'checkbox',
			'label' => __('Enable HTTPS enforcement for requests to the Megaplan API?', 'wordplan'),
			'description' => __('If enabled, all API requests from the site to Megaplan will be made over the secure HTTPS protocol.', 'wordplan'),
			'default' => 'yes'
		];

		$fields['api_megaplan_timeout'] =
		[
			'title' => __('Timeout', 'wordplan'),
			'type' => 'text',
			'description' => __('This timeout is used for API connection. If the timeout is unknown, use the value: 30', 'wordplan'),
			'default' => '30',
			'css' => 'min-width: 111px;',
		];

		return $fields;
	}

	/**
	 * Add fields for Conns
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_conns($fields): array
	{
		$fields['conns_title'] =
		[
			'title' => __('Conns', 'wordplan'),
			'type' => 'title',
			'description' => __('Some settings for the conns.', 'wordplan'),
		];

		$fields['conns_unique_name'] =
		[
			'title' => __('Unique names', 'wordplan'),
			'type' => 'checkbox',
			'label' => __('Require unique names for conns?', 'wordplan'),
			'description' => __('If enabled, will need to provide unique names for the conns.', 'wordplan'),
			'default' => 'yes'
		];

		$fields['conns_show_per_page'] =
		[
			'title' => __('Number in the list', 'wordplan'),
			'type' => 'text',
			'description' => __('The number of displayed conns on one page.', 'wordplan'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		$fields['conns_draft_delete'] =
		[
			'title' => __('Deleting drafts without trash', 'wordplan'),
			'type' => 'checkbox',
			'label' => __('Enable deleting drafts without placing them in the trash?', 'wordplan'),
			'description' => __('If enabled, conns for connections in the draft status will be deleted without being added to the basket.', 'wordplan'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Add for Technical
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_technical($fields): array
    {
		$fields['technical_title'] =
		[
			'title' => __('Technical settings', 'wordplan'),
			'type' => 'title',
			'description' => __('Used to set up the environment.', 'wordplan'),
		];

		$fields['php_max_execution_time'] =
		[
			'title' => __('Maximum time for execution PHP', 'wordplan'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s <br /> %s <b>%s</b> <br /> %s',
				__('Value is seconds. wordplan will run until a time limit is set.', 'wordplan'),
				__('Server value:', 'wordplan'),
				wordplan()->environment()->get('php_max_execution_time'),
				__('If specify 0, the time limit will be disabled. Specifying 0 is not recommended, it is recommended not to exceed the server limit.', 'wordplan')
			),
			'default' => wordplan()->environment()->get('php_max_execution_time'),
			'css' => 'min-width: 100px;',
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum request size', 'wordplan'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wordplan'),
			'default' => wordplan()->environment()->get('php_post_max_size'),
			'css' => 'min-width: 100px;',
		];

		return $fields;
	}
}