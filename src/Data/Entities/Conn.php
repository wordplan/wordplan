<?php namespace Wordplan\Data\Entities;

defined('ABSPATH') || exit;

use Digiom\ApiMegaplan\Client;
use Wordplan\Data\Abstracts\ConnsDataAbstract;
use Wordplan\Data\Abstracts\DataAbstract;
use Wordplan\Data\Storage;
use Wordplan\Datetime;
use Wordplan\Exceptions\Exception;
use function Wordplan\core;

/**
 * Conn
 *
 * @package Wordplan\Data
 */
class Conn extends ConnsDataAbstract
{
	/**
	 * @var null|Client
	 */
	protected $megaplan = null;

	/**
	 * @var array Default data
	 */
	protected $data =
	[
		'user_id' => 0,
		'name' => '',
		'conn_type' => '',
		'status' => 'draft',
		'options' => [],
		'date_create' => null,
		'date_modify' => null,
		'date_activity' => null,
		'megaplan_login' => '',
		'megaplan_password' => ''
	];

	/**
	 * 'token' => '',
	'token_refresh' => '',
	'token_expire' => 0,
	'app_token' => '',
	'app_uuid' => '',
	 * @var array Default metadata
	 */
	protected $meta_data =
	[

	];

	/**
	 * Object constructor.
	 *
	 * @param int|DataAbstract $data
	 *
	 * @throws Exception|\Exception
	 */
	public function __construct($data = 0)
	{
		parent::__construct();

		if(is_numeric($data) && $data > 0)
		{
			$this->setId($data);
		}
		elseif($data instanceof self)
		{
			$this->setId(absint($data->getId()));
		}
		else
		{
			$this->setObjectRead(true);
		}

		$this->storage = Storage::load($this->object_type);

		if($this->getId() > 0)
		{
			$this->storage->read($this);
		}
	}

	/**
	 * Get user id
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getUserId(string $context = 'view'): string
	{
		return $this->getProp('user_id', $context);
	}

	/**
	 * Set user id
	 *
	 * @param string|int $value user_id
	 */
	public function setUserId($value)
	{
		$this->setProp('user_id', $value);
	}

	/**
	 * Get name
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getName(string $context = 'view'): string
	{
		return $this->getProp('name', $context);
	}

	/**
	 * Set name
	 *
	 * @param string $value name
	 */
	public function setName(string $value)
	{
		$this->setProp('name', $value);
	}

	/**
	 * Get status
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getStatus(string $context = 'view'): string
	{
		return $this->getProp('status', $context);
	}

	/**
	 * Set status
	 *
	 * @param string $value status
	 */
	public function setStatus(string $value)
	{
		$this->setProp('status', $value);
	}

	/**
	 * Get options
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return array
	 */
	public function getOptions(string $context = 'view'): array
	{
		return $this->getProp('options', $context);
	}

	/**
	 * Set options
	 *
	 * @param array $value options
	 */
	public function setOptions(array $value)
	{
		$this->setProp('options', $value);
	}

	/**
	 * Get created date
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return Datetime|NULL object if the date is set or null if there is no date.
	 */
	public function getDateCreate(string $context = 'view')
	{
		return $this->getProp('date_create', $context);
	}

	/**
	 * Get modified date
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return Datetime|NULL object if the date is set or null if there is no date.
	 */
	public function getDateModify(string $context = 'view')
	{
		return $this->getProp('date_modify', $context);
	}

	/**
	 * Get activity date
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return Datetime|NULL object if the date is set or null if there is no date.
	 */
	public function getDateActivity(string $context = 'view')
	{
		return $this->getProp('date_activity', $context);
	}

	/**
	 * Set created date
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 * If the DateTime string has no timezone or
	 * offset, WordPress site timezone will be assumed. Null if their is no date.
	 *
	 * @throws \Exception
	 */
	public function setDateCreate($date = null)
	{
		$this->setDateProp('date_create', $date);
	}

	/**
	 * Set modified date
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 * If the DateTime string has no timezone or
	 * offset, WordPress site timezone will be assumed. Null if their is no date.
	 *
	 * @throws \Digiom\Woplucore\Data\Exceptions\Exception
	 */
	public function setDateModify($date = null)
	{
		$this->setDateProp('date_modify', $date);
	}

	/**
	 * Set activity date
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime.
	 * If the DateTime string has no timezone or
	 * offset, WordPress site timezone will be assumed. Null if their is no date.
	 *
	 * @throws \Digiom\Woplucore\Data\Exceptions\Exception
	 */
	public function setDateActivity($date = null)
	{
		$this->setDateProp('date_activity', $date);
	}

	/**
	 * Returns if configuration is active.
	 *
	 * @return bool True if validation passes.
	 */
	public function isActive(): bool
	{
		return $this->isStatus('active');
	}

	/**
	 * Returns if configuration is inactive.
	 *
	 * @return bool True if validation passes.
	 */
	public function isInactive(): bool
	{
		return $this->isStatus('inactive');
	}

	/**
	 * Returns if configuration enabled or not enabled.
	 *
	 * @return bool True if passes.
	 */
	public function isEnabled(): bool
	{
		$enabled = true;

		if($this->isInactive() || $this->isDraft())
		{
			$enabled = false;
		}

		return apply_filters($this->getHookPrefix() . 'enabled', $enabled, $this);
	}

	/**
	 * Returns if configuration is draft.
	 *
	 * @return bool True if validation passes.
	 */
	public function isDraft(): bool
	{
		return $this->isStatus('draft');
	}

	/**
	 * Returns if configuration is status.
	 *
	 * @param string $status
	 *
	 * @return bool True if validation passes.
	 */
	public function isStatus(string $status = 'active'): bool
	{
		return $status === $this->getStatus();
	}

	/**
	 * Returns if conn is type.
	 *
	 * @param string $type
	 *
	 * @return bool True if validation passes.
	 */
	public function isConnType(string $type = 'user'): bool
	{
		return $type === $this->getConnType();
	}

	/**
	 * Returns upload directory for configuration.
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getUploadDirectory(string $context = 'main'): string
	{
		$upload_directory = core()->environment()->get('wordplan_conns_directory') . '/' . $this->getId();

		if($context === 'logs')
		{
			$upload_directory .= '/logs';
		}

        if($context === 'files')
        {
            $upload_directory .= '/files';
        }

		return $upload_directory;
	}

	/**
	 * Get megaplan_token
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanToken(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_token', true, $context);
	}

	/**
	 * Set megaplan_token
	 *
	 * @param string $value megaplan_token
	 */
	public function setMegaplanToken(string $value)
	{
		$this->addMetaData('megaplan_token', $value, true);
	}

	/**
	 * Set megaplan_host
	 *
	 * @param string $value megaplan_host
	 */
	public function setMegaplanHost(string $value)
	{
		$this->addMetaData('megaplan_host', $value, true);
	}

	/**
	 * Get megaplan_host
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanHost(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_host', true, $context);
	}

	/**
	 * Get megaplan_token_refresh
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanTokenRefresh(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_token_refresh', true, $context);
	}

	/**
	 * Set megaplan_token_refresh
	 *
	 * @param string $value megaplan_token_refresh
	 */
	public function setMegaplanTokenRefresh(string $value)
	{
		$this->addMetaData('megaplan_token_refresh', $value, true);
	}

	/**
	 * Get megaplan_token_expires
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanTokenExpires(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_token_expires', true, $context);
	}

	/**
	 * Set megaplan_token_expires
	 *
	 * @param string $value megaplan_token_expires
	 */
	public function setMegaplanTokenExpires(string $value)
	{
		$this->addMetaData('megaplan_token_expires', $value, true);
	}

	/**
	 * Set megaplan_app
	 *
	 * @param string $value megaplan_app
	 */
	public function setMegaplanApp(string $value)
	{
		$this->addMetaData('megaplan_app', $value, true);
	}

	/**
	 * Get megaplan_app
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanApp(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_app', true, $context);
	}

	/**
	 * Set megaplan_app_token
	 *
	 * @param string $value megaplan_app_token
	 */
	public function setMegaplanAppToken(string $value)
	{
		$this->addMetaData('megaplan_app_token', $value, true);
	}

	/**
	 * Get megaplan_app_token
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanAppToken(string $context = 'view'): string
	{
		return $this->getMeta('megaplan_app_token', true, $context);
	}

	/**
	 * Get connection type
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getConnType(string $context = 'view'): string
	{
		return $this->getProp('conn_type', $context);
	}

	/**
	 * Set connection type
	 *
	 * @param string $value Type - user ot app
	 */
	public function setConnType(string $value)
	{
		$this->setProp('conn_type', $value);
	}

	/**
	 * Queries for API Megaplan by current Conn
	 *
	 * @return Client
	 * @throws \Exception
	 */
	public function megaplan(): Client
	{
		if(!is_null($this->megaplan))
		{
			return $this->megaplan;
		}

		$host = $this->getMegaplanHost();

		$force_https = true;
		if(core()->settings()->get('api_megaplan_force_https', 'yes') !== 'yes')
		{
			$force_https = false;
		}

		$client = new Client($host, $force_https, []);

		if($this->isConnType('app'))
		{
			$client->setType('app');

			$client->setAppUuid($this->getMegaplanApp());
			$client->setAppToken($this->getMegaplanAppToken());
		}
		else
		{
			$client->setType('user');
			$time = time();

			/*
			 * Если пароль и логин, то запрашиваем токен с дальнейшим сохранением
			 */
			if($this->getMegaplanLogin())
			{
				$token = $client->generateTokenByData
				(
					[
						'login' => $this->getMegaplanLogin(),
						'password' => $this->getMegaplanPassword()
					]
				);

				if(!empty($token))
				{
					$this->setMegaplanToken($token);

					$expires = $client->getTokenExpires() + $time;

					$this->setMegaplanTokenRefresh($client->getTokenRefresh());
					$this->setMegaplanTokenExpires($expires);

					$this->save();
				}
			}

			/*
			 * Если срок токена истекает, запрашиваем новый
			 */
			if((int)$this->getMegaplanTokenExpires() < 0)
			{

			}

			$client->setToken($this->getMegaplanToken());
		}

		$this->megaplan = $client;

		return $this->megaplan;
	}

	/**
	 * Get megaplan_password
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanPassword(string $context = 'view'): string
	{
		return $this->getProp('megaplan_password', $context);
	}

	/**
	 * Set megaplan_password
	 *
	 * @param string $value megaplan_password
	 */
	public function setMegaplanPassword(string $value)
	{
		$this->setProp('megaplan_password', $value);
	}

	/**
	 * Get megaplan_login
	 *
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return string
	 */
	public function getMegaplanLogin(string $context = 'view'): string
	{
		return $this->getProp('megaplan_login', $context);
	}

	/**
	 * Set megaplan_login
	 *
	 * @param string $value megaplan_login
	 */
	public function setMegaplanLogin(string $value)
	{
		$this->setProp('megaplan_login', $value);
	}
}