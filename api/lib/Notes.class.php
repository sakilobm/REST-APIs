<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Database.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/api/lib/Share.class.php');
require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
use Carbon\Carbon;

class Notes extends Share
{
    public function __construct($id=null)
    {
        parent::__construct($id, 'note');
        $this->db = Database::getConnection();
        if ($id != null) {
            $this->id = $id;
            $this->refresh();
        }
    }
    public function refresh()
    {
        if ($this->id != null) {
            $query = "SELECT * FROM notes WHERE id=$this->id;";
            $result = mysqli_query($this->db, $query);
            if ($result) {
                $this->data = mysqli_fetch_assoc($result);
                $this->id = $this->data['id'];
            } else {
                throw new Exception("Note not found");
            }
        }
    }
    public function getOwner()
    {
        if ($this->data and isset($this->data['owner'])) {
            return $this->data['owner'];
        }
    }
    public function getBody()
    {
        if ($this->data and isset($this->data['body'])) {
            return $this->data['body'];
        }
    }
    public function getFolderId()
    {
        if ($this->data and isset($this->data['folder_id'])) {
            return $this->data['folder_id'];
        }
    }
    public function getTitle()
    {
        if ($this->data and isset($this->data['title'])) {
            return $this->data['title'];
        }
    }
    public function createdAt()
    {
        if ($this->data and isset($this->data['created_at'])) {
            $c = new Carbon($this->data['created_at'], date_default_timezone_get());
            return $c->diffForHumans() ;
        }
    }
    public function setBody($body)
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "UPDATE `notes` SET `body` = '$body' WHERE `notes`.`id` = $this->id;";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Folder Note Loaded");
            }
        } else {
            throw new Exception("unathorized");
        }
    }
    public function setTitle($title)
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "UPDATE `notes` SET `title` = $title WHERE `notes`.`id` = $this->id;";
                $result = mysqli_query($this->db, $query);
                $this->refresh();
                return $result;
            } else {
                throw new Exception("Notes Note Loaded");
            }
        } else {
            throw new Exception("unathorized");
        }
    }
    public function delete()
    {
        if (isset($_SESSION['username']) and $this->getOwner() == $_SESSION['username']) {
            if ($this->id) {
                $query = "DELETE FROM `notes` WHERE `notes`.`id` = $this->id;";
                $result = mysqli_query($this->db, $query);
                return $result;
            } else {
                throw new Exception("Notes Note Loaded ");
            }
        } else {
            throw new Exception("unathorized 1 ");
        }
    }

    public function createNew($title, $body, $folderId)
    {
        new Folder($folderId);
        if (isset($_SESSION['username']) and strlen($title) <= 45) {
            $query = "INSERT INTO `notes` (`title`, `body`, `owner`, `folder_id`) VALUES ( '$title', '$body', '$_SESSION[username]', '$folderId');";
            if (mysqli_query($this->db, $query)) {
                $this->id = mysqli_insert_id($this->db);
                $this->refresh();
                return $this->id;
            }
        } else {
            throw new Exception("Can't Create Note");
        }
    }
}
