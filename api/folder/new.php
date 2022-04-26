<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['name'])) {
        $f = new Folder();
        $id = $f->createNew($this->_request['name']);
        $data = [
            'folder_id' => $id
        ];
        $data = $this->response($this->json($data), 200);
    } else {
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};
