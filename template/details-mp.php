<?php
$ubo_result = $this->mp->test_vendor_ubo($vendor_id);
$kyc_result = $this->mp->test_vendor_kyc($vendor_id);
$kyc_documents = $this->mangopay_kyc_html($mp_user_id);

$settings = array();
$hints = array();

$settings['user_mp_status']     = isset($vendor_data['payment'][$gateway_slug]['user_mp_status']) ? $vendor_data['payment'][$gateway_slug]['user_mp_status'] : '';
$settings['user_business_type'] = isset($vendor_data['payment'][$gateway_slug]['user_business_type']) ? $vendor_data['payment'][$gateway_slug]['user_business_type'] : '';

$settings['birthday']             = isset($vendor_data['payment'][$gateway_slug]['birthday']) ? $vendor_data['payment'][$gateway_slug]['birthday'] : '';

$settings['nationality']         = isset($vendor_data['payment'][$gateway_slug]['nationality']) ? $vendor_data['payment'][$gateway_slug]['nationality'] : '';
$settings['compagny_number']         = isset($vendor_data['payment'][$gateway_slug]['compagny_number']) ? $vendor_data['payment'][$gateway_slug]['compagny_number'] : '';
$settings['kyc_details']         = isset($vendor_data['payment'][$gateway_slug]['kyc_details']) ? $vendor_data['payment'][$gateway_slug]['kyc_details'] : array();
$settings['bank_details']         = isset($vendor_data['payment'][$gateway_slug]['bank_details']) ? $vendor_data['payment'][$gateway_slug]['bank_details'] : array();

//Headquarters Address
$settings['hq_address']         = get_user_meta($vendor_id, 'headquarters_addressline1', true) ? get_user_meta($vendor_id, 'headquarters_addressline1', true) : '';
$settings['hq_address2']         = get_user_meta($vendor_id, 'headquarters_addressline2', true) ? get_user_meta($vendor_id, 'headquarters_addressline2', true) : '';
$settings['hq_region']         = get_user_meta($vendor_id, 'headquarters_region', true) ? get_user_meta($vendor_id, 'headquarters_region', true) : '';
$settings['hq_city']         = get_user_meta($vendor_id, 'headquarters_city', true) ? get_user_meta($vendor_id, 'headquarters_city', true) : '';
$settings['hq_postalcode']         = get_user_meta($vendor_id, 'headquarters_postalcode', true) ? get_user_meta($vendor_id, 'headquarters_postalcode', true) : '';
$settings['hq_country']         = get_user_meta($vendor_id, 'headquarters_country', true) ? get_user_meta($vendor_id, 'headquarters_country', true) : '';
$settings['compagny_number']         = get_user_meta($vendor_id, 'compagny_number', true) ? get_user_meta($vendor_id, 'compagny_number', true) : '';
$settings['legal_email']         = get_user_meta($vendor_id, 'legal_email', true) ? get_user_meta($vendor_id, 'legal_email', true) : '';
$settings['termsconditions'] = get_user_meta($vendor_id, 'termsconditions', true) ? 'yes' : 'no';
$settings['legal_address']         = get_user_meta($vendor_id, 'legal_address', true) ? get_user_meta($vendor_id, 'legal_address', true) : '';


// GET ALL HINTS
$business_information_hints = get_option('wcfm_business_information_hints');
$company_number_hints = get_option('wcfm_company_number_hints');
$legal_rep_email_hints = get_option('wcfm_legal_rep_email_hints');
$headquarter_address_hints = get_option('wcfm_headquarter_address_hints');
$ubo_information_hints = get_option('wcfm_ubo_information_hints');
$kyc_details_hints = get_option('wcfm_kyc_details_hints');
$bank_details_hints = get_option('wcfm_bank_details_hints');
$add_bank_hints = get_option('wcfm_add_bank_hints');
$upload_files_hints = get_option('wcfm_upload_files_hints');
$submit_kyc_hints = get_option('wcfm_submit_kyc_hints');


// GET ALL NOTIFICATION
$company_number_notification = get_option('wcfm_company_number_notification', 'You need to supply a valid company number to do a payout.');
$bank_account_notification = get_option('wcfm_bank_account_notification', 'You need to add a Bank Account to do a payout.');
$kyc_document_notification = get_option('wcfm_kyc_document_notification', 'You need to validate your KYC documents to do a payout.');
$kyc_validate_notification = get_option('wcfm_kyc_validated_notification', 'Your KYC is Validated!');
$ubo_document_notification = get_option('wcfm_ubo_document_notification', 'You need to submit UBO documents to do a payout.');
$mangopay_notice_title = get_option('wcfm_mangopay_notice_title');
$mangopay_notice_description = get_option('wcfm_mangopay_notice_description');


$vendor_user_billing_fields = array();
$user_mp_status = get_user_meta($vendor_id, 'user_mp_status', true) ? get_user_meta($vendor_id, 'user_mp_status', true) : '';
$user_business_type = get_user_meta($vendor_id, 'user_business_type', true) ? get_user_meta($vendor_id, 'user_business_type', true) : '';
$kyc_options = $this->mangopay_kyc_list($mp_user_id, $user_mp_status, $user_business_type);
$kyc_validation = $this->mangopay_kyc_validation($mp_user_id, $user_business_type);


//REMOVE KYC DOCUMENT FROM LIST
$remove_kyc_type = array('SHAREHOLDER DECLARATION', 'ADDRESS PROOF');
if ($user_mp_status == 'business') {
    if ($user_business_type == 'soletrader') {
        array_push($remove_kyc_type, 'ARTICLES OF ASSOCIATION');
    }
} else {
    array_push($remove_kyc_type, 'REGISTRATION PROOF');
    array_push($remove_kyc_type, 'ARTICLES OF ASSOCIATION');
}

/**
 * My Custom functionality data retrive from database - raju
 */

// birtdate rerive from user meta with format 
$settings['user_birthday'] = date('F j, Y', strtotime(get_user_meta($vendor_id, 'user_birthday', true)));

// nationality rerive from user meta with format
$settings['user_nationality'] = get_user_meta($vendor_id, 'user_nationality', true);

// company number rerive from user meta with format
$settings['compagny_number'] = get_user_meta($vendor_id, 'compagny_number', true) ? get_user_meta($vendor_id, 'compagny_number', true) : '';

// Legal Representative Country O fResidence rerive from user meta with format
$settings['billing_country'] = get_user_meta($vendor_id, 'billing_country', true);

// Legal email rerive from user meta with format
$settings['legal_email'] = get_user_meta($vendor_id, 'legal_email', true) ? get_user_meta($vendor_id, 'legal_email', true) : '';


if (!$user_mp_status) {
    if ('either' === $this->mp->default_vendor_status) {
        $vendor_user_billing_fields += array(
            $gateway_slug . '_user_mp_status' => array(
                'label'         => __('User Type', 'wc-multivendor-marketplace'),
                'type'             => 'select',
                'options'         => array(
                    'individual'    => __('NATURAL', 'wc-multivendor-marketplace'),
                    'business'        => __('BUSINESS', 'wc-multivendor-marketplace'),
                ),
                'name'             => 'payment[' . $gateway_slug . '][user_mp_status]',
                'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
                'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
                'value'         => $settings['user_mp_status'],
                'custom_attributes'    => array(
                    'required'    => 'required'

                ),
            ),
        );
    }

    if ('either' === $this->mp->default_vendor_status || 'businesses' === $this->mp->default_vendor_status) {
        if ('either' == $this->mp->default_business_type) {
            $vendor_user_billing_fields += array(
                $gateway_slug . '_user_business_type' => array(
                    'label'         => __('Business Type', 'wc-multivendor-marketplace'),
                    'type'             => 'select',
                    'options'         => array(
                        'business'        => __('BUSINESSs', 'wc-multivendor-marketplace'),
                        'organisation'    => __('ORGANIZATION', 'wc-multivendor-marketplace'),
                        'soletrader'    => __('SOLETRADER', 'wc-multivendor-marketplace'),
                    ),
                    'name'             => 'payment[' . $gateway_slug . '][user_business_type]',
                    'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
                    'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
                    'value'         => $settings['user_business_type'],
                    'custom_attributes'    => array(
                        'required'    => 'required'
                    ),
                ),
            );
        }
    }
}

if ($user_business_type == 'business' && empty($settings['compagny_number'])) {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_notice_business_number' => array(
            'type'    => 'html',
            'class' => 'mp_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text">' . $company_number_notification . '</div>',
        ),
    );
}


$vendor_user_billing_fields += array(
    $gateway_slug . '_vendor_id' => array(
        'type'            => 'hidden',
        'name'             => $gateway_slug . '_vendor_id',
        'value'         => $vendor_id
    ),

    $gateway_slug . '_wrapper_mp_id' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<input type="hidden" id="mangopay_id" name="ubo_mp_id" value="' . $mp_user_id . '">',
    ),

    $gateway_slug . '_wrapper_button_text' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<input type="hidden" id="showbutton_text" value="Show Details"/><input type="hidden" id="hidebutton_text" value="Hide Details"/>',
    )
);

$bank_account = get_user_meta($vendor_id, 'mp_bank_account', true) ? get_user_meta($vendor_id, 'mp_bank_account', true) : false;

if (!$bank_account) {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_notice_bank' => array(
            'type'    => 'html',
            'class' => 'mp_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text">' . $bank_account_notification . '</div>',
        )
    );
}

if ($kyc_validation) {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_notice_kyc' => array(
            'type'    => 'html',
            'class' => 'mp_success_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text">' . $kyc_validate_notification . '</div>',
        )
    );
} else {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_notice_kyc' => array(
            'type'    => 'html',
            'class' => 'mp_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text">' . $kyc_document_notification . '</div>',
        )
    );
}



if ($user_business_type == 'business' && !$ubo_result) {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_notice_ubo' => array(
            'type'    => 'html',
            'class' => 'mp_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text">' . $ubo_document_notification . '</div>',
        )
    );
}

if ($mangopay_notice_description && !empty($mangopay_notice_description)) {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_important_notice' => array(
            'type'    => 'html',
            'class' => 'mp_imp_notice_common wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mp_notice_commom_text"><h6>' . $mangopay_notice_title . '</h6><p>' . $mangopay_notice_description . '</p></div>',
        ),
    );
}


//CREATE OPTIONS
$mangopay_tab_option_html = '<ul>';
$mangopay_tab_option_html .= '<li><a href="#" id="business_info" class="active" data-link="mangopay_wrapper_businessinfo">Business Information</a></li>';
if ($user_mp_status == 'business' && $user_business_type == 'business') $mangopay_tab_option_html .= '<li><a href="#" id="business_info" data-link="mangopay_wrapper_uboinfo">UBO Information</a></li>';
$mangopay_tab_option_html .= '<li><a href="#" id="business_info" data-link="mangopay_wrapper_kyc">KYC Details</a></li>';
$mangopay_tab_option_html .= '<li><a href="#" id="business_info" data-link="mangopay_wrapper_bank_account">Bank Details</a></li>';
$mangopay_tab_option_html .= '</ul>';

$vendor_user_billing_fields += array(
    $gateway_slug . '_wrapper_section_options' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => $mangopay_tab_option_html,
    ),
);

// Business Information
if ($user_mp_status == 'business') {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_businessinfo' => array(
            'type'    => 'html',
            'class' => 'mangopay_information_section wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="business_information_header mangopay-headlines"><span class="img_tip wcfmfa fa-question" data-tip="' . $business_information_hints . '" data-hasqtip="50" aria-describedby="qtip-50"></span><h3><strong>' . __('Business Information', 'wc-multivendor-marketplace') . '</strong></h3>',
        ),

        /**
         * Our custom field set here on Mangopay payment page - raju
         */
        $gateway_slug . '_birthday' => array(
            'label'         => __('Birthday', 'wc-multivendor-marketplace'),
            'type'            => 'datepicker',
            'name'             => 'payment[' . $gateway_slug . '][birthday]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['user_birthday'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),
        $gateway_slug . '_birthday_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),
        $gateway_slug . '_nationality' => array(
            'label'         => __('Nationality', 'wc-multivendor-marketplace'),
            'type'             => 'select',
            'options'         => WC()->countries->get_countries(),
            'name'             => 'payment[' . $gateway_slug . '][nationality]',
            'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['user_nationality'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),
        $gateway_slug . '__nationality_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),
        $gateway_slug . '_billing_country' => array(
            'label'         => __('Legal Representative Country Of Residence', 'wc-multivendor-marketplace'),
            'type'             => 'select',
            'options'         => WC()->countries->get_countries(),
            'name'             => 'payment[' . $gateway_slug . '][billing_country]',
            'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['billing_country'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),
        $gateway_slug . '_billing_country_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),
        $gateway_slug . '_legal_email' => array(
            'label'         => __('Legal Representative Email', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][legal_email]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['legal_email'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
            'hints'            => $legal_rep_email_hints,
        ),
        $gateway_slug . '_legal_email_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),
    );
}

/**
 * Our custom field - step 1
 */
if ($user_business_type == 'business') {

    $vendor_user_billing_fields += array(
        /**
         * Our custom field set here on Mangopay payment page - raju
         */
        $gateway_slug . '_compagny_number' => array(
            'label'         => __('Company Number', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][compagny_number]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['compagny_number'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
            'hints'         => $company_number_hints,
        ),
        $gateway_slug . '_compagny_number_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),
    );
}



if ($user_mp_status == 'business') {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_header_hqaddress' => array(
            'type'    => 'html',
            'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<h3><strong>' . __('Headquartes Address', 'wc-multivendor-marketplace') . '</strong></h3>',
        ),

        $gateway_slug . '_hq_address' => array(
            'label'         => __('Headquarters address', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][hq_address]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_address'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
            'hints'            =>    $headquarter_address_hints,
        ),

        $gateway_slug . '_hq_address_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_hq_address2' => array(
            'label'         => __('Headquarters address 2', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][hq_address2]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_address2'],
        ),

        $gateway_slug . '_hq_city' => array(
            'label'         => __('Headquarters city', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][hq_city]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_city'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),

        $gateway_slug . '_hq_city_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_hq_region' => array(
            'label'         => __('Headquarters region', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][hq_region]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_region'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),

        ),

        $gateway_slug . '_hq_region_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_hq_postalcode' => array(
            'label'         => __('Headquarters postalcode', 'wc-multivendor-marketplace'),
            'type'            => 'text',
            'name'             => 'payment[' . $gateway_slug . '][hq_postalcode]',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_postalcode'],
            'custom_attributes'    => array(
                'required'    => 'required'

            ),
        ),

        $gateway_slug . '_hq_postalcode_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_hq_country'     => array(
            'label'         => __('Headquarters country', 'wc-multivendor-marketplace'),
            'name'             => 'payment[' . $gateway_slug . '][hq_country]',
            'type'             => 'select',
            'options'         => WC()->countries->get_countries(),
            'class'         => 'wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
            'value'         => $settings['hq_country'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),

        $gateway_slug . '_hq_country_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_termsAndConditionsAccepted' => array(
            'label'         => __('Agree  <a target="_blank" href="' . $site_url . '">Terms & Conditions!</a>', 'wc-multivendor-marketplace'),
            'name'             => $gateway_slug . '_termsAndConditionsAccepted',
            'type'             => 'checkbox',
            'class'         => 'wcfm-checkbox wcfm_ele paymode_field paymode_' . $gateway_slug,
            'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
            'value'         => 'yes',
            'dfvalue'         => $settings['termsconditions'],
            'custom_attributes'    => array(
                'required'    => 'required'
            ),
        ),

        $gateway_slug . '_termsAndConditionsAccepted_error_msg' => array(
            'type'    => 'html',
            'value'    => '<p id="error-message" class="description custom-mangopay-form-error"></p>',
        ),

        $gateway_slug . '_button_businessinfo' => array(
            'type'    => 'html',
            'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<input type="button" id="update_business_information" value="' . __('Update', 'wc-multivendor-marketplace') . '"><img src="' . plugin_dir_url(__DIR__) . 'assets/images/ajax-loader.gif' . '" height="20" width="20" id="ajax_loader"/><span id="bi_updated">Succesfully Updated</span></div>',
        ),
    );
}

// UBO Information
if ($user_business_type == 'business') {
    $vendor_user_billing_fields += array(
        $gateway_slug . '_wrapper_uboinfo' => array(
            'type'    => 'html',
            'class' => 'mangopay_information_section wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'    => '<div class="mangopay-headlines"><span class="img_tip wcfmfa fa-question" data-tip="' . $ubo_information_hints . '" data-hasqtip="50" aria-describedby="qtip-50"></span><h3><strong>' . __('UBO Information', 'wc-multivendor-marketplace') . '</strong></h3></div><div class="btn button" id="ubo_data"></div>',
        ),

    );
}

// kyc fields
$vendor_kyc_billing_fields = array(
    $gateway_slug . '_wrapper_kyc' => array(
        'type'    => 'html',
        'class' => 'mangopay_information_section wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<div>',
    ),
);
$vendor_kyc_billing_fields += array(
    $gateway_slug . '_header_kyc' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<div class="mangopay-headlines"><span class="img_tip wcfmfa fa-question" data-tip="' . $kyc_details_hints . '" data-hasqtip="50" aria-describedby="qtip-50"></span><h3><strong>' . __('KYC Details', 'wc-multivendor-marketplace') . '</strong></h3></div>',
    ),
);

/* if ($user_mp_status == 'business') {
        if ($user_business_type == 'business' || $user_business_type == 'organisation') {
            $vendor_kyc_billing_fields += array(
                $gateway_slug . '_kyc_notice' => array(
                    'type'			=> 'html',
                    'class' 		=> 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
                    'value' 		=> sprintf(__('<span class="mangopay-kyc-notice">Please upload 3 document(s) i.e. IDENTITY PROOF, REGISTRATION PROOF, ARTICLES OF ASSOCIATION</span>', 'wc-multivendor-marketplace')),
                ),
            );

        } else {
            $vendor_kyc_billing_fields += array(
                $gateway_slug . '_kyc_notice' => array(
                    'type'			=> 'html',
                    'class' 		=> 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
                    'value' 		=> sprintf(__('<span class="mangopay-kyc-notice">Please upload 2 document(s) i.e. IDENTITY PROOF, REGISTRATION PROOF</span>', 'wc-multivendor-marketplace')),
                ),
            );
        }
    } else {
        $vendor_kyc_billing_fields += array(
            $gateway_slug . '_kyc_notice' => array(
                'type'			=> 'html',
                'class' 		=> 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
                'value' 		=> sprintf(__('<span class="mangopay-kyc-notice">Please upload %s document(s) i.e. %s', 'wc-multivendor-marketplace'), count(get_mangopay_kyc_document_types_required('natural')), implode(', ', get_mangopay_kyc_document_types_required('natural'))),
            ),
        );
    } */

if (count($kyc_options) > 0) {
    $vendor_kyc_billing_fields += array(
        $gateway_slug . '_kyc_notice' => array(
            'type'            => 'html',
            'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
            'value'         => sprintf(__('<span class="mangopay-kyc-notice">Please upload %s document(s) i.e. %s', 'wc-multivendor-marketplace'), count($kyc_options), implode(', ', $kyc_options)),
        ),

    );
}

$vendor_kyc_billing_fields += array(
    $gateway_slug . '_kyc_detail' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<div>' . $kyc_documents . '</div>',
    ),

    $gateway_slug . '_kyc_details' => array(
        'name'            => 'payment[' . $gateway_slug . '][kyc_details]',
        'type'             => 'multiinput',
        'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title wcfm_full_title paymode_field paymode_' . $gateway_slug,
        //'value' 		=> $settings['kyc_details'],
        'options'         => array(
            "type"     => array(
                'label'         => __('Document Type', 'wc-multivendor-marketplace'),
                'type'             => 'select',
                'options'         => array_diff(get_mangopay_kyc_document_types(), $remove_kyc_type),
                'class'         => 'wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
                'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
            ),

            'file' => array(
                'label'         => __('Upload File', 'wc-multivendor-marketplace'),
                'type'             => 'upload',
                'mime'             => 'Uploads',
                'class'         => 'wcfm_ele',
                'label_class'     => 'wcfm_title',
                'hints'         => $upload_files_hints,
            )
        ),
        'custom_attributes' => array(
            'limit' => count(get_mangopay_kyc_document_types()),
        ),

    ),

    $gateway_slug . '_upload_kyc' => array(
        'label'         => __('Submit KYC documents', 'wc-multivendor-marketplace'),
        'name'             => $gateway_slug . '_upload_kyc',
        'type'             => 'checkbox',
        'class'         => 'wcfm-checkbox wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
        'value'         => 'yes',
        'dfvalue'         => 'no',
        'hints'         => $submit_kyc_hints,
    ),

    $gateway_slug . '_upload_kyc_status' => array(
        'name' => $gateway_slug . '_upload_kyc_status',
        'type' => 'hidden',
        'value' => 'no',
    ),
);

$vendor_kyc_billing_fields += array(
    $gateway_slug . '_wrapper_kyc_close' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '</div>',
    ),

);

// common fields
$vendor_bank_billing_fields = array(
    $gateway_slug . '_header_bank' => array(
        'type'    => 'html',
        'class'    => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<div class="mangopay-headlines"><span class="img_tip wcfmfa fa-question" data-tip="' . $bank_details_hints . '" data-hasqtip="50" aria-describedby="qtip-50"></span><h3><strong>' . __('Bank Details', 'wc-multivendor-marketplace') . '</strong></h3></div>',
    ),

    $gateway_slug . '_vendor_account_type'     => array(
        'label'         => __('Type', 'wc-multivendor-marketplace'),
        'name'             => 'payment[' . $gateway_slug . '][bank_details][vendor_account_type]',
        'type'             => 'select',
        'options'         => get_mangopay_bank_types(),
        'class'         => 'mangopay-type wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
        'value'         => isset($settings['bank_details']['vendor_account_type']) ? $settings['bank_details']['vendor_account_type'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_account_name' => array(
        'label'         => __('Owner name', 'wc-multivendor-marketplace'),
        'name'             => 'payment[' . $gateway_slug . '][bank_details][vendor_account_name]',
        'type'             => 'text',
        'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'         => isset($settings['bank_details']['vendor_account_name']) ? $settings['bank_details']['vendor_account_name'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_account_address1' => array(
        'label' => __('Owner address line 1', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_account_address1]',
        'type' => 'text',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_account_address1']) ? $settings['bank_details']['vendor_account_address1'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),

    ),

    $gateway_slug . '_vendor_account_address2' => array(
        'label' => __('Owner address line 2', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_account_address2]',
        'type' => 'text',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_account_address2']) ? $settings['bank_details']['vendor_account_address2'] : '',
    ),

    $gateway_slug . '_vendor_account_city' => array(
        'label' => __('Owner city', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_account_city]',
        'type' => 'text',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_account_city']) ? $settings['bank_details']['vendor_account_city'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_account_region' => array(
        'label' => __('Owner region', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_account_region]',
        'type' => 'text',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_account_region']) ? $settings['bank_details']['vendor_account_region'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),

    ),

    $gateway_slug . '_vendor_account_postcode' => array(
        'label' => __('Owner postal code', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_account_postcode]',
        'type' => 'text',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_account_postcode']) ? $settings['bank_details']['vendor_account_postcode'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_account_country'     => array(
        'label'         => __('Owner country', 'wc-multivendor-marketplace'),
        'name'             => 'payment[' . $gateway_slug . '][bank_details][vendor_account_country]',
        'type'             => 'select',
        'options'         => WC()->countries->get_countries(),
        'class'         => 'wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
        'value'         => isset($settings['bank_details']['vendor_account_country']) ? $settings['bank_details']['vendor_account_country'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),
);

// IBAN fields
$vendor_iban_billing_fields = array(
    $gateway_slug . '_vendor_iban' => array(
        'label' => __('IBAN', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_iban]',
        'type' => 'text',
        'class' => 'bank-type bank-type-iban wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_iban']) ? $settings['bank_details']['vendor_iban'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_bic' => array(
        'label' => __('BIC', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_bic]',
        'type' => 'text',
        'class' => 'bank-type bank-type-iban wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_bic']) ? $settings['bank_details']['vendor_bic'] : '',
    ),
);

// GB fields
$vendor_gb_billing_fields = array(
    $gateway_slug . '_vendor_gb_accountnumber' => array(
        'label' => __('Account Number', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_gb_accountnumber]',
        'type' => 'text',
        'class' => 'bank-type bank-type-gb wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_gb_accountnumber']) ? $settings['bank_details']['vendor_gb_accountnumber'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_sort_code' => array(
        'label' => __('Sort Code', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][sort_code]',
        'type' => 'text',
        'class' => 'bank-type bank-type-gb wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['sort_code']) ? $settings['bank_details']['sort_code'] : '',
    ),

);

// US fields
$vendor_us_billing_fields = array(
    $gateway_slug . '_vendor_us_accountnumber' => array(
        'label' => __('Account Number', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_us_accountnumber]',
        'type' => 'text',
        'class' => 'bank-type bank-type-us wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_us_accountnumber']) ? $settings['bank_details']['vendor_us_accountnumber'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_us_aba' => array(
        'label' => __('ABA', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_us_aba]',
        'type' => 'text',
        'class' => 'bank-type bank-type-us wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_us_aba']) ? $settings['bank_details']['vendor_us_aba'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),

    ),

    $gateway_slug . '_vendor_us_datype'     => array(
        'label'         => __('Deposit Account Type', 'wc-multivendor-marketplace'),
        'name'             => 'payment[' . $gateway_slug . '][bank_details][vendor_us_datype]',
        'type'             => 'select',
        'options'         => get_mangopay_deposit_account_types(),
        'class'         => 'bank-type bank-type-us wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
        'value'         => isset($settings['bank_details']['vendor_us_datype']) ? $settings['bank_details']['vendor_us_datype'] : '',
    ),
);

// CA fields
$vendor_ca_billing_fields = array(
    $gateway_slug . '_vendor_ca_bankname' => array(
        'label' => __('Bank Name', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ca_bankname]',
        'type' => 'text',
        'class' => 'bank-type bank-type-ca wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ca_bankname']) ? $settings['bank_details']['vendor_ca_bankname'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_ca_instnumber' => array(
        'label' => __('Institution Number', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ca_instnumber]',
        'type' => 'text',
        'class' => 'bank-type bank-type-ca wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ca_instnumber']) ? $settings['bank_details']['vendor_ca_instnumber'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_ca_branchcode' => array(
        'label' => __('Branch Code', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ca_branchcode]',
        'type' => 'text',
        'class' => 'bank-type bank-type-ca wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ca_branchcode']) ? $settings['bank_details']['vendor_ca_branchcode'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_ca_accountnumber' => array(
        'label' => __('Account Number', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ca_accountnumber]',
        'type' => 'text',
        'class' => 'bank-type bank-type-ca wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ca_accountnumber']) ? $settings['bank_details']['vendor_ca_accountnumber'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),
);

// OTHER fields
$vendor_other_billing_fields = array(
    $gateway_slug . '_vendor_ot_country' => array(
        'label' => __('Country', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ot_country]',
        'type' => 'text',
        'class' => 'bank-type bank-type-other wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ot_country']) ? $settings['bank_details']['vendor_ot_country'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),

    $gateway_slug . '_vendor_ot_bic' => array(
        'label' => __('BIC', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ot_bic]',
        'type' => 'text',
        'class' => 'bank-type bank-type-other wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ot_bic']) ? $settings['bank_details']['vendor_ot_bic'] : '',
    ),

    $gateway_slug . '_vendor_ot_accountnumber' => array(
        'label' => __('Account Number', 'wc-multivendor-marketplace'),
        'name' => 'payment[' . $gateway_slug . '][bank_details][vendor_ot_accountnumber]',
        'type' => 'text',
        'class' => 'bank-type bank-type-other wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class' => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value' => isset($settings['bank_details']['vendor_ot_accountnumber']) ? $settings['bank_details']['vendor_ot_accountnumber'] : '',
        'custom_attributes'    => array(
            'required'    => 'required'
        ),
    ),
);

$vendor_add_bank_account = array(
    $gateway_slug . '_add_bank' => array(
        'label'         => __('Add Bank Account', 'wc-multivendor-marketplace'),
        'name'             => $gateway_slug . '_add_bank',
        'type'             => 'checkbox',
        'class'         => 'wcfm-checkbox wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
        'value'         => 'yes',
        'dfvalue'         => 'no',
        'hints'         => $add_bank_hints,
    ),

    $gateway_slug . '_add_bank_status' => array(
        'label' => __('BIC', 'wc-multivendor-marketplace'),
        'name' => $gateway_slug . '_add_bank_status',
        'type' => 'hidden',
        'value' => 'no',
    ),
);

$vendor_wrapper_bank_account_open = array(
    $gateway_slug . '_wrapper_bank_account' => array(
        'type'    => 'html',
        'class' => 'mangopay_information_section wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<div>',
    ),
);

$vendor_wrapper_bank_account_close = array(
    $gateway_slug . '_wrapper_bank_account_close' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '</div>',
    ),

);


$user_mp_details_field = array_merge(
    //  $vendor_billing_fields,
    $vendor_user_billing_fields,
    $vendor_kyc_billing_fields,
    $vendor_wrapper_bank_account_open,
    $vendor_bank_billing_fields,
    $vendor_iban_billing_fields,
    $vendor_gb_billing_fields,
    $vendor_us_billing_fields,
    $vendor_ca_billing_fields,
    $vendor_other_billing_fields,
    $vendor_add_bank_account,
    $vendor_wrapper_bank_account_close
);
