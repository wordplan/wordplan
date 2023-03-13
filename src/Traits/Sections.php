<?php namespace Wordplan\Traits;

defined('ABSPATH') || exit;

/**
 * Trait Sections
 *
 * @package Wordplan
 */
trait Sections
{
	/**
	 * Sections
	 *
	 * @var array
	 */
	private $sections = [];

	/**
	 * Current section
	 *
	 * @var string
	 */
	private $current_section = '';

	/**
	 * Get current section
	 *
	 * @return string
	 */
	public function getCurrentSection(): string
	{
		return $this->current_section;
	}

	/**
	 * Set current section
	 *
	 * @param string $current_section
	 */
	public function setCurrentSection(string $current_section)
	{
		$this->current_section = $current_section;
	}

	/**
	 * Initializing current section
	 *
	 * @return string
	 */
	public function initCurrentSection(): string
	{
		$current_section = !empty($_GET['section']) ? sanitize_key($_GET['section']) : '';

		if($current_section !== '')
		{
			$this->setCurrentSection($current_section);
		}

		return $this->getCurrentSection();
	}

	/**
	 * Initialization
	 *
	 * @param array $sections
	 */
	public function initSections($sections = [])
	{
		$default_sections = [];

		if(!empty($sections) && is_array($sections))
		{
			$default_sections = array_merge($default_sections, $sections);
		}

		$final = apply_filters('wordplan_admin_init_sections', $default_sections);

		$this->setSections($final);
	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function getSections(): array
	{
		return apply_filters('wordplan_admin_get_sections', $this->sections);
	}

	/**
	 * Set sections
	 *
	 * @param array $sections
	 */
	public function setSections(array $sections)
	{
		// hook
		$sections = apply_filters('wordplan_admin_set_sections', $sections);

		$this->sections = $sections;
	}
}