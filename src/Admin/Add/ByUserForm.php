<?php namespace Wordplan\Admin\Add;

defined('ABSPATH') || exit;

use Exception;

/**
 * Class ByUserForm
 *
 * @package Wordplan\Admin\Add
 */
class ByUserForm extends Form
{
	/**
	 * CreateForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('add');

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_main'], 10);

		$this->init();
	}

	/**
	 * Add for Main
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function init_fields_main(array $fields): array
	{
		$fields['title_main'] =
		[
			'title' => __('Connect by User', 'wordplan'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s %s',
				__('Connection to Megaplan by login and password.', 'wordplan'),
				__('Login and password will not be saved. Instead, an access token will be generated.', 'wordplan'),
				__('All further manipulations will be performed using the user token.', 'wordplan')
			),
		];

		$fields['login'] =
		[
			'title' => __('Login', 'wordplan'),
			'type' => 'text',
			'description' => __('This login is used to enter the Megaplan service.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		$fields['password'] =
		[
			'title' => __('Password', 'wordplan'),
			'type' => 'text',
			'description' => __('Password from the entered login to enter the Megaplan service.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		return $fields;
	}
}