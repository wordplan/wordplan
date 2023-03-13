<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Forms\InlineForm;
use Wordplan\Admin\Traits\ProcessConnTrait;
use Wordplan\Data\Storage;
use Wordplan\Traits\DatetimeUtilityTrait;
use Wordplan\Traits\SectionsTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Update
 *
 * @package Wordplan\Admin
 */
class Update
{
	use SingletonTrait;
	use ProcessConnTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;
	use SectionsTrait;

	/**
	 * Update constructor.
	 */
	public function __construct()
	{
		$this->setSectionKey('update_section');

		$default_sections['main'] =
		[
			'title' => __('Main', 'wordplan'),
			'visible' => true,
			'callback' => [MainUpdate::class, 'instance']
		];

		if(has_action('wordplan_admin_conns_update_sections'))
		{
			$default_sections = apply_filters('wordplan_admin_conns_update_sections', $default_sections);
		}

		$this->initSections($default_sections);
		$this->setCurrentSection('main');

		$conn_id = wordplan()->getVar($_GET['conn_id'], 0);

		if(false === $this->setConn($conn_id))
		{
			$this->process();
		}
		else
		{
			add_action('wordplan_admin_show', [$this, 'outputError'], 10);
			wordplan()->log()->notice('Conn update is not available.', ['configuration_id' => $conn_id]);
			return;
		}

		$this->route();

		add_action('wordplan_admin_show', [$this, 'output'], 10);
	}

	/**
	 *  Routing
	 */
	public function route()
	{
		$sections = $this->getSections();
		$current_section = $this->initCurrentSection();

		if(!array_key_exists($current_section, $sections) || !isset($sections[$current_section]['callback']))
		{
			add_action('wordplan_admin_conns_update_show', [$this, 'wrapError']);
		}
		else
		{
			add_action('wordplan_admin_before_conns_update_show', [$this, 'wrapSections'], 5);

			$callback = $sections[$current_section]['callback'];

			if(is_callable($callback, false, $callback_name))
			{
				$callback_obj = $callback_name();
				$callback_obj->setAccount($this->getConn());
				$callback_obj->process();
			}
		}
	}

	/**
	 * Update processing
	 */
	public function process()
	{
		$configuration = $this->getConn();

		$fields['name'] =
		[
			'title' => __('Conn name', 'wordplan'),
			'type' => 'text',
			'description' => __('Used for convenient distribution of multiple conns.', 'wordplan'),
			'default' => '',
			'class' => 'form-control form-control-sm',
			'button' => __('Rename', 'wordplan'),
		];

		$inline_args =
		[
			'id' => 'conns-name',
			'fields' => $fields
		];

		$inline_form = new InlineForm($inline_args);
		$inline_form->loadSavedData(['name' => $configuration->getName()]);

		if(isset($_GET['form']) && $_GET['form'] === $inline_form->getId())
		{
			$configuration_name = $inline_form->save();

			if(isset($configuration_name['name']))
			{
				$configuration->setDateModify(time());
				$configuration->setName($configuration_name['name']);

				$saved = $configuration->save();

				if($saved)
				{
					wordplan()->admin()->notices()->create
					(
						[
							'type' => 'update',
							'data' => __('Conn name update success.', 'wordplan')
						]
					);
				}
				else
				{
					wordplan()->admin()->notices()->create
					(
						[
							'type' => 'error',
							'data' => __('Conn name update error. Please retry saving or change fields.', 'wordplan')
						]
					);
				}
			}
		}

		add_action('wordplan_admin_conns_update_header_show', [$inline_form, 'outputForm'], 10);
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wordplan()->views()->getView('error.php');
	}

	/**
	 * Output error
	 */
	public function outputError()
	{
		$args['back_url'] = $this->utilityAdminConnsGetUrl('all');

		wordplan()->views()->getView('conns/update_error.php', $args);
	}

	/**
	 * Sections
	 *
	 * @return void
	 */
	public function wrapSections()
	{
		$args['object'] = $this;

		wordplan()->views()->getView('conns/update_sections.php', $args);
	}

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output()
	{
		$conns = new Storage('conn');
		$total_items = $conns->count();

		$args = [];

		if($total_items > 1)
		{
			$args['back_url'] = $this->utilityAdminConnsGetUrl('all');
		}

		wordplan()->views()->getView('conns/update.php', $args);
	}
}