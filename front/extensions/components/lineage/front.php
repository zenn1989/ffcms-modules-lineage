<?php
if (!extension::registerPathWay(array('lineage'), 'lineage')) {
    exit("Component lineage2 cannot be registered!");
}
// мы сами управляем элементами кеширования, отказываемся от стандартного.
page::setNoCache('lineage');

class com_lineage_front implements com_front {
    private $server_id = 0;
    private $action = null;

    public function load()
    {
        global $page, $extension, $system, $template;
        $way = $page->getPathway();
        $this->server_id = (int)$way[1];
        $this->action = $way[2];
        $work_body = null;
        $servers_count = $extension->getConfig('count_servers', 'lineage_connector', 'hooks', 'int');
        if($this->server_id <= $servers_count && $this->server_id != 0) {
            if($this->action == "reg.html") {
                $work_body = $this->viewRegistration();
            } elseif($this->action == "stats") {
                $work_body = $this->viewStatistic();
            } elseif($this->action == "donate") {
                $work_body = $this->viewDonate();
            } elseif($this->action == "account") {
                $work_body = $this->viewAccounts();
            }
        }  else {
            $work_body = $this->viewServerSelector($servers_count);
        }
        if($work_body == null)
        {
            $work_body = $template->compile404();
        }
        $page->setContentPosition('body', $work_body);
    }

    private function viewAccounts()
    {
        global $user, $system, $template, $database, $constant, $hook, $page;
        $userid = $user->get('id');
        if($userid < 1) {
            return $template->compile404();
        }
        $way = $page->getPathway();
        if($way[3] != null && $system->suffixEquals($way[3], '.html')) {
            $id = (int)strstr($way[3], '.', true);
            $stmt = $database->con()->prepare("SELECT * FROM {$constant->db['prefix']}_com_lineage_accounts WHERE ownerid = ? AND id = ? AND server_id = ?");
            $stmt->bindParam(1, $userid, PDO::PARAM_INT);
            $stmt->bindParam(2, $id, PDO::PARAM_INT);
            $stmt->bindParam(3, $this->server_id, PDO::PARAM_INT);
            $stmt->execute();
            if($stmt->rowCount() == 1) {
                $notify = null;
                $result = $stmt->fetch();
                $theme = $template->get('account_actions', 'components/lineage/');
                if($system->post('password_change')) {
                    $oldpwd = $system->post('oldpassword');
                    $newpwd = $system->post('password');
                    $repwd = $system->post('repassword');
                    if(strlen($oldpwd) < 6 || strlen($newpwd) < 6) {
                        $notify .= $template->stringNotify('error', 'Поля паролей заполнены не корректно');
                    }
                    if($newpwd != $repwd) {
                        $notify .= $template->stringNotify('error', 'Новый пароль не совпадает с его повторением');
                    }
                    if($notify == null) {
                        $query = array('id' => $this->server_id, 'action' => array('login.change_password'), 'fields' => array('login' => $result['game_login'], 'oldpwd' => $oldpwd, 'newpwd' => $newpwd, 'email' => $user->get('email')));
                        $response = $hook->get('lineage_connector')->query($this->server_id, $query);
                        if($response['login.change_password'] == "TRUE") {
                            $notify = $template->stringNotify('success', 'Ваш пароль успешно изменен');
                        } else {
                            $notify = $template->stringNotify('error', 'Старый пароль указан не корректно, повторите попытку');
                        }
                    }
                }
                $theme = $template->assign(array('lineage_account_name', 'lineage_server_name', 'notify'), array($result['game_login'], $hook->get('lineage_connector')->getName($this->server_id), $notify), $theme);
                return $theme;
            }
            return $template->compile404();
        } else {
            $theme_head = $template->assign('lineage_server_name', $hook->get('lineage_connector')->getName($this->server_id), $template->get('account_head', 'components/lineage/'));
            $theme_body = $template->get('account_body', 'components/lineage/');
            $stmt = $database->con()->prepare("SELECT * FROM {$constant->db['prefix']}_com_lineage_accounts WHERE ownerid = ?");
            $stmt->bindParam(1, $userid, PDO::PARAM_INT);
            $stmt->execute();
            $compiled_body = null;
            $i = 1;
            while($result = $stmt->fetch()) {
                $compiled_body .= $template->assign(array('lineage_number', 'lineage_account', 'lineage_master_id', 'lineage_server_id'), array($i, $result['game_login'], $result['id'], $this->server_id), $theme_body);
                $i++;
            }
            return $template->assign('lineage_account_body', $compiled_body, $theme_head);
        }
    }

    private function viewDonate()
    {
        global $page;
        $way = $page->getPathway();
        switch($way[3]) {
            case "waytopay.html":
                return $this->viewWaytoPay();
                break;
        }
        return;
    }

    private function viewWaytoPay()
    {
        global $template, $system, $rule, $hook, $database, $constant, $extension;
        $notify = null;
        $theme = $template->get('waytopay_donate', 'components/lineage/');
        if($system->post('submit')) {
            $char_name = $system->post('l2_charname');
            $donate_count = (int)$system->post('l2_donate_count');
            if(strlen($char_name) > 0) {
                $response = $hook->get('lineage_connector')->query($this->server_id, array('id' => $this->server_id, 'action' => array('game.character_exist'), 'fields' => array('char_name' => $char_name)));
                if($response['game.character_exist'] == "TRUE" && $donate_count > 0) {
                    $time = time();
                    $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_com_lineage_donate (`server_id`, `char_name`, `count`, `time`) VALUES(?, ?, ?, ?)");
                    $stmt->bindParam(1, $this->server_id, PDO::PARAM_INT);
                    $stmt->bindParam(2, $char_name, PDO::PARAM_STR);
                    $stmt->bindParam(3, $donate_count, PDO::PARAM_INT);
                    $stmt->bindParam(4, $time, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt = null;
                    $stmt = $database->con()->prepare("SELECT * FROM {$constant->db['prefix']}_com_lineage_donate WHERE `server_id` = ? AND `count` = ? AND `time` = ? AND `char_name` = ?");
                    $stmt->bindParam(1, $this->server_id, PDO::PARAM_INT);
                    $stmt->bindParam(2, $donate_count, PDO::PARAM_INT);
                    $stmt->bindParam(3, $time, PDO::PARAM_INT);
                    $stmt->bindParam(4, $char_name, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetch();
                    $transaction_service_id = $extension->getConfig('wp_service_id', 'lineage', 'components', 'int');
                    $transaction_payment_id = $result['id'];
                    $transaction_payment_price = $result['count'] * $extension->getConfig('wp_donate_price', 'lineage', 'components', 'int');
                    $transaction_payment_char = $result['char_name'];
                    if($transaction_payment_id > 0) {
                        $theme = $template->assign(array('lineage_trans_id', 'lineage_trans_price', 'lineage_char_name', 'lineage_wp_id'), array($transaction_payment_id, $transaction_payment_price, $transaction_payment_char, $transaction_service_id), $theme);
                        $rule->add('com.lineage.wp_payment_generated', true);
                    } else {
                        $notify .= $template->stringNotify('error', 'При генерации квитанции произошла ошибка.');
                    }
                } else {
                    $notify .= $template->stringNotify('error', 'Такой персонаж не найден на сервере');
                }
            }
        }
        $theme = $template->assign(array('lineage_server_name', 'notify'), array($hook->get('lineage_connector')->getName($this->server_id), $notify), $theme);
        return $theme;
    }

    private function viewRegistration()
    {
        global $template, $hook, $system, $user, $database, $constant, $language;
        $notify = null;
        if($system->post('submit')) {
            $login = $system->post('login');
            $pass = $system->post('pass');
            $repass = $system->post('repass');
            $email = $user->get('id') > 0 ? $user->get('email') : $system->post('email');
            if(!$system->isLatinOrNumeric($login) || strlen($login) < 3 || strlen($login) > 16) {
                $notify .= $template->stringNotify('error', $language->get('com_lineage_reg_notify_loginbad'));
            }
            if(!$system->isLatinOrNumeric($pass) || strlen($pass) < 6 || strlen($pass) > 16) {
                $notify .= $template->stringNotify('error', $language->get('com_lineage_reg_notify_passbad'));
            }
            if($pass != $repass) {
                $notify .= $template->stringNotify('error', $language->get('com_lineage_reg_notify_passnotequal'));
            }
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $notify .= $template->stringNotify('error', $language->get('com_lineage_reg_notify_emailbad'));
            }
            if($notify == null) {
                $array_data = array('id' => $this->server_id, 'action' => array('login.register'), 'fields' => array('login' => $login, 'pass' => $pass, 'email' => $email));
                $response = $hook->get('lineage_connector')->query($this->server_id, $array_data);
                if($response['login.register'] == "TRUE") {
                    if($user->get('id') > 0) {
                        $ownerid = $user->get('id');
                        $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_com_lineage_accounts (`game_login`, `ownerid`, `server_id`) VALUES (?, ?, ?)");
                        $stmt->bindParam(1, $login, PDO::PARAM_STR);
                        $stmt->bindParam(2, $ownerid, PDO::PARAM_INT);
                        $stmt->bindParam(3, $this->server_id, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                    $notify = $template->stringNotify('success', $language->get('com_lineage_reg_notify_success'));
                } else {
                    $notify = $template->stringNotify('error', $language->get('com_lineage_reg_notify_loginused'));
                }
            }
        }
        $server_name = $hook->get('lineage_connector')->getName($this->server_id);
        $theme = $template->get('register', 'components/lineage/');
        return $template->assign(array('lineage_server_name', 'notify'),
                                array($server_name, $notify),
                                $theme);
    }

    private function viewStatistic()
    {
        global $page;
        $way = $page->getPathway();
        switch($way[3]) {
            case "pvp.html":
                return $this->viewPvPRaiting();
                break;
            case "pk.html":
                return $this->viewPkRaiting();
                break;
            case "clan.html":
                return $this->viewClanRaiting();
                break;
            default:
                return $this->viewMainStatistic();
                break;
        }
    }

    private function viewClanRaiting()
    {
        global $template, $hook, $extension;
        if(!$extension->getConfig('show_clantop', 'lineage', 'components', 'boolean')) {
            return $template->compile404();
        }
        $theme_head = $template->get('top_clan_head', 'components/lineage/');
        $theme_body = $template->get('top_clan_body', 'components/lineage/');
        $rpcquery = array('id' => $this->server_id, 'action' => array('game.clantop'));
        $response = $hook->get('lineage_connector')->query($this->server_id, $rpcquery);
        if($response == null) {
            return $template->get('connection_down', 'components/lineage/');
        }
        $response_array = $response['game.clantop'];
        $compiled_body = null;
        for($i=0;$i<=sizeof($response_array);$i++) {
            if($response_array[$i][0] != null) {
                $lineage_index = $i+1;
                $compiled_body .= $template->assign(array('lineage_clan_name', 'lineage_clan_level', 'lineage_clan_reputation', 'lineage_clan_owner', 'lineage_clan_members', 'lineage_index'),
                array($response_array[$i][0], $response_array[$i][1], $response_array[$i][2], $response_array[$i][3], $response_array[$i][4], $lineage_index),
                $theme_body);
            }
        }
        return $template->assign('lineage_table_clan_body', $compiled_body, $theme_head);
    }

    private function viewPvPRaiting()
    {
        global $template, $hook, $language, $extension;
        if(!$extension->getConfig('show_pvptop', 'lineage', 'components', 'boolean')) {
            return $template->compile404();
        }
        $theme_head = $template->get('top_pvp_head', 'components/lineage/');
        $theme_body = $template->get('top_pvp_body', 'components/lineage/');
        $theme_on = $template->get('player_online', 'components/lineage/');
        $theme_off = $template->get('player_offline', 'components/lineage/');
        $rpcquery = array('id' => $this->server_id, 'action' => array('game.pvptop'));
        $response = $hook->get('lineage_connector')->query($this->server_id, $rpcquery);
        if($response == null) {
            return $template->get('connection_down', 'components/lineage/');
        }
        $response_array = $response['game.pvptop'];
        $compiled_body = null;
        for($i=0;$i<=sizeof($response_array);$i++) {
            if($response_array[$i][0] != null) {
                $lineage_index = $i+1;
                $player_status = $response_array[$i][4] == "1" ? $theme_on : $theme_off;
                $clan_name = $response_array[$i][5] == null ? $language->get('com_lineage_stats_noclan') : $response_array[$i][5];
                $compiled_body .= $template->assign(array('lineage_player_name', 'lineage_player_level', 'lineage_player_pvp', 'lineage_player_pk', 'lineage_player_online', 'lineage_player_clan', 'lineage_index'),
                    array($response_array[$i][0], $response_array[$i][1], $response_array[$i][2], $response_array[$i][3], $player_status, $clan_name, $lineage_index),
                    $theme_body);
            }
        }

        return $template->assign('lineage_body_pvp', $compiled_body, $theme_head);
    }

    private function viewPkRaiting()
    {
        global $template, $hook, $language, $extension;
        if(!$extension->getConfig('show_pktop', 'lineage', 'components', 'boolean')) {
            return $template->compile404();
        }
        $theme_head = $template->get('top_pk_head', 'components/lineage/');
        $theme_body = $template->get('top_pk_body', 'components/lineage/');
        $theme_on = $template->get('player_online', 'components/lineage/');
        $theme_off = $template->get('player_offline', 'components/lineage/');
        $rpcquery = array('id' => $this->server_id, 'action' => array('game.pktop'));
        $response = $hook->get('lineage_connector')->query($this->server_id, $rpcquery);
        if($response == null) {
            return $template->get('connection_down', 'components/lineage/');
        }
        $response_array = $response['game.pktop'];
        $compiled_body = null;
        for($i=0;$i<=sizeof($response_array);$i++) {
            if($response_array[$i][0] != null) {
                $lineage_index = $i+1;
                $player_status = $response_array[$i][4] == "1" ? $theme_on : $theme_off;
                $clan_name = $response_array[$i][5] == null ? $language->get('com_lineage_stats_noclan') : $response_array[$i][5];
                $compiled_body .= $template->assign(array('lineage_player_name', 'lineage_player_level', 'lineage_player_pvp', 'lineage_player_pk', 'lineage_player_online', 'lineage_player_clan', 'lineage_index'),
                    array($response_array[$i][0], $response_array[$i][1], $response_array[$i][2], $response_array[$i][3], $player_status, $clan_name, $lineage_index),
                    $theme_body);
            }
        }

        return $template->assign('lineage_body_pvp', $compiled_body, $theme_head);
    }

    private function viewMainStatistic()
    {
        global $template, $hook, $extension, $rule;
        $theme = $template->get('statistic_main', 'components/lineage/');
        $theme_on = $template->get('status_on', 'components/lineage/');
        $theme_off = $template->get('status_off', 'components/lineage/');
        if($extension->getConfig('show_accounts', 'lineage', 'components', 'boolean')) {
            $rule->add('com.lineage.show_accounts', true);
        }
        if($extension->getConfig('show_characters', 'lineage', 'components', 'boolean')) {
            $rule->add('com.lineage.show_characters', true);
        }
        if($extension->getConfig('show_pvptop', 'lineage', 'components', 'boolean')) {
            $rule->add('com.lineage.show_pvptop', true);
        }
        if($extension->getConfig('show_pktop', 'lineage', 'components', 'boolean')) {
            $rule->add('com.lineage.show_pktop', true);
        }
        if($extension->getConfig('show_clantop', 'lineage', 'components', 'boolean')) {
            $rule->add('com.lineage.show_clantop', true);
        }
        $rpcquery = array('id' => $this->server_id, 'action' => array('login.status', 'game.status', 'game.online', 'login.accounts', 'game.characters'));
        $response = $hook->get('lineage_connector')->query($this->server_id, $rpcquery);
        if($response == null) {
            return $template->get('connection_down', 'components/lineage/');
        }
        $login_status = $response['login.status'] == "TRUE" ? $theme_on : $theme_off;
        $game_status = $response['game.status'] == "TRUE" ? $theme_on : $theme_off;
        return $template->assign(array('lineage_server_name', 'lineage_server_id', 'lineage_server_login_status', 'lineage_server_game_status', 'lineage_server_online', 'lineage_server_accounts', 'lineage_server_characters'),
            array($hook->get('lineage_connector')->getName($this->server_id), $this->server_id, $login_status, $game_status, $response['game.online'], $response['login.accounts'], $response['game.characters']),
            $theme);
    }

    private function viewServerSelector($count)
    {
        global $template, $hook;
        $theme_head = $template->get('server_selector_head', 'components/lineage/');
        $theme_body = $template->get('server_selector_body', 'components/lineage/');
        $result_body = null;
        for($i=1;$i<=$count;$i++) {
            $server_name = $hook->get('lineage_connector')->getName($i);
            $result_body .= $template->assign(array('lineage_server_id', 'lineage_server_name'), array($i, $server_name), $theme_body);
        }
        return $template->assign('lineage_selector_body', $result_body, $theme_head);
    }
}


?>