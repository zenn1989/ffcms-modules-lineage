<?php

class mod_lineage_status_back implements backend {

    public function install() {
        global $database, $constant, $language;
        $dir = "lineage_status";
        $enabled = 0;
        $path_choice = 1;
        $path_allow = "*";
        $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_modules (`dir`, `enabled`, `path_choice`, `path_allow`) VALUES (?, ?, ?, ?)");
        $stmt->bindParam(1, $dir, PDO::PARAM_STR);
        $stmt->bindParam(2, $enabled, PDO::PARAM_INT);
        $stmt->bindParam(3, $path_choice, PDO::PARAM_INT);
        $stmt->bindParam(4, $path_allow, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = null;
        $language_front = array(
            'ru' => array(
                'mod_lineage_status_name' => 'Статистика',
                'mod_lineage_status_server' => 'Сервер',
                'mod_lineage_status_status' => 'Статус',
                'mod_lineage_status_refreshing' => 'Обновление: 2минуты'
            ),
            'en' => array(
                'mod_lineage_status_name' => 'Statistic',
                'mod_lineage_status_server' => 'Server',
                'mod_lineage_status_status' => 'Status',
                'mod_lineage_status_refreshing' => 'Refreshing: 2min'
            )
        );
        $language->addLinesLanguage($language_front, false);
        return;
    }

    public function load() {
        global $admin;
        if ($admin->getAction() == "turn") {
            return $admin->turn();
        }
        return;
    }
}


?>