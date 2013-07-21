<?php

class database {

    private $con = array();

    function database()
    {
        global $main, $system;
        $server_count = sizeof($main['server']);
        for($i=1;$i<=$server_count;$i++) {
            try {
                $this->con['login'][$i] = @new PDO("mysql:host={$main['server'][$i]['login']['db_host']};dbname={$main['server'][$i]['login']['db_name']}", $main['server'][$i]['login']['db_user'], $main['server'][$i]['login']['db_pass']);
                // отключаем эмуляцию, т.к. мы не фильтруем INPUT данные, ведь это умеет PDO
                // ставим жесткий указатель на UTF8
                $this->con['login'][$i]->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->con['login'][$i]->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
                $this->con['login'][$i]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->con['game'][$i] = @new PDO("mysql:host={$main['server'][$i]['game']['db_host']};dbname={$main['server'][$i]['game']['db_name']}", $main['server'][$i]['game']['db_user'], $main['server'][$i]['game']['db_pass']);
                // отключаем эмуляцию, т.к. мы не фильтруем INPUT данные, ведь это умеет PDO
                // ставим жесткий указатель на UTF8
                $this->con['game'][$i]->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->con['game'][$i]->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
                $this->con['game'][$i]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                $system->printout('ERROR_DB');
            }
        }
    }

    public function con($type, $id) {
        return $this->con[$type][$id];
    }

    function __destruct(){
        for($i=1;$i<=sizeof($this->con);$i++) {
            $this->con['login'][$i] = null;
            $this->con['game'][$i] = null;
        }
    }

}
?>