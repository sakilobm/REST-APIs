<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/OAuth.class.php");    
    try {
        $oauth = new OAuth($refresh_token = 'r.35d3af29440b74b97d9c7b0fa9287f26a1705024b36512903a9d0a08bf4afc');                                                                                                   
        print_r($oauth->refreshAccess());
    }catch(Exception $e){
        echo $e->getMessage();
    }
?>