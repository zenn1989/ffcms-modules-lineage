<?php

class mod_lineage_status_front implements mod_front {

    public function before()
    {
        global $hook, $page, $cache, $extension, $template;
        $resulthtml = null;
        if($cache->getBlock('mod_lineage_status') != null) {
            $resulthtml = $cache->getBlock('mod_lineage_status');
        } else {
            $servers_count = $extension->getConfig('count_servers', 'lineage_connector', 'hooks', 'int');
            $theme_head = $template->get('status_head', 'modules/lineage_status/');
            $theme_body = $template->get('status_body', 'modules/lineage_status/');
            $theme_on = $template->get('status_port_on', 'modules/lineage_status/');
            $theme_off = $template->get('status_port_off', 'modules/lineage_status/');
            $build_rows = null;
            for($i=1;$i<=$servers_count;$i++) {
                $query = array('id' => $i, 'action' => array('login.status', 'game.status', 'game.online'));
                $response = $hook->get('lineage_connector')->query($i, $query);
                $name = $hook->get('lineage_connector')->getName($i);
                $login_status = $this->responseType($response['login.status']) ? $theme_on : $theme_off;
                $game_status = $this->responseType($response['game.status']) ? $theme_on : $theme_off;
                $online = $response['game.online'] == null ? 0 : $response['game.online'];

                $build_rows .= $template->assign(array('lineage_server_login_status', 'lineage_server_game_status', 'lineage_server_online', 'lineage_server_name'), array($login_status, $game_status, $online, $name), $theme_body);
            }
            $resulthtml = $template->assign('status_tbody', $build_rows, $theme_head);
            $cache->saveBlock('mod_lineage_status', $resulthtml);
        }
        $page->setContentPosition('left', $resulthtml, 0);
    }

    public function after() {

    }

    private function responseType($response)
    {
        return $response == "TRUE" ? true : false;
    }
}