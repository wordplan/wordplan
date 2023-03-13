<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Traits\ProcessConnTrait;
use Wordplan\Traits\ConnsUtilityTrait;
use Wordplan\Traits\DatetimeUtilityTrait;
use Wordplan\Traits\SectionsTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Dashboard
 *
 * @package Wordplan\Admin\Conns
 */
class Dashboard
{
	use SingletonTrait;
	use ProcessConnTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;
	use SectionsTrait;
    use ConnsUtilityTrait;

	/**
	 * Dashboard constructor.
	 */
	public function __construct()
	{
		$this->setSectionKey('dashboard_section');

		$default_sections['main'] =
		[
			'title' => __('Settings', 'wordplan'),
            'priority' => 5,
			'visible' => true,
			'callback' => [MainUpdate::class, 'instance'],
			'description' => __('Updating the parameters of all basic settings, including data for authorization in Megaplan.', 'wordplan'),
		];

		if(has_action('wordplan_admin_conns_dashboard_sections'))
		{
			$default_sections = apply_filters('wordplan_admin_conns_dashboard_sections', $default_sections);
		}

		$this->initSections($default_sections);
		$this->setCurrentSection('');

		$conn_id = wordplan()->getVar($_GET['conn_id'], 0);

		if(false === $this->setConn($conn_id))
		{
			$this->process();
		}
		else
		{
			add_action('wordplan_admin_show', [$this, 'outputError'], 10);
			wordplan()->log()->notice('Conn is not available.', ['conn_id' => $conn_id]);
			return;
		}

		$this->route();

		add_action('wordplan_admin_show', [$this, 'output'], 10);
	}

	/**
	 *  Routing
	 */
	public function route()
	{
		$sections = $this->getSections();
		$current_section = $this->initCurrentSection();

		if(empty($current_section))
		{
			add_action('wordplan_admin_conns_dashboard_show', [$this, 'wrapSections'], 5);
			add_action('wordplan_admin_conns_dashboard_sidebar_show', [$this, 'outputSidebar'], 10);

			return;
		}

		if(!array_key_exists($current_section, $sections) || !isset($sections[$current_section]['callback']))
		{
			add_action('wordplan_admin_conns_dashboard_show', [$this, 'wrapError']);
		}
		else
		{
			$callback = $sections[$current_section]['callback'];

			if(is_callable($callback, false, $callback_name))
			{
				$callback_obj = $callback_name();
				$callback_obj->setConn($this->getConn());
				$callback_obj->process();
			}
		}
	}

	/**
	 * Update processing
	 */
	public function process()
	{
		add_action('wordplan_admin_header_items_show', [$this, 'headerItem'], 10);
	}

	public function headerItem()
	{
		$conn = $this->getConn();
		echo wp_kses_post(' > ' . $conn->getName());
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wordplan()->views()->getView('error.php');
	}

	/**
	 * Output error
	 */
	public function outputError()
	{
		$args['back_url'] = $this->utilityAdminConnsGetUrl('all');

		wordplan()->views()->getView('conns/error.php', $args);
	}

	/**
	 * Sections
	 *
	 * @return void
	 */
	public function wrapSections()
	{
		$args['object'] = $this;

		wordplan()->views()->getView('conns/sections.php', $args);
	}

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output()
	{
		$args = [];
		$section = $this->initCurrentSection();

		if($section)
		{
			$name = '';
			$sections = $this->getSections();
			if(isset($sections[$section]['title']))
			{
				$name = $sections[$section]['title'];
			}

			wordplan()->views()->getView('conns/sections_single.php', ['object' => $this, 'name' => $name]);

			return;
		}

		wordplan()->views()->getView('conns/dashboard.php', $args);
	}

	/**
	 * Sidebar show
	 */
	public function outputSidebar()
	{
		$conn = $this->getConn();

		$args =
		[
			'header' => '<h3 class="p-0 m-0">' . __('About conn', 'wordplan') . '</h3>',
			'object' => $this
		];

		$body = '<ul class="list-group m-0 list-group-flush">';

        $body .= '<li class="list-group-item p-2 m-0">';
        $body .= __('Status', 'wordplan') . ': <b>' . $this->utilityConnsGetStatusesLabel($conn->getStatus()) . '</b>';
        $body .= '</li>';

        $body .= '<li class="list-group-item p-2 m-0">';
        $body .= __('Date active:', 'wordplan') . '<div class="p-1 mt-1 bg-light">' . $this->utilityPrettyDate($conn->getDateActivity());

        if($conn->getDateActivity())
        {
            $body .= sprintf(_x(' (%s ago).', '%s = human-readable time difference', 'wordplan'), human_time_diff($conn->getDateActivity()->getOffsetTimestamp(), current_time('timestamp')));
        }
        $body .= '</div></li>';

		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('ID:', 'wordplan') . ' <b>' . $conn->getId() . '</b>';
		$body .= '</li>';

		$body .= '<li class="list-group-item p-2 m-0">';
		$user_id = $conn->getUserId();
		$user = get_userdata($user_id);
		if($user instanceof \WP_User && $user->exists())
		{
			$body .= __('Owner:', 'wordplan') . ' <b>' . $user->get('nickname') . '</b> (' . $user_id. ')';
		}
		else
		{
			$body .= __('User is not exists.', 'wordplan');
		}
		$body .= '</li>';

		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date create:', 'wordplan') . '<div class="p-1 mt-1 bg-light">' . $this->utilityPrettyDate($conn->getDateCreate());

		if($conn->getDateCreate())
		{
			$body .= sprintf(_x(' (%s ago).', '%s = human-readable time difference', 'wordplan'), human_time_diff($conn->getDateCreate()->getOffsetTimestamp(), current_time('timestamp')));
		}

		$body .= '</div></li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date modify:', 'wordplan') . '<div class="p-1 mt-1 bg-light">'. $this->utilityPrettyDate($conn->getDateModify());

		if($conn->getDateModify())
		{
			$body .= sprintf(_x(' (%s ago).', '%s = human-readable time difference', 'wordplan'), human_time_diff($conn->getDateModify()->getOffsetTimestamp(), current_time('timestamp')));
		}

		$body .= '</div></li>';

		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Directory:', 'wordplan') . '<div class="p-1 mt-1 bg-light">' . wp_normalize_path($conn->getUploadDirectory()) . '</div>';
		$body .= '</li>';

		$size = 0;
		$files = wordplan()->filesystem()->files($conn->getUploadDirectory('uploads'));

		foreach($files as $file)
		{
			$size += wordplan()->filesystem()->size($file);
		}

		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Directory size:', 'wordplan') . ' <b>' . size_format($size) . '</b>';
		$body .= '</li>';

		$size = 0;
		$files = wordplan()->filesystem()->files($conn->getUploadDirectory('logs'));

		foreach($files as $file)
		{
			$size += wordplan()->filesystem()->size($file);
		}

		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Logs directory size:', 'wordplan') . ' <b>' . size_format($size) . '</b>';
		$body .= '</li>';

		$body .= '</ul>';

		$args['body'] = $body;

		wordplan()->views()->getView('conns/sidebar_item.php', $args);
	}
}