<?php
class hook_lineage_masteraccount_back implements backend
{
    public function install()
    {
        global $database, $constant, $language;
        $dir = "lineage_masteraccount";
        $type = "lineage_masteraccount";
        $enabled = 0;
        $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_hooks (`dir`, `type`, `enabled`) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $dir, PDO::PARAM_STR);
        $stmt->bindParam(2, $type, PDO::PARAM_STR);
        $stmt->bindParam(3, $enabled, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = null;
        $array_lang = array(
            'ru' => array(
                'hook_lineage_masteraccount_linkname' => 'Аккаунты Lineage 2'
            ),
            'en' => array(
                'hook_lineage_masteraccount_linkname' => 'Lineage 2 MasterAccount'
            )
        );
        $admin_lang = array(
            'ru' => array(
                'admin_hook_lineage_masteraccount.name' => 'Меню мастераккаунт',
                'admin_hook_lineage_masteraccount.desc' => 'Добавление в меню управления аккаунтом на сайте ссылки на мастер аккаунт Lineage 2'
            ),
            'en' => array(
                'admin_hook_lineage_masteraccount.name' => 'Lineage 2 masteracc menu',
                'admin_hook_lineage_masteraccount.desc' => 'Adding to profile settings links master account link lineage 2'
            ),
        );
        $language->addLinesLanguage($array_lang, false);
        $language->addLinesLanguage($admin_lang, true);
    }

    public function load() {
        global $admin;
        if ($admin->getAction() == "turn") {
            return $admin->turn();
        }
    }
}


?>