<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['id'])) {
        $f = new Folder($this->_request['id']);
        $data = [
            'Count' => $f->countNotes(),
            'Notes' => $f->getAllNotes()
        ];
        $data =$this->json($data);
        $this->response(($data), 200);
    } else {
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};
