<?php namespace Wordplan\Admin\Add;

defined('ABSPATH') || exit;

use Exception;
use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Abstracts\FormAbstract;
use Wordplan\Data\Entities\Conn;
use Wordplan\Traits\ConnsUtilityTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Class Form
 *
 * @package Wordplan\Admin\Add
 */
abstract class Form extends FormAbstract
{
	use SingletonTrait;
	use ConnsUtilityTrait;
	use UtilityTrait;

	/**
	 * Lazy load
	 *
	 * @throws Exception
	 */
	protected function init()
	{
		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_name'], 5);

		$this->loadFields();
		$this->save();

		add_action('wordplan_admin_show', [$this, 'outputForm']);
	}

	/**
	 * Add for name
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function init_fields_name(array $fields): array
	{
		$fields['name'] =
		[
			'title' => __('Name', 'wordplan'),
			'type' => 'text',
			'description' => __('An arbitrary name for the connection. Used for reference purposes.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		$fields['host'] =
		[
			'title' => __('Host', 'wordplan'),
			'type' => 'text',
			'description' => __('Megaplan address. You can copy from the browser line, leaving only the domain name. Example: mp97470037.megaplan.ru', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		return $fields;
	}

	/**
	 * Save
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save()
	{
		$post_data = $this->getPostedData();

		if(!isset($post_data['_wordplan-admin-nonce-add']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wordplan-admin-nonce-add'], 'wordplan-admin-add-save'))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Connection error. Please retry.', 'wordplan')
				]
			);

			return false;
		}

		foreach($this->getFields() as $key => $field)
		{
			$field_type = $this->getFieldType($field);

			if('title' === $field_type || 'raw' === $field_type)
			{
				continue;
			}

			try
			{
				$this->saved_data[$key] = $this->getFieldValue($key, $field, $post_data);
			}
			catch(Exception $e)
			{
				wordplan()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => $e->getMessage()
					]
				);
			}
		}

		$data = $this->getSavedData();

		if(empty($data['name']))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Create connection error. Name is required.', 'wordplan')
				]
			);

			return false;
		}

		if(empty($data['host']))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Create connection error. Host is required.', 'wordplan')
				]
			);

			return false;
		}

		$conn_type = 'user';
		if(!empty($data['uuid']))
		{
			$conn_type = 'app';
		}

		if('user' === $conn_type)
		{
			if(empty($data['login']))
			{
				wordplan()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Create connection error. Login is required.', 'wordplan')
					]
				);

				return false;
			}

			if(empty($data['password']))
			{
				wordplan()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Create connection error. Password is required.', 'wordplan')
					]
				);

				return false;
			}
		}

		if('app' === $conn_type && empty($data['token']))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Create connection error. APP Token is required.', 'wordplan')
				]
			);

			return false;
		}

		$conn = new Conn();
		$data_storage = $conn->getStorage();

		$conn->setConnType($conn_type);
		$conn->setStatus('draft');

		if('yes' === wordplan()->settings()->get('conns_unique_name', 'yes') && $data_storage->is_existing_by_name($data['name']))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Create connection error. Name is exists.', 'wordplan')
				]
			);

			return false;
		}

		$conn->setName($data['name']);
		$conn->setMegaplanHost($data['host']);

		if('user' === $conn_type)
		{
			$conn->setMegaplanLogin($data['login']);
			$conn->setMegaplanPassword($data['password']);
		}

		if('app' === $conn_type)
		{
			$conn->setMegaplanApp($data['uuid']);
			$conn->setMegaplanAppToken($data['token']);
		}

		try
		{
			$conn->megaplan();
		}
		catch(\Throwable $e)
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => $e->getMessage()
				]
			);

			return false;
		}

		$conn->saveMetaData();

		if($conn->save())
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'update',
					'data' => __('Create connection success. connection id:', 'wordplan') . ' ' . $conn->getId()
					           . ' (<a href="' . $this->utilityAdminConnsGetUrl('dashboard', $conn->getId()) . '">' . __('edit conn', 'wordplan') . '</a>)'
				]
			);

			$this->setSavedData([]);
			return true;
		}

		wordplan()->admin()->notices()->create
		(
			[
				'type' => 'error',
				'data' => __('Create connection error. Please retry saving or change fields.', 'wordplan')
			]
		);

		return false;
	}

	/**
	 * Form show
	 */
	public function outputForm()
	{
		$args =
		[
			'object' => $this,
			'back_url' => $this->utilityAdminConnsGetUrl()
		];

		wordplan()->views()->getView('add/form.php', $args);
	}
}