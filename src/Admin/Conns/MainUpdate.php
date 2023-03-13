<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Admin\Traits\ProcessConnTrait;
use Wordplan\Traits\DatetimeUtilityTrait;
use Wordplan\Traits\SectionsTrait;
use Wordplan\Traits\UtilityTrait;

/**
 * MainUpdate
 *
 * @package Wordplan\Admin
 */
class MainUpdate
{
	use SingletonTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;
	use SectionsTrait;
	use ProcessConnTrait;

	/**
	 * Update processing
	 */
	public function process()
	{
		$conn = $this->getConn();

		add_filter('wordplan_conns-update_form_load_fields', [$this, 'connsFieldsOther'], 120, 1);
		add_filter('wordplan_conns-update_form_load_fields', [$this, 'connsFieldsLogs'], 100, 1);

		if($conn->getConnType() === 'app')
		{
			add_filter('wordplan_conns-update_form_load_fields', [$this, 'connsFieldsByApp'], 20, 1);
		}
		else
		{
			add_filter('wordplan_conns-update_form_load_fields', [$this, 'connsFieldsLoginAndPassword'], 20, 1);
		}

		$form = new UpdateForm();

		$form_data = $conn->getOptions();

        $form_data['status'] = $conn->isEnabled() ? 'yes' : 'no';

		if($conn->isConnType('user'))
		{
			$form_data['token'] = $conn->getMegaplanToken();
		}
		else
		{
			$form_data['uuid'] = $conn->getMegaplanApp();
			$form_data['token'] = $conn->getMegaplanAppToken();
		}

		$form->loadSavedData($form_data);

		if(isset($_GET['form']) && $_GET['form'] === $form->getId())
		{
			$data = $form->save();

			if($data)
			{
                // Галка стоит
                if($data['status'] === 'yes')
                {
                    if($conn->isEnabled() === false)
                    {
                        $conn->setStatus('active');
                    }
                }
                // галка не стоит
                else
                {
                    $conn->setStatus('inactive');
                }

				if($conn->isConnType('user'))
				{
					$conn->setMegaplanLogin($data['login']);
					$conn->setMegaplanPassword($data['password']);
					$conn->setMegaplanToken($data['token']);
				}
				else
				{
					$conn->setMegaplanApp($data['uuid']);
					$conn->setMegaplanAppToken($data['token']);
				}

				unset($data['status'], $data['login'], $data['password'], $data['token'], $data['uuid']);

				$conn->setDateModify(time());
				$conn->setOptions($data);

				$saved = $conn->save();

				if($saved)
				{
					wordplan()->admin()->notices()->create
					(
						[
							'type' => 'update',
							'data' => __('Conn update success.', 'wordplan')
						]
					);
				}
				else
				{
					wordplan()->admin()->notices()->create
					(
						[
							'type' => 'error',
							'data' => __('Conn update error. Please retry saving or change fields.', 'wordplan')
						]
					);
				}
			}
		}

		add_action('wordplan_admin_conns_sections_single_show', [$form, 'outputForm'], 10);
        add_action('wordplan_admin_conns_update_sidebar_show', [$this, 'outputSidebar'], 10);
	}

	/**
	 * Conns fields: app
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function connsFieldsByApp(array $fields): array
	{
        $fields['title_auth'] =
        [
            'title' => __('Authorization data', 'wordplan'),
            'type' => 'title',
            'description' => sprintf
            (
                '%s %s',
                __('Authorization of requests for current conn.', 'wordplan'),
                __('Used for authorization in Megaplan service.', 'wordplan')
            )
        ];

		$fields['uuid'] =
		[
			'title' => __('UUID', 'wordplan'),
			'type' => 'text',
			'description' => __('Unique application identifier from Megaplan.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		$fields['token'] =
		[
			'title' => __('API Token', 'wordplan'),
			'type' => 'text',
			'description' => __('The token can be generated in Megaplan.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		return $fields;
	}

	/**
	 * Conns fields: login & password
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function connsFieldsLoginAndPassword(array $fields): array
	{
		$fields['title_auth'] =
		[
			'title' => __('Authorization data', 'wordplan'),
			'type' => 'title',
			'description' => sprintf
            (
                '%s %s',
                __('Authorization of requests for current conn.', 'wordplan'),
                __('Used for authorization in Megaplan service.', 'wordplan')
            )
		];

		$fields['login'] =
		[
			'title' => __('Username', 'wordplan'),
			'type' => 'text',
			'description' => __('Login in Megaplan. After adding an conn, changing the login is not possible.', 'wordplan'),
			'default' => '',
			'css' => 'min-width: 350px;',
			'class' => 'disabled',
		];

		$fields['password'] =
		[
			'title' => __('User password', 'wordplan'),
			'type' => 'password',
			'description' => __('Password for the specified user Megaplan.', 'wordplan'),
			'default' => '',
			'css' => 'min-width: 350px;'
		];

		$fields['token'] =
		[
			'title' => __('API Token', 'wordplan'),
			'type' => 'text',
			'description' => __('The current access token. Has an expiration date. To force the update, you must enter a username and password.', 'wordplan'),
			'default' => '',
			'css' => 'width: 100%;',
		];

		return $fields;
	}

	/**
	 * Conns fields: logs
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function connsFieldsLogs($fields): array
	{
		$fields['title_logger'] =
		[
			'title' => __('Event logs', 'wordplan'),
			'type' => 'title',
			'description' => __('Maintaining event logs for the current conn. You can view the logs through the extension or via FTP.', 'wordplan'),
		];

		$fields['logger_level'] =
		[
			'title' => __('Level for events', 'wordplan'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wordplan'),
			'default' => '300',
			'options' =>
				[
					'logger_level' => __('Use level for main events', 'wordplan'),
					'100' => __('DEBUG (100)', 'wordplan'),
					'200' => __('INFO (200)', 'wordplan'),
					'250' => __('NOTICE (250)', 'wordplan'),
					'300' => __('WARNING (300)', 'wordplan'),
					'400' => __('ERROR (400)', 'wordplan'),
				],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wordplan'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 30 days.', 'wordplan'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		return $fields;
	}

	/**
	 * Conn fields: other
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function connsFieldsOther($fields): array
	{
		$fields['title_other'] =
		[
			'title' => __('Other parameters', 'wordplan'),
			'type' => 'title',
			'description' => __('Change of data processing behavior for environment compatibility and so on.', 'wordplan'),
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum size of accepted requests', 'wordplan'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<br />%s <b>%s</b><br />%s',
				__('Enter the maximum size of accepted requests from Megaplan at a time in bytes. May be specified with a dimension suffix, such as 7M, where M = megabyte, K = kilobyte, G - gigabyte.', 'wordplan'),
				__('Current WORDPLAN limit:', 'wordplan'),
				wordplan()->settings()->get('php_post_max_size', wordplan()->environment()->get('php_post_max_size')),
				__('Can only decrease the value, because it must not exceed the limits from the WORDPLAN settings.', 'wordplan')
			),
			'default' => wordplan()->settings()->get('php_post_max_size', wordplan()->environment()->get('php_post_max_size')),
			'css' => 'min-width: 100px;',
		];

        $fields['php_max_execution_time'] =
        [
            'title' => __('Maximum time for execution PHP', 'wordplan'),
            'type' => 'text',
            'description' => sprintf
            (
                '%s <br /> %s <b>%s</b> <br /> %s',
                __('Value is seconds. Algorithms of current configuration will run until a time limit is end.', 'wordplan'),
                __('Current WORDPLAN limit:', 'wordplan'),
                wordplan()->settings()->get('php_max_execution_time', wordplan()->environment()->get('php_max_execution_time')),
                __('If specify 0, the time limit will be disabled. Specifying 0 is not recommended, it is recommended not to exceed the WORDPLAN limit.', 'wordplan')
            ),
            'default' => wordplan()->settings()->get('php_max_execution_time', wordplan()->environment()->get('php_max_execution_time')),
            'css' => 'min-width: 100px;',
        ];

		return $fields;
	}

    /**
     * Sidebar show
     */
    public function outputSidebar()
    {
        $conn = $this->getConn();

        $conn_options = $conn->getOptions();
        if(isset($conn_options['logger_level']))
        {
            if((int)$conn_options['logger_level'] === 100)
            {
                $args =
                [
                    'type' => 'danger',
                    'header' => '<h4 class="alert-heading mt-0 mb-1">' . __('Debug is enabled!', 'wordplan') . '</h4>',
                    'object' => $this,
                    'body' => __('The current conn has debug mode enabled. You must disable this mode after debugging is complete.', 'wordplan')
                ];
            }

            if((int)$conn_options['logger_level'] === 200)
            {
                $args =
                [
                    'type' => 'warning',
                    'header' => '<h4 class="alert-heading mt-0 mb-1">' . __('Info is enabled!', 'wordplan') . '</h4>',
                    'object' => $this,
                    'body' => __('The extended information recording mode is enabled for the current conn. It is recommended to disable this mode after debugging is complete.', 'wordplan')
                ];
            }

            if((int)$conn_options['logger_level'] <= 200)
            {
                wordplan()->views()->getView('conns/sidebar_alert_item.php', $args);
            }
        }
    }
}