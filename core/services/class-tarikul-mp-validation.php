<?php
class TARIKUL_MP_VALIDATION
{
    // Common function to validate input data
    public function validate_input($user_id, $input_data)
    {
        // Check if 'action' is set
        if (!isset($input_data['action'])) {
            wp_send_json_error("Error: 'action' is required.", 400);
        }

        $action = $input_data['action'];

        $errors = [];

        // Validate action-specific fields
        switch ($action) {
            case 'create_mp_account':
                $this->validation_for_create_mp_account($user_id, $input_data, $errors);
                //error_log(print_r($input_data));
                break;

            case 'update_mp_business_information':
                //   $this->validation_for_update_mp_business_information($input_data, $errors);
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

    private function validation_for_create_mp_account($user_id, $data, &$errors)
    {
        // Define create required fields
        $required_fields = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'user_birthday' => 'User Birthday',
            'user_nationality' => 'User Nationality',
            'billing_country' => 'Billing Country',
            'billing_state' => 'Billing State',
            'user_mp_status' => 'User MP Status',
            'user_business_type' => 'User Business Type',
        ];

        // Validate common required fields
        $this->validate_required_fields($user_id, $data, $required_fields, $errors);
    }

    // private function validation_for_update_mp_business_information($data, &$errors)
    // {
    //     //	$data = $this->mangopay_acount_update_sample_data();
    //     //	error_log(print_r($data, true));

    //     // Define common required fields
    //     $common_required_fields = [
    //         'vendor_id' => 'Vendor ID',
    //         'user_birthday' => 'User Birthday',
    //         'user_nationality' => 'User Nationality',
    //         'billing_country' => 'Billing Country',
    //     ];

    //     // Define type-specific required fields and conditions for 'update' action
    //     $update_specific_fields = [
    //         'legal_email' => 'Legal Email',
    //         'compagny_number' => 'Company Number',
    //         'headquarters_addressline1' => 'Headquarters Addressline1',
    //         'headquarters_city' => 'Headquarters City',
    //         'headquarters_region' => 'Headquarters Region',
    //         'headquarters_postalcode' => 'Headquarters Postalcode',
    //         'headquarters_country' => 'Headquarters Country',
    //         'termsconditions' => 'Please agree',
    //         // Add other fields specific to this type
    //     ];

    //     // Validate common required fields
    //     $this->validate_required_fields($data, $common_required_fields, $errors);

    //     // Validate type-specific fields for 'update' action
    //     $this->validate_required_fields($data, $update_specific_fields, $errors);
    // }

    private function validate_required_fields($user_id, $data, $required_fields, &$errors)
    {
        error_log(print_r($data, true));
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
}
