<?php namespace Wordplan\Traits;

defined('ABSPATH') || exit;

/**
 * ConnsUtilityTrait
 *
 * @package Wordplan\Traits
 */
trait ConnsUtilityTrait
{
	/**
	 * Get all available Conns statuses
	 *
	 * @return array
	 */
	public function utilityConnsGetStatuses(): array
	{
		$statuses =
		[
			'draft',
			'inactive',
			'active',
			'processing',
			'error',
			'deleted',
		];

		return apply_filters('wordplan_conns_get_statuses', $statuses);
	}

	/**
	 * Get normal Conns status
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function utilityConnsGetStatusesLabel(string $status): string
	{
		$default_label = __('Undefined', 'wordplan');

		$statuses_labels = apply_filters
		(
			'wordplan_conns_get_statuses_labels',
			[
				'draft' => __('Draft', 'wordplan'),
				'active' => __('Active', 'wordplan'),
				'inactive' => __('Inactive', 'wordplan'),
				'error' => __('Error', 'wordplan'),
				'processing' => __('Processing', 'wordplan'),
				'deleted' => __('Deleted', 'wordplan'),
			]
		);

		if(empty($status) || !array_key_exists($status, $statuses_labels))
		{
			$status_label = $default_label;
		}
		else
		{
			$status_label = $statuses_labels[$status];
		}

		return apply_filters('wordplan_conns_get_statuses_label_return', $status_label, $status, $statuses_labels);
	}

	/**
	 * Get normal Conns types
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function utilityConnsGetTypesLabel(string $status): string
	{
		$default_label = __('Undefined', 'wordplan');

		$statuses_labels = apply_filters
		(
			'wordplan_conns_get_types_labels',
			[
				'user' => __('by User', 'wordplan'),
				'app' => __('by Application', 'wordplan'),
			]
		);

		if(empty($status) || !array_key_exists($status, $statuses_labels))
		{
			$status_label = $default_label;
		}
		else
		{
			$status_label = $statuses_labels[$status];
		}

		return apply_filters('wordplan_conns_get_types_label_return', $status_label, $status, $statuses_labels);
	}

	/**
	 * Get folder name for conn statuses
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function utilityConnsGetStatusesFolder(string $status): string
	{
		$default_folder = __('Undefined', 'wordplan');

		$statuses_folders = apply_filters
		(
			'wordplan_conns_get_statuses_folders',
			[
				'draft' => __('Drafts', 'wordplan'),
				'active' => __('Activated', 'wordplan'),
				'inactive' => __('Deactivated', 'wordplan'),
				'error' => __('With errors', 'wordplan'),
				'processing' => __('In processing', 'wordplan'),
				'deleted' => __('Trash', 'wordplan'),
			]
		);

		$status_folder = $default_folder;

		if(!empty($status) || array_key_exists($status, $statuses_folders))
		{
			$status_folder = $statuses_folders[$status];
		}

		return apply_filters('wordplan_conns_get_statuses_folder_return', $status_folder, $status, $statuses_folders);
	}
}