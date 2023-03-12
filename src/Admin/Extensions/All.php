<?php namespace Wordplan\Admin\Extensions;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Abstracts\ScreenAbstract;

/**
 * All
 *
 * @package Wordplan\Admin\Extensions
 */
class All extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * Build and output table
	 */
	public function output()
	{
		$extensions = wordplan()->extensions()->get();

		if(empty($extensions))
		{
			wordplan()->views()->getView('extensions/empty.php');
			return;
		}

		$args['extensions'] = $extensions;

		wordplan()->views()->getView('extensions/all.php', $args);
	}
}