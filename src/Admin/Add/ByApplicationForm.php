<?php namespace Wordplan\Admin\Add;

defined('ABSPATH') || exit;

use Exception;

/**
 * Class ByApplicationForm
 *
 * @package Wordplan\Admin\Add
 */
class ByApplicationForm extends Form
{
	/**
	 * CreateForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('add-by-token');

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
		$fields['title_token'] =
		[
			'title' => __('Connect by Application', 'wordplan'),
			'type' => 'title',
			'description' => sprintf
			(
				'%s %s',
				__('Connection to Megaplan using a token generated on the Megaplan side.', 'wordplan'),
				__('The token must be for the specified unique application identifier.', 'wordplan')
			)
		];

		$fields['uuid'] =
		[
			'title' => __('Uuid', 'wordplan'),
			'type' => 'text',
			'description' => __('Unique application identifier from Megaplan.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		$fields['token'] =
		[
			'title' => __('Token', 'wordplan'),
			'type' => 'text',
			'description' => __('The token can be generated in Megaplan.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		return $fields;
	}
}