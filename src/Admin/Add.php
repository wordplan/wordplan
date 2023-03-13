<?php namespace Wordplan\Admin;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Add\ByUserForm;
use Wordplan\Admin\Add\ByApplicationForm;
use Wordplan\Traits\SectionsTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Add
 *
 * @package Wordplan\Admin
 */
final class Add
{
	use SingletonTrait;
	use SectionsTrait;
	use UtilityTrait;

	/**
	 * @var array Available actions
	 */
	private $actions =
	[
		'all',
	];

	/**
	 * @var string Current action
	 */
	private $current_action = 'all';

	/**
	 * Connections constructor.
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action('wordplan_admin_add_after_init');

		$default_sections['login'] =
		[
			'title' => __('By User', 'wordplan'),
			'visible' => true,
			'callback' => [ByUserForm::class, 'instance']
		];

		$default_sections['token'] =
		[
			'title' => __('By Application', 'wordplan'),
			'visible' => true,
			'callback' => [ByApplicationForm::class, 'instance']
		];

		$this->initSections($default_sections);

		// hook
		do_action('wordplan_admin_add_after_init');
	}

	/**
	 * Initializing current section
	 *
	 * @return string
	 */
	public function initCurrentSection(): string
	{
		$current_section = !empty($_GET['do_add']) ? sanitize_key($_GET['do_add']) : 'login';

		if($current_section !== '')
		{
			$this->setCurrentSection($current_section);
		}

		return $this->getCurrentSection();
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
			add_action('wordplan_admin_show', [$this, 'wrapError']);
		}
		else
		{
			add_action('wordplan_admin_header_show', [$this, 'wrapHeader'], 3);
			add_action('wordplan_admin_show', [$this, 'wrapSections'], 7);

			$callback = $sections[$current_section]['callback'];

			if(is_callable($callback, false, $callback_name))
			{
				$callback_name();
			}
		}

		wordplan()->views()->getView('wrap.php');
	}

	/**
	 * Sections
	 */
	public function wrapSections()
	{
		wordplan()->views()->getView('add/sections.php');
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wordplan()->views()->getView('add/error.php');
	}

	/**
	 * Header
	 */
	public function wrapHeader()
	{
		wordplan()->views()->getView('add/header.php');
	}
}