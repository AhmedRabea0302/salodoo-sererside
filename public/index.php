<?php

require "../bootstrap.php";

use Src\Controllers\BikerController;
use Src\Controllers\ParcelController;
use Src\Controllers\UserController;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("HTTP/1.1 200 OK");


$requestMethod = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Getting Params
$param = null;
if (isset($uri[5])) {
    $param = (int) $uri[5];
}

// Getting URI
# Resource
$resource = null;
if (isset($uri[3])) {
    $resource = $uri[3];
}
# Controller Method
$method = null;
if (isset($uri[4])) {
    $method = $uri[4];
}

// Routing
switch ($resource) {
    case  'user':
        // Pass the request method to the UserController and process the HTTP request:
        $controller = new UserController($dbConnection, $requestMethod, $param, $method);
        $controller->processRequest();
        break;
    case 'parcel' :
        // Pass the request method to the ParcelController and process the HTTP request:
        $controller = new ParcelController($dbConnection, $requestMethod, $param, $method);
        $controller->processRequest();
        break;
    case 'biker' :
        // Pass the request method to the BikerController and process the HTTP request:
        $controller = new BikerController($dbConnection, $requestMethod, $param, $method);
        $controller->processRequest();      
        break;
    default:
        http_response_code(404);
        break;
}