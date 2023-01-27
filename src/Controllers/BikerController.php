<?php
namespace Src\Controllers;

use Src\Middleware\Authenticate;
use Src\TableGateways\ParcelGateway;
use Src\Validations\ParcelValidator;

class BikerController {
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
            case $this->requestMethod == 'POST' && $this->method == 'updateparcel':
                $response = $this->updateParcel($this->param);
                break;
            case $this->requestMethod == 'GET' && $this->method == 'biker-todolist':
                $response = $this->getBikerTodoParcels($this->param);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header(http_response_code());
        if ($response['body']) {
            echo $response['body'];
        }
    }

     private function getBikerTodoParcels($biker_id) {

        $jwtData = (new Authenticate)->authenticate();
        $id = (int) $jwtData->id;
        if($id != $biker_id) {
            http_response_code(401);
            $response['body'] = json_encode(['message' => 'UnAuthorized']);
            return $response;
        }
        $result = $this->parcelGateWay->findAllBikerToDoParcels($biker_id);
        http_response_code(200);
        $response['body'] = json_encode($result);
        return $response;
    }

    private function updateParcel($parcel_id) {
        $jwtData = (new Authenticate)->authenticate();
        $id = (int) $jwtData->id;

        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        $validator = (new ParcelValidator)->validateUpdateParcel($input);
        if ($validator) {
            http_response_code(500);
            $response['body'] = json_encode($validator);
            return $response;
        }
        
        $input['user_id'] = $id;
        if(isset($input['status'])) {
            $result = $this->parcelGateWay->updateStatus($parcel_id, $input);
        } else {
            $result = $this->parcelGateWay->pickupAndUpdateStatus($parcel_id, $input);
        }  

        http_response_code(200);
        $response['body'] = json_encode(['message' => 'Updated', 'result' => $result]);
        return $response;
    }

    private function notFoundResponse()
    {
        http_response_code(404);
        $response['body'] = null;
        return $response;
    }
}