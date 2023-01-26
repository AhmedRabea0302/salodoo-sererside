<?php
declare(strict_types=1);
namespace Src\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authenticate {
    public function generateToken($id, $email) {
        $issuer = getenv('SERVER_NAME');
        $issuedAt = time();
        $nbf = $issuedAt;
        $exp = $issuedAt + 9500;
        $aud = 'myusers';
        $data = array(
            'id' => $id,
            'email' => $email
        );
        $secrect_key = getenv('JWT_SECRET_KEY');
        $payload = [
            'iat'  => $issuedAt,
            'iss'  => $issuer,
            'nbf'  => $nbf,
            'exp'  => $exp,
            'aud ' => $aud,
            'data' => $data
        ];

        return JWT::encode(
            $payload,
            $secrect_key,
            'HS256'
        );
    }

    public function authenticate() {
        switch(true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER) :
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER) :
                $authHeader = $_SERVER['Authorization'];
                break;
            case array_key_exists('HTTP_X_AUTH_TOKEN', $_SERVER) :
                $authHeader = $_SERVER['HTTP_X_AUTH_TOKEN'];
                break;
            default :
                $authHeader = null;
                break;
        }
        if($authHeader != null) {
            try {
                if(str_starts_with($authHeader, 'Bearer ')) { 
                    $token = explode(' ', $authHeader)[1];
                    $jwtVerifier = JWT::decode($token, new key(getenv('JWT_SECRET_KEY'), 'HS256'));
                    return $jwtVerifier->data;
                }
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }
            throw new \Exception('Invalid authorization header');
        } else {
            throw new \Exception('No Bearer Token');
        }
        
    }
}