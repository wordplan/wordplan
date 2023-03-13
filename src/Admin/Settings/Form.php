<?php namespace Wordplan\Admin\Settings;

defined('ABSPATH') || exit;

use Exception;
use Digiom\Woplucore\Interfaces\SettingsInterface;
use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Abstracts\FormAbstract;

/**
 * Class Form
 *
 * @package Wordplan\Admin\Settings
 */
abstract class Form extends FormAbstract
{
	use SingletonTrait;

	/**
	 * @var SettingsInterface
	 */
	public $settings;

	/**
	 * @return SettingsInterface
	 */
	public function getSettings()
	{
		return $this->settings;
	}

	/**
	 * @param SettingsInterface $settings
	 */
	public function setSettings($settings)
	{
		$this->settings = $settings;
	}

	/**
	 * Lazy load
	 *
	 * @throws Exception
	 */
	protected function init()
	{
		$this->loadFields();
		$this->getSettings()->init();
		$this->loadSavedData($this->getSettings()->get());
		$this->save();

		add_action('wordplan_admin_show', [$this, 'outputForm']);
	}

	/**
	 * Save
	 *
	 * @return bool
	 */
	public function save()
	{
		$post_data = $this->getPostedData();

		if(!isset($post_data['_wordplan-admin-nonce']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wordplan-admin-nonce'], 'wordplan-admin-settings-save'))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Save error. Please retry.', 'wordplan')
				]
			);

			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->getFields() as $key => $field)
		{
			if('title' === $this->getFieldType($field))
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

		try
		{
			$this->getSettings()->set($this->getSavedData());
			$this->getSettings()->save();
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

			return false;
		}

		wordplan()->admin()->notices()->create
		(
			[
				'type' => 'update',
				'data' => __('Save success.', 'wordplan')
			]
		);

		return true;
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

		wordplan()->views()->getView('settings/form.php', $args);
	}
}