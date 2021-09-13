<pre>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/User.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Folder.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Notes.class.php');

session_start();
$_SESSION['username'] = 'sibi1995';

try{
    print_r(Folder::getAllFolders());

} catch(Exception $e){
    echo $e->getMessage();
}



?>
</pre>