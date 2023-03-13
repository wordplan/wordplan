<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Exception;
use Wordplan\Data\Entities\Conn;
use Wordplan\Traits\UtilityTrait;

/**
 * Class Delete
 *
 * @package Wordplan\Admin\Conns
 */
class Delete
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var Conn
	 */
	protected $conn;

	/**
	 * Delete constructor.
     *
	 * @throws Exception
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

			$this->setAccount($conn);
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
			$this->process($this->getAccount());
		}
	}

	/**
	 * Delete processing
	 *
	 * @param $conn
	 *
	 * @throws Exception
	 */
	public function process($conn)
	{
		$delete = false;
		$redirect = true;
		$force_delete = false;
		$conn_status = $conn->getStatus();

		$notice_args['type'] = 'error';
		$notice_args['data'] = __('Error. The conn to be deleted is active and cannot be deleted.', 'wordplan');

		/**
		 * Защита от удаления активных соединений
		 */
		if(!$conn->isStatus('active') && !$conn->isStatus('processing'))
		{
			/**
			 * Окончательное удаление черновиков без корзины
			 */
			if($conn_status === 'draft' && 'yes' === wordplan()->settings()->get('conns_draft_delete', 'yes'))
			{
				$delete = true;
				$force_delete = true;
			}

			/**
			 * Помещение в корзину без удаления
			 */
			if($conn_status !== 'deleted' && $force_delete === false)
			{
				$delete = true;
			}

			/**
			 * Окончательное удаление из корзины - вывод формы для подтверждения удаления
			 */
			if($conn_status === 'deleted')
			{
				$redirect = false;
				$delete_form = new DeleteForm();

				if(!$delete_form->save())
				{
					add_action('wordplan_admin_conns_form_delete_show', [$delete_form, 'outputForm']);
					add_action('wordplan_admin_show', [$this, 'output'], 10);
				}
				else
				{
					$delete = true;
					$force_delete = true;
					$redirect = true;
				}
			}

			/**
			 * Удаление с переносом в список всех учетных записей и выводом уведомления об удалении
			 */
			if($delete)
			{
				$notice_args =
				[
					'type' => 'update',
					'data' => __('The conn has been marked as deleted.', 'wordplan')
				];

				if($force_delete)
				{
					$notice_args =
					[
						'type' => 'update',
						'data' => __('The conn has been successfully disconnected.', 'wordplan')
					];
				}

				if(!$conn->delete($force_delete))
				{
					$notice_args['type'] = 'error';
					$notice_args['data'] = __('Deleting error. Please retry again.', 'wordplan');
				}
			}
		}

		if($redirect)
		{
			wordplan()->admin()->notices()->create($notice_args);
			wp_safe_redirect($this->utilityAdminConnsGetUrl());
			die;
		}
	}

	/**
	 * @return Conn
	 */
	public function getAccount(): Conn
    {
		return $this->conn;
	}

	/**
	 * @param Conn $conn
	 */
	public function setAccount(Conn $conn)
	{
		$this->conn = $conn;
	}

	/**
	 * Output error
	 */
	public function output_error()
	{
		wordplan()->views()->getView('conns/delete_error.php');
	}

	/**
	 * Output permanent remove
	 *
	 * @return void
	 */
	public function output()
	{
        $args['conn'] = $this->getAccount();

		wordplan()->views()->getView('conns/delete.php', $args);
	}
}