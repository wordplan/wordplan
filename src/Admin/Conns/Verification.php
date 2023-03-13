<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Data\Entities\Conn;
use Wordplan\Traits\UtilityTrait;

/**
 * Class Verification
 *
 * @package Wordplan\Admin\Conns
 */
class Verification
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var Conn
	 */
	protected $conn;

	/**
	 * Verification constructor.
	 */
	public function __construct()
	{
		$conn_id = wordplan()->getVar($_GET['conn_id'], 0);
		$error = false;

		try
		{
			$conn = new Conn($conn_id);

			if(!$conn->getStorage()->isExistingById($conn_id))
			{
				$error = true;
			}

			$this->setConn($conn);
		}
		catch(\Throwable $e)
		{
			$error = true;
		}

		if($error)
		{
			add_action('wordplan_admin_show', [$this, 'output_error'], 10);
		}
		else
		{
			$this->process($this->getConn());
		}
	}

	/**
	 * Verification processing
	 *
	 * @param $conn
	 */
	public function process($conn)
	{
		$conn_status = $conn->getStatus();

		if($conn_status === 'deleted')
		{
			$notice_args =
			[
				'dismissible' => true,
				'type' => 'error',
				'data' => __('The conn from Megaplan has been deleted. It is not possible to check the relevance.', 'wordplan')
			];
		}
		else
		{
			$notice_args =
			[
				'dismissible' => true,
				'type' => 'update',
				'data' => sprintf
				(
					'%1$s <span class="name">%2$s</span>',
					__('The following conns have been successfully verified and connected:', 'wordplan'),
					$conn->getName()
				)
			];

			try
			{
				$response = $conn->megaplan()->api('/currency')->get();

				$response = json_decode($response, true);

				if(!empty($response['errors']))
				{
					$notice_args =
					[
						'dismissible' => true,
						'type' => 'error',
						'data' => __('Verify connection error. Test connection is not success.', 'wordplan')
					];
				}
			}
			catch(\Throwable $e)
			{
				$notice_args['type'] = 'error';
				$notice_args['data'] = sprintf
				(
					'%1$s <span class="name">%2$s</span>',
					__('The following conns contain errors and have been disabled:', 'wordplan'),
					$conn->getName()
				);
				$notice_args['extra_data'] = $e->getMessage();
			}
		}

		wordplan()->admin()->notices()->create($notice_args);
		wp_safe_redirect($this->utilityAdminConnsGetUrl('all'));
		die;
	}

	/**
	 * @return Conn
	 */
	public function getConn(): Conn
	{
		return $this->conn;
	}

	/**
	 * @param Conn $conn
	 */
	public function setConn(Conn $conn)
	{
		$this->conn = $conn;
	}

	/**
	 * Output error
	 */
	public function output_error()
	{
		wordplan()->views()->getView('conns/error.php');
	}
}