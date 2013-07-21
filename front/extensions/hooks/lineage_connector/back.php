<?php

class hook_lineage_connector_back
{

    public function install()
    {
        global $database, $constant, $language;
        $dir = "lineage_connector";
        $type = "lineage_connector";
        $enabled = 0;
        $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_hooks (`dir`, `type`, `enabled`) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $dir, PDO::PARAM_STR);
        $stmt->bindParam(2, $type, PDO::PARAM_STR);
        $stmt->bindParam(3, $enabled, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = null;
        $query_cache_table = "CREATE TABLE IF NOT EXISTS `{$constant->db['prefix']}_hook_lineage_query` (
                                `query` VARCHAR( 128 ) NOT NULL ,
                                `server_id` INT( 12 ) NOT NULL ,
                                `result` TEXT NOT NULL ,
                                `time` INT( 16 ) NOT NULL
                                ) ENGINE = MYISAM ;";
        $database->con()->exec($query_cache_table);
        $language_back = array(
            'ru' => array(
                'admin_hook_lineage_connector.name' => 'Lineage2 tunnel',
                'admin_hook_lineage_connector.desc' =>'Настройка количества игровых серверов и подключений к их socketServer',
                'admin_hook_lineage_connector_settings' => 'Настройки',
                'admin_hook_lineage_connector_desc' => 'В данном хуке вы можете настроить подключения к бекендам(lineage2 RPC) ваших игровых серверов Lineage 2',
                'admin_hook_lineage_connector_backend_name' => 'Бекенд соккет №',
                'admin_hook_lineage_connector_config_count_servers_title' => 'Серверов',
                'admin_hook_lineage_connector_config_count_servers_desc' => 'Количество подключений к бекенд серверам. Увеличьте если вам нужно 2 и более и появится необходимая настройка',
                'admin_hook_lineage_connector_config_host_title' => 'URL',
                'admin_hook_lineage_connector_config_host_desc' => 'URL подключения к бекенд серверу(http://127.0.0.1:2543)',
                'admin_hook_lineage_connector_config_key_title' => 'Ключ',
                'admin_hook_lineage_connector_config_key_desc' => 'Ключ для взаимодествия с API backend сервером (задается и в настройках бекенда)',
                'admin_hook_lineage_connector_config_name_title' => 'Название',
                'admin_hook_lineage_connector_config_name_desc' => 'Название сервера доступное на сайте при выборе такового (пример: x10000, Bartz)',
                'admin_hook_lineage_connector_config_cache_title' => 'Кеширование',
                'admin_hook_lineage_connector_config_cache_desc' => 'Время на которое данные сохраняются в базу(для синхронизации запросов) в секундах. Рекомендуемое время - от 60 секунд.'
            ),
            'en' => array(
                'admin_hook_lineage_connector.name' => 'Lineage2 tunnel',
                'admin_hook_lineage_connector.desc' =>'Configuration count of backservers and they data',
                'admin_hook_lineage_connector_settings' => 'Configuration',
                'admin_hook_lineage_connector_desc' => 'In this hook you can configure connect to backend of lineage 2 server (RPC)',
                'admin_hook_lineage_connector_backend_name' => 'Backend socket №',
                'admin_hook_lineage_connector_config_count_servers_title' => 'Servers',
                'admin_hook_lineage_connector_config_count_servers_desc' => 'Count of backservers. You can increase this configure if you need more.',
                'admin_hook_lineage_connector_config_host_title' => 'URL',
                'admin_hook_lineage_connector_config_host_desc' => 'URL connect to backend socket (http://127.0.0.1:2543/index.php)',
                'admin_hook_lineage_connector_config_key_title' => 'Key',
                'admin_hook_lineage_connector_config_key_desc' => 'Secret backend API key to start sucessful connection',
                'admin_hook_lineage_connector_config_name_title' => 'Name',
                'admin_hook_lineage_connector_config_name_desc' => 'Server name what displayed on site (example: x10000, Bartz)',
                'admin_hook_lineage_connector_config_cache_title' => 'Caching',
                'admin_hook_lineage_connector_config_cache_desc' => 'Time to caching queryes to remote socket API. We recomend set 60 or more seconds on this field.'
            )
        );
        $language->addLinesLanguage($language_back, true);
        return;
    }

    public function load()
    {
        global $template, $admin, $language, $system;
        if ($admin->getAction() == "turn") {
            return $admin->turn();
        }
        $action_page_title = $admin->getExtName() . " : " . $language->get('admin_hook_lineage_connector_settings');
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
        $config_set = $language->get('admin_hook_lineage_connector_desc');
        $config_set .= $admin->tplSettingsInputText('config:count_servers', $admin->getConfig('count_servers', 'int'), $language->get('admin_hook_lineage_connector_config_count_servers_title'), $language->get('admin_hook_lineage_connector_config_count_servers_desc'));
        $config_set .= $admin->tplSettingsInputText('config:cache_time', $admin->getConfig('cache_time', 'int'), $language->get('admin_hook_lineage_connector_config_cache_title'), $language->get('admin_hook_lineage_connector_config_cache_desc'));
        $servers_count = $admin->getConfig('count_servers', 'int');
        for($i=1;$i<=$servers_count;$i++) {
            $config_set .= $admin->tplSettingsDirectory($language->get('admin_hook_lineage_connector_backend_name')." - {$i}");
            $config_set .= $admin->tplSettingsInputText('config:server_'.$i.'_host', $admin->getConfig('server_'.$i.'_host'), $language->get('admin_hook_lineage_connector_config_host_title'), $language->get('admin_hook_lineage_connector_config_host_desc'));
            $config_set .= $admin->tplSettingsInputText('config:server_'.$i.'_key', $admin->getConfig('server_'.$i.'_key'), $language->get('admin_hook_lineage_connector_config_key_title'), $language->get('admin_hook_lineage_connector_config_key_desc'));
            $config_set .= $admin->tplSettingsInputText('config:server_'.$i.'_name', $admin->getConfig('server_'.$i.'_name'), $language->get('admin_hook_lineage_connector_config_name_title'), $language->get('admin_hook_lineage_connector_config_name_desc'));
        }
        $work_body .= $template->assign('ext_form', $config_set, $config_form);

        $menu_link = null;
        $menu_link .= $template->assign(array('ext_menu_link', 'ext_menu_text'), array('?object=hooks&id=' . $admin->getID(), $language->get('admin_modules_staticonmain_settings')), $menu_theme);
        $body_form = $template->assign(array('ext_configs', 'ext_menu', 'ext_action_title'), array($work_body, $menu_link, $action_page_title), $template->get('config_head'));
        return $body_form;
    }
}

?>