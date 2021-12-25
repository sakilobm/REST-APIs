<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
// $username = 'completed',$password = 'successfully'
        try {
            $auth = new Auth($token = 'a.93f98d0566d4b5fafe2129257a6adbb197990cc466220e4db45fffec7e8a507d');                                                                                                   
            print_r("Username: ".$auth->getUsername());
        }catch(Exception $e){
            echo $e->getMessage();
        }

?>