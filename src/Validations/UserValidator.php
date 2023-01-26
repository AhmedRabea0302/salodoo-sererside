<?php
namespace Src\Validations;
class UserValidator {
    public function validateUserRegister($input) {
        $errors = [];
        if (! isset($input['name']) | $input['name'] == '') {
            array_push($errors, ['name' => 'Please provide a name']);
        }
        if (! isset($input['phone_number']) | $input['phone_number'] == '') {
            array_push($errors, ['phone_number' => 'Please provide a phone_number']);
        }
        if (! isset($input['email']) | $input['email'] == '') {
            array_push($errors, ['email' => 'Please provide a valid email']);
        }
        if (! isset($input['password'])| $input['password'] == '') {
            array_push($errors, ['password' => 'Please provide a password']);
        }
        if (! isset($input['user_type']) | $input['user_type'] == '') {
            array_push($errors, ['password' => 'Please provide a user Type']);
        }
        return $errors;
    }

    public function validateUserLogin($input) {
        $errors = [];
        if (! isset($input['email']) | $input['email'] == '') {
            array_push($errors, ['email' => 'Please provide a valid email']);
        }
        if (! isset($input['password']) | $input['password'] == '') {
            array_push($errors, ['password' => 'Please provide a password']);
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