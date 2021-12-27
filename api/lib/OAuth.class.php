<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/User.class.php');

class OAuth{
    private $db;
    private $refresh_token = null;
    private $access_token = null;
    private $valid_for = 7200;
    private $username;
    private $user;
    /**
     * Can construct without refresh_token for new session
     * can construct with refresh_token for refresh session 
     */

    public function __construct($token = NULL){
        $this->db = Database::getConnection();
        if($token != NULL){
            if($this->startWith($token, 'a.')){
                $this->access_token = $token;
            } else if($this->startWith($token, 'r.')){
                $this->refresh_token = $token;
            }else {
                $this->setUsername($token);
            }
        }
    } 
    public function setUsername($username){ 
        $this->username = $username;
        $this->user = new User($this->username);
    }
    public function getUsername(){
        return $this->username;
    }

    public function authenticate(){
        if($this->access_token != NULL);
        $query = "SELECT * FROM session WHERE access_token = '$this->access_token';";
        $result = mysqli_query($this->db,$query);
        if($result){
            $data = mysqli_fetch_assoc($result);
            $created_at = strtotime($data['created_at']);
            $expires_at = $created_at + $data['valid_for'];

            if(time() <= $expires_at ){
                if(session_status() == PHP_SESSION_NONE){
                    session_start();
                }
                $this->username = $_SESSION['username'] = $data['username'];
                $_SESSION['token'] = $this->access_token;
                return true;
            }else{
                throw new Exception("Expired token");
            } 

        }else{
            throw new Exception(mysqli_error($this->db));
        }
    }

    public function newSession($valid_for = 7200, $reference_token = 'auth_grant'){
        if($this->username == NULL){
            throw new Exception("Username not set for OAuth");
        }
        $this->valid_for = $valid_for;
        $this->access_token = 'a.'.Auth::generateRandomHash(32);
        if($reference_token  == 'auth_grant'){
            $this->refresh_token =  'r.'.Auth::generateRandomHash(32);
        }else{
            $this->refresh_token = 'd.'.Auth::generateRandomHash(16);
        }
        $query = "INSERT INTO session (username, access_token, refresh_token, valid_for, reference_token) VALUES ('$this->username', '$this->access_token', '$this->refresh_token', '$this->valid_for', '$reference_token');";

        if(mysqli_query($this->db,$query)){
            return array(
                "access_token" => $this->access_token,
                "valid_for" => $this->valid_for,
                "refresh_token" => $this->refresh_token,
                "reference_token" => $reference_token,
                "type" => 'api'  
            );
        }else{
            throw new Exception(mysqli_error(($this->db)));
        }
    }

    public function refreshAccess(){
        if($this->refresh_token != NULL and !$this->startWith($this->refresh_token,'d.')){
            $query = "SELECT * FROM session WHERE refresh_token = '$this->refresh_token';";
            // echo $query;
            $result = mysqli_query($this->db,$query);
            if($result){ 
                $data = mysqli_fetch_assoc($result);
                // print_r($data);
                $this->username = $data['username'];
                if($data['valid'] == 1){
                    return $this->newSession(7200, $this->refresh_token);
                }else{
                    throw new Exception("Expired token");
                }
            }else{
                throw new Exception("Error: "+mysqli_error($this->db));
            }  
        }else{
            throw new Exception("Invalid request");
        }

    }
    private function startWith ($string, $startString){
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}
