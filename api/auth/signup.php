<?php

${basename(__FILE__,'.php')} = function(){
    if($this->get_request_method() == "POST" and isset($this->_request['username']) and isset($this->_request['password']) and isset($this->_request['email'])){
        $username = $this->_request['username'];
        $password = $this->_request['password'];
        $email = $this->_request['email'];
        
        try{
            $s = new Signup($username, $password, $email);
            $data = [
                "message" => "Signup success",
                "userid" => $s->getInsertID()
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