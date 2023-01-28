<?php
namespace Src\Controllers;
use Src\Middleware\Authenticate;
use Src\TableGateways\ParcelGateway;
use Src\Validations\ParcelValidator;

class ParcelController {
    private $db;
    private $requestMethod;
    private $param;
    private $method;

    private $parcelGateWay;

    public function __construct($db, $requestMethod, $param, $method)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->param = $param;
        $this->method = $method;
        $this->parcelGateWay = new ParcelGateway($db);
    }

    public function processRequest()
    {
        switch (true) {
            case $this->requestMethod == 'GET' && $this->method == 'all-parcels':
                $response = $this->getAllParcels($this->param);
                break;
            case $this->requestMethod == 'GET' && $this->method == 'user-all-parcels':
                $response = $this->getUserAllParcels($this->param);
                break;
            case $this->requestMethod == 'GET' && $this->method == 'parcel-details':
                $response = $this->findParcel($this->param);
                break;
            case $this->requestMethod == 'POST' && $this->method == 'createparcel':
                $response = $this->createParcel();
                break;
            default:
                $response = (new ParcelValidator)->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }
    private function findParcel($param) {
        $result = $this->parcelGateWay->findParcelDetails($param);
        http_response_code(200);
        $response['body'] = json_encode($result);
        return $response;
    }
    private function getUserAllParcels($param)
    {
        $jwtData = (new Authenticate)->authenticate();
        $user_id = (int) $jwtData->id;
        if($param != $user_id) {
            http_response_code(401);
            $response['body'] = json_encode(['message' => 'UnAuthorized']);
            return $response;
        }
        $result = $this->parcelGateWay->findAllParcels($user_id);
        http_response_code(200);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getAllParcels($param)
    {
        $jwtData = (new Authenticate)->authenticate();
        $user_id = (int) $jwtData->id;

        if($param != $user_id) {
            http_response_code(401);
            $response['body'] = json_encode(['message' => 'UnAuthorized']);
            return $response;
        }
        $result = $this->parcelGateWay->findAllBikerParcels();
        http_response_code(200);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createParcel() 
    {
        $jwtData = (new Authenticate)->authenticate();
        $user_id = (int) $jwtData->id;

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $validator = (new ParcelValidator)->validateCreateParcel($input);
        if ($validator) {
            http_response_code(500);
            $response['body'] = json_encode($validator);
            return $response;
        }

        $input['user_id'] = $user_id;
        $this->parcelGateWay->insert($input);
        http_response_code(200);
        $response['body'] = json_encode(['message' => 'Parcel Created Successfully!']);
        return $response;
        
    }
}