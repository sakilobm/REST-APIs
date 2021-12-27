<?php
${basename(__FILE__,'.php')} = function(){
    //Todo : Last Changes Not Checked
    if($this->get_request_method() == "POST" and $this->isAuthenticated() ){ 
        try{
            $data = [
                "username" => $this->getUsername()
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