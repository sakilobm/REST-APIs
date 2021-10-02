<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once($_SERVER['DOCUMENT_ROOT']."/api/REST.api.php");
    require_once ($_SERVER['DOCUMENT_ROOT']."/api/lib/Database.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Signup.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");
    require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/User.class.php");

    class API extends REST {

        public $data = "";
        
        private $db = NULL;

        public function __construct(){
            parent::__construct();                    // Init parent contructor
            $this-> db = Database::getConnection();  // Initiate Database connection
        }
        /*
         * Public method for access api.
         * This method dynmically call the method based on the query string
         *
         */
        public function processApi(){
            $func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
            if((int)method_exists($this,$func) > 0)
                $this->$func();
            else
                $this->response('',400);                // If the method not exist with in this class, response would be "Page not found".
        } 
        function generate_hash(){
            $bytes = random_bytes(16);
            return bin2hex($bytes);
        }

        /*************API SPACE START*******************/
        
        private function about(){

            if($this->get_request_method() != "POST"){
                $error = array('status' => 'WRONG_CALL', "msg" => "The type of call cannot be accepted by our servers.");
                $error = $this->json($error);
                $this->response($error,406);
            }
            $data = array('version' => '0.1', 'desc' => 'This API is created by Blovia Technologies Pvt. Ltd., for the public usage for accessing data about vehicles.');
            $data = $this->json($data);
            $this->response($data,200);
   
        }
        private function test(){
                $data = $this->json(getallheaders());
                $this->response($data,200);
        } 
        // Genarate password to hash
        private function gen_hash(){
            $st = microtime(true);
            if(isset($this->_request['pass'])){
                $cost = (int)$this->_request['cost'];
                // $s = new Signup("" ,$this->_request['pass'], "");  
                // $hash = $s->hashPassword($cost);
                $hash = password_hash($this->_request['pass'],PASSWORD_BCRYPT); 
                $data =[
                    "hash" => $hash, 
                    "info" => password_get_info($hash),
                    "val" => $this->_request['pass'],
                    "verified" => password_verify($this->_request['pass'], $hash),
                    "time_in_ms" => microtime(true) - $st ,
                
                ];
                $data = $this->json($data);
                $this->response($data,200);
            }
        }
             
           private function verify_hash(){
            if(isset($this->_request['pass']) and isset($this->_request['hash'])){
                $hash = $this->_request['hash'];
                $data =[
                    "hash" => $hash,
                    "info" => password_get_info($hash),
                    "val" => $this->_request['pass'],
                    "verified" => password_verify($this->_request['pass'], $hash)

                ];
                $data = $this->json($data);
                $this->response($data,200);
                }
        }
        private function signup(){
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
                    // "method" => $this->get_request_method(),
                    
                    
                ];
                $data = $this->json($data);
                $this->response($data, 400);
            }
        }
        public function __call($method, $args)
        {
            $methods = get_class_methods('API');
            foreach($methods as $m){
                    if($m == $methods){
                        return $this->$m();
                    }
            }
        if(isset($_GET['namespace'])){
        $dir = __DIR__.'/api/apis'.$_GET['namespace'];
        $methods = scandir($dir);
        foreach($methods as $m){
            if($m =="." or $m == ".."){
                continue;
            }
            $basem = basename($m, '.php');
            echo "trying to call $basem() for $method()\n";
            if($basem == $method){
                include $dir."/".$m;
                $func = Closure::bind(${$basem}, $thi, get_class());
                if(is_callable($func)){
                    return call_user_func_array($func,$args);
                }else{
                    $this->response($this->json(['error'=>'method_not_callable']),404);
                }
            }
        }
        }else{
            $this->response($this->json(['error'=>'method_not_found']),404);
        }
        }
        /*************API SPACE END*********************/

        /*
            Encode array into JSON
        */
        
        private function json($data){
            if(is_array($data)){
                return json_encode($data, JSON_PRETTY_PRINT);
            } else {
                return "{}";
            }
        }

    }

    // Initiiate Library

    $api = new API;
    $api->processApi();