<?php

class Share{
    public function __construct($id,$type){
        if($type == "note" or $type == "folder"){

        }else{
            throw new Exception("Unknown share type");
        }
    }

    public function shareWith($username){

    }
    public function revoke($username){

    }
    public function hasAccess($username){

    }
}