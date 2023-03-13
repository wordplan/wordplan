<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Exception;
use Wordplan\Abstracts\TableAbstract;
use Wordplan\Data\Storage;
use Wordplan\Data\Storages\ConnsStorage;
use Wordplan\Traits\ConnsUtilityTrait;
use Wordplan\Traits\DatetimeUtilityTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * Class AllTable
 *
 * @package Wordplan\Admin\Conns
 */
class AllTable extends TableAbstract
{
	use ConnsUtilityTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;

	/**
	 * Conns storage
	 *
	 * @var ConnsStorage
	 */
	public $storage_conns;

	/**
	 * ListsTable constructor.
	 */
	public function __construct()
	{
	    $params =
        [
            'singular' => 'conn',
            'plural' => 'conns',
            'ajax' => false
        ];

		try
		{
			$this->storage_conns = Storage::load('conn');
		}
		catch(\Throwable $e){}

		parent::__construct($params);
	}

	/**
	 * No items found text
	 */
	public function noItems()
	{
		wordplan()->views()->getView('conns/empty.php');
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag
	 *
	 * @return array - list of CSS classes for the table tag
	 */
	protected function getTableClasses(): array
	{
		return
        [
		    'widefat',
            'striped',
            $this->_args['plural']
        ];
	}

	/**
	 * Default print rows
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string
	 */
	public function columnDefault($item, string $column_name): string
	{
		switch ($column_name)
		{
			case 'conn_id':
				return $item['conn_id'];
			case 'date_create':
			case 'date_activity':
			case 'date_modify':
				return $this->prettyColumnsDate($item, $column_name);
			default:
				return print_r($item, true);
		}
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return string
	 */
	private function prettyColumnsDate($item, $column_name): string
	{
		$date = $item[$column_name];
		$timestamp = $this->utilityStringToTimestamp($date) + $this->utilityTimezoneOffset();

		if(!empty($date))
		{
			return sprintf
			(
				'%s <br/><span class="time">%s: %s</span><br>%s',
				date_i18n('d/m/Y', $timestamp),
				__('Time', 'wordplan'),
				date_i18n('H:i:s', $timestamp),
				sprintf(_x('(%s ago)', '%s = human-readable time difference', 'wordplan'), human_time_diff($timestamp, current_time('timestamp')))
			);
		}

		return __('No activity', 'wordplan');
	}

	/**
	 * Conn status
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function columnStatus($item): string
	{
		$status = $this->utilityConnsGetStatusesLabel($item['status']);
		$status_return = $this->utilityConnsGetStatusesLabel('error');

		if($item['status'] === 'draft')
		{
			$status_return = '<span class="draft">' . $status . '</span>';
		}
		if($item['status'] === 'active')
		{
			$status_return = '<span class="active">' . $status . '</span>';
		}
		if($item['status'] === 'inactive')
		{
			$status_return = '<span class="inactive">' . $status . '</span>';
		}
		if($item['status'] === 'processing')
		{
			$status_return = '<span class="processing">' . $status . '</span>';
		}
		if($item['status'] === 'error')
		{
			$status_return = '<span class="error">' . $status . '</span>';
		}
		if($item['status'] === 'deleted')
		{
			$status_return = '<span class="deleted">' . $status . '</span>';
		}

		return $status_return;
	}

	/**
	 * Conn connection type
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_conn_type($item): string
	{
		$actions =
		[
			'dashboard' => '<a href="' . $this->utilityAdminConnsGetUrl('dashboard', $item['conn_id']) . '">' . __('Dashboard', 'wordplan') . '</a>',
			'verification' => '<a href="' . $this->utilityAdminConnsGetUrl('verification', $item['conn_id']) . '">' . __('Verification', 'wordplan') . '</a>',
			'delete' => '<a href="' . $this->utilityAdminConnsGetUrl('delete', $item['conn_id']) . '">' . __('Mark as deleted', 'wordplan') . '</a>',
		];

		if('deleted' === $item['status'] || ('draft' === $item['status'] && 'yes' === wordplan()->settings()->get('conns_draft_delete', 'yes')))
		{
			unset($actions['verification']);
			$actions['delete'] = '<a href="' . $this->utilityAdminConnsGetUrl('delete', $item['conn_id']) . '">' . __('Remove forever', 'wordplan') . '</a>';
		}

		if('active' === $item['status'])
		{
			unset($actions['delete']);
		}

		$actions = apply_filters('wordplan_admin_conns_all_row_actions', $actions, $item);

		$user = get_userdata($item['user_id']);
		if($user instanceof \WP_User && $user->exists())
		{
			$metas['user'] = __('User: ', 'wordplan') . $user->get('nickname') . ' (' . $item['user_id']. ')';
		}
		else
		{
			$metas['user'] =  __('User is not exists.', 'wordplan');
		}

        if(has_filter('wordplan_admin_conns_all_row_metas'))
        {
            $metas = apply_filters('wordplan_admin_conns_all_row_metas', $metas, $item);
        }

		$metas['conn_type'] = __('Connection type: ', 'wordplan') . '<b>' . $this->utilityConnsGetTypesLabel($item['conn_type']) . '</b>';

		return sprintf( '<span class="conn-name">%1$s</span><div class="conn-metas">%2$s</div><div class="conn-actions">%3$s</div>',
			$item['name'],
			$this->rowMetas($metas),
			$this->rowActions($actions, true)
		);
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public function rowMetas($data): string
	{
		$metas_count = count($data);

		if(!$metas_count)
		{
			return '';
		}

		$out = '<div class="row-metas">';

		foreach($data as $meta => $meta_text)
		{
			$out .= "<div class='row-metas-line $meta'>$meta_text</div>";
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * All columns
	 *
	 * @return array
	 */
	public function getColumns(): array
	{
		$columns = [];

		$columns['conn_id'] = __('ID', 'wordplan');
		$columns['conn_type'] = __('Base information', 'wordplan');
		$columns['status'] = __('Status', 'wordplan');
		$columns['date_create'] = __('Connection date', 'wordplan');
		$columns['date_activity'] = __('Last activity', 'wordplan');

		return $columns;
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function getSortableColumns(): array
	{
		$sortable_columns['conn_id'] = ['conn_id', false];
		$sortable_columns['status'] = ['status', false];

		return $sortable_columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string The name of the primary column
	 */
	protected function getDefaultPrimaryColumnName(): string
	{
		return 'conn_id';
	}

	/**
	 * Creates the different status filter links at the top of the table.
	 *
	 * @return array
	 * @throws Exception
	 */
	public function getViews(): array
	{
		$status_links = [];
		$current = !empty($_REQUEST['status']) ? sanitize_text_field($_REQUEST['status']) : 'all';

		// All link
		$class = $current === 'all' ? ' class="current"' :'';
		$all_url = remove_query_arg('status');

		$status_links['all'] = sprintf
		(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$all_url,
			$class,
			__('All', 'wordplan'),
			$this->storage_conns->count()
		);

		$statuses = $this->utilityConnsGetStatuses();

		foreach($statuses as $status_key)
		{
			$count = $this->storage_conns->countBy
            (
				[
					'status' => $status_key
				]
			);

			if($count === 0)
			{
				continue;
			}

			$class = $current === $status_key ? ' class="current"' :'';
			$sold_url = esc_url(add_query_arg('status', $status_key));

			$status_links[$status_key] = sprintf
			(
				'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
				$sold_url,
				$class,
				$this->utilityConnsGetStatusesFolder($status_key),
				$count
			);
		}

		return $status_links;
	}

	/**
	 * Build items
	 */
	public function prepareItems()
	{
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = wordplan()->settings()->get('conns_show_per_page', 10);

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->getColumns();
		$hidden = [];
		$sortable = $this->getSortableColumns();

		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = [$columns, $hidden, $sortable];

		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->getPagenum();

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$offset = 0;

		if(1 < $current_page)
		{
			$offset = $per_page * ($current_page - 1);
		}

		$orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'conn_id';
		$order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'desc';

		$storage_args = [];

		if(array_key_exists('status', $_GET) && in_array($_GET['status'], $this->utilityConnsGetStatuses(), true))
		{
			$storage_args['status'] = sanitize_text_field($_GET['status']);
		}

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		if(empty($storage_args))
		{
			$total_items = $this->storage_conns->count();
		}
		else
		{
			$total_items = $this->storage_conns->countBy($storage_args);
		}

		$storage_args['offset'] = $offset;
		$storage_args['limit'] = $per_page;
		$storage_args['orderby'] = $orderby;
		$storage_args['order'] = $order;

		$this->items = $this->storage_conns->getData($storage_args, ARRAY_A);

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->setPaginationArgs
		(
			[
				'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ]
		);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string $which
	 */
	protected function extraTablenav(string $which)
	{
		if('top' === $which)
		{
			$this->views();
		}
	}
}