<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Exception;
use Wordplan\Abstracts\FormAbstract;

/**
 * Class DeleteForm
 *
 * @package Wordplan\Admin\Conns
 */
class DeleteForm extends FormAbstract
{
	/**
	 * DeleteForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('conns-delete');

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_main'], 10);

		$this->loadFields();
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
		$fields['accept'] =
		[
			'title' => __('Delete confirmation', 'wordplan'),
			'type' => 'checkbox',
			'label' => sprintf
            (
                "%s<hr>%s",
                __('I confirm that Conn will be permanently and irrevocably deleted from WordPress.', 'wordplan'),
                __('The directory with files for conn from the FILE system will be completely removed.', 'wordplan')
            ),
			'default' => 'no',
		];

		return $fields;
	}

	/**
	 * Form show
	 */
	public function outputForm()
	{
		$args =
		[
			'object' => $this
		];

		wordplan()->views()->getView('conns/delete_form.php', $args);
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

		if(!isset($post_data['_wordplan-admin-nonce-conns-delete']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wordplan-admin-nonce-conns-delete'], 'wordplan-admin-conns-delete-save'))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Delete error. Please retry.', 'wordplan')
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
		}

		$data = $this->getSavedData();

		if(!isset($data['accept']) || $data['accept'] !== 'yes')
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Delete error. Confirmation of final deletion is required.', 'wordplan')
				]
			);

			return false;
		}

		return true;
	}
}