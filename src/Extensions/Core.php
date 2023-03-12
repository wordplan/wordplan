<?php namespace Wordplan\Extensions;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Traits\SingletonTrait;
use Wordplan\Exceptions\Exception;
use Wordplan\Extensions\Contracts\ExtensionContract;

/**
 * Core
 *
 * @package Wordplan\Extensions
 */
final class Core
{
	use SingletonTrait;

	/**
	 * @var array All loaded
	 */
	private $extensions = [];

	/**
	 * Set
	 *
	 * @param array $extensions
	 *
	 * @return void
	 * @throws Exception
	 */
	public function set($extensions)
	{
		if(!is_array($extensions))
		{
			throw new Exception(__('Set $extensions is not valid.', 'wordplan'));
		}

		$this->extensions = $extensions;
	}

	/**
	 * Initializing extensions
	 *
	 * @param string $extension_id If an extension ID is specified, only the specified extension is loaded
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init($extension_id = '')
	{
		try
		{
			$extensions = $this->get();
		}
		catch(Exception $e)
		{
			throw $e;
		}

		/**
		 * Init specified extension
		 */
		if('' !== $extension_id)
		{
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Exception(__('Extension not found by id.', 'wordplan'));
			}

			if(!$extensions[$extension_id] instanceof ExtensionContract)
			{
				throw new Exception(__('Extension is not implementation ExtensionContract. Skipped init.', 'wordplan'));
			}

			if($extensions[$extension_id]->isInitialized())
			{
				return;
			}

			try
			{
				$extensions[$extension_id]->init();
				$extensions[$extension_id]->setInitialized(true);
			}
			catch(Exception $e)
			{
				throw new Exception(__('Init extension exception:', 'wordplan') . ' ' . $e->getMessage());
			}

			$this->set($extensions);

			return;
		}

		/**
		 * Init all extensions
		 */
		foreach($extensions as $extension => $extension_object)
		{
			try
			{
				$this->init($extension);
			}
			catch(Exception $e)
			{
				wordplan()->log()->warning($e->getMessage(), ['exception' => $e]);
			}
		}
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|ExtensionContract
	 * @throws Exception
	 */
	public function get($extension_id = '')
	{
		if('' !== $extension_id)
		{
			if(array_key_exists($extension_id, $this->extensions))
			{
				return $this->extensions[$extension_id];
			}

			throw new Exception(__('Get extension by id is unavailable.', 'wordplan'));
		}

		return $this->extensions;
	}

	/**
	 * Extensions load
	 *
	 * @return void
	 * @throws Exception
	 */
	public function load()
	{
		$extensions = [];

		if(has_filter('wordplan_extensions_loading') && 'yes' === wordplan()->settings('main')->get('extensions', 'yes'))
		{
			$extensions = apply_filters('wordplan_extensions_loading', $extensions);
		}

		try
		{
			$this->set($extensions);
		}
		catch(Exception $e)
		{
			throw new Exception(__('Extensions load exception:', 'wordplan') . ' ' . $e->getMessage());
		}
	}
}