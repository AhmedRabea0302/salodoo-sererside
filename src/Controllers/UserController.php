<?php
namespace Src\Controllers;

use Src\Middleware\Authenticate;
use Src\TableGateways\UserGateway;
use Src\Validations\UserValidator;

class UserController {
    private $db;
    private $requestMethod;
    private $param;
    private $method;

    private $userGatway;

    public function __construct($db, $requestMethod, $param, $method)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->param = $param;
        $this->method = $method;
        $this->userGatway = new UserGateway($db);
    }

    public function processRequest()
    {
        switch (true) {
            case $this->requestMethod == 'POST' && $this->method == 'login':
                $response = $this->login();
                break;
            case $this->requestMethod == 'POST' && $this->method == 'register':
                $response = $this->register();
                break;
            default:
                $response = (new UserValidator)->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function login() 
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $validator = (new UserValidator)->validateUserLogin($input);
        if ($validator) {
            http_response_code(500);
            $response['body'] = json_encode($validator);
            return $response;
        }
        $user = $this->userGatway->identifyUser($input['email']);
        if($user) {
            if(password_verify($input['password'], $user[0]['password'])) {
                $token = (new Authenticate)->generateToken($user[0]['id'], $input['email']);
                http_response_code(200);
                $response['token'] = $token;
                $response['body'] = json_encode([
                    'message' => 'User Authenticated', 
                    'user_id' => $user[0]['id'],
                    'user_type' => $user[0]['user_type'],
                    'token' => $token]);
                return $response;
            } else {
                http_response_code(401);
                $response['body'] = json_encode(['message' => 'UnAuthorized']);
                return $response;
            }
        } else {
            http_response_code(401);
            $response['body'] = json_encode(['message' => 'UnAuthorized']);
            return $response;
        }
    }

    private function register()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $validator = (new UserValidator)->validateUserRegister($input);
        if ($validator) {
            http_response_code(500);
            $response['body'] = json_encode($validator);
            return $response;
        }
        $userExists = $this->userGatway->identifyUser($input['email']);
        if($userExists) {
            http_response_code(409);
            $response['body'] = json_encode([
                'message' => 'This email address already exists',
                'status' => '0'
            ]);
            return $response;
        } else {
            $this->userGatway->insert($input);
            http_response_code(201);
            $response['body'] = json_encode([
                'message' => 'User created successfully, You can login now!', 
                'status' => '1'
            ]);
            return $response;
        }
    }
}