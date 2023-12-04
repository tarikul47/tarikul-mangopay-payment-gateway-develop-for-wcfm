<?php

/**
 * Data retrive 
 */

$settings['first_name'] = get_user_meta($vendor_id, 'first_name', true) ? get_user_meta($vendor_id, 'first_name', true) : '';

$settings['last_name'] = get_user_meta($vendor_id, 'last_name', true) ? get_user_meta($vendor_id, 'last_name', true) : '';

$settings['user_birthday'] = get_user_meta($vendor_id, 'user_birthday', true) ? date('F j, Y', strtotime(get_user_meta($vendor_id, 'user_birthday', true))) : '';

$settings['user_nationality'] = get_user_meta($vendor_id, 'user_nationality', true) ? get_user_meta($vendor_id, 'user_nationality', true) : '';

$settings['billing_country'] = get_user_meta($vendor_id, 'billing_country', true) ? get_user_meta($vendor_id, 'billing_country', true) : '';

$settings['billing_state'] = get_user_meta($vendor_id, 'billing_state', true) ? get_user_meta($vendor_id, 'billing_state', true) : '';

$settings['user_mp_status'] = isset($vendor_data['payment'][$gateway_slug]['user_mp_status']) ? $vendor_data['payment'][$gateway_slug]['user_mp_status'] : '';

$settings['user_business_type'] = isset($vendor_data['payment'][$gateway_slug]['user_business_type']) ? $vendor_data['payment'][$gateway_slug]['user_business_type'] : '';

$user_mp_status = get_user_meta($vendor_id, 'user_mp_status', true) ? get_user_meta($vendor_id, 'user_mp_status', true) : '';


//var_dump($user_mp_status);
//var_dump(get_user_meta($vendor_id, 'first_name', true));
//var_dump(get_user_meta($vendor_id, 'last_name', true));
//var_dump(get_user_meta($vendor_id, 'user_birthday', true));

$states = array();

if (isset($settings['billing_country'])) {
    $states = WC()->countries->get_states($settings['billing_country']);
}

$vendor_user_mp_register_fields = array();

$vendor_user_mp_register_fields += array(
    $gateway_slug . '_wrapper_mg_payment' => array(
        'type'                  => 'html',
        'class'                 => 'mangopay_information_section wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                 => '<h3 class="business_information_header mangopay-headlines">You have to create Mangopay account for your withdraw.</h3>',
    ),

    $gateway_slug . '_vendor_id' => array(
        'type'            => 'hidden',
        'name'             => $gateway_slug . '_vendor_id',
        'class' => 'raju',
        'value'         => $vendor_id
    ),

    $gateway_slug . '_firstname' => array(
        'label'                 => __('First Name', 'wc-multivendor-marketplace'),
        'type'                  => 'text',
        'name'                  => 'payment[' . $gateway_slug . '][first_name]',
        'class'                 => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                 => $settings['first_name'],
        'custom_attributes'     => array(
            'required'          => 'required'
        ),
    ),

    $gateway_slug . '_firstname_error_msg' => array(
        'type'    => 'html',
        'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
    ),

    $gateway_slug . '_lastname' => array(
        'label'                 => __('Last Name', 'wc-multivendor-marketplace'),
        'type'                  => 'text',
        'name'                  => 'payment[' . $gateway_slug . '][last_name]',
        'class'                 => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                 => $settings['last_name'],
        'custom_attributes'     => array(
            'required'          => 'required'
        ),
    ),

    $gateway_slug . '_laststname_error_msg' => array(
        'type'    => 'html',
        'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
    ),

    $gateway_slug . '_birthday' => array(
        'label'                 => __('Birthday', 'wc-multivendor-marketplace'),
        'type'                  => 'datepicker',
        'name'                  => 'payment[' . $gateway_slug . '][user_birthday]',
        'class'                 => 'wcfm-text wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                 => $settings['user_birthday'],
        'custom_attributes'     => array(
            'required'          => 'required',
        ),
    ),


    $gateway_slug . '_birthday_error_msg' => array(
        'type'    => 'html',
        'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
    ),

    $gateway_slug . '_nationality' => array(
        'label'                 => __('Nationality', 'wc-multivendor-marketplace'),
        'type'                  => 'select',
        'options'               => WC()->countries->get_countries(),
        'name'                  => 'payment[' . $gateway_slug . '][user_nationality]',
        'class'                 => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                 => $settings['user_nationality'],
        'custom_attributes'     => array(
            'required'          => 'required'
        ),
    ),

    $gateway_slug . '__nationality_error_msg' => array(
        'type'    => 'html',
        'value'    => '<p id="error-message" class="description  wcfm_page_options_desc custom-mangopay-form-error"></p>',
    ),

    $gateway_slug . '_billing_country' => array(
        'label'                 => __('Billing Country', 'wc-multivendor-marketplace'),
        'type'                  => 'select',
        'options'               => WC()->countries->get_countries(),
        'name'                  => 'payment[' . $gateway_slug . '][billing_country]',
        'class'                 => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'         => $settings['billing_country'],
        'custom_attributes'     => array(
            'required'          => 'required'
        ),
    ),


    $gateway_slug . '_billing_state' => array(
        'label'                 => __('State', 'wc-multivendor-marketplace'),
        'type'                  => 'select',
        'options'               => $states,
        'name'                  => 'payment[' . $gateway_slug . '][billing_state]',
        'class'                 => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'         => $settings['billing_state'],
        // 'custom_attributes'     => array(
        //     'required'          => 'required'
        // ),
    ),
);

if (!$user_mp_status) {
    if ('either' === $this->mp->default_vendor_status) {
        $vendor_user_mp_register_fields += array(
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
            $vendor_user_mp_register_fields += array(
                $gateway_slug . '_user_business_type' => array(
                    'label'         => __('Business Type', 'wc-multivendor-marketplace'),
                    'type'             => 'select',
                    'options'         => array(
                        'business'        => __('BUSINESS', 'wc-multivendor-marketplace'),
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

$vendor_user_mp_register_fields += array(
    $gateway_slug . '_button_mp_submit' => array(
        'type'                  => 'html',
        'name'                  => 'payment[' . $gateway_slug . ']',
        'class'                 => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        'value'                  => '<input type="button" id="submit_mp" value="' . __('Submit', 'wc-multivendor-marketplace') . '"><img src="' . plugin_dir_url(__DIR__) . 'assets/images/ajax-loader.gif' . '" height="20" width="20" id="ajax_loader"/><div id="mp_submit"></div>'
    ),
    $gateway_slug . '_error_messages' => array(
        'type'                  => 'html',
        'name'                  => 'payment[' . $gateway_slug . ']',
        'class'                 => 'wcfm-select wcfm_ele paymode_field paymode_' . $gateway_slug,
        'label_class'           => 'wcfm_title wcfm_ele paymode_field paymode_' . $gateway_slug,
        // 'value'                  => '<div id="error-messages"></div>'
    ),
);
