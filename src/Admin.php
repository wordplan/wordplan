<?php namespace Wordplan;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Digiom\Wotices\Interfaces\ManagerInterface;
use Digiom\Wotices\Manager;
use Wordplan\Admin\Conns;
use Wordplan\Admin\Add;
use Wordplan\Admin\Extensions;
use Wordplan\Admin\Settings;
use Wordplan\Admin\Tools;
use Wordplan\Traits\SectionsTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Class Admin
 *
 * @package Wordplan
 */
final class Admin
{
	use SingletonTrait;
	use SectionsTrait;
	use UtilityTrait;

	/**
	 * Admin notices
	 *
	 * @var ManagerInterface
	 */
	private $notices;

	/**
	 * Admin constructor.
	 */
	public function __construct()
	{
		// hook
		do_action('wordplan_admin_before_loading');

		$this->notices();

		add_action('admin_menu', [$this, 'addMenu'], 30);

		if(wordplan()->context()->isAdmin())
		{
			add_action('admin_init', [$this, 'init'], 10);
			add_action('admin_enqueue_scripts', [$this, 'initStyles']);
			add_action('admin_enqueue_scripts', [$this, 'initScripts']);

			Admin\Helps\Init::instance();
			Admin\Wizards\Init::instance();
		}

		add_filter('plugin_action_links_' . wordplan()->environment()->get('plugin_basename'), [$this, 'linksLeft']);

		// hook
		do_action('wordplan_admin_after_loading');
	}

	/**
	 * Admin notices
	 *
	 * @return ManagerInterface
	 */
	public function notices()
	{
		if(empty($this->notices))
		{
			$args =
			[
				'auto_save' => true,
				'admin_notices' => false,
				'user_admin_notices' => false,
				'network_admin_notices' => false
			];

			$this->notices = new Manager('wordplan_admin_notices', $args);
		}

		return $this->notices;
	}

	/**
	 * Init menu
	 */
	public function addMenu()
	{
		$icon_data_uri = wordplan()->environment()->get('plugin_directory_url') . 'assets/images/menu-icon.png';

		add_menu_page
		(
			__('Megaplan', 'wordplan'),
			__('Megaplan', 'wordplan'),
			'manage_options',
			'wordplan',
			[$this, 'route'],
			$icon_data_uri,
			30
		);

		if(get_option('wordplan_wizard', false))
		{
			return;
		}

		add_submenu_page
		(
			'wordplan',
			__('Add conns', 'wordplan'),
			__('Add conns', 'wordplan'),
			'manage_options',
			'wordplan_add',
			[Add::instance(), 'route']
		);

		add_submenu_page
		(
			'wordplan',
			__('Tools', 'wordplan'),
			__('Tools', 'wordplan'),
			'manage_options',
			'wordplan_tools',
			[Tools::instance(), 'route']
		);

		add_submenu_page
		(
			'wordplan',
			__('Settings', 'wordplan'),
			__('Settings', 'wordplan'),
			'manage_options',
			'wordplan_settings',
			[Settings::instance(), 'route']
		);

		add_submenu_page
		(
			'wordplan',
			__('Extensions', 'wordplan'),
			__('Extensions', 'wordplan'),
			'manage_options',
			'wordplan_extensions',
			[Extensions::instance(), 'route']
		);
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action('wordplan_admin_before_init');

		$default_sections['conns'] =
		[
			'title' => __('Conns', 'wordplan'),
			'visible' => true,
			'callback' => [Conns::class, 'instance']
		];

		$this->initSections($default_sections);
		$this->setCurrentSection('conns');

		// hook
		do_action('wordplan_admin_after_init');
	}

	/**
	 * Styles
	 */
	public function initStyles()
	{
		wp_enqueue_style
        (
            'wordplan_admin_main',
            wordplan()->environment()->get('plugin_directory_url') . 'assets/css/main.css',
            [],
            wordplan()->environment()->get('wordplan_version')
        );
	}

	/**
	 * Scripts
	 */
	public function initScripts()
	{
		wp_enqueue_script
        (
            'wordplan_admin_bootstrap',
            wordplan()->environment()->get('plugin_directory_url') . 'assets/js/bootstrap.bundle.min.js',
            [],
            wordplan()->environment()->get('wordplan_version')
        );
		wp_enqueue_script
        (
            'wordplan_admin_tocbot',
            wordplan()->environment()->get('plugin_directory_url') . 'assets/js/tocbot/tocbot.min.js',
            [],
            wordplan()->environment()->get('wordplan_version')
        );
		wp_enqueue_script
        (
            'wordplan_admin_main',
            wordplan()->environment()->get('plugin_directory_url') . 'assets/js/admin.js',
            [],
            wordplan()->environment()->get('wordplan_version')
        );
	}

	/**
	 * Route sections
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
			add_action( 'wordplan_admin_header_show', [$this, 'wrapHeader'], 3);

			$callback = $sections[$current_section]['callback'];

			if(is_callable($callback, false, $callback_name))
			{
				$callback_name();
			}
		}

		wordplan()->views()->getView('wrap.php');
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wordplan()->views()->getView('error.php');
	}

	/**
	 * Header
	 */
	public function wrapHeader()
	{
		if(get_option('wordplan_wizard', false))
		{
			return;
		}

		$args['admin'] = $this;

		wordplan()->views()->getView('header.php', $args);
	}

	/**
	 * Setup left links
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function linksLeft($links): array
	{
		return array_merge(['site' => '<a href="' . admin_url('admin.php?page=wordplan') . '">' . __('Dashboard', 'wordplan') . '</a>'], $links);
	}
}