<?php

/**
 * WCFM PG MangoPay plugin core
 *
 * Plugin intiate
 *
 * @author 		WC Lovers
 * @package 	wcfm-pg-mangopay
 * @version   1.0.0
 */

class WCFM_PG_MangoPay
{

	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $mp;
	public $mp_create_on_wcfm;
	public $mp_update_on_wcfm;

	public function __construct($file)
	{
		global $mngpp_o;
		$this->mangopayWCMain = $mngpp_o;
		$this->file = $file;
		$this->plugin_base_name = plugin_basename($file);
		$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
		$this->plugin_path = trailingslashit(dirname($file));
		$this->token = WCFMpgmp_TOKEN;
		$this->text_domain = WCFMpgmp_TEXT_DOMAIN;
		$this->version = WCFMpgmp_VERSION;
		$this->mp = mpAccess::getInstance();

		include "method/class-mp-create.php";
		include "method/class-mp-update.php";
		$this->mp_create_on_wcfm = new TARIKUL_PG_MP_CREATE;
		$this->mp_update_on_wcfm = new TARIKUL_PG_MP_UPDATE();

		add_action('wcfm_init', array(&$this, 'init'), 10);
	}

	function init()
	{
		global $WCFM, $WCFMre;

		// Init Text Domain
		$this->load_plugin_textdomain();

		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));

		add_filter('wcfm_marketplace_withdrwal_payment_methods', array(&$this, 'wcfmmp_custom_pg'));
		add_filter('wcfm_marketplace_settings_fields_withdrawal_charges', array(&$this, 'wcfmmp_custom_pg_withdrawal_charges'), 50, 3);

		add_filter('wcfm_marketplace_settings_fields_billing', array(&$this, 'wcfmmp_custom_pg_vendor_setting'), 50, 2);

		add_filter('mangopay_vendor_role', array(&$this, 'set_mangopay_vendor_role'));
		add_filter('mangopay_vendors_required_class', array(&$this, 'set_mangopay_vendors_required_class'));

		// add_action( 'wcfm_vendor_settings_update', array( &$this, 'update_mangopay_settings' ), 10, 2 );
		//		add_action('wcfm_wcfmmp_settings_update', array(&$this, 'update_mangopay_settings'), 10, 2);


		//	add_action("wp_ajax_create_mp_account", array(&$this, "create_mp_account"));
		add_action("wp_ajax_create_mp_account", array(&$this->mp_create_on_wcfm, "tarikul_create_mp_account"));

		add_action("wp_ajax_update_mp_business_information", array(&$this->mp_update_on_wcfm, "tarikul_update_mp_business_information"));

		// Load Gateway Class
		require_once $this->plugin_path . 'gateway/class-wcfmmp-gateway-mangopay.php';
	}

	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_base_name, $this->plugin_url . 'assets/css/wcfm-pg-mangopay.css', array(), $this->version, 'all');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script($this->plugin_base_name, $this->plugin_url . 'assets/js/wcfm-pg-mangopay.js', array('jquery'), $this->version, false);
		wp_localize_script($this->plugin_base_name, 'wecoder_mg_settings', [
			'ajaxurl' => admin_url('admin-ajax.php'),
			'states' => WC()->countries->get_states()
		]);
	}

	public function update_mp_business_information()
	{
		// Get input data
		$update_input_data = $_POST;

		$user_id = $update_input_data['vendor_id'];

		//	error_log('$mp_user_id');

		$mp_user_id = $this->mp->set_mp_user($user_id);

		//	error_log($mp_user_id);

		// Validate the input data
		$validation_result = $this->validate_input($update_input_data);

		if ($validation_result === true) {

			try {
				// Sanitize the input data
				$sanitized_data = $this->sanitize_input($update_input_data);

				// Save the sanitized data as user meta
				$this->save_user_meta($sanitized_data);

				$this->mp->update_user($mp_user_id, $sanitized_data);

				// 	/** Update MP user account **/
				//	$this->on_shop_settings_saved($input_data['vendor_id']);
				wp_send_json_success("Successfully You have created Mangopay account!", 200);
			} catch (Exception  $e) {
				wp_send_json_error('Something is not going right way!', 500);
			}
		}
	}

	// create mp account 
	public function create_mp_account()
	{

		// Get input data
		$input_data = $_POST;

		// $input_data = [
		// 	'action' => '',
		// 	'vendor_id' => '',
		// 	'first_name' => '',
		// 	'last_name' => '',
		// 	'user_birthday' => '21-12-2023',
		// 	'user_nationality' => 'AT',
		// 	'billing_country' => 'BD',
		// 	'billing_state' => 'BD-17',
		// 	'user_mp_status' => 'business',
		// 	//'user_mp_status' => 'individual',
		// 	'user_business_type' => '',
		// ];

		/**
		 * Array
			(
				[action] => create_mp_account
				[vendor_id] => 8
				[first_name] => test
				[last_name] => vendor
				[user_birthday] => 21-12-2023
				[user_nationality] => AT
				[billing_country] => BD
				[billing_state] => BD-17
				[user_mp_status] => business
				[user_business_type] => organisation
			)
		 */

		if ($input_data['action'] === 'create_mp_account') {
			unset($input_data['action']);
		}

		// Validate the input data
		$validation_result = $this->validate_input($input_data);

		if ($validation_result === true) {

			try {
				// Sanitize the input data
				$sanitized_data = $this->sanitize_input($input_data);

				// Save the sanitized data as user meta
				//	$this->save_user_meta($sanitized_data);

				//	$mp_user_id = $this->mp->set_mp_user($input_data['vendor_id']);

				//	$this->mp->set_mp_wallet($mp_user_id);

				// 	/** Update MP user account **/
				//	$this->on_shop_settings_saved($input_data['vendor_id']);
				wp_send_json_success("Successfully You have created Mangopay account!", 200);
			} catch (Exception  $e) {
				wp_send_json_error('Something is not going right way!', 500);
			}
		}
	}

	// Common function to save input data as user meta
	private function save_user_meta($data)
	{
		// Assuming $data['vendor_id'] is the user ID
		$user_id = $data['vendor_id'];

		// Check if it's a create or update action
		$action = isset($data['action']) ? $data['action'] : '';

		// Update general user meta
		//$this->update_general_user_meta($user_id, $data);

		// Perform action-specific updates
		switch ($action) {
			case 'create_mp_account':
				$this->mp_account_creation_data_save($user_id, $data);
				break;

			case 'update_mp_business_information':
				$this->update_mp_business_information_data_save($user_id, $data);
				break;

				// Add more cases for other actions if needed

			default:
				// Handle unknown action or no action
				break;
		}
	}

	private function mp_account_creation_data_save($user_id, $data)
	{
	}

	private function update_mp_business_information_data_save($user_id, $data)
	{
		//error_log(print_r($data, true));

		// Define common required fields
		$common_required_fields = [
			'user_birthday',
			'user_nationality',
			'billing_country',
		];

		// Define type-specific required fields and conditions for 'update' action
		$update_specific_fields = [
			'legal_email',
			'compagny_number',
			'headquarters_addressline1',
			'headquarters_city',
			'headquarters_region',
			'headquarters_postalcode',
			'headquarters_country',
			'termsconditions',
			// Add other fields specific to this type
		];

		// Validate common required fields
		$this->save_fields($user_id, $data, $common_required_fields);

		// Validate type-specific fields for 'update' action
		$this->save_fields($user_id, $data, $update_specific_fields);
	}

	private function save_fields($user_id, $data, $fields)
	{
		foreach ($fields as $field) {
			// Additional validation for specific fields
			switch ($field) {
				case 'payment_method':
					$vendor_data = get_user_meta($user_id, 'wcfmmp_profile_settings', true);

					if (!$vendor_data || !is_array($vendor_data)) {
						$vendor_data = array();
					}

					// Ensure the 'payment' key is an array
					if (!isset($vendor_data['payment']) || !is_array($vendor_data['payment'])) {
						$vendor_data['payment'] = array();
					}

					// Update 'method' in 'payment' array
					$vendor_data['payment']['method'] = $data['payment_method'];

					// Update user meta
					update_user_meta($user_id, 'wcfmmp_profile_settings', $vendor_data);

					break;
				case 'first_name':
					update_user_meta($user_id, $field, $data[$field]);
					break;
				case 'last_name':
					update_user_meta($user_id, $field, $data[$field]);
					break;
				case 'user_birthday':
					//Convert date and log the result for debugging
					$convertedDate = $this->convertDate($data[$field]);
					// Update 'user_birthday' in user meta
					update_user_meta($user_id, $field, $convertedDate);
					break;
				default:
					// No specific validation for other fields
					update_user_meta($user_id, $field, $data[$field]);
					break;
			}
		}
	}

	private function sanitize_input($input_data)
	{
		$sanitized_data = array();

		foreach ($input_data as $key => $value) {
			switch ($key) {
				case 'legal_email':
					$sanitized_data[$key] = sanitize_email($value);
					break;

					// user id skip sanitize
				case 'vendor_id':
					$sanitized_data[$key] = $value;
					break;
					// Add more cases for other fields

				default:
					// For general fields, use sanitize_text_field
					$sanitized_data[$key] = sanitize_text_field($value);
					break;
			}
		}

		return $sanitized_data;
	}

	// Common function to validate input data
	private function validate_input($input_data)
	{
		// Check if 'action' is set
		if (!isset($input_data['action'])) {
			wp_send_json_error("Error: 'action' is required.", 400);
		}

		$action = $input_data['action'];

		// Unset unnecessary fields based on 'individual' user_mp_status for 'create' action
		// if ($action === 'create_mp_account' && isset($input_data['user_mp_status']) && $input_data['user_mp_status'] === 'individual') {
		// 	unset($input_data['user_mp_status'], $input_data['user_business_type']);
		// }

		// Unset billing_state fields if empty for 'create' action Unset  
		// if (isset($input_data['billing_state']) && empty($input_data['billing_state'])) {
		// 	unset($input_data['billing_state']);
		// }

		$errors = [];

		// Validate action-specific fields
		switch ($action) {
			case 'create_mp_account':
				$this->validation_for_create_mp_account($input_data, $errors);
				break;

			case 'update_mp_business_information':
				$this->validation_for_update_mp_business_information($input_data, $errors);
				break;

				// Add more cases for other actions if needed

			default:
				// Handle unknown action or no action
				break;
		}

		// If there are errors, send the error response
		if (!empty($errors)) {
			error_log(print_r($errors, true));
			wp_send_json_error($errors, 400);
		}

		// If validation passes, return true
		return true;
	}

	private function validation_for_create_mp_account($data, &$errors)
	{
		// Define common required fields
		$common_required_fields = [
			'vendor_id' => 'Vendor ID',
			'user_birthday' => 'User Birthday',
			'user_nationality' => 'User Nationality',
			'billing_country' => 'Billing Country',
		];

		// Define type-specific required fields and conditions for 'create' action
		$create_specific_fields = [
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'billing_state' => 'Billing State',
			'user_mp_status' => 'User MP Status',
			'user_business_type' => 'User Business Type',
		];

		// Validate common required fields
		$this->validate_required_fields($data, $common_required_fields, $errors);

		// Validate type-specific fields for 'create' action
		$this->validate_required_fields($data, $create_specific_fields, $errors);
	}

	private function validation_for_update_mp_business_information($data, &$errors)
	{
		//	$data = $this->mangopay_acount_update_sample_data();
		//	error_log(print_r($data, true));

		// Define common required fields
		$common_required_fields = [
			'vendor_id' => 'Vendor ID',
			'user_birthday' => 'User Birthday',
			'user_nationality' => 'User Nationality',
			'billing_country' => 'Billing Country',
		];

		// Define type-specific required fields and conditions for 'update' action
		$update_specific_fields = [
			'legal_email' => 'Legal Email',
			'compagny_number' => 'Company Number',
			'headquarters_addressline1' => 'Headquarters Addressline1',
			'headquarters_city' => 'Headquarters City',
			'headquarters_region' => 'Headquarters Region',
			'headquarters_postalcode' => 'Headquarters Postalcode',
			'headquarters_country' => 'Headquarters Country',
			'termsconditions' => 'Please agree',
			// Add other fields specific to this type
		];

		// Validate common required fields
		$this->validate_required_fields($data, $common_required_fields, $errors);

		// Validate type-specific fields for 'update' action
		$this->validate_required_fields($data, $update_specific_fields, $errors);
	}

	private function validate_required_fields($data, $required_fields, &$errors)
	{
		foreach ($required_fields as $field => $label) {
			// Check if the field is empty
			if (empty($data[$field])) {
				$errors[$field] = "Error: {$label} is required.";
			} else {
				// Additional validation for specific fields
				switch ($field) {
					case 'legal_email':
						// Validate email format
						if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
							$errors[$field] = "Error: {$label} is not a valid email address.";
						}
						break;

					case 'compagny_number':
						// Validate company number using a custom function
						$cn_validation = $this->mp->check_company_number_patterns($data[$field]);

						if ($cn_validation != 'found') {
							$errors[$field] = "Error: {$label} is not a valid.";
						}
						break;

						// Add more cases for other fields

					default:
						// No specific validation for other fields
						break;
				}
			}
		}
	}

	public function wcfmmp_custom_pg($payment_methods)
	{
		$payment_methods[WCFMpgmp_GATEWAY] = __(WCFMpgmp_GATEWAY_LABEL, 'wcfm-pg-mangopay');
		return $payment_methods;
	}

	public function wcfmmp_custom_pg_withdrawal_charges($withdrawal_charges, $wcfm_withdrawal_options, $withdrawal_charge)
	{
		$gateway_slug  = WCFMpgmp_GATEWAY;
		$gateway_label = __(WCFMpgmp_GATEWAY_LABEL, 'wcfm-pg-mangopay') . ' ';

		$withdrawal_charge_brain_tree = isset($withdrawal_charge[$gateway_slug]) ? $withdrawal_charge[$gateway_slug] : array();
		$payment_withdrawal_charges = array("withdrawal_charge_" . $gateway_slug => array('label' => $gateway_label . __('Charge', 'wcfm-pg-mangopay'), 'type' => 'multiinput', 'name' => 'wcfm_withdrawal_options[withdrawal_charge][' . $gateway_slug . ']', 'class' => 'withdraw_charge_block withdraw_charge_' . $gateway_slug, 'label_class' => 'wcfm_title wcfm_ele wcfm_fill_ele withdraw_charge_block withdraw_charge_' . $gateway_slug, 'value' => $withdrawal_charge_brain_tree, 'custom_attributes' => array('limit' => 1), 'options' => array(
			"percent" => array('label' => __('Percent Charge(%)', 'wcfm-pg-mangopay'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_percent withdraw_charge_percent_fixed', 'attributes' => array('min' => '0.1', 'step' => '0.1')),
			"fixed" => array('label' => __('Fixed Charge', 'wcfm-pg-mangopay'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'label_class' => 'wcfm_title wcfm_ele withdraw_charge_field withdraw_charge_fixed withdraw_charge_percent_fixed', 'attributes' => array('min' => '0.1', 'step' => '0.1')),
			"tax" => array('label' => __('Charge Tax', 'wcfm-pg-mangopay'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array('min' => '0.1', 'step' => '0.1'), 'hints' => __('Tax for withdrawal charge, calculate in percent.', 'wcfm-pg-mangopay')),
		)));
		$withdrawal_charges = array_merge($withdrawal_charges, $payment_withdrawal_charges);
		return $withdrawal_charges;
	}

	public function wcfmmp_custom_pg_vendor_setting($vendor_billing_fields, $vendor_id)
	{
		$gateway_slug  = WCFMpgmp_GATEWAY;

		// site home url 
		$site_url = home_url('mangopay-terms-conditions');

		$vendor_data = get_user_meta($vendor_id, 'wcfmmp_profile_settings', true);

		if (!$vendor_data) $vendor_data = array();

		// check vendor exist mp account or not 
		$mp_user_id = $this->mp->get_mp_user_id($vendor_id);

		//echo "<pre>";
		//print_r($vendor_data);

		$settings = array();

		if (isset($mp_user_id) && !empty($mp_user_id)) {
			include_once(plugin_dir_path(__FILE__) . '../template/details-mp.php');
			return array_merge($vendor_billing_fields, $user_mp_details_field);
		} else {
			include_once(plugin_dir_path(__FILE__) . '../template/register-mp.php');
			return array_merge($vendor_billing_fields, $vendor_user_mp_register_fields);
		}

		return $vendor_billing_fields;
	}

	public function set_mangopay_vendor_role($role)
	{
		return 'wcfm_vendor';
	}

	public function set_mangopay_vendors_required_class($class_name)
	{
		return 'WCFMmp';
	}

	public function update_mangopay_settings($wp_user_id, $wcfm_settings_form)
	{
		$gateway_slug  	= WCFMpgmp_GATEWAY;
		$vendor_data 	= get_user_meta($wp_user_id, 'wcfmmp_profile_settings', true);

		if ('either' === $this->mp->default_vendor_status) {
			if (isset($wcfm_settings_form['payment'][$gateway_slug]['user_mp_status'])) {
				update_user_meta($wp_user_id, 'user_mp_status', $wcfm_settings_form['payment'][$gateway_slug]['user_mp_status']);
			}
		}

		if ('either' === $this->mp->default_vendor_status || 'businesses' === $this->mp->default_vendor_status) {
			if ('either' === $this->mp->default_business_type) {
				if (isset($wcfm_settings_form['payment'][$gateway_slug]['user_business_type'])) {
					update_user_meta($wp_user_id, 'user_business_type', $wcfm_settings_form['payment'][$gateway_slug]['user_business_type']);
				}
			}
		}

		if (isset($wcfm_settings_form['payment'][$gateway_slug]['birthday'])) {
			update_user_meta($wp_user_id, 'user_birthday', $wcfm_settings_form['payment'][$gateway_slug]['birthday']);
		}

		if (isset($wcfm_settings_form['payment'][$gateway_slug]['nationality'])) {
			update_user_meta($wp_user_id, 'user_nationality', $wcfm_settings_form['payment'][$gateway_slug]['nationality']);
		}

		ob_start();
		$mp_user_id = $this->mp->set_mp_user($wp_user_id);
		$a = ob_get_clean();

		if (!$mp_user_id) {
			mangopay_log(__('Can not create mangopay user, please make sure to fill up your profile & address fields such as First Name, Last Name, Email, Billing Country etc', 'wc-multivendor-marketplace'), 'error');
			return;
		}

		if (isset($wcfm_settings_form['mangopay_upload_kyc']) && 'yes' == $wcfm_settings_form['mangopay_upload_kyc']) {

			$kyc_details = isset($wcfm_settings_form['payment'][$gateway_slug]['kyc_details']) ? $wcfm_settings_form['payment'][$gateway_slug]['kyc_details'] : array();

			if (is_array($kyc_details) && !empty($kyc_details)) {
				$kyc_details 	= wp_list_pluck($kyc_details, 'file', 'type');

				foreach ($kyc_details as $type => $file) {
					$KycDocument = new \MangoPay\KycDocument();
					$KycDocument->Tag = "wp_user_id:" . $wp_user_id;
					$KycDocument->Type = $type;

					try {
						$document_created = $this->mp->create_kyc_document($mp_user_id, $KycDocument);
						$kycDocumentId = $document_created->Id;

						if ($kycDocumentId) {
							$uploaded = $this->mp->create_kyc_page_from_file($mp_user_id, $kycDocumentId, get_attached_file($file));

							if ($uploaded) {
								$KycDocument = new \MangoPay\KycDocument();
								$KycDocument->Id = $kycDocumentId;
								$KycDocument->Status = \MangoPay\KycDocumentStatus::ValidationAsked;
								$Result = $this->mp->update_kyc_document($mp_user_id, $KycDocument);

								if ($Result) {
									$data_meta['type'] = $type;
									$data_meta['id_mp_doc'] = $kycDocumentId;
									$data_meta['creation_date'] = $Result->CreationDate;
									$data_meta['document_name'] = basename(get_attached_file($file));
									update_user_meta($wp_user_id, 'kyc_document_' . $kycDocumentId, $data_meta);
								}
							}
						}
					} catch (MangoPay\Libraries\ResponseException $e) {
						mangopay_log($e->GetMessage(), 'error');
						$this->message['message'] = $e->GetMessage();
					} catch (MangoPay\Libraries\Exception $e) {
						mangopay_log($e->GetMessage(), 'error');
						$this->message['message'] = $e->GetMessage();
					}
				}
			}

			// we don't need this field value to be saved
			unset($wcfm_settings_form['mangopay_upload_kyc']);
		}

		$umeta_key = 'mp_account_id';
		if (!$this->mp->is_production()) {
			$umeta_key .= '_sandbox';
		}

		$existing_account_id = get_user_meta($wp_user_id, $umeta_key, true);

		$bank_details 	= $wcfm_settings_form['payment'][$gateway_slug]['bank_details'];

		$type 		= isset($bank_details['vendor_account_type']) ? $bank_details['vendor_account_type'] : '';
		$name 		= isset($bank_details['vendor_account_name']) ? $bank_details['vendor_account_name'] : '';
		$address1 	= isset($bank_details['vendor_account_address1']) ? $bank_details['vendor_account_address1'] : '';
		$address2 	= isset($bank_details['vendor_account_address2']) ? $bank_details['vendor_account_address2'] : '';
		$city 		= isset($bank_details['vendor_account_city']) ? $bank_details['vendor_account_city'] : '';
		$postcode 	= isset($bank_details['vendor_account_postcode']) ? $bank_details['vendor_account_postcode'] : '';
		$region 	= isset($bank_details['vendor_account_region']) ? $bank_details['vendor_account_region'] : '';
		$country 	= isset($bank_details['vendor_account_country']) ? $bank_details['vendor_account_country'] : '';

		$account_types 	= mangopayWCConfig::$account_types;
		$account_type 	= $account_types[$type];
		$needs_update 	= false;
		$account_data 	= array();

		/** Record redacted bank account data in vendor's usermeta **/
		foreach ($account_type as $field => $c) {
			if (isset($bank_details[$field]) && $bank_details[$field] && !preg_match('/\*\*/', $bank_details[$field])) {
				if (isset($c['redact']) && $c['redact']) {
					$needs_update = true;
					list($obf_start, $obf_end) = explode(',', $c['redact']);
					$strlen = strlen($bank_details[$field]);

					/**
					 * if its <=5 characters, lets just redact the whole thing
					 * @see: https://github.com/Mangopay/wordpress-plugin/issues/12
					 */
					if ($strlen <= 5) {
						$to_be_stored = str_repeat('*', $strlen);
					} else {
						$obf_center = $strlen - $obf_start - $obf_end;
						if ($obf_center < 2) {
							$obf_center = 2;
						}
						$to_be_stored = substr($bank_details[$field], 0, $obf_start) .
							str_repeat('*', $obf_center) .
							substr($bank_details[$field], -$obf_end, $obf_end);
					}
				} else {
					if (get_user_meta($wp_user_id, $field, true) != $bank_details[$field]) {
						$needs_update = true;
					}
					$to_be_stored = $bank_details[$field];
				}
				$wcfm_settings_form['payment'][$gateway_slug]['bank_details'][$field] = $to_be_stored;
				update_user_meta($wp_user_id, $field, $to_be_stored);
				$account_data[$field] = $bank_details[$field];
			}
		}

		/** Record clear text bank account data in vendor's usermeta **/
		$account_clear_data = array(
			'headquarters_addressline1',
			'headquarters_addressline2',
			'headquarters_city',
			'headquarters_region',
			'headquarters_postalcode',
			'headquarters_country',
			'vendor_account_type',
			'vendor_account_name',
			'vendor_account_address1',
			'vendor_account_address2',
			'vendor_account_city',
			'vendor_account_postcode',
			'vendor_account_region',
			'vendor_account_country'
		);
		foreach ($account_clear_data as $field) {
			/** update_user_meta() returns "false" if the value is unchanged **/
			if (isset($bank_details[$field]) && update_user_meta($wp_user_id, $field, $bank_details[$field])) {
				$needs_update = true;
			}
		}

		if ($needs_update) {
			try {
				$mp_account_id = $this->mp->save_bank_account(
					$mp_user_id,
					$wp_user_id,
					$existing_account_id,
					$type,
					$name,
					$address1,
					$address2,
					$city,
					$postcode,
					$region,
					$country,
					$account_data,
					$account_types
				);

				update_user_meta($wp_user_id, $umeta_key, $mp_account_id);
			} catch (MangoPay\Libraries\ResponseException $e) {
				mangopay_log($e->GetMessage(), 'error');
				$this->message['message'] = $e->GetMessage();
			} catch (MangoPay\Libraries\Exception $e) {
				mangopay_log($e->GetMessage(), 'error');
				$this->message['message'] = $e->GetMessage();
			}
		}

		update_user_meta($wp_user_id, 'wcfmmp_profile_settings', $wcfm_settings_form);
	}

	public function supported_format($date_format)
	{
		if (date('Y-m-d') == $this->convertDate(date_i18n(get_option('date_format'), time()), get_option('date_format')))
			return $date_format;
		return preg_replace('/F/', 'm', $date_format);
	}

	public function convertDate($date, $format = null)
	{
		if (!$format)
			$format = $this->supported_format(get_option('date_format'));

		if (preg_match('/F/', $format) && function_exists('strptime')) {

			/** Convert date format to strftime format */
			$format = preg_replace('/j/', '%d', $format);
			$format = preg_replace('/F/', '%B', $format);
			$format = preg_replace('/Y/', '%Y', $format);
			$format = preg_replace('/,\s*/', ' ', $format);
			$date = preg_replace('/,\s*/', ' ', $date);

			setlocale(LC_TIME, get_locale());
			do_action('mwc_set_locale_date_validation', get_locale());

			$d = strptime($date, $format);
			if (false === $d)    // Fix problem with accentuated month names on some systems
				$d = strptime(utf8_decode($date), $format);
			if (!$d)
				return false;
			return
				1900 + $d['tm_year'] . '-' .
				sprintf('%02d', $d['tm_mon'] + 1) . '-' .
				sprintf('%02d', $d['tm_mday']);
		} else if (preg_match('/S/', $format) && function_exists('strptime')) {
			$formated = date_parse_from_format($format, $date);
			if (empty($formated['year']) || empty($formated['month']) || empty($formated['day'])) {
				return false;
			}
			return $formated['year'] . '-' . sprintf("%02d", $formated['month']) . '-' . sprintf("%02d", $formated['day']);
		} else {
			$d = DateTime::createFromFormat($format, $date);
			if (!$d)
				return false;
			return $d->format('Y-m-d');
		}
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain()
	{
		$locale = function_exists('get_user_locale') ? get_user_locale() : get_locale();
		$locale = apply_filters('plugin_locale', $locale, 'wcfm-pg-mangopay');

		//load_plugin_textdomain( 'wcfm-tuneer-orders' );
		//load_textdomain( 'wcfm-pg-mangopay', WP_LANG_DIR . "/wcfm-pg-mangopay/wcfm-pg-mangopay-$locale.mo");
		load_textdomain('wcfm-pg-mangopay', $this->plugin_path . "lang/wcfm-pg-mangopay-$locale.mo");
		load_textdomain('wcfm-pg-mangopay', ABSPATH . "wp-content/languages/plugins/wcfm-pg-mangopay-$locale.mo");
	}

	public function load_class($class_name = '')
	{
		if ('' != $class_name && '' != $this->token) {
			require_once('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}

	public function mangopay_kyc_validation($mp_user_id, $user_business_type)
	{
		$validated = array();
		$kyc_options = get_mangopay_kyc_document_types();
		$required_kyc = array('IDENTITY PROOF', 'ADDRESS PROOF');

		try {
			$kyc_documents = $this->mp->get_kyc_documents($mp_user_id);
			if ($kyc_documents) {
				foreach ($kyc_documents as $kyc_document) {
					if ($kyc_document->Status == 'VALIDATED' && !in_array($kyc_options[$kyc_document->Type], $validated)) $validated[] =  $kyc_options[$kyc_document->Type];
				}
			}
		} catch (Exception $e) {
			$validated = array();
		}
		switch ($user_business_type) {
			case 'business':
				$required_kyc = array('IDENTITY PROOF', 'REGISTRATION PROOF', 'ARTICLES OF ASSOCIATION');
				break;
			case 'organisation':
				$required_kyc = array('IDENTITY PROOF', 'REGISTRATION PROOF', 'ARTICLES OF ASSOCIATION');
				break;
			case 'soletrader':
				$required_kyc = array('IDENTITY PROOF', 'REGISTRATION PROOF');
				break;
			default:
				$required_kyc = array('IDENTITY PROOF', 'ADDRESS PROOF');
				break;
		}
		if (count($validated) > 0 && count($required_kyc) > 0) {
			$diff = array_diff($required_kyc, $validated);
			if (count($diff) > 0) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function mangopay_kyc_list($mp_user_id, $user_mp_status, $user_business_type)
	{
		$submitted = array();
		$kyc_options = get_mangopay_kyc_document_types();
		try {
			$kyc_documents = $this->mp->get_kyc_documents($mp_user_id);
			if ($kyc_documents) {
				foreach ($kyc_documents as $kyc_document) {
					if (!in_array($kyc_options[$kyc_document->Type], $submitted)) $submitted[] =  $kyc_options[$kyc_document->Type];
				}
			}
		} catch (Exception $e) {
			$submitted = array();
		}
		$remove_kyc_type = array('SHAREHOLDER DECLARATION');
		if ($user_mp_status == 'business') {
			if ($user_business_type == 'soletrader') {
				array_push($remove_kyc_type, 'ARTICLES OF ASSOCIATION');
			}
			array_push($remove_kyc_type, 'ADDRESS PROOF');
		} else {
			array_push($remove_kyc_type, 'REGISTRATION PROOF');
			array_push($remove_kyc_type, 'ARTICLES OF ASSOCIATION');
		}
		if (count($submitted) > 0) $kyc_options = array_diff($kyc_options, $submitted);
		if (count($remove_kyc_type) > 0) $kyc_options = array_diff($kyc_options, $remove_kyc_type);
		return $kyc_options;
	}

	public function mangopay_kyc_html($mp_user_id)
	{
		$html = '';
		try {
			$kyc_documents = $this->mp->get_kyc_documents($mp_user_id);
			if ($kyc_documents) {
				$kyc_options = get_mangopay_kyc_document_types();
				$html .= '<table class="kyc-detail-table">';
				$html .= '<tr class="kyc-detail-header"><th>Type</th><th>Status</th><th>Refused Reason</th><th>ID</th><th>Processed Date</th><th>Creation Date</th></tr>';
				foreach ($kyc_documents as $kyc_document) {
					$process_date = isset($kyc_document->ProcessedDate) && !empty($kyc_document->ProcessedDate) ? date("jS M Y", $kyc_document->ProcessedDate) : '';
					$creation_date = isset($kyc_document->CreationDate) && !empty($kyc_document->CreationDate) ? date("jS M Y", $kyc_document->CreationDate) : '';
					$html .= '<tr>';
					$html .= '<td>' . $kyc_options[$kyc_document->Type] . '</td>';
					$html .= '<td>' . $kyc_document->Status . '</td>';
					$html .= '<td>' . $kyc_document->RefusedReasonMessage . '</td>';
					$html .= '<td>' . $kyc_document->Id . '</td>';
					$html .= '<td class="kyc-date">' . $process_date . '</td>';
					$html .= '<td class="kyc-date">' . $creation_date . '</td>';
					$html .= '</tr>';
				}
				$html .= '</table>';
			}
		} catch (Exception $e) {
			$html = '';
		}
		return $html;
	}

	private function mangopay_acount_creation_sample_data()
	{
		return [
			'action' => '',
			'vendor_id' => '',
			'first_name' => '',
			'last_name' => '',
			'user_birthday' => '21-12-2023',
			'user_nationality' => 'AT',
			'billing_country' => 'BD',
			'billing_state' => 'BD-17',
			'user_mp_status' => 'business',
			//'user_mp_status' => 'individual',
			'user_business_type' => '',
		];
	}
	private function mangopay_acount_update_sample_data()
	{
		return [
			'action' => 'update_mp_business_information',
			'vendor_id' => '11',
			'user_birthday' => 'Nove',
			'user_nationality' => 'FR',
			'billing_country' => 'FR',
			'legal_email' => 'tarikul@gmail.com',
			'compagny_number' => '1255656911',
			'headquarters_addressline1' => '',
			'headquarters_addressline2' => '',
			'headquarters_city' => '',
			'headquarters_region' => '',
			'headquarters_postalcode' => '',
			'headquarters_country' => '',
			'termsconditions' => '',
		];
	}
}
