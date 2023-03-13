<?php namespace Wordplan\Admin\Conns;

defined('ABSPATH') || exit;

use Wordplan\Exceptions\Exception;
use Wordplan\Abstracts\FormAbstract;
use Wordplan\Traits\ConnsUtilityTrait;

/**
 * UpdateForm
 *
 * @package Wordplan\Admin\Conns
 */
class UpdateForm extends FormAbstract
{
    use ConnsUtilityTrait;

	/**
	 * UpdateForm constructor.
	 */
	public function __construct()
	{
		$this->setId('conns-update');

		add_filter('wordplan_' . $this->getId() . '_form_load_fields', [$this, 'init_fields_main'], 3);
		add_action('wordplan_admin_conns_update_sidebar_show', [$this, 'output_navigation'], 20);

		$this->loadFields();
	}

	/**
	 * Add for Main
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function init_fields_main(array $fields): array
	{
		$fields['status'] =
		[
			'title' => __('Status', 'wordplan'),
			'type' => 'checkbox',
			'label' => __('Check the box if you want to enable this conn. Disabled by default.', 'wordplan'),
			'default' => 'no',
			'description' => sprintf
			(
				'%s',
				__('The conn is either enabled or disabled. In the off state, all conn mechanisms will not work.', 'wordplan')
			),
		];

		return $fields;
	}

	/**
	 * Form show
	 */
	public function outputForm()
	{
		$args =
		[
			'object' => $this
		];

		wordplan()->views()->getView('conns/update_form.php', $args);
	}

	/**
	 * Save
	 *
	 * @return array|boolean
	 */
	public function save()
	{
		$post_data = $this->getPostedData();

		if(!isset($post_data['_wordplan-admin-nonce']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wordplan-admin-nonce'], 'wordplan-admin-conns-update-save'))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Update error. Please retry.', 'wordplan')
				]
			);

			return false;
		}

		foreach($this->getFields() as $key => $field)
		{
			$field_type = $this->getFieldType($field);

			if('title' === $field_type || 'raw' === $field_type)
			{
				continue;
			}

			try
			{
				$this->saved_data[$key] = $this->getFieldValue($key, $field, $post_data);
			}
			catch(Exception $e)
			{
				wordplan()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => $e->getMessage()
					]
				);

				return false;
			}
		}

		return $this->getSavedData();
	}

	/**
	 * Navigation show
	 */
	public function output_navigation()
	{
        $show = false;

		$args =
        [
            'header' => '<h3 class="p-0 m-0">' . __('Fast navigation', 'wordplan') . '</h3>',
            'object' => $this
        ];

		$body = '<div class="wordplan-toc m-0">';

		$form_fields = $this->getFields();

		foreach($form_fields as $k => $v)
		{
			$type = $this->getFieldType($v);

			if($type !== 'title')
			{
				continue;
			}

			if(method_exists($this, 'generateNavigationHtml'))
			{
                $show = true;
				$body .= $this->{'generateNavigationHtml'}($k, $v);
			}
		}

		$body .= '</div>';

        if($show)
        {
	        $args['body'] = $body;

	        wordplan()->views()->getView('conns/sidebar_toc.php', $args);
        }
	}

	/**
	 * Generate navigation HTML
	 *
	 * @param string $key - field key
	 * @param array $data - field data
	 *
	 * @return string
	 */
	public function generateNavigationHtml(string $key, array $data): string
	{
		$field_key = $this->getPrefixFieldKey($key);

		$defaults = array
		(
			'title' => '',
			'class' => '',
		);

		$data = wp_parse_args($data, $defaults);

		ob_start();
		?>
		<a class="list-group-item m-0 border-0" href="#<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></a>
		<?php

		return ob_get_clean();
	}
}