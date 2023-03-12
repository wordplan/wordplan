<?php namespace Wordplan;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Exceptions\Exception;
use Wordplan\Exceptions\RuntimeException;

/**
 * Environment
 *
 * @package Wordplan
 */
final class Environment
{
	use SingletonTrait;

	/**
	 * @var array Environ data
	 */
	private $data;

	/**
	 * Environment constructor
	 */
	public function __construct(){}

	/**
	 * Get data
	 *
	 * @param $key
	 * @param $default
	 *
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if(isset($this->data[$key]))
		{
			return $this->data[$key];
		}

		$key_getter = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

		$getter = "init$key_getter";

		if(is_callable([$this, $getter]))
		{
			try
			{
				$getter_value = $this->{$getter}($default);
				$this->set($key, $getter_value);
			}
			catch(Exception $e){}

			return $this->get($key);
		}

		if(false === is_null($default))
		{
			return $default;
		}

		return false;
	}

	/**
	 * Set environ data
	 *
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * WordPress upload directory
	 *
	 * @return bool
	 * @throws RuntimeException
	 */
	public function initUploadDirectory()
	{
		if(false === function_exists('wp_upload_dir'))
		{
			throw new RuntimeException('function wp_upload_dir is not exists');
		}

		$wp_upload_dir = wp_upload_dir();

		$this->set('upload_directory', $wp_upload_dir['basedir']);

		return $this->get('upload_directory');
	}

	/**
	 * WordPress plugin directory URL
	 *
	 * @return string
	 */
	public function initPluginDirectoryUrl()
	{
		if(false === function_exists('plugin_dir_url'))
		{
			throw new RuntimeException('Function plugin_dir_url is not exists.');
		}

		$this->set('plugin_directory_url', plugin_dir_url(WORDPLAN_PLUGIN_FILE));

		return $this->get('plugin_directory_url');
	}

	/**
	 * WordPress plugin directory path
	 *
	 * @return string
	 */
	public function initPluginDirectoryPath()
	{
		if(false === function_exists('plugin_dir_path'))
		{
			throw new RuntimeException('Function plugin_dir_path is not exists.');
		}

		$this->set('plugin_directory_path', plugin_dir_path(WORDPLAN_PLUGIN_FILE));

		return $this->get('plugin_directory_path');
	}

	/**
	 * WordPress plugin basename
	 *
	 * @return string
	 */
	public function initPluginBasename()
	{
		if(false === function_exists('plugin_basename'))
		{
			throw new RuntimeException('Function plugin_basename is not exists.');
		}

		$this->set('plugin_basename', plugin_basename(WORDPLAN_PLUGIN_FILE));

		return $this->get('plugin_basename');
	}

	/**
	 * PHP post max size
	 */
	public function initPhpPostMaxSize()
	{
		$this->set('php_post_max_size', ini_get('post_max_size'));

		return $this->get('php_post_max_size');
	}

	/**
	 * PHP max execution time
	 */
	public function initPhpMaxExecutionTime()
	{
		$this->set('php_max_execution_time', ini_get('max_execution_time'));

		return $this->get('php_max_execution_time');
	}

	/**
	 * WORDPLAN upload directory
	 *
	 * @return bool
	 */
	public function initWordplanUploadDirectory()
	{
		$wordplan_upload_dir = $this->get('upload_directory') . DIRECTORY_SEPARATOR . 'wordplan';

		$this->set('wordplan_upload_directory', $wordplan_upload_dir);

		return $this->get('wordplan_upload_directory');
	}

	/**
	 * WORDPLAN logs directory
	 *
	 * @return bool
	 */
	public function initWordplanLogsDirectory()
	{
		$wordplan_logs_dir = $this->get('wordplan_upload_directory') . DIRECTORY_SEPARATOR . 'logs';

		$this->set('wordplan_logs_directory', $wordplan_logs_dir);

		return $this->get('wordplan_logs_directory');
	}

	/**
	 * WORDPLAN tools directory
	 *
	 * @return bool
	 */
	public function initWordplanToolsDirectory()
	{
		$wordplan_logs_dir = $this->get('wordplan_upload_directory') . DIRECTORY_SEPARATOR . 'tools';

		$this->set('wordplan_tools_directory', $wordplan_logs_dir);

		return $this->get('wordplan_tools_directory');
	}

	/**
	 * WORDPLAN tools logs directory
	 *
	 * @return bool
	 */
	public function initWordplanToolsLogsDirectory()
	{
		$wordplan_logs_dir = $this->get('wordplan_tools_directory') . DIRECTORY_SEPARATOR . 'logs';

		$this->set('wordplan_tools_logs_directory', $wordplan_logs_dir);

		return $this->get('wordplan_tools_logs_directory');
	}

	/**
	 * WORDPLAN conns directory
	 *
	 * @return bool
	 */
	public function initWordplanConnsDirectory()
	{
		$wordplan_logs_dir = $this->get('wordplan_upload_directory') . DIRECTORY_SEPARATOR . 'conns';

		$this->set('wordplan_conns_directory', $wordplan_logs_dir);

		return $this->get('wordplan_conns_directory');
	}

	/**
	 * WORDPLAN conns logs directory
	 *
	 * @return bool
	 */
	public function initWordplanConnsLogsDirectory()
	{
		$wordplan_logs_dir = $this->get('wordplan_conns_directory') . DIRECTORY_SEPARATOR . 'logs';

		$this->set('wordplan_conns_logs_directory', $wordplan_logs_dir);

		return $this->get('wordplan_conns_logs_directory');
	}

	/**
	 * WORDPLAN version
	 *
	 * @return bool
	 */
	public function initWordplanVersion()
	{
		if(!function_exists('get_file_data'))
		{
			throw new RuntimeException('Function get_file_data is not exists');
		}

		$plugin_data = get_file_data(WORDPLAN_PLUGIN_FILE, ['Version' => 'Version']);

		$this->set('wordplan_version', $plugin_data['Version']);

		return $this->get('wordplan_version');
	}

	/**
	 * Get all data
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Set all data
	 *
	 * @param array $data
	 */
	public function setData(array $data)
	{
		$this->data = $data;
	}
}