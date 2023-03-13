<?php namespace Wordplan\Admin;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Conns\Dashboard;
use Wordplan\Admin\Conns\Delete;
use Wordplan\Admin\Conns\All;
use Wordplan\Admin\Conns\Verification;
use Wordplan\Data\Storage;
use Wordplan\Data\Storages\ConnsStorage;
use Wordplan\Traits\UtilityTrait;

/**
 * Class Conns
 *
 * @package Wordplan\Admin
 */
class Conns
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var array Available actions
	 */
	private $actions =
	[
		'all',
		'dashboard',
		'delete',
		'verification'
	];

	/**
	 * Current action
	 *
	 * @var string
	 */
	private $current_action = 'all';

	/**
	 * Conns constructor.
	 */
	public function __construct()
	{
		$actions = apply_filters('wordplan_admin_conns_init_actions', $this->actions);

		$this->set_actions($actions);

		$current_action = $this->init_current_action();

		switch($current_action)
		{
			case 'dashboard':
				Dashboard::instance();
				break;
			case 'delete':
				Delete::instance();
				break;
			case 'verification':
				Verification::instance();
				break;
			default:
				/** @var ConnsStorage $conns */
				$conns = Storage::load('conn');

				$total_items = $conns->count();

				if($total_items === 1)
				{
					$storage_args['limit'] = 2;
					$data = $conns->getData($storage_args, ARRAY_A);

					if(isset($data[0]))
					{
						wp_safe_redirect($this->utilityAdminConnsGetUrl('dashboard', $data[0]['conn_id']));
					}
				}
				else
				{
					All::instance();
				}
		}
	}

	/**
	 * Current action
	 *
	 * @return string
	 */
	public function init_current_action(): string
	{
		$do_action = wordplan()->getVar($_GET['do_action'], 'all');

		if(in_array($do_action, $this->get_actions(), true))
		{
			$this->set_current_action($do_action);
		}

		return $this->get_current_action();
	}

	/**
	 * Get actions
	 *
	 * @return array
	 */
	public function get_actions(): array
	{
		return apply_filters('wordplan_admin_conns_get_actions', $this->actions);
	}

	/**
	 * Set actions
	 *
	 * @param array $actions
	 */
	public function set_actions(array $actions)
	{
		// hook
		$actions = apply_filters('wordplan_admin_conns_set_actions', $actions);

		$this->actions = $actions;
	}

	/**
	 * Get current action
	 *
	 * @return string
	 */
	public function get_current_action(): string
	{
		return apply_filters('wordplan_admin_conns_get_current_action', $this->current_action);
	}

	/**
	 * Set current action
	 *
	 * @param string $current_action
	 */
	public function set_current_action(string $current_action)
	{
		// hook
		$current_action = apply_filters('wordplan_admin_conns_set_current_action', $current_action);

		$this->current_action = $current_action;
	}
}