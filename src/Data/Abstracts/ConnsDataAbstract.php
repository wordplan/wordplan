<?php namespace Wordplan\Data\Abstracts;

defined('ABSPATH') || exit;

/**
 * ConnsDataAbstract
 *
 * @package Wordplan\Data\Abstracts
 */
abstract class ConnsDataAbstract extends WithMetaDataAbstract
{
	/**
	 * This is the name of this object type
	 *
	 * @var string
	 */
	protected $object_type = 'conn';
}