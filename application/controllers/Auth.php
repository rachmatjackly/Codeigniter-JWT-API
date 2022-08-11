<?php

use chriskacerguis\RestServer\RestController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends RestController
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function configToken()
    {
        $cnf['exp'] = time() + 60; //milisecond
        $cnf['secretkey'] = '2212336221';
        return $cnf;
    }

    public function authtoken()
    {
        $secret_key = $this->configToken()['secretkey'];
        $token = null;
        $authHeader = $this->input->request_headers()['Authorization'];
        $arr = explode(" ", $authHeader);
        $token = $arr[1];
        // echo json_encode(JWT::decode($token, new Key($this->configToken()['secretkey'], 'HS256')));die;
        if ($token) {
            try {
                $decoded = JWT::decode($token, new Key($this->configToken()['secretkey'], 'HS256'));
                if ($decoded) {
                    return 'benar';
                }
            } catch (\Exception $e) {
                $result = array('pesan' => 'Kode Signature Tidak Sesuai');
                return 'salah';

            }
            die;
        }
    }

    public function getToken_post()
    {
        $exp = $this->configToken()['exp'];

        $token = array(
            "iss" => 'apprestservice',
            "aud" => 'pengguna',
            "iat" => time(),
            "nbf" => time() + 10,
            "exp" => $exp,
            "data" => array(
                "username" => $this->post('username'),
                "password" => $this->post('password'),
            ),
        );

        $jwt = JWT::encode($token, $this->configToken()['secretkey'], 'HS256');
        $output = [
            'status' => 200,
            'message' => 'Berhasil login',
            "token" => $jwt,
            "expireAt" => date("Y-m-d H:i:s", $token['exp']),
        ];
        $data = $output;
        $this->response($data, 200);
    }
    
}
