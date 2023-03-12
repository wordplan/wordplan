<?php namespace Wordplan\Admin\Wizards\Setup;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Wizards\StepAbstract;
use Wordplan\Traits\UtilityTrait;

/**
 * Complete
 *
 * @package Wordplan\Admin\Wizards
 */
class Complete extends StepAbstract
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * Complete constructor.
	 */
	public function __construct()
	{
		$this->setId('complete');
	}

	/**
	 * Precessing step
	 */
	public function process()
	{
		delete_option('wordplan_wizard');
		update_option('wordplan_version', wordplan()->environment()->get('wordplan_version'));

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
			'step' => $this,
			'back_url' => $this->utilityAdminConnsGetUrl('all'),
		];

		wordplan()->views()->getView('wizards/steps/complete.php', $args);
	}
}