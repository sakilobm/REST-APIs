<?php

${basename(__FILE__,'.php')} = function(){
    if($this->get_request_method() == "POST" and isset($_SESSION['username']) ){ 
        try{
            $data = [
                "username" => $_SESSION['username']
            ];
            $this->response($this->json($data), 200);

        }catch(Exception $e){
            $data = [
                "error" => $e->getMessage()
            ];
            $this->response($this->json($data), 403);
        }
    }else{
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};