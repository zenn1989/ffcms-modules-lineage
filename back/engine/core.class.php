<?php

class core {

    private $socket_id = 1;

    function core() {
        global $system, $main;
        if($system->get('key') != $main['secret_api_key'])
            $this->printout("ERROR_API");
    }

    public function compile() {
        global $system;
        $this->socket_id = (int)$system->post('id');
        $result = array();
        if(is_array($system->post('action'))) {
            foreach($system->post('action') as $current_acton) {
                list($target, $action) = explode('.', $current_acton);
                if($target == "login") {
                    $result[$current_acton] = $this->loginAction($action);
                } elseif($target == "game") {
                    $result[$current_acton] = $this->gameAction($action);
                }
            }
        } else {
            return "error";
        }
        return serialize($result);
    }

    private function loginAction($action)
    {
        global $system, $database, $main;
        if($database->con('login', $this->socket_id) == null)
            return $this->printout('ERROR_SID');
        switch($action) {
            case "status":
                return $this->showPortStatus($main['server'][$this->socket_id]['login']['ip'], $main['server'][$this->socket_id]['login']['port']);
                break;
            case "register":
                return $this->doRegisterAccount();
                break;
            case "accounts":
                return $this->showAccountsCount();
                break;
            case "change_password":
                return $this->showChangePassword();
                break;
        }
        return "login.unknown";
    }

    private function gameAction($action)
    {
        global $database, $main;
        if($database->con('game', $this->socket_id) == null)
            return $this->printout('ERROR_SID');
        switch($action) {
            case "status":
                return $this->showPortStatus($main['server'][$this->socket_id]['game']['ip'], $main['server'][$this->socket_id]['game']['port']);
                break;
            case "online":
                return $this->gameOnline();
                break;
            case "characters":
                return $this->showCharactersCount();
                break;
            case "pvptop":
                return $this->showPvPTop();
                break;
            case "pktop":
                return $this->showPkTop();
                break;
            case "clantop":
                return $this->showClanTop();
                break;
            case "character_exist":
                return $this->isCharacterExist();
                break;
            case "takeitem":
                return $this->takeItem();
                break;
        }
        return "game.unknown";
    }

    private function showChangePassword()
    {
        global $system, $database, $main;
        $post_fields = $system->post('fields');
        $login = $post_fields['login'];
        $email = $post_fields['email'];
        $oldpwd = $post_fields['oldpwd'];
        $hashOldPwd = base64_encode(pack('H*', sha1($oldpwd)));
        $newpwd = $post_fields['newpwd'];
        $hashNewPwd = base64_encode(pack('H*', sha1($newpwd)));
        $stmt = $database->con('login', $this->socket_id)->prepare($main['query']['login']['check_change_password']);
        $stmt->bindParam(1, $login, PDO::PARAM_STR);
        $stmt->bindParam(2, $hashOldPwd, PDO::PARAM_STR);
        $stmt->bindParam(3, $email, PDO::PARAM_STR);
        $stmt->execute();
        $resCount = $stmt->fetch();
        logging::log("Trying to change password to $login:$email");
        if($resCount[0] == 1) {
            $stmt = null;
            $stmt = $database->con('login', $this->socket_id)->prepare($main['query']['login']['set_change_password']);
            $stmt->bindParam(1, $hashNewPwd, PDO::PARAM_STR);
            $stmt->bindParam(2, $login, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->execute();
            logging::log("Password for $login:$email changed success returning true");
            return $this->resultTrue();
        }
        return $this->resultFalse();
    }

    private function takeItem()
    {
        global $database, $main, $system;
        $post_fields = $system->post('fields');
        $char_name = $post_fields['char_name'];
        $item_id = $post_fields['item_id'];
        $item_count = $post_fields['count'];
        logging::log('Taking item('.$item_id.':'.$item_count.') to '.$char_name);
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['takeitem']);
        $stmt->bindParam(1, $char_name, PDO::PARAM_STR);
        $stmt->bindParam(2, $item_id, PDO::PARAM_INT);
        $stmt->bindParam(3, $item_count, PDO::PARAM_INT);
        $stmt->execute();
        return $this->resultNull();
    }

    private function isCharacterExist()
    {
        global $database, $system, $main;
        $post_fields = $system->post('fields');
        $char_name = $post_fields['char_name'];
        logging::log('Checking character exist with name '.$char_name);
        if(strlen($char_name) > 0) {
            $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['character_exist']);
            $stmt->bindParam(1, $char_name, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result[0] == 1 ? $this->resultTrue() : $this->resultFalse();
        }
        return $this->resultFalse();
    }

    private function showClanTop()
    {
        global $database, $main;
        logging::log("Returning clan statistic");
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['topclan']);
        $stmt->execute();
        $output = null;
        while($result = $stmt->fetch()) {
            $output[] = array($result['clan_name'], $result['clan_level'], $result['reputation_score'], $result['char_name'], $result['clan_size']);
        }
        return $output;
    }

    private function showPkTop()
    {
        global $database, $main;
        logging::log("Returning Pk statistic");
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['toppk']);
        $stmt->execute();
        $output = array();
        while($result = $stmt->fetch()) {
            $output[] = array($result['char_name'], $result['level'], $result['pvpkills'], $result['pkkills'], $result['online'], $result['clan_name']);
        }
        return $output;
    }

    private function showPvPTop()
    {
        global $database, $main;
        logging::log("Returning PVP statistic");
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['toppvp']);
        $stmt->execute();
        $output = array();
        while($result = $stmt->fetch()) {
            $output[] = array($result['char_name'], $result['level'], $result['pvpkills'], $result['pkkills'], $result['online'], $result['clan_name']);
        }
        return $output;
    }

    private function showAccountsCount()
    {
        global $database, $main;
        logging::log("Returning account count");
        $stmt = $database->con('login', $this->socket_id)->prepare($main['query']['login']['accounts']);
        $stmt->execute();
        $result = $stmt->fetch();
        return (($result[0]+$main['server'][$this->socket_id]['game']['online_add'])*$main['server'][$this->socket_id]['game']['online_mul']);
    }

    private function showCharactersCount()
    {
        global $database, $main;
        logging::log("Returning characters count");
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['characters']);
        $stmt->execute();
        $result = $stmt->fetch();
        return (($result[0]+$main['server'][$this->socket_id]['game']['online_add'])*$main['server'][$this->socket_id]['game']['online_mul']);
    }

    private function doRegisterAccount()
    {
        global $database, $system, $main;
        $post_fields = $system->post('fields');
        $login = $post_fields['login'];
        $pass = $post_fields['pass'];
        $hashPass = base64_encode(pack('H*', sha1($pass)));
        $email = $post_fields['email'];
        logging::log("Try to registering account with login: ".$login);
        $stmt = null;
        $stmt = $database->con('login', $this->socket_id)->prepare("SELECT COUNT(*) FROM accounts WHERE login = ?");
        $stmt->bindParam(1, $login, PDO::PARAM_STR);
        $stmt->execute();
        $resultRows = $stmt->fetch();
        if($resultRows[0] < 1) {
            $stmt = null;
            $stmt = $database->con('login', $this->socket_id)->prepare($main['query']['login']['register']);
            $stmt->bindParam(1, $login, PDO::PARAM_STR);
            $stmt->bindParam(2, $hashPass, PDO::PARAM_STR);
            $stmt->bindParam(3, $email, PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
            logging::log("Registering for {$login} is sucessful");
            return $this->resultTrue();
        }
        logging::log('Registering for {$login} is failed');
        return $this->resultFalse();
    }

    private function showPortStatus($ip, $port)
    {
        $status = null;
        logging::log("Returning ip:port status for {$ip}:{$port}");
        $fp = @fsockopen($ip, $port, $errno, $errstr, 1);
        if($fp)
            $status = $this->resultTrue();
        else
            $status = $this->resultFalse();
        $fp ? fclose($fp) : null;
        return $status;
    }

    private function gameOnline()
    {
        global $database, $main;
        logging::log("Returning game online");
        $stmt = $database->con('game', $this->socket_id)->prepare($main['query']['game']['online']);
        $stmt->execute();
        $res = $stmt->fetch();
        return (($res[0]+$main['server'][$this->socket_id]['game']['online_add'])*$main['server'][$this->socket_id]['game']['online_mul']);
    }


    public function printout($data) {
        die($data);
    }

    private function resultTrue()
    {
        return "TRUE";
    }

    private function resultFalse()
    {
        return "FALSE";
    }

    private function resultNull()
    {
        return "NULL";
    }

}
?>