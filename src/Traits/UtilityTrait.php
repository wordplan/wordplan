<?php namespace Wordplan\Traits;

defined('ABSPATH') || exit;

/**
 * UtilityTrait
 *
 * @package Wordplan\Traits
 */
trait UtilityTrait
{
	/**
	 * Convert kb, mb, gb to bytes
	 *
	 * @param $size
	 *
	 * @return float|int
	 */
	public function utilityConvertFileSize($size)
	{
		if(empty($size))
		{
			return 0;
		}

		$type = $size[strlen($size) - 1];

		if(!is_numeric($type))
		{
			$size = (int) $size;

			switch($type)
			{
				case 'K':
					$size *= 1024;
					break;
				case 'M':
					$size *= 1024 * 1024;
					break;
				case 'G':
					$size *= 1024 * 1024 * 1024;
					break;
				default:
					return $size;
			}
		}

		return (int)$size;
	}

	/**
	 * Is WORDPLAN admin tools request?
	 *
	 * @param string $tool_id
	 *
	 * @return bool
	 */
	public function utilityIsWordplanAdminToolsRequest($tool_id = '')
	{
		if('' === $tool_id)
		{
			return true;
		}

		$get_tool_id = wordplan()->getVar($_GET['tool_id'], '');

		if($get_tool_id !== $tool_id)
		{
			return false;
		}

		return true;
	}

	/**
	 * Is WORDPLAN admin request?
	 *
	 * @return bool
	 */
	public function utilityIsWordplanAdmin()
	{
		if(false !== is_admin() && 'wordplan' === wordplan()->getVar($_GET['page'], ''))
		{
			return true;
		}

		return false;
	}

	/**
	 * Is WC1C admin section request?
	 *
	 * @param string $section
	 *
	 * @return bool
	 */
	public function utilityIsWordplanAdminSectionRequest($section = '')
	{
		if(wordplan()->getVar($_GET['section'], '') !== $section)
		{
			return false;
		}

		if($this->utilityIsWordplanAdmin())
		{
			return true;
		}

		return false;
	}

	/**
	 * @param string $tool_id
	 *
	 * @return string
	 */
	public function utilityAdminToolsGetUrl($tool_id = '')
	{
		$path = 'admin.php?page=wordplan_tools&section=main';

		if('' === $tool_id)
		{
			return admin_url($path);
		}

		$path = 'admin.php?page=wordplan_tools&section=main&tool_id=' . $tool_id;

		return admin_url($path);
	}

	/**
	 * @param string $action
	 * @param string $conn_id
	 *
	 * @return string
	 */
	public function utilityAdminConnsGetUrl($action = 'all', $conn_id = '')
	{
		$path = 'admin.php?page=wordplan';

		if('all' !== $action)
		{
			$path .= '&do_action=' . $action;
		}

		if('' === $conn_id)
		{
			return admin_url($path);
		}

		$path .= '&conn_id=' . $conn_id;

		return admin_url($path);
	}

	/**
	 * @param $data
	 * @param bool $die
	 *
	 * @return void
	 */
	public function dump($data, bool $die = false)
	{
		echo '<pre>';
		var_dump($data);
		echo '</pre>';

		if($die)
		{
			die;
		}
	}
}