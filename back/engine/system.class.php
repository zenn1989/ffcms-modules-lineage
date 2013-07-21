<?php

class system {
    public $get;
    public $post;

    function system() {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    public function get($var) {
        return $this->get[$var];
    }

    public function post($var) {
        return $this->post[$var];
    }

    public static function printout($message) {
        die($message);
    }
}
?>