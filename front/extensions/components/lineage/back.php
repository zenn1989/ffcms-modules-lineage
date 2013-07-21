<?php

class com_lineage_back implements backend {

    public function install()
    {
        global $database, $constant, $language;
        $dir = "lineage";
        $enabled = 0;
        $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_components (`dir`, `enabled`) VALUES (?, ?)");
        $stmt->bindParam(1, $dir, PDO::PARAM_STR);
        $stmt->bindParam(2, $enabled, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = null;
        $spec_table = "CREATE TABLE `{$constant->db['prefix']}_com_lineage_accounts` (
                        `id` INT( 16 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                        `game_login` VARCHAR( 36 ) NOT NULL ,
                        `ownerid` INT( 36 ) NOT NULL
                        ) ENGINE = MYISAM ;";
        $stmt = $database->con()->exec($spec_table);
        $stmt = null;
        $lang_front = array(
            'ru' => array(
                'com_lineage_switch_title' => 'Выбор сервера',
                'com_lineage_switch_desc' => 'Выберите сервер для проведения дальнейших операций, в числе которых - регистрация в игре, статистика проекта',
                'com_lineage_switch_name' => 'Сервер',
                'com_lineage_switch_statistic' => 'Статистика',
                'com_lineage_switch_reg' => 'Регистрация',
                'com_lineage_reg_title' => 'Регистрация',
                'com_lineage_reg_desc' => 'Укажите ваши данные для регистрации на игровом сервере lineage 2',
                'com_lineage_reg_auth' => 'Вы авторизованный пользователь нашего сайта. Для регистрируемого аккаунта ваш аккаунт на сайте будет указан как мастер-доступ.',
                'com_lineage_reg_guest' => 'Вы не авторизованы на нашем сайте, поэтому вам необходимо указать почтовый ящик. Мы рекомендуем вам зарегистрироваться на сайте до регистрации аккаунта в игре.',
                'com_lineage_reg_label_login' => 'Логин',
                'com_lineage_reg_help_login<=>Ваш логин для входа в игру. Допустимые данные - английский алфавит и арабские цифры. Длина - от 3 до 16 символов.',
                'com_lineage_reg_label_pass' =>'Пароль',
                'com_lineage_reg_help_pass' => 'Укажите ваш пароль для входа в игру. Допустимые данные - английский алфавит и арабские цифры. Длина от 6 до 32 символов. Мы рекомендуем указывать пароль в виде букв разного регистра и цифр.',
                'com_lineage_reg_label_repass' =>'Повтор',
                'com_lineage_reg_help_repass' => 'Для избежания ошибок в вашем пароле и спорных ситуаций, пожалуйста повторите его еще раз.',
                'com_lineage_reg_label_mail' => 'Почта',
                'com_lineage_reg_help_mail' => 'Укажите ваш действующий почтовый ящик. Помните, если почта не указана или указана не верно вы не сможете восстановить доступ к аккаунту в случае его утери или кражи.',
                'com_lineage_reg_button' => 'Зарегистрировать',
                'com_lineage_reg_notify_loginbad' => 'Ваш login заполнен не корректно',
                'com_lineage_reg_notify_passbad' => 'Пароль заполнен не корректно',
                'com_lineage_reg_notify_passnotequal' => 'Ваши пароли не совпадают',
                'com_lineage_reg_notify_emailbad' => 'Вы указали не корректный email адресс',
                'com_lineage_reg_notify_loginused' => 'Такой аккаунт уже зарегистрирован на нашем игровом сервере',
                'com_lineage_reg_notify_success' => 'Вы успешно зарегистрировали игровой аккаунт на нашем сервере',
                'com_lineage_stats_pvp_title' => 'Рейтинг PvP',
                'com_lineage_stats_pvp_desc' => 'На данной странице отображены лучшие игроки в категории "Игрок против игрока"',
                'com_lineage_stats_pk_title' => 'Рейтинг Pk',
                'com_lineage_stats_pk_desc' => 'На данной странице отображены лучшие игроки в категории "Игрок - убийца"',
                'com_lineage_stats_back' => 'Назад',
                'com_lineage_pvppk_cname' => 'Игрок',
                'com_lineage_pvppk_level' => 'Уровень',
                'com_lineage_pvppk_pvp' => 'PvP',
                'com_lineage_pvppk_pk' => 'Pk',
                'com_lineage_pvppk_status' => 'Статус',
                'com_lineage_pvppk_clan' => 'Клан',
                'com_lineage_stats_main_title' => 'Статистика',
                'com_lineage_stats_main_desc' => 'На данной странице отображается основная статистика игрового сервера.',
                'com_lineage_stats_main_login' => 'Логин сервер',
                'com_lineage_stats_main_game' => 'Гейм сервер',
                'com_lineage_stats_main_online' => 'Онлайн',
                'com_lineage_stats_main_accounts' => 'Аккаунтов',
                'com_lineage_stats_main_characters' => 'Персонажей',
                'com_lineage_stats_main_pvpraiting' => 'Топ-рейтинг PvP',
                'com_lineage_stats_main_pkraiting' => 'Топ-рейтинг Pk',
                'com_lineage_stats_main_clanraiting' => 'Топ-рейтинг кланов',
                'com_lineage_stats_noclan' => 'нет',
                'com_lineage_clan_title' => 'Рейтинг кланов',
                'com_lineage_clan_desc' => 'На данной странице отображаются лучшие кланы игрового сервера lineage 2',
                'com_lineage_clan_cname' => 'Клан',
                'com_lineage_clan_level' => 'Уровень',
                'com_lineage_clan_rep' => 'Репутация',
                'com_lineage_clan_owner' => 'Владелец',
                'com_lineage_clan_members' => 'Участников',
                'com_lineage_down_title' => 'Подключение сброшено',
                'com_lineage_down_desc' => 'Подключение к игровому серверу не было успешно осуществлено. Повторите попытку позже и уведомите администратора о данной пролеме.',
                'com_lineage_donate_title' => 'Пожертвования на сервере',
                'com_lineage_donate_desc' => 'Вы можете совершить пожвертвование воспользовавшись формой ниже',
                'com_lineage_donate_label_count_title' => 'Количество монет',
                'com_lineage_donate_label_count_desc' => 'Количество донат монет которые вы желаете получить',
                'com_lineage_donate_label_charname_title' => 'Персонаж',
                'com_lineage_donate_label_charname_desc' => 'Никнейм вашего персонажа в игре на сервере',
                'com_lineage_donate_button_generate' => 'Сгенерировать квитанцию',
                'com_lineage_donate_gen_alert' => 'Для вас был сгенерирован уникальный платеж, продолжите оплату',
                'com_lineage_donate_gen_id' => 'Квитанция к оплате',
                'com_lineage_donate_gen_sum' => 'Сумма к оплате',
                'com_lineage_donate_gen_valute' => 'руб',
                'com_lineage_donate_gen_char' => 'Имя персонажа',
                'com_lineage_ma_title' => 'Игровые аккаунты lineage 2',
                'com_lineage_ma_desc' => 'На данной странице отображаются ваши аккаунты в игре lineage 2 на сервере',
                'com_lineage_ma_th_acc' => 'Аккаунт',
                'com_lineage_ma_th_actions' => 'Операции',
                'com_lineage_ma_td_change_pass' => 'Изменить пароль',
                'com_lineage_ma_change_title' => 'Операции с аккаунтом',
                'com_lineage_ma_change_desc' => 'На данной странице вы можете произвести операции с аккаунтом',
                'com_lineage_ma_change_label_login' => 'Логин',
                'com_lineage_ma_change_label_newpwd' => 'Новый пароль',
                'com_lineage_ma_change_label_repwd' => 'Повторите пароль',
                'com_lineage_ma_change_label_oldpwd' => 'Старый пароль',
                'com_lineage_ma_change_button_pass' => 'Изменить пароль'
            ),
            'en' => array(
                'com_lineage_switch_title' => 'Server selecting',
                'com_lineage_switch_desc' => 'Choice the server to doing next operation, example statistic or registration',
                'com_lineage_switch_name' => 'Server',
                'com_lineage_switch_statistic' => 'Statistic',
                'com_lineage_switch_reg' => 'Registration',
                'com_lineage_reg_title' => 'Registration',
                'com_lineage_reg_desc' => 'Put your data in this fields to playing lineage 2',
                'com_lineage_reg_auth' => 'You are registered user on our website. We set this site account as master account to game account.',
                'com_lineage_reg_guest' => 'You are not registered on our website so you must enter your email. We recommend you register on our site to make master access to your game accounts.',
                'com_lineage_reg_label_login' => 'Login',
                'com_lineage_reg_help_login' => 'Your game login. Allowed data - english characters and arabic numeric. Length - from 3 to 16 symbols.',
                'com_lineage_reg_label_pass' =>'Password',
                'com_lineage_reg_help_pass' => 'Set your password to enter the game. Allowed data - english characters and arabic numeric. Length from 6 to 32 symbols. We recommend setting password with characters and numeric in different registers.',
                'com_lineage_reg_label_repass' =>'Repeat',
                'com_lineage_reg_help_repass' => 'For exclude exceptions and misstake please put your password again.',
                'com_lineage_reg_label_mail' => 'Email',
                'com_lineage_reg_help_mail' => 'Put here your really email. This allow you recover your access data if it be lost or stolen.',
                'com_lineage_reg_button' => 'Register',
                'com_lineage_reg_notify_loginbad' => 'Your email is incorrent',
                'com_lineage_reg_notify_passbad' => 'Your password is incorrent',
                'com_lineage_reg_notify_passnotequal' => 'Your passwords does not match',
                'com_lineage_reg_notify_emailbad' => 'Your email is incorrent',
                'com_lineage_reg_notify_loginused' => 'Account with same login always registered on our server',
                'com_lineage_reg_notify_success' => 'You are successfully registered on our game server! Congratulation!',
                'com_lineage_stats_pvp_title' => 'Rating PvP',
                'com_lineage_stats_pvp_desc' => 'On this page you can see top players in rank player vs player',
                'com_lineage_stats_pk_title' => 'Rating Pk',
                'com_lineage_stats_pk_desc' => 'On this page you can see top players in rank player killer',
                'com_lineage_stats_back' => 'Back',
                'com_lineage_pvppk_cname' => 'Player',
                'com_lineage_pvppk_level' => 'Level',
                'com_lineage_pvppk_pvp' => 'PvP',
                'com_lineage_pvppk_pk' => 'Pk',
                'com_lineage_pvppk_status' => 'Status',
                'com_lineage_pvppk_clan' => 'Clan',
                'com_lineage_stats_main_title' => 'Statistic',
                'com_lineage_stats_main_desc' => 'On this page displayed main server statistic',
                'com_lineage_stats_main_login' => 'Login server',
                'com_lineage_stats_main_game' => 'Game server',
                'com_lineage_stats_main_online' => 'Online',
                'com_lineage_stats_main_accounts' => 'Accounts',
                'com_lineage_stats_main_characters' => 'Characters',
                'com_lineage_stats_main_pvpraiting' => 'Top rating PvP',
                'com_lineage_stats_main_pkraiting' => 'Top rating Pk',
                'com_lineage_stats_main_clanraiting' => 'Топ-рейтинг кланов',
                'com_lineage_stats_noclan' => 'no',
                'com_lineage_clan_title' => 'Clan rating',
                'com_lineage_clan_desc' => 'On this page you can see most rated clan on game',
                'com_lineage_clan_cname' => 'Clan',
                'com_lineage_clan_level' => 'Level',
                'com_lineage_clan_rep' => 'Reputation',
                'com_lineage_clan_owner' => 'Owner',
                'com_lineage_clan_members' => 'Members',
                'com_lineage_down_title' => 'Connection refused',
                'com_lineage_down_desc' => 'Connection to game server is not be available. Try latter and notice administrator.',
                'com_lineage_donate_title' => 'Donate to server',
                'com_lineage_donate_desc' => 'You can make donate payment to server using this form',
                'com_lineage_donate_label_count_title' => 'Coin count',
                'com_lineage_donate_label_count_desc' => 'Count of donate coins what you taking',
                'com_lineage_donate_label_charname_title' => 'Character',
                'com_lineage_donate_label_charname_desc' => 'Name of your game character',
                'com_lineage_donate_button_generate' => 'Generate payment',
                'com_lineage_donate_gen_alert' => 'Special for you we generate transaction payment. Please continue pay.',
                'com_lineage_donate_gen_id' => 'Transaction',
                'com_lineage_donate_gen_sum' => 'Pricing',
                'com_lineage_donate_gen_valute' => 'rub',
                'com_lineage_donate_gen_char' => 'Character name',
                'com_lineage_ma_title' => 'Game accounts lineage 2',
                'com_lineage_ma_desc' => 'On this page displayed your game accounts in lineage 2',
                'com_lineage_ma_th_acc' => 'Account',
                'com_lineage_ma_th_actions' => 'Actions',
                'com_lineage_ma_td_change_pass' => 'Change password',
                'com_lineage_ma_change_title' => 'Account actions',
                'com_lineage_ma_change_desc' => 'On this page you can make account actions',
                'com_lineage_ma_change_label_login' => 'Login',
                'com_lineage_ma_change_label_newpwd' => 'New password',
                'com_lineage_ma_change_label_repwd' => 'Repeat password',
                'com_lineage_ma_change_label_oldpwd' => 'Old password',
                'com_lineage_ma_change_button_pass' => 'Change now'
            )
        );
        $lang_back = array(
          'ru' => array(
                'admin_component_lineage.name' => 'Lineage 2',
                'admin_component_lineage.desc' => 'Регистрация, статистика и мастер аккаунт для сервера lineage 2 java.',
                'admin_component_lineage_settings' => 'Настройки',
                'admin_component_lineage_config_account_title' => 'Аккаунты',
                'admin_component_lineage_config_account_desc' => 'Отображать ли количество аккаунтов на сайте?',
                'admin_component_lineage_config_chars_title' => 'Персонажи',
                'admin_component_lineage_config_chars_desc' => 'Отображать ли количество персонажей на сайте?',
                'admin_component_lineage_config_pvptop_title' => 'PvP Top',
                'admin_component_lineage_config_pvptop_desc' => 'Отображать ли статистику рейтингов Top PVP на сайте?',
                'admin_component_lineage_config_pktop_title' => 'Pk Top',
                'admin_component_lineage_config_pktop_desc' => 'Отображать ли статистику рейтингов Top PK на сайте?',
                'admin_component_lineage_config_clantop_title' => 'Clan Top',
                'admin_component_lineage_config_clantop_desc' => 'Отображать ли статистику рейтингов Top Clan на сайте?',
                'admin_component_lineage_config_maindir' => 'Статистика',
                'admin_component_lineage_config_donatdir' => 'Донат WayToPay (<a href="http://waytopay.org/~94" target="_blank">Сервис</a>)',
                'admin_component_lineage_config_wpid_title' => 'ID сервиса',
                'admin_component_lineage_config_wpid_desc' => 'ID сервиса полученный при создании его в waytopay',
                'admin_component_lineage_config_wpkey_title' => 'Ключ',
                'admin_component_lineage_config_wpkey_desc' => 'Сгенерированный ключ в сервисе waytopay',
                'admin_component_lineage_config_wpitemid_title' => 'ID предмета',
                'admin_component_lineage_config_wpitemid_desc' => 'ID предмета который выдается при оплате пользователем счета',
                'admin_component_lineage_config_wpitemcount_title' => 'Стоимость',
                'admin_component_lineage_config_wpitemcount_desc' => 'Стоимость 1 предмета с указанным выше ID в рублях'
        ),
        'en' => array(
              'admin_component_lineage.name' => 'Lineage 2',
              'admin_component_lineage.desc' => 'Registering, statistic and master account for server lineage 2 java',
              'admin_component_lineage_settings' => 'Settings',
              'admin_component_lineage_config_account_title' => 'Accounts',
              'admin_component_lineage_config_account_desc' => 'Show account statistic on site(count) ?',
              'admin_component_lineage_config_chars_title' => 'Characters',
              'admin_component_lineage_config_chars_desc' => 'Show character statistic on site(count) ?',
              'admin_component_lineage_config_pvptop_title' => 'PvP Top',
              'admin_component_lineage_config_pvptop_desc' => 'Show pvp top statistic on site?',
              'admin_component_lineage_config_pktop_title' => 'Pk Top',
              'admin_component_lineage_config_pktop_desc' => 'Show Pk top statistic on site?',
              'admin_component_lineage_config_clantop_title' => 'Clan Top',
              'admin_component_lineage_config_clantop_desc' => 'Show clan top statistic on site?',
              'admin_component_lineage_config_maindir' => 'Statistic',
              'admin_component_lineage_config_donatdir' => 'WayToPay service (<a href="http://waytopay.org/~94" target="_blank">Service Site</a>)',
              'admin_component_lineage_config_wpid_title' => 'ID service',
              'admin_component_lineage_config_wpid_desc' => 'Service id taken on waytopay website store',
              'admin_component_lineage_config_wpkey_title' => 'Key',
              'admin_component_lineage_config_wpkey_desc' => 'Security key generated on waytopay store',
              'admin_component_lineage_config_wpitemid_title' => 'ItemId',
              'admin_component_lineage_config_wpitemid_desc' => 'ID item what be taked to player',
              'admin_component_lineage_config_wpitemcount_title' => 'Pricing',
              'admin_component_lineage_config_wpitemcount_desc' => 'Price 1 item in rubble'
          )
        );
        $language->addLinesLanguage($lang_front, false);
        $language->addLinesLanguage($lang_back, true);
    }

    public function load()
    {
        global $admin, $system, $language, $template;
        if ($admin->getAction() == "turn") {
            return $admin->turn();
        }
        $action_page_title = $admin->getExtName() . " : " . $language->get('admin_component_lineage_settings');
        $menu_theme = $template->get('config_menu', null, true);
        $work_body = null;
        if ($system->post('submit')) {
            $save_try = $admin->trySaveConfigs();
            if ($save_try)
                $work_body .= $template->stringNotify('success', $language->get('admin_extension_config_update_success'), true);
            else
                $work_body .= $template->stringNotify('error', $language->get('admin_extension_config_update_fail'), true);;
        }

        $config_form = $template->get('config_form');
        $config_set = null;
        $config_set .= $admin->tplSettingsDirectory($language->get('admin_component_lineage_config_maindir'));
        $config_set .= $admin->tplSettingsSelectYorN('config:show_accounts', $language->get('admin_component_lineage_config_account_title'), $language->get('admin_component_lineage_config_account_desc'), $admin->getConfig('show_accounts', 'boolean'));
        $config_set .= $admin->tplSettingsSelectYorN('config:show_characters', $language->get('admin_component_lineage_config_chars_title'), $language->get('admin_component_lineage_config_chars_desc'), $admin->getConfig('show_characters', 'boolean'));
        $config_set .= $admin->tplSettingsSelectYorN('config:show_pvptop', $language->get('admin_component_lineage_config_pvptop_title'), $language->get('admin_component_lineage_config_pvptop_desc'), $admin->getConfig('show_pvptop', 'boolean'));
        $config_set .= $admin->tplSettingsSelectYorN('config:show_pktop', $language->get('admin_component_lineage_config_pktop_title'), $language->get('admin_component_lineage_config_pktop_desc'), $admin->getConfig('show_pktop', 'boolean'));
        $config_set .= $admin->tplSettingsSelectYorN('config:show_clantop', $language->get('admin_component_lineage_config_clantop_title'), $language->get('admin_component_lineage_config_clantop_desc'), $admin->getConfig('show_clantop', 'boolean'));
        $config_set .= $admin->tplSettingsDirectory($language->get('admin_component_lineage_config_donatdir'));
        $config_set .= $admin->tplSettingsInputText('config:wp_service_id', $admin->getConfig('wp_service_id', 'int'), $language->get('admin_component_lineage_config_wpid_title'), $language->get('admin_component_lineage_config_wpid_desc'));
        $config_set .= $admin->tplSettingsInputText('config:wp_service_key', $admin->getConfig('wp_service_key'), $language->get('admin_component_lineage_config_wpkey_title'), $language->get('admin_component_lineage_config_wpkey_desc'));
        $config_set .= $admin->tplSettingsInputText('config:wp_donate_id', $admin->getConfig('wp_donate_id', 'int'), $language->get('admin_component_lineage_config_wpitemid_title'), $language->get('admin_component_lineage_config_wpitemid_desc'));
        $config_set .= $admin->tplSettingsInputText('config:wp_donate_price', $admin->getConfig('wp_donate_price', 'int'), $language->get('admin_component_lineage_config_wpitemcount_title'), $language->get('admin_component_lineage_config_wpitemcount_desc'));
        $work_body .= $template->assign('ext_form', $config_set, $config_form);

        $menu_link = null;
        $menu_link .= $template->assign(array('ext_menu_link', 'ext_menu_text'), array('?object=components&id=' . $admin->getID(), $language->get('admin_component_lineage_settings')), $menu_theme);
        $body_form = $template->assign(array('ext_configs', 'ext_menu', 'ext_action_title'), array($work_body, $menu_link, $action_page_title), $template->get('config_head'));
        return $body_form;


    }
}