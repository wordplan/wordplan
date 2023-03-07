<?php namespace Wordplan\Log;

defined('ABSPATH') || exit;

use Monolog\Logger as Monolog;

/**
 * Logger
 *
 * @package Wordplan
 */
final class Logger extends Monolog
{
	/**
	 * @var string
	 */
	protected $name = 'main';
}