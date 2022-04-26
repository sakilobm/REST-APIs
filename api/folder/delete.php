<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request['id'])) {
        $f = new Folder($this->_request['id']);
        if ($f->delete()) {
            $data = [
                'Message' => "Success"
            ];
            $data = $this->response($this->json($data), 200);
        } else {
            $data = [
                'Message' => "Error"
            ];
            $data = $this->response($this->json($data), 400);
        }
    } else {
        $data = [
            "error" => "Bad request"
        ];
        $this->response($this->json($data), 400);
    }
};
