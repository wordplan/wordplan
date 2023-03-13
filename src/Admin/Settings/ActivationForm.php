<?php namespace Wordplan\Admin\Settings;

defined('ABSPATH') || exit;

use Wordplan\Exceptions\Exception;
use Wordplan\Settings\MainSettings;

/**
 * ActivationForm
 *
 * @package Wordplan\Admin
 */
class ActivationForm extends Form
{
	/**
	 * MainForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->setId('activation');
		$this->setSettings(new MainSettings());

		add_filter($this->prefix . '_' . $this->getId() . '_form_load_fields', [$this, 'init_form_fields_tecodes'], 10);

		$this->init();
	}

	/**
	 * Save
	 *
	 * @return bool
	 */
	public function save()
	{
		$post_data = $this->getPostedData();

		if(!isset($post_data['_wordplan-admin-nonce']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wordplan-admin-nonce'], 'wordplan-admin-settings-save'))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Save error. Please retry.', 'wordplan')
				]
			);

			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->getFields() as $key => $field)
		{
			if('title' === $this->getFieldType($field))
			{
				continue;
			}

			try
			{
				$this->saved_data[$key] = $this->getFieldValue($key, $field, $post_data);
			}
			catch(\Exception $e)
			{
				wordplan()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => $e->getMessage()
					]
				);
			}
		}

        $code = $post_data['wordplan_activation_form_field_tecodes_code'] ?? '';

		$value_valid = explode('-', $code);
		if('WPWORDPLAN' !== strtoupper(reset($value_valid)) && 'WORDPLAN' !== strtoupper(reset($value_valid)))
		{
			wordplan()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('The code is invalid. Enter the correct code.', 'wordplan')
				]
			);
			return '';
		}

		wordplan()->tecodes()->delete_local_code();
		wordplan()->tecodes()->set_code($code);

		if(false === wordplan()->tecodes()->validate())
		{
			$errors = wordplan()->tecodes()->get_errors();

			if(is_array($errors))
			{
				foreach(wordplan()->tecodes()->get_errors() as $error_key => $error)
				{
					wordplan()->admin()->notices()->create
					(
						[
							'type' => 'error',
							'data' => $error
						]
					);
				}
			}

            return false;
		}

		wordplan()->admin()->notices()->create
		(
			[
				'type' => 'info',
				'data' => __('Code activated successfully.', ('wordplan'))
			]
		);

        return true;
	}

	/**
	 * Validate tecodes code
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return string
	 */
	public function validate_tecodes_code_field(string $key, string $value): string
	{
		return '';
	}

	/**
	 * Add fields for tecodes
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_form_fields_tecodes($fields): array
	{
		if(wordplan()->tecodes()->is_valid())
		{
			$fields['tecodes_status'] =
            [
                'title' => __('Status', 'wordplan'),
                'type' => 'tecodes_status',
                'class' => 'p-2',
                'default' => ''
            ];
		}

        $fields['tecodes_code'] =
        [
            'title' => __('Code for activation', 'wordplan'),
            'type' => 'tecodes_text',
            'class' => 'p-2',
            'description' => sprintf
            (
                '%s <b>%s</b><br /> <hr> %s <b>%s</b>',
                __('Enter the code only on the actual workstation.', 'wordplan'),
                __('If enter the correct code, the current environment will be activated.', 'wordplan'),
                __('Current activation API status:', 'wordplan'),
                esc_attr__(wordplan()->tecodes()->api_get_status(), 'wordplan')
            ),
            'default' => ''
        ];

		return $fields;
	}

	/**
	 * Generate Tecodes data HTML
	 *
	 * @param string $key Field key.
	 * @param array $data Field data.
	 *
	 * @return string
	 */
	public function generate_tecodes_status_html(string $key, array $data): string
	{
		$field_key = $this->getPrefixFieldKey($key);

		$defaults =
		[
			'title' => '',
			'disabled' => false,
			'class' => '',
			'css' => '',
			'placeholder' => '',
			'type' => 'text',
			'desc_tip' => false,
			'description' => '',
			'custom_attributes' => [],
		];

		$data = wp_parse_args($data, $defaults);

		$local = wordplan()->tecodes()->get_local_code();
		$local_data = wordplan()->tecodes()->get_local_code_data($local);

		ob_start();

		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->getTooltipHtml( $data ); ?></label>
            </th>
            <td class="forminp">
                <div class="wordplan-custom-metas">

		            <?php

                        if($local_data['code_date_expires'] === 'never')
                        {
                            $local_data['code_date_expires'] = __('never', 'wordplan');
                        }
                        else
                        {
	                        $local_data['code_date_expires'] = date_i18n(get_option('date_format'), $local_data['code_date_expires']);
                        }

                        printf
                        (
                                '%s: <b>%s</b> (%s %s)<br />%s: <b>%s</b><br />%s: <b>%s</b>',
                                __('Code ID', 'wordplan'),
                                $local_data['code_id'],
                                __('expires:', 'wordplan'),
                                $local_data['code_date_expires'] ,
                                __('Instance ID', 'wordplan'),
                                $local_data['instance_id'],
                                __('Domain', 'wordplan'),
                                $local_data['instance']['domain']
                        );
		            ?>

                </div>
				<?php echo wp_kses_post($this->getDescriptionHtml($data)); // WPCS: XSS ok.?>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Tecodes Text Input HTML
	 *
	 * @param string $key Field key.
	 * @param array $data Field data.
	 *
	 * @return string
	 */
	public function generate_tecodes_text_html(string $key, array $data): string
	{
		$field_key = $this->getPrefixFieldKey($key);
		$defaults = array
		(
			'title' => '',
			'disabled' => false,
			'class' => '',
			'css' => '',
			'placeholder' => '',
			'type' => 'text',
			'desc_tip' => false,
			'description' => '',
			'custom_attributes' => [],
		);

		$data = wp_parse_args($data, $defaults);

		ob_start();
		?>
		<tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->getTooltipHtml( $data ); ?></label>
            </th>
			<td class="forminp">
                <div class="input-group">
                    <input class="form-control input-text regular-input <?php echo esc_attr($data['class']); ?>"
                    type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>"
                    id="<?php echo esc_attr($field_key); ?>" style="<?php echo esc_attr($data['css']); ?>"
                    value="<?php echo esc_attr($this->getFieldData($key)); ?>"
                    placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->getCustomAttributeHtml($data); // WPCS: XSS ok.
                    ?> />
                    <button name="save" class="btn btn-primary" type="submit" value="<?php _e('Activate', 'wordplan') ?>"><?php _e('Activate', 'wordplan') ?></button>
                </div>
                <?php echo wp_kses_post($this->getDescriptionHtml($data)); // WPCS: XSS ok.?>
            </td>
		</tr>
		<?php

		return ob_get_clean();
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

		wordplan()->views()->getView('activation/form.php', $args);
	}
}