<?php

${basename(__FILE__,'.php')} = function(){
    if($this->isAuthenticated()){
        $data = [
            "error" => "Already logged in"
        ];
        $data = $this->json($data);
        $this->response($data, 400);
    }
    if($this->get_request_method() == "POST" and isset($this->_request['username']) and isset($this->_request['password'])){
        $username = $this->_request['username'];
        $password = $this->_request['password'];
        
        try{
            $auth = new Auth($username,$password,); 
            $data = [
                "message" => "Login success",
                "tokens" => $auth->getAuthTokens(),
            ];
            $this->response($this->json($data), 200);

        }catch(Exception $e){
            $data = [
                "error" => $e->getMessage()
            ];
            $this->response($this->json($data), 409);
        }
    }else{
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};