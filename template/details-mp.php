<?php


$settings = array();



$settings['first_name'] = isset($vendor_data['payment'][$gateway_slug]['user_mp_status']) ? $vendor_data['payment'][$gateway_slug]['user_mp_status'] : '';
$settings['billing_last_name'] = get_user_meta($vendor_id, 'last_name', true);

$settings['user_mp_status']     = isset($vendor_data['payment'][$gateway_slug]['user_mp_status']) ? $vendor_data['payment'][$gateway_slug]['user_mp_status'] : '';
$settings['user_business_type'] = isset($vendor_data['payment'][$gateway_slug]['user_business_type']) ? $vendor_data['payment'][$gateway_slug]['user_business_type'] : '';
$settings['birthday']             = isset($vendor_data['payment'][$gateway_slug]['birthday']) ? $vendor_data['payment'][$gateway_slug]['birthday'] : '';
$settings['nationality']         = isset($vendor_data['payment'][$gateway_slug]['nationality']) ? $vendor_data['payment'][$gateway_slug]['nationality'] : '';
$settings['kyc_details']         = isset($vendor_data['payment'][$gateway_slug]['kyc_details']) ? $vendor_data['payment'][$gateway_slug]['kyc_details'] : array();
$settings['bank_details']         = isset($vendor_data['payment'][$gateway_slug]['bank_details']) ? $vendor_data['payment'][$gateway_slug]['bank_details'] : array();

$vendor_user_billing_fields = array();

$user_mp_status = get_user_meta($vendor_id, 'user_mp_status', true) ? get_user_meta($vendor_id, 'user_mp_status', true) : '';

// if (!$user_mp_status) {
//     if ('either' === $this->mp->default_vendor_status) {
//         $vendor_user_billing_fields += array(
//             $gateway_slug . '_user_mp_status' => array(
//                 'label'         => __('User Type', 'wc-multivendor-marketplace'),
//                 'type'             => 'select',
//                 'options'         => array(
//                     'individual'    => __('NATURAL', 'wc-multivendor-marketplace'),
//                     'business'        => __('BUSINESS', 'wc-multivendor-marketplace'),
//                 ),
//                 'name'             => 'payment[' . $gateway_slug . '][user_mp_status]',
//                 'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
//                 'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
//                 'value'         => $settings['user_mp_status'],
//                 'custom_attributes'    => array(
//                     'required'    => 'required'
//                 ),
//             ),
//         );
//     }

//     if ('either' === $this->mp->default_vendor_status || 'businesses' === $this->mp->default_vendor_status) {
//         if ('either' == $this->mp->default_business_type) {
//             $vendor_user_billing_fields += array(
//                 $gateway_slug . '_user_business_type' => array(
//                     'label'         => __('Business Type', 'wc-multivendor-marketplace'),
//                     'type'             => 'select',
//                     'options'         => array(
//                         'business'        => __('BUSINESS', 'wc-multivendor-marketplace'),
//                         'organisation'    => __('ORGANIZATION', 'wc-multivendor-marketplace'),
//                         'soletrader'    => __('SOLETRADER', 'wc-multivendor-marketplace'),
//                     ),
//                     'name'             => 'payment[' . $gateway_slug . '][user_business_type]',
//                     'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
//                     'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
//                     'value'         => $settings['user_business_type'],
//                     'custom_attributes'    => array(
//                         'required'    => 'required'
//                     ),
//                 ),
//             );
//         }
//     }
// }

// $vendor_user_billing_fields += array(
//     $gateway_slug . '_birthday' => array(
//         'label'         => __('Birthday', 'wc-multivendor-marketplace'),
//         'type'            => 'datepicker',
//         'name'             => 'payment[' . $gateway_slug . '][birthday]',
//         'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
//         'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
//         'value'         => $settings['birthday'],
//         'custom_attributes'    => array(
//             'required'        => 'required',
//             'date_format'    => 'dd-mm-yy'
//         ),
//     ),
//     $gateway_slug . '_nationality' => array(
//         'label'         => __('Nationality', 'wc-multivendor-marketplace'),
//         'type'             => 'select',
//         'options'         => WC()->countries->get_countries(),
//         'name'             => 'payment[' . $gateway_slug . '][nationality]',
//         'class'         => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
//         'label_class'     => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
//         'value'         => $settings['nationality'],
//         'custom_attributes'    => array(
//             'required'    => 'required'
//         ),
//     ),
// );

// kyc fields
$vendor_kyc_billing_fields = array(
    $gateway_slug . '_header_kyc' => array(
        'type'    => 'html',
        'class' => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<h3><strong>' . __('KYC Details', 'wc-multivendor-marketplace') . '</strong></h3>',
    ),
    $gateway_slug . '_kyc_notice' => array(
        'type'            => 'html',
        'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'         => sprintf(__('<span class="mangopay-kyc-notice">***For NATURAL/INDIVIDUAL user : Please upload %s document(s) i.e. %s<br/>***For BUSINESS user : Please upload %s document(s) i.e. %s<br/>%s</span>', 'wc-multivendor-marketplace'), count(get_mangopay_kyc_document_types_required('natural')), implode(', ', get_mangopay_kyc_document_types_required('natural')), count(get_mangopay_kyc_document_types_required('business')), implode(', ', get_mangopay_kyc_document_types_required('business')), $user_mp_status ? sprintf(__('***you are : %s user', 'wc-multivendor-marketplace'), strtoupper($user_mp_status)) : ''),
    ),
    $gateway_slug . '_kyc_details' => array(
        'name'            => 'payment[' . $gateway_slug . '][kyc_details]',
        'type'             => 'multiinput',
        'class'         => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'     => 'wcfm_title wcfm_full_title paymode_field paymode_' . $gateway_slug,
        'value'         => $settings['kyc_details'],
        'options'         => array(
            "type"     => array(
                'label'         => __('Document Type', 'wc-multivendor-marketplace'),
                'type'             => 'select',
                'options'         => get_mangopay_kyc_document_types(),
                'class'         => 'wcfm-select wcfm_ele field_type_options paymode_field paymode_' . $gateway_slug,
                'label_class'     => 'wcfm_title paymode_field paymode_' . $gateway_slug,
            ),
            'file' => array(
                'label'         => __('Upload File', 'wc-multivendor-marketplace'),
                'type'             => 'upload',
                'mime'             => 'Uploads',
                'class'         => 'wcfm_ele',
                'label_class'     => 'wcfm_title',
                'hints'         => __('please upload .pdf, .doc', 'wc-multivendor-marketplace'),
            ),
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
        'hints'         => __('If this field is checked kyc files will be uploaded to mangopay account.<br/> P.S. remember to uncheck it after first time use to avoid multiple uploads', 'wc-multivendor-marketplace'),
    ),
);

// common fields
$vendor_bank_billing_fields = array(
    $gateway_slug . '_header_bank' => array(
        'type'    => 'html',
        'class'    => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'    => '<h3><strong>' . __('Bank Details', 'wc-multivendor-marketplace') . '</strong></h3>',
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

// return array_merge(
//     $vendor_billing_fields,
//     $vendor_user_billing_fields,
//     $vendor_kyc_billing_fields,
//     $vendor_bank_billing_fields,
//     $vendor_iban_billing_fields,
//     $vendor_gb_billing_fields,
//     $vendor_us_billing_fields,
//     $vendor_ca_billing_fields,
//     $vendor_other_billing_fields
// );
