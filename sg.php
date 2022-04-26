<pre>
<?php
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/User.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Folder.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Notes.class.php');

session_start();
$_SESSION['username'] = "Sakil2002";

try {
    // print_r(Folder::getAllFolders());
    $f = new Folder(2);
    echo $f->getName();
    echo $f->rename("");
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
</pre>