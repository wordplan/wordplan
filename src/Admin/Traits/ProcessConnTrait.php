<?php namespace Wordplan\Admin\Traits;

defined('ABSPATH') || exit;

use Wordplan\Data\Entities\Conn;
use Wordplan\Exceptions\Exception;

/**
 * ProcessConnTrait
 *
 * @package Wordplan\Admin\Traits
 */
trait ProcessConnTrait
{
	/**
	 * @var Conn
	 */
	protected $conn;

	/**
	 * @param $conn_id
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function setAccount($conn_id): bool
	{
		if($conn_id instanceof Conn)
		{
			$this->conn = $conn_id;

			return false;
		}

		$error = false;

		try
		{
			$conn = new Conn($conn_id);

			if(!$conn->getStorage()->isExistingById($conn_id))
			{
				$error = true;
			}

			$this->conn = $conn;
		}
		catch(Exception $e)
		{
			$error = true;
		}

		return $error;
	}

	/**
	 * @return Conn
	 */
	public function getAccount(): Conn
	{
		return $this->conn;
	}
}