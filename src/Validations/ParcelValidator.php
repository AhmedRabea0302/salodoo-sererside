<?php
namespace Src\Validations;
class ParcelValidator {

    public function validateCreateParcel($input)
    {
        $errors = [];
        if (! isset($input['parcel_name']) || $input['parcel_name'] == '') {
            array_push($errors, ['parcel_name' => 'Please provide a parcel name']);
        }
        if (! isset($input['pickup_address']) || $input['pickup_address'] == '') {
            array_push($errors, ['pickup_address' => 'Please provide Pickup address']);
        }
        if (! isset($input['dropoff_address']) || $input['dropoff_address'] == '') {
            array_push($errors, ['dropoff_address' => 'Please provide a dropoff address']);
        }
        return $errors;
    }

    public function validateUpdateParcel($input)
    {
        $errors = [];
        $pickup_condition = (! isset($input['pickedup_at']) || !$input['pickedup_at'] == '')
            && (! isset($input['dropedoff_at']) || $input['dropedoff_at'] == '');

        if ((! isset($input['status']) || $input['status'] == '') && $pickup_condition) {
            array_push($errors, ['status' => 'Please provide a parcel status']);
        }  else {
            return $errors;
        }

        if (! isset($input['pickedup_at']) || $input['pickedup_at'] == '') {
            array_push($errors, ['pickedup_at' => 'Please provide Pickup Time']);
        }
        if (! isset($input['dropedoff_at']) || $input['dropedoff_at'] == '') {
            array_push($errors, ['dropedoff_at' => 'Please provide a Dropoff Time']);
        }
        return $errors;
    }

    public function notFoundResponse()
    {
        http_response_code(404);
        $response['body'] = null;
        return $response;
    }
}