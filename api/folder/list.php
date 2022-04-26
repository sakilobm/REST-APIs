<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->isAuthenticated()) {
        $data = Folder::getAllFolders();
        $data = $this->response($this->json($data), 200);
    } else {
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};
