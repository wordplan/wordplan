<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Abstracts\ScreenAbstract;

/**
 * Class Lists
 *
 * @package Wordplan\Admin\Conns
 */
class All extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * Build and output table
	 */
	public function output()
	{
		$list_table = new AllTable();

		$args['object'] = $list_table;

		wordplan()->views()->getView('conns/all.php', $args);
	}
}