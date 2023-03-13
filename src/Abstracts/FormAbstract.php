<?php namespace Wordplan\Abstracts;

defined('ABSPATH') || exit;

/**
 * Class FormAbstract
 *
 * @package Wordplan\Abstracts
 */
abstract class FormAbstract extends \Digiom\Woplucore\Abstracts\FormAbstract
{
	/**
	 * @var string Unique slug
	 */
    protected $prefix = 'wordplan';
}