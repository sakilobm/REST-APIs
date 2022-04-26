<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/api/lib/Database.class.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Share.class.php');
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
use Carbon\Carbon;

class Folder extends Share
{
    private $db;
    private $data = null;
    private $id = null;
    public function __construct($id=null)
    {
        parent::__construct($id, 'folder');
        $this->db = Database::getConnection();
        if ($id != null) {
            $this->id = $id;
            $this->refresh();
        }
    }
    public function refresh()
    {
        if ($this->id != null) {
            $query = "SELECT * FROM folders WHERE id=$this->id;";
            $result = mysqli_query($this->db, $query);
            if ($result) {
                $this->data = mysqli_fetch_assoc($result);
                $this->id = $this->data['id'];
            } else {
                throw new Exception("Note not found ".mysqli_error($this->db));
            }
        }
    }

    public function getName()
    {
        if ($this->data and isset($this->data['name'])) {
            return $this->data['name'];
        }
    }
    public function getId()
    {
        if ($this->id) {
            return $this->id;
        }
    }
    public function createdAt()
    {
        if ($this->data and isset($this->data['created_at'])) {
            return $this->data['created_at'];
        }
    }
    public function getAllNotes()
    {
        $query = "SELECT * FROM notes WHERE folder_id =$this->id;";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            for ($i=0; $i<count($data); $i++) {
                $c_at = $data[$i]['created_at'];
                $u_at = $data[$i]['updated_at'];
                $c_c = new Carbon($c_at);
                $u_c = new Carbon($u_at);
                $data[$i]['created'] = $c_c->diffForHumans();
                $data[$i]['updated'] = $u_c->diffForHumans();
            }
            return $data;
        } else {
            return [];
        }
    }
    public function countNotes()
    {
        $query = "SELECT COUNT(*) FROM notes WHERE folder_id = '$this->id';";
        $result = mysqli_query($this->db, $query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            return $data['COUNT(*)'];
        }
    }
    public static function getAllFolders()
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM folders WHERE owner='$_SESSION[username]';";
        $result = mysqli_query($db, $query);
        if ($result) {
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            for ($i=0; $i<count($data); $i++) {
                $date = $data[$i]['created_at'];
                $c = new Carbon($date);
                $data[$i]['created'] = $c->diffForHumans();
            }
            return $data;
        } else {
            return [];
        }
    }
    public function delete()
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            $notes = $this->getAllNotes();
            foreach ($notes as $note) {
                $n = new Notes($note['id']);
                $n->delete();
            }
            if ($this->id) {
                $query = "DELETE FROM `Folders` WHERE `Folders`.`id` = $this->id;";
                $result = mysqli_query($this->db, $query);
                return $result;
            } else {
                throw new Exception("Folders Note Loaded");
            }
        } else {
            throw new Exception("Unable to delete");
        }
    }
    public function getOwner()
    {
        if ($this->data and isset($this->data['owner'])) {
            return $this->data['owner'];
        }
    }

    public function createNew($name = 'New Folder')
    {
        if (isset($_SESSION['username']) and strlen($name) <= 45) {
            $query = "INSERT INTO `folders` (`name`, `owner`) VALUES ('$name', '$_SESSION[username]');";
            if (mysqli_query($this->db, $query)) {
                $this->id = mysqli_insert_id($this->db);
                return $this->id;
            }
        } else {
            throw new Exception("Can't Create Folder");
        }
    }
    public function rename($name)
    {
        if ($this->id) {
            $query = "UPDATE `folders` SET `name` = '$name' WHERE `folders`.`id` = $this->id;";
            $result = mysqli_query($this->db, $query);
            $this->refresh();
            return $result;
        } else {
            throw new Exception("Folder Note Loaded");
        }
    }
}
