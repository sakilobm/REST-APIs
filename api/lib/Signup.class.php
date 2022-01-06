<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Folders.class.php');
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
class Signup
{
    private $username;
    private $password;
    private $email;

    private $db;

    public function __construct($username, $password, $email)
    {
        $this->db = database::getConnection();
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        if ($this->userExists()) {
            throw new Exception("User already exists");
        }
       
        $bytes = random_bytes(16);
        $this->token = $token =  bin2hex($bytes); //to verify users over email
        $password = $this->hashPassword();

        //Make a proper flow to throw already exist
        $query = "INSERT INTO tables (username, password, email, active, token) VALUES ('$username', '$password', '$email', '0', '$token');";
        if (!mysqli_query($this->db, $query)) {
            throw new Exception("Unable to signup, user account might already exists");
        }
        if ($this->userExists()) {
            throw new Exception("User already exists");
        } else {
            $this->id = mysqli_insert_id($this->db);
            // $this->sendVerificationMail();
        }
    }
    /*
    * .........................SendGrid...............................
    */
    public function sendVerificationMail()
    {
        $config_json = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/../env.json');
        $config = json_decode($config_json, true);
        $token = $this->token;
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@obm", "OBM Creators Support");
        $email->setSubject("Verify Your Account");
        $email->addTo($this->email, $this->username);
        $email->addContent("text/plain", "Please Verify Your Account at:");
        $email->addContent(
            "text/html",
            "<strong>Please Verify Your Account by <a href= </strong>"
        );
        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        try {
            $response = $sendgrid->send($email);
            // print $response->statusCode() . "\n";
            // print_r($response->headers());
            // print $response->body() . "\n";
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
    public function getInsertID()
    {
        return $this->id;
    }
    public function userExists()
    {
        
        //TODO: write the code to check if user exists. query eluthanum
        return false;
    }
    public function hashPassword($cost = 10)
    {
        $options = [
            "cost" => $cost
        ];
        return password_hash($this->password, PASSWORD_BCRYPT);
    }
    public static function verifyAccount($token)
    {
        $query = "SELECT * FROM `tables` WHERE `token`='$token';";
        $db= Database::getConnection();
        $result=mysqli_query($db, $query);
        if ($result and mysqli_num_rows($result)==1) {
            $data = mysqli_fetch_assoc($result);
            if ($data['active']==1) {
                throw new Exception("Already Verified");
            }
            mysqli_query($db, "UPDATE `tables` SET `active` = `1` WHERE (`token` = `$token`);");
            return true;
        } else {
            return false;
        }
    }
}
