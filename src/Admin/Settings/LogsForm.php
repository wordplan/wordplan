<?php namespace Wordplan\Admin\Settings;

defined('ABSPATH') || exit;

use Wordplan\Exceptions\Exception;
use Wordplan\Settings\LogsSettings;

/**
 * LogsForm
 *
 * @package Wordplan\Admin
 */
class LogsForm extends Form
{
	/**
	 * LogsForm constructor.
	 *
	 * @throws Exception
	 * @throws \Exception
	 */
	public function __construct()
	{
		$this->setId('settings-logs');
		$this->setSettings(new LogsSettings());

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_logger'], 10);

		$this->init();
	}

	/**
	 * Add settings for logger
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_logger($fields): array
	{
		$fields['logger_level'] =
		[
			'title' => __('Level for main events', 'wordplan'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wordplan'),
			'default' => '300',
			'options' =>
			[
				'100' => __('DEBUG (100)', 'wordplan'),
				'200' => __('INFO (200)', 'wordplan'),
				'250' => __('NOTICE (250)', 'wordplan'),
				'300' => __('WARNING (300)', 'wordplan'),
				'400' => __('ERROR (400)', 'wordplan'),
			],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wordplan'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 30 days.', 'wordplan'),
			'default' => 30,
			'css' => 'min-width: 20px;',
		];

		$fields['logger_title_level'] =
		[
			'title' => __('Levels by context', 'wordplan'),
			'type' => 'title',
			'description' => __('Event log settings based on context.', 'wordplan'),
		];

		$fields['logger_conns_level'] =
		[
			'title' => __('Conns', 'wordplan'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the conns events in the log file. The higher the level, the less data is recorded.', 'wordplan'),
			'default' => 'logger_level',
			'options' =>
				[
					'logger_level' => __('Use level for main events', 'wordplan'),
					'100' => __('DEBUG (100)', 'wordplan'),
					'200' => __('INFO (200)', 'wordplan'),
					'250' => __('NOTICE (250)', 'wordplan'),
					'300' => __('WARNING (300)', 'wordplan'),
					'400' => __('ERROR (400)', 'wordplan'),
				],
		];

		$fields['logger_tools_level'] =
		[
			'title' => __('Tools', 'wordplan'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded the tools events in the log file. The higher the level, the less data is recorded.', 'wordplan'),
			'default' => 'logger_level',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wordplan'),
				'100' => __('DEBUG (100)', 'wordplan'),
				'200' => __('INFO (200)', 'wordplan'),
				'250' => __('NOTICE (250)', 'wordplan'),
				'300' => __('WARNING (300)', 'wordplan'),
				'400' => __('ERROR (400)', 'wordplan'),
			],
		];

		return $fields;
	}
}