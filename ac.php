<pre>
<?php 

require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT']."/api/lib/Auth.class.php");

try {
    $username = 'balumahesh';
    $password = 'obmstudio';

    $auth = new Auth($username,$password);
    echo $auth;

} catch (Exception $e) {
    echo $e->getMessage();
}


?>
</pre>