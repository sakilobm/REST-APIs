<pre>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/User.class.php');

try{
    $user = new User('sakil');
    echo $user->getEmail();
}catch(Exception $e){
    echo $e->getMessage();
}
?>
</pre>