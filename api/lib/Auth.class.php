<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/User.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/OAuth.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/auth/login.php');
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

    class Auth {
        private $db;
        private $isTokenAuth = false;
        private $loginTokens = null;
        
        public function __construct($username,$password = NULL){
            $this->db = Database::getConnection();
            if($password==NULL){
                //token based auth
                $this->token = $username;
                $this->isTokenAuth = true;    //we have to validate the token
            }else{
                //password based auth
                $this->username = $username; //might be email or username.
                $this->password = $password;
            }
            if($this->isTokenAuth){
                throw new Exception("Not Implemented");
            }else{
                $user = new User($this->username);
                $hash = $user->getPasswordHash(); 
                $this->username = $user->getUsername(); 
                if(password_verify($this->password,$hash)){
                   if(!$user->isActive()){
                    throw new Exception("Please check your email and activate your account");
                   }
                   $this->loginTokens = $this->addSession();
                }else{
                    throw new Exception("Password Mismatch"); 
                }   
            }
        }
        public function getAuthTokens(){
            return $this->loginTokens;  
        }

        private function addSession(){
            $oauth = new OAuth();
            $oauth->setUsername($this->username);
            $session = $oauth->newSession();
            return $session; 
            
        }
        public static function generateRandomHash($len){
            $bytes = openssl_random_pseudo_bytes($len, $cstrong);
            return bin2hex($bytes);
        }
    }