<?php namespace Wordplan;

defined('ABSPATH') || exit;

use Digiom\Woap\Client;

/**
 * Connection
 *
 * @package Wordplan
 */
final class Connection extends Client
{
	/**
	 * @var string
	 */
	protected $host = 'https://wordplan.ru';
}