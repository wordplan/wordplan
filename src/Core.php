<?php namespace Wordplan;

defined('ABSPATH') || exit;

use wpdb;
use Digiom\Woplucore\Interfaces\SettingsInterface;
use Psr\Log\LoggerInterface;
use Wordplan\Log\Formatter;
use Wordplan\Log\Handler;
use Wordplan\Log\Logger;
use Wordplan\Log\Processor;
use Wordplan\Settings\ConnectionSettings;
use Wordplan\Settings\InterfaceSettings;
use Wordplan\Settings\LogsSettings;
use Wordplan\Settings\MainSettings;
use Wordplan\Exceptions\Exception;
use Digiom\Woplucore\Abstracts\CoreAbstract;
use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Core
 *
 * @package Wordplan
 */
final class Core extends CoreAbstract
{
	use SingletonTrait;

	/**
	 * @var array
	 */
	private $log = [];

	/**
	 * @var Timer
	 */
	private $timer;

	/**
	 * @var SettingsInterface
	 */
	private $settings = [];

	/**
	 * @var Tecodes\Client
	 */
	private $tecodes;

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action($this->context()->getSlug() . '_before_init');

		$this->localization();

		try
		{
			$this->timer();
		}
		catch(Exception $e)
		{
			core()->log()->alert(__('Timer not loaded.', 'wordplan'), ['exception' => $e]);
			return;
		}

		try
		{
			$this->extensions()->load();
		}
		catch(Exception $e)
		{
			core()->log()->alert(__('Extensions not loaded.', 'wordplan'), ['exception' => $e]);
		}

		try
		{
			$this->extensions()->init();
		}
		catch(Exception $e)
		{
			core()->log()->alert(__('Extensions not initialized.', 'wordplan'), ['exception' => $e]);
		}

		try
		{
			$this->tools()->load();
		}
		catch(Exception $e)
		{
			core()->log()->alert(__('Tools not loaded.', 'wordplan'), ['exception' => $e]);
		}

		if(false !== core()->context()->isAdmin())
		{
			try
			{
				$this->tools()->init();
			}
			catch(Exception $e)
			{
				core()->log()->alert(__('Tools not initialized.', 'wordplan'), ['exception' => $e]);
			}
		}

		// hook
		do_action($this->context()->getSlug() . '_after_init');
	}

	/**
	 * Extensions
	 *
	 * @return Extensions\Core
	 */
	public function extensions(): Extensions\Core
	{
		return Extensions\Core::instance();
	}

	/**
	 * Filesystem
	 *
	 * @return Filesystem
	 */
	public function filesystem(): Filesystem
	{
		return Filesystem::instance();
	}

	/**
	 * Environment
	 *
	 * @return Environment
	 */
	public function environment(): Environment
	{
		return Environment::instance();
	}

	/**
	 * Views
	 *
	 * @return Views
	 */
	public function views(): Views
	{
		return Views::instance()->setSlug('wordplan')->setPluginDir($this->environment()->get('plugin_directory_path'));
	}

	/**
	 * Tools
	 *
	 * @return Tools\Core
	 */
	public function tools(): Tools\Core
	{
		return Tools\Core::instance();
	}

	/**
	 * Logger
	 *
	 * @param string $channel
	 * @param string $name
	 * @param mixed $hard_level
	 *
	 * @return LoggerInterface
	 */
	public function log(string $channel = 'main', string $name = '', $hard_level = null)
	{
		$channel = strtolower($channel);

		if(!isset($this->log[$channel]))
		{
			if('' === $name)
			{
				$name = $channel;
			}

			$path = '';
			$max_files = $this->settings('logs')->get('logger_files_max', 30);

			$logger = new Logger($channel);

			switch($channel)
			{
				case 'tools':
					$path = $this->environment()->get('wordplan_tools_logs_directory') . '/' . $name . '.log';
					$level = $this->settings('logs')->get('logger_tools_level', 'logger_level');
					break;
				case 'conns':
					$path = $name . '.log';
					$level = $this->settings('logs')->get('logger_conns_level', 'logger_level');
					break;
				default:
					$level = $this->settings('logs')->get('logger_level', 300);
			}

			if('logger_level' === $level)
			{
				$level = $this->settings('logs')->get('logger_level', 300);
			}

			if(!is_null($hard_level))
			{
				$level = $hard_level;
			}

			if('' === $path)
			{
				$path = $this->environment()->get('wordplan_logs_directory') . '/main.log';
			}

			try
			{
				$uid_processor = new Processor();
				$formatter = new Formatter();
				$handler = new Handler($path, $max_files, $level);

				$handler->setFormatter($formatter);

				$logger->pushProcessor($uid_processor);
				$logger->pushHandler($handler);
			}
			catch(\Throwable $e){}

			/**
			 * Внешние назначения для логгера
			 *
			 * @param LoggerInterface $logger Текущий логгер
			 *
			 * @return LoggerInterface
			 */
			if(has_filter($this->context()->getSlug() . '_log_load_before'))
			{
				$logger = apply_filters($this->context()->getSlug() . '_log_load_before', $logger);
			}

			$this->log[$channel] = $logger;
		}

		return $this->log[$channel];
	}

	/**
	 * Settings
	 *
	 * @param string $context
	 *
	 * @return SettingsInterface
	 */
	public function settings(string $context = 'main')
	{
		if(!isset($this->settings[$context]))
		{
			switch($context)
			{
				case 'connection':
					$class = ConnectionSettings::class;
					break;
				case 'logs':
					$class = LogsSettings::class;
					break;
				case 'interface':
					$class = InterfaceSettings::class;
					break;
				default:
					$class = MainSettings::class;
			}

			$settings = new $class();

			try
			{
				$settings->init();
			}
			catch(\Throwable $e)
			{
				core()->log()->error($e->getMessage(), ['exception' => $e]);
			}

			$this->settings[$context] = $settings;
		}

		return $this->settings[$context];
	}

	/**
	 * Timer
	 *
	 * @return Timer
	 */
	public function timer(): Timer
    {
		if(is_null($this->timer))
		{
			$timer = new Timer();

			$php_max_execution = $this->environment()->get('php_max_execution_time', 20);

			if($php_max_execution !== $this->settings()->get('php_max_execution_time', $php_max_execution))
			{
				$php_max_execution = $this->settings()->get('php_max_execution_time', $php_max_execution);
			}

			$timer->setMaximum($php_max_execution);

			$this->timer = $timer;
		}

		return $this->timer;
	}

	/**
	 * Tecodes
	 *
	 * @return Tecodes\Client
	 */
	public function tecodes(): Tecodes\Client
	{
		if($this->tecodes instanceof Tecodes\Client)
		{
			return $this->tecodes;
		}

		if(!class_exists('Tecodes_Local'))
		{
			include_once $this->environment()->get('plugin_directory_path') . '/vendor/tecodes/tecodes-local/bootstrap.php';
		}

		$options =
		[
			'timeout' => 5,
			'verify_ssl' => false,
			'version' => 'tecodes/v1'
		];

		$tecodes_local = new Tecodes\Client('https://wordplan.ru/', $options);

		/**
		 * Languages
		 */
		$tecodes_local->status_messages =
		[
			'status_1' => __('This activation code is active.', 'wordplan'),
			'status_2' => __('Error: This activation code has expired.', 'wordplan'),
			'status_3' => __('Activation code republished. Awaiting reactivation.', 'wordplan'),
			'status_4' => __('Error: This activation code has been suspended.', 'wordplan'),
			'code_not_found' => __('This activation code is not found.', 'wordplan'),
			'localhost' => __('This activation code is active (localhost).', 'wordplan'),
			'pending' => __('Error: This activation code is pending review.', 'wordplan'),
			'download_access_expired' => __('Error: This version of the software was released after your download access expired. Please downgrade software or contact support for more information.', 'wordplan'),
			'missing_activation_key' => __('Error: The activation code variable is empty.', 'wordplan'),
			'could_not_obtain_local_code' => __('Error: I could not obtain a new local code.', 'wordplan'),
			'maximum_delay_period_expired' => __('Error: The maximum local code delay period has expired.', 'wordplan'),
			'local_code_tampering' => __('Error: The local key has been tampered with or is invalid.', 'wordplan'),
			'local_code_invalid_for_location' => __('Error: The local code is invalid for this location.', 'wordplan'),
			'missing_license_file' => __('Error: Please create the following file (and directories if they dont exist already): ', 'wordplan'),
			'license_file_not_writable' => __('Error: Please make the following path writable: ', 'wordplan'),
			'invalid_local_key_storage' => __('Error: I could not determine the local key storage on clear.', 'wordplan'),
			'could_not_save_local_key' => __('Error: I could not save the local key.', 'wordplan'),
			'code_string_mismatch' => __('Error: The local code is invalid for this activation code.', 'wordplan'),
			'code_status_delete' => __('Error: This activation code has been deleted.', 'wordplan'),
			'code_status_draft' => __('Error: This activation code has draft.', 'wordplan'),
			'code_status_available' => __('Error: This activation code has available.', 'wordplan'),
			'code_status_blocked' => __('Error: This activation code has been blocked.', 'wordplan'),
		];

		$tecodes_local->set_local_code_storage(new Tecodes\Storage());
		$tecodes_local->set_instance(new Tecodes\Instance());

		$tecodes_local->validate();

		$this->tecodes = $tecodes_local;

		return $this->tecodes;
	}

	/**
	 * Load localisation
	 */
	public function localization()
	{
		$locale = determine_locale();

		if(has_filter('plugin_locale'))
		{
			$locale = apply_filters('plugin_locale', $locale, 'wordplan');
		}

		load_textdomain('wordplan', WP_LANG_DIR . '/plugins/wordplan-' . $locale . '.mo');
		load_textdomain('wordplan', core()->environment()->get('plugin_directory_path') . 'assets/languages/wordplan-' . $locale . '.mo');

		core()->log()->debug(__('Localization loaded.', 'wordplan'), ['locale' => $locale]);
	}

	/**
	 * Use in plugin for DB queries
	 *
	 * @return wpdb
	 */
	public function database(): wpdb
	{
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Main instance of Admin
	 *
	 * @return Admin
	 */
	public function admin(): Admin
	{
		ob_start();
		return Admin::instance();
	}

	/**
	 * Get data if set, otherwise return a default value or null
	 * Prevents notices when data is not set
	 *
	 * @param mixed $var variable
	 * @param string $default default value
	 *
	 * @return mixed
	 */
	public function getVar(&$var, $default = null)
	{
		return $var ?? $default;
	}

	/**
	 * Define constant if not already set
	 *
	 * @param string $name constant name
	 * @param string|bool $value constant value
	 */
	public function define(string $name, $value)
	{
		if(!defined($name))
		{
			define($name, $value);
		}
	}
}