<?php

${basename(__FILE__,'.php')} = function(){
    if($this->get_request_method() == "POST" and isset($this->_request['username']) and isset($this->_request['password'])){
        $username = $this->_request['username'];
        $password = $this->_request['password'];   
        try{
            $auth = new Auth($username,$password);
            $data = [
                "message" => "Login Success",
                "tokens" => $auth->getAuthTokens(),
            ];
            $data = $this->json($data);
            $this->response($data, 200);
        }catch (Exception $e) {
            $data = [
                "error" => $e->getMessage(),
            ];
            $data = $this->json($data);
            $this->response($data, 406);
        }
    }else{
        $data = [
            "error" => "Bad request"
        ];
        $data = $this->json($data);
        $this->response($data, 400);
    }
};