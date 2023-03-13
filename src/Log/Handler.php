<?php namespace Wordplan\Log;

defined('ABSPATH') || exit;

use Monolog\Handler\RotatingFileHandler;

/**
 * Handler
 *
 * @package Wordplan
 */
final class Handler extends RotatingFileHandler
{
}