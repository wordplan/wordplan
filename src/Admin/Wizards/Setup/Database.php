<?php namespace Wordplan\Admin\Wizards\Setup;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Wizards\StepAbstract;

/**
 * Database
 *
 * @package Wordplan\Admin\Wizards
 */
class Database extends StepAbstract
{
	use SingletonTrait;

	/**
	 * Database constructor.
	 */
	public function __construct()
	{
		$this->setId('database');
	}

	/**
	 * Precessing step
	 */
	public function process()
	{
		if(isset($_POST['_wordplan-admin-nonce']))
		{
			if(wp_verify_nonce($_POST['_wordplan-admin-nonce'], 'wordplan-admin-wizard-database'))
			{
				$this->tablesInstall();
				wp_safe_redirect($this->wizard()->getNextStepLink());
				die;
			}

			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Create tables error. Please retry.', 'wordplan')
				]
			);
		}

		add_action('wordplan_wizard_content_output', [$this, 'output'], 10);
	}

	/**
	 * Output wizard content
	 *
	 * @return void
	 */
	public function output()
	{
		$args =
		[
			'step' => $this
		];

		wordplan()->views()->getView('wizards/steps/database.php', $args);
	}

	/**
	 * Install db tables
	 *
	 * @return bool
	 */
	public function tablesInstall(): bool
	{
		$wordplan_version_database = 1;

		$current_db = get_site_option('wordplan_version_database', 0);

		if($current_db === $wordplan_version_database)
		{
			return false;
		}

		$charset_collate = wordplan()->database()->get_charset_collate();
		$table_name = wordplan()->database()->base_prefix . 'wordplan';
		$table_name_meta = $table_name . '_meta';

		$sql = "CREATE TABLE $table_name (
		`conn_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`conn_type` VARCHAR(50) NULL DEFAULT NULL,
		`site_id` INT(11) UNSIGNED NULL DEFAULT NULL,
		`user_id` INT(11) UNSIGNED NULL DEFAULT NULL,
		`name` VARCHAR(255) NULL DEFAULT NULL,
		`status` VARCHAR(50) NULL DEFAULT NULL,
		`options` TEXT NULL DEFAULT NULL,
		`date_create` VARCHAR(50) NULL DEFAULT NULL,
		`date_modify` VARCHAR(50) NULL DEFAULT NULL,
		`date_activity` VARCHAR(50) NULL DEFAULT NULL,
		`wordplan_version` VARCHAR(50) NULL DEFAULT NULL,
		`wordplan_version_init` VARCHAR(50) NULL DEFAULT NULL,
		PRIMARY KEY (`conn_id`),
		UNIQUE INDEX `conn_id` (`conn_id`)
		) $charset_collate;";

		$sql_meta = "CREATE TABLE $table_name_meta (
		`meta_id` BIGINT(20) NOT NULL AUTO_INCREMENT,
		`conn_id` BIGINT(20) NULL DEFAULT NULL,
		`name` VARCHAR(90) NULL DEFAULT NULL,
		`value` LONGTEXT NULL DEFAULT NULL,
		PRIMARY KEY (`meta_id`),
		UNIQUE INDEX `meta_id` (`meta_id`)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);
		dbDelta($sql_meta);

		add_site_option('wordplan_version_database', $wordplan_version_database);

		return true;
	}
}