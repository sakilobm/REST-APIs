<?php

${basename(__FILE__, '.php')} = function () {
    if ($this->get_request_method() == "POST" and $this->isAuthenticated() and isset($this->_request[''])) {
    } else {
        $data = [
            "error" => "Bad request",
        ];
        $this->response($this->json($data), 400);
    }
};
