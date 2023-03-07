<?php namespace Wordplan\Abstracts;

defined('ABSPATH') || exit;

/**
 * Class ScreenAbstract
 *
 * @package Wordplan\Abstracts
 */
abstract class ScreenAbstract
{
	/**
	 * ScreenAbstract constructor.
	 */
	public function __construct()
	{
		add_action('wordplan_admin_show', [$this, 'output'], 10);
	}

	/**
	 * @return mixed
	 */
	abstract public function output();
}