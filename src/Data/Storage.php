<?php namespace Wordplan\Data;

defined('ABSPATH') || exit;

/**
 * Storage
 *
 * @package Wordplan\Data
 */
class Storage extends \Digiom\Woplucore\Data\Storage
{
	/**
	 * @var string Unique prefix
	 */
	public $unique_prefix = 'wordplan';

	/**
	 * Contains an array of default supported data storages
	 *
	 * Format of object name => class name
	 * Example: 'key' => 'UniqueNameStorage'
	 *
	 * You can also pass something like key_<type> for codes storage and
	 * that type will be used first when available, if a store is requested like
	 * this and doesn't exist, then the store would fall back to 'key'.
	 * Ran through PREFIX `_data_storages`.
	 *
	 * @var array
	 */
	public $storages =
	[
		'conn' => Storages\ConnsStorage::class,
	];
}
