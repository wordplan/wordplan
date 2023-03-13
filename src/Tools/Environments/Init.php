<?php namespace Wordplan\Tools\Environments;

defined('ABSPATH') || exit;

use Wordplan\Exceptions\Exception;
use Wordplan\Tools\Abstracts\ToolAbstract;
use Wordplan\Traits\UtilityTrait;

/**
 * Init
 *
 * @package Wordplan\Tools\Environments
 */
class Init extends ToolAbstract
{
	use UtilityTrait;

	/**
	 * @var array Wordplan data
	 */
	private $wordplan_data = [];

	/**
	 * @var array Server data
	 */
	private $server_data = [];

	/**
	 * @var array WordPress data
	 */
	private $wp_data = [];

	/**
	 * @var array WooCommerce data
	 */
	private $wc_data = [];

	/**
	 * Init constructor.
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize
	 */
	public function init()
	{
		$this->setId('environments');
		$this->setName(__('Environments', 'wordplan'));
		$this->setDescription(__('Data about all current environments.', 'wordplan'));

		if(!$this->utilityIsWordplanAdminToolsRequest('environments'))
		{
			return;
		}

		add_action('wordplan_admin_tools_single_show', [$this, 'output']);

		/**
		 * Print
		 */
		add_filter('wordplan_admin_report_data_row_print', [$this, 'filter_data_row_print'], 10, 2);

		/**
		 * WC1C data output
		 */
		add_action('wordplan_admin_tools_single_show', [$this, 'wordplan_data_output'], 10);

		/**
		 * WC data output
		 */
		add_action('wordplan_admin_tools_single_show', [$this, 'wc_data_output'], 10);

		/**
		 * WP data output
		 */
		add_action('wordplan_admin_tools_single_show', [$this, 'wp_data_output'], 10);

		/**
		 * Server data output
		 */
		add_action('wordplan_admin_tools_single_show', [$this, 'server_data_output'], 10);
	}

	/**
	 * Show on page
	 */
	public function output()
	{
		//echo 'Example content';
	}

	/**
	 * Normalize data to print
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function filter_data_row_print($data)
	{
		/**
		 * Boolean
		 */
		if(is_bool($data))
		{
			if($data)
			{
				$data = __('yes', 'wordplan');
			}
			else
			{
				$data = __('not', 'wordplan');
			}
		}

		/**
		 * Array
		 */
		if(is_array($data))
		{
			$data = implode(', ', $data);
		}

		return $data;
	}

	/**
	 * WordPress data output
	 *
	 * @return void
	 */
	public function wp_data_output()
	{
		$wp_data = $this->load_wp_data();

		$args = ['title' => __('WordPress environment', 'wordplan'), 'data' => $wp_data];

		wordplan()->views()->getView('tools/environments/item.php', $args);
	}

	/**
	 * WC1C data output
	 *
	 * @return void
	 */
	public function wordplan_data_output()
	{
		$wordplan_data = $this->load_wordplan_data();

		$args = ['title' => __('WORDPLAN environment', 'wordplan'), 'data' => $wordplan_data];

		wordplan()->views()->getView('tools/environments/item.php', $args);
	}

	/**
	 * WooCommerce data output
	 *
	 * @return void
	 */
	public function wc_data_output()
	{
		if(!function_exists('WC'))
		{
			return;
		}

		$wp_data = $this->load_wc_data();

		$args = ['title' => __('WooCommerce environment', 'wordplan'), 'data' => $wp_data];

		wordplan()->views()->getView('tools/environments/item.php', $args);
	}

	/**
	 * Server data output
	 *
	 * @return void
	 */
	public function server_data_output()
	{
		$server_data = $this->load_server_data();

		$args = ['title' => __('Server environment', 'wordplan'), 'data' => $server_data];

		wordplan()->views()->getView('tools/environments/item.php', $args);
	}

	/**
	 * WordPress data
	 *
	 * @return array
	 */
	public function load_wp_data()
	{
		/**
		 * Final
		 *
		 * title: show title, required
		 * description: optional
		 * data: raw data for entity
		 */
		$env_array = [];

		/**
		 * Home URL
		 */
		$env_array['wp_home_url'] = array
		(
			'title' => __('Home URL', 'wordplan'),
			'description' => '',
			'data' => get_option('home')
		);

		/**
		 * Site URL
		 */
		$env_array['wp_site_url'] = array
		(
			'title' => __('Site URL', 'wordplan'),
			'description' => '',
			'data' => get_option('siteurl')
		);

		/**
		 * Version
		 */
		$env_array['wp_version'] = array
		(
			'title' => __('WordPress version', 'wordplan'),
			'description' => '',
			'data' => get_bloginfo('version')
		);

		/**
		 * WordPress multisite
		 */
		$env_array['wp_multisite'] = array
		(
			'title' => __('WordPress multisite', 'wordplan'),
			'description' => '',
			'data' => is_multisite()
		);

		/**
		 * WordPress debug
		 */
		$env_array['wp_debug_mode'] = array
		(
			'title' => __('WordPress debug', 'wordplan'),
			'description' => '',
			'data' => (defined( 'WP_DEBUG' ) && WP_DEBUG)
		);

		/**
		 * WordPress debug
		 */
		$env_array['wp_cron'] = array
		(
			'title' => __('WordPress cron', 'wordplan'),
			'description' => '',
			'data' => !(defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON)
		);

		/**
		 * WordPress language
		 */
		$env_array['wp_language'] = array
		(
			'title' => __('WordPress language', 'wordplan'),
			'description' => '',
			'data' => get_locale()
		);

		/**
		 * WordPress memory limit
		 */
		$env_array['wp_memory_limit'] = array
		(
			'title' => __('WordPress memory limit', 'wordplan'),
			'description' => '',
			'data' => WP_MEMORY_LIMIT
		);

		/**
		 * Set wp data
		 */
		$this->set_wp_data($env_array);

		/**
		 * Return wp data
		 */
		return $this->get_wp_data();
	}

	/**
	 * Server data
	 */
	public function load_server_data(): array
    {
		/**
		 * Final
		 *
		 * title: show title, required
		 * description: optional
		 * data: raw data for entity
		 */
		$env_array = [];

		/**
		 * Server info
		 */
		$env_array['server_info'] = array
		(
			'title' => __('Server info', 'wordplan'),
			'description' => '',
			'data' => sanitize_text_field($_SERVER['SERVER_SOFTWARE'])
		);

		/**
		 * PHP version
		 */
		$env_array['php_version'] = array
		(
			'title' => __('PHP version', 'wordplan'),
			'description' => '',
			'data' => PHP_VERSION
		);

		/**
		 * Database version
		 */
		$env_array['db_version'] = array
		(
			'title' => __('Database version', 'wordplan'),
			'description' => '',
			'data' => (!empty(wordplan()->database()->is_mysql) ? wordplan()->database()->db_version() : '')
		);

		/**
		 * Suhosin
		 */
		$env_array['suhosin_installed'] = array
		(
			'title' => __('Suhosin', 'wordplan'),
			'description' => '',
			'data' => extension_loaded('suhosin')
		);

		/**
		 * Fsockopen or curl enabled
		 */
		$env_array['fsockopen_or_curl'] = array
		(
			'title' => __('Fsockopen or curl enabled', 'wordplan'),
			'description' => '',
			'data' => (function_exists('fsockopen') || function_exists('curl_init'))
		);

		/**
		 * CURL
		 */
		if(function_exists('curl_version'))
		{
			$curl_version = curl_version();

			$env_array['curl_version'] = array
			(
				'title' => __('CURL info', 'wordplan'),
				'description' => '',
				'data' => $curl_version['version'] . ', ' . $curl_version['ssl_version']
			);
		}

		/**
		 * Default timezone
		 */
		$env_array['default_timezone'] = array
		(
			'title' => __('Default timezone', 'wordplan'),
			'description' => '',
			'data' => date_default_timezone_get()
		);

		/**
		 * PHP post max size
		 */
		$env_array['php_post_max_size'] = array
		(
			'title' => __('PHP post max size', 'wordplan'),
			'description' => '',
			'data' => ini_get('post_max_size')
		);

		/**
		 * PHP max upload size
		 */
		$env_array['php_max_upload_size'] = array
		(
			'title' => __('PHP max upload size', 'wordplan'),
			'description' => '',
			'data' => (wp_max_upload_size() / 1024 / 1024) . 'M'
		);

		/**
		 * PHP max execution time
		 */
		$env_array['php_max_execution_time'] = array
		(
			'title' => __('PHP max execution time', 'wordplan'),
			'description' => '',
			'data' => ini_get('max_execution_time')
		);

		/**
		 * PHP max input vars
		 */
		$env_array['php_max_input_vars'] = array
		(
			'title' => __('PHP max input vars', 'wordplan'),
			'description' => '',
			'data' => ini_get('max_input_vars')
		);

		/**
		 * PHP soapclient enabled
		 */
		$env_array['php_soapclient_enabled'] = array
		(
			'title' => __('PHP soapclient enabled', 'wordplan'),
			'description' => '',
			'data' => class_exists('SoapClient')
		);

		/**
		 * PHP domdocument enabled
		 */
		$env_array['php_domdocument_enabled'] = array
		(
			'title' => __('PHP domdocument enabled', 'wordplan'),
			'description' => '',
			'data' => class_exists('DOMDocument')
		);

		/**
		 * PHP gzip enabled
		 */
		$env_array['php_gzip_enabled'] = array
		(
			'title' => __('PHP gzip enabled', 'wordplan'),
			'description' => '',
			'data' => is_callable('gzopen')
		);

		/**
		 * PHP mbstring enabled
		 */
		$env_array['php_mbstring_enabled'] = array
		(
			'title' => __('PHP mbstring enabled', 'wordplan'),
			'description' => '',
			'data' => extension_loaded('mbstring')
		);

		/**
		 * Set server data
		 */
		$this->set_server_data($env_array);

		/**
		 * Return final server data
		 */
		return $this->get_server_data();
	}

	/**
	 * WC1C data
	 */
	public function load_wordplan_data()
	{
		/**
		 * Container
		 */
		$env_array = [];

		/**
		 * WC1C version
		 */
		$env_array['wordplan_version'] = array
		(
			'title' => __('WORDPLAN version', 'wordplan'),
			'description' => '',
			'data' => wordplan()->environment()->get('wordplan_version', '')
		);

		/**
		 * WC1C upload directory
		 */
		$env_array['wordplan_upload_directory'] = array
		(
			'title' => __('Upload directory', 'wordplan'),
			'description' => '',
			'data' => wordplan()->environment()->get('wordplan_upload_directory')
		);

		/**
		 * Extensions count
		 */
		try
		{
			$extensions = wordplan()->extensions()->get();
			$env_array['wordplan_extensions_count'] = array
			(
				'title' => __('Count extensions', 'wordplan'),
				'description' => '',
				'data' => count($extensions)
			);
		}
		catch(Exception $e){}

		/**
		 * Tools count
		 */
		try
		{
			$tools = wordplan()->tools()->get();
			$env_array['wordplan_tools_count'] = array
			(
				'title' => __('Count tools', 'wordplan'),
				'description' => '',
				'data' => count($tools)
			);
		}
		catch(Exception $e)
		{}

		$this->set_wordplan_data($env_array);

		return $this->get_wordplan_data();
	}

	/**
	 * WooCommerce data
	 */
	private function load_wc_data()
	{
		/**
		 * Container
		 */
		$env_array = [];

		if(!function_exists('WC'))
		{
			return $env_array;
		}

		/**
		 * WooCommerce version
		 */
		$env_array['wc_version'] = array
		(
			'title' => __('WooCommerce version', 'wordplan'),
			'description' => '',
			'data' => WC()->version
		);

		$term_response = [];
		$terms = get_terms( 'product_type', array( 'hide_empty' => 0 ) );
		foreach($terms as $term)
		{
			$term_response[$term->slug] = strtolower($term->name);
		}

		/**
		 * Product types
		 */
		$env_array['wc_product_types'] = array
		(
			'title' => __('WooCommerce product types', 'wordplan'),
			'description' => '',
			'data' => $term_response
		);

		/**
		 * WooCommerce currency
		 */
		$env_array['wc_currency'] = array
		(
			'title' => __('WooCommerce currency', 'wordplan'),
			'description' => '',
			'data' => get_woocommerce_currency()
		);

		/**
		 * WooCommerce currency symbol
		 */
		$env_array['wc_currency_symbol'] = array
		(
			'title' => __('WooCommerce currency symbol', 'wordplan'),
			'description' => '',
			'data' => get_woocommerce_currency_symbol()
		);

		/**
		 * Final set
		 */
		$this->set_wc_data($env_array);

		/**
		 * Return all data
		 */
		return $this->get_wc_data();
	}

	/**
	 * Get WooCommerce data
	 *
	 * @return array
	 */
	public function get_wc_data(): array
    {
		return $this->wc_data;
	}

	/**
	 * Set WooCommerce data
	 *
	 * @param array $wc_data
	 */
	public function set_wc_data(array $wc_data)
	{
		$this->wc_data = $wc_data;
	}

	/**
	 * @return array
	 */
	public function get_wordplan_data(): array
    {
		return $this->wordplan_data;
	}

	/**
	 * @param array $wordplan_data
	 */
	public function set_wordplan_data(array $wordplan_data)
	{
		$this->wordplan_data = $wordplan_data;
	}

	/**
	 * @return array
	 */
	public function get_server_data(): array
    {
		return $this->server_data;
	}

	/**
	 * @param array $server_data
	 */
	public function set_server_data(array $server_data)
	{
		$this->server_data = $server_data;
	}

	/**
	 * @return array
	 */
	public function get_wp_data(): array
    {
		return $this->wp_data;
	}

	/**
	 * @param array $wp_data
	 */
	public function set_wp_data(array $wp_data)
	{
		$this->wp_data = $wp_data;
	}
}