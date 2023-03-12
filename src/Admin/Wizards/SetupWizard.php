<?php namespace Wordplan\Admin\Wizards;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Wizards\Setup\Check;
use Wordplan\Admin\Wizards\Setup\Complete;
use Wordplan\Admin\Wizards\Setup\Database;
use Wordplan\Exceptions\Exception;

/**
 * SetupWizard
 *
 * @package Wordplan\Admin\Wizards
 */
final class SetupWizard extends WizardAbstract
{
	use SingletonTrait;

	/**
	 * SetupWizard constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('setup');
		$this->setDefaultSteps();
		$this->setStep(isset($_GET[$this->getId()]) ? sanitize_key($_GET[$this->getId()]) : current(array_keys($this->getSteps())));

		$this->init();
	}

	/**
	 * Initialize
	 */
	public function init()
	{
		add_filter('wordplan_admin_init_sections', [$this, 'hideSections'], 20, 1);
		add_filter('wordplan_admin_init_sections_current', [$this, 'setSectionsCurrent'], 20, 1);
		add_action( 'wordplan_admin_header_show', [$this, 'wrapHeader'], 3);
		add_action('wordplan_admin_show', [$this, 'route']);
	}

	/**
	 * @param $sections
	 *
	 * @return array
	 */
	public function hideSections($sections)
	{
		$default_sections[$this->getId()] =
		[
			'title' => __('Setup wizard', 'wordplan'),
			'visible' => true,
			'callback' => [__CLASS__, 'instance']
		];

		return $default_sections;
	}

	/**
	 * Header
	 */
	public function wrapHeader()
	{
		wordplan()->views()->getView('wizards/header.php');
	}

	/**
	 * @param $section
	 *
	 * @return string
	 */
	public function setSectionsCurrent($section)
	{
		return $this->getId();
	}

	/**
	 * @return void
	 */
	private function setDefaultSteps()
	{
		$default_steps =
		[
			'check' =>
			[
				'name' => __('Compatibility', 'wordplan'),
				'callback' => [Check::class, 'instance'],
			],
			'database' =>
			[
				'name' => __('Database', 'wordplan'),
				'callback' => [Database::class, 'instance'],
			],
			'complete' =>
			[
				'name' => __('Completing', 'wordplan'),
				'callback' => [Complete::class, 'instance'],
			],
		];

		$this->setSteps($default_steps);
	}
}