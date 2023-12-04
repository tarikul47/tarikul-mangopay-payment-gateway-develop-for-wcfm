<?php
// Add the action hook
add_action("wp_ajax_create_mp_account", array(&$this, "create_mp_account"));

// Hook callback function
function create_mp_account()
{
    // Get input data
    $input_data = $_POST;

    // Validate the input data
    $validation_result = $this->validate_input($input_data);

    if ($validation_result === true) {
        // Sanitize the input data
        $sanitized_data = $this->sanitize_input($input_data);

        // Save the sanitized data as user meta
        $this->save_user_meta($sanitized_data);

        // Execute another function after sanitizing
        $this->execute_after_sanitize($sanitized_data);

        // Additional processing or response handling can be added here

        // Send success response if needed
        wp_send_json_success("Data saved successfully");
    } else {
        // Send error response with validation message
        wp_send_json_error($validation_result);
    }
}

// Common function to validate input data
function validate_input($input_data)
{
    foreach ($input_data as $key => $value) {
        // Check if any required field is empty
        if (empty($value)) {
            return "Error: {$key} is required.";
        }

        // Additional custom validation rules can be added here
    }

    // If validation passes, return true
    return true;
}

// Common function to sanitize input data
function sanitize_input($input_data)
{
    $sanitized_data = array_map(array($this, 'sanitize_field'), $input_data);
    // Additional custom sanitization can be added if needed
    return $sanitized_data;
}

// Helper function to sanitize a single field
function sanitize_field($value)
{
    return sanitize_text_field($value);
}

// Common function to save input data as user meta
function save_user_meta($data)
{
    // Assuming $data['vendor_id'] is the user ID
    $user_id = $data['vendor_id'];

    // Save data as user meta using update_user_meta
    foreach ($data as $key => $value) {
        update_user_meta($user_id, $key, $value);
    }
}

// Common function to execute after sanitizing
function execute_after_sanitize($data)
{
    // Additional actions or functions to execute can be added here
    // For example, trigger an email, update some other records, etc.
    // You can customize this function based on your specific requirements
}


//---------------------------

function validate_input($input_data)
{
    $errors = array();

    foreach ($input_data as $key => $value) {
        // Check if any required field is empty
        if (empty($value)) {
            $errors[$key] = "Error: {$key} is required.";
        }

        // Additional custom validation rules can be added here
    }

    // If there are errors, send the error response
    if (!empty($errors)) {
        wp_send_json_error($errors);
    }

    // If validation passes, return true
    return true;
}


//-------------------------------------------

function wooc_save_extra_register_fields($customer_id)
{

    if (isset($_POST['billing_first_name'])) {
        // WordPress default first name field.
        update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']));

        // WooCommerce billing first name.
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    }

    if (isset($_POST['billing_last_name'])) {
        // WordPress default last name field.
        update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']));

        // WooCommerce billing last name.
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    }

    if (isset($_POST['user_birthday'])) {
        // New custom user meta field
        update_user_meta(
            $customer_id,
            'user_birthday',
            $this->convertDate($_POST['user_birthday'])
        );
    }

    if (isset($_POST['user_nationality'])) {
        // New custom user meta field
        update_user_meta($customer_id, 'user_nationality', sanitize_text_field($_POST['user_nationality']));
    }

    if (isset($_POST['billing_country'])) {
        // WooCommerce billing country.
        update_user_meta($customer_id, 'billing_country', sanitize_text_field($_POST['billing_country']));
    }

    if (isset($_POST['billing_state'])) {
        // WooCommerce billing state.
        update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['billing_state']));
    }

    if (isset($_POST['user_mp_status'])) {
        // New custom user meta field
        update_user_meta($customer_id, 'user_mp_status', sanitize_text_field($_POST['user_mp_status']));
    }

    if (isset($_POST['user_business_type'])) {
        // New custom user meta field
        update_user_meta($customer_id, 'user_business_type', sanitize_text_field($_POST['user_business_type']));
    }

    $mp_user_id = $this->mp->set_mp_user($customer_id);

    $this->mp->set_mp_wallet($mp_user_id);

    /** Update MP user account **/
    $this->on_shop_settings_saved($customer_id);
}


//-----------------------

// Common function to save input data as user meta
function save_user_meta($data)
{
    // Assuming $data['vendor_id'] is the user ID
    $user_id = $data['vendor_id'];

    // Save data as user meta using update_user_meta
    foreach ($data as $key => $value) {
        update_user_meta($user_id, $key, $value);
    }
    // }
}

// Common function to save input data as user meta
function save_user_meta($data)
{
    // Assuming $data['vendor_id'] is the user ID
    $user_id = $data['vendor_id'];

    // payment mood save 
    if ($data['payment_method']) {


        $vendor_data = get_user_meta($user_id, 'wcfmmp_profile_settings', true);
        if (!$vendor_data || !is_array($vendor_data)) {
            $vendor_data = array();
        }

        // Ensure the 'payment' key is an array
        if (!isset($vendor_data['payment']) || !is_array($vendor_data['payment'])) {
            $vendor_data['payment'] = array();
        }

        $vendor_data['payment']['method'] = $data['payment_method'];
        update_user_meta($user_id, 'wcfmmp_profile_settings', $vendor_data);
    } else if ($data['first_name']) {

        // WordPress default first name field.
        update_user_meta($user_id, 'first_name', $data['first_name']);

        // WooCommerce billing first name.
        update_user_meta($user_id, 'billing_first_name', $data['first_name']);
    } else if ($data['last_name']) {

        // WordPress default last name field.
        update_user_meta($user_id, 'last_name', $data['last_name']);

        // WooCommerce billing last name.
        update_user_meta($user_id, 'billing_last_name', $data['last_name']);
    } else if ($data['user_birthday']) {
        // New custom user meta field
        update_user_meta(
            $user_id,
            'user_birthday',
            $this->convertDate($data['user_birthday'])
        );
    } else {
        // Save data as user meta using update_user_meta
        foreach ($data as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }
    }
}
