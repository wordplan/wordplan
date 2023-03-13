<?php namespace Wordplan\Admin\Wizards\Setup;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Wizards\StepAbstract;

/**
 * Check
 *
 * @package Wordplan\Admin\Wizards
 */
class Check extends StepAbstract
{
	use SingletonTrait;

	/**
	 * Check constructor.
	 */
	public function __construct()
	{
		$this->setId('check');
	}

	/**
	 * Precessing step
	 */
	public function process()
	{
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

		wordplan()->views()->getView('wizards/steps/check.php', $args);
	}
}