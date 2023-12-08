<?php
function save_user_meta($data)
{
    // Assuming $data['vendor_id'] is the user ID
    $user_id = $data['vendor_id'];

    // Check if it's a create or update action
    $action = isset($data['action']) ? $data['action'] : '';

    // Update general user meta
    $this->update_general_user_meta($user_id, $data);

    // Perform action-specific updates
    switch ($action) {
        case 'create_mp_account':
            $this->create_mp_account_specific_updates($user_id, $data);
            break;

        case 'update_mp_business_information':
            $this->update_mp_business_information_specific_updates($user_id, $data);
            break;

            // Add more cases for other actions if needed

        default:
            // Handle unknown action or no action
            break;
    }
}

function update_general_user_meta($user_id, $data)
{
    // Define an array of fields that don't require special handling
    $fields = array_diff(array_keys($data), array('vendor_id', 'action', 'payment_method', 'first_name', 'last_name', 'user_birthday'));

    foreach ($fields as $key) {
        update_user_meta($user_id, $key, $data[$key]);
    }
}

function create_mp_account_specific_updates($user_id, $data)
{
    // Update payment method if present in $data
    if (isset($data['payment_method'])) {
        $vendor_data = get_user_meta($user_id, 'wcfmmp_profile_settings', true);

        if (!$vendor_data || !is_array($vendor_data)) {
            $vendor_data = array();
        }

        if (!isset($vendor_data['payment']) || !is_array($vendor_data['payment'])) {
            $vendor_data['payment'] = array();
        }

        $vendor_data['payment']['method'] = $data['payment_method'];

        update_user_meta($user_id, 'wcfmmp_profile_settings', $vendor_data);
    }
}

function update_mp_business_information_specific_updates($user_id, $data)
{
    // Define an array of fields that require special handling
    $specialFields = array('first_name', 'last_name', 'user_birthday');

    foreach ($specialFields as $key) {
        if (isset($data[$key])) {
            if ($key === 'user_birthday') {
                // Convert date if it's the 'user_birthday' field
                $convertedDate = $this->convertDate($data[$key]);
                update_user_meta($user_id, $key, $convertedDate);
            } else {
                // Handle other special fields
                update_user_meta($user_id, $key, $data[$key]);
            }
        }
    }
}
