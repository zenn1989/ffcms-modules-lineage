<?php
class hook_lineage_masteraccount_front implements hook_front
{
    public function load()
    {
        return $this;
    }

    public function before()
    {
        global $extension, $template, $hook;
        if ($extension->object['com']['usercontrol']) {
            $theme_head = $template->get('menu_head', 'hooks/lineage_masteraccount/');
            $theme_body = $template->get('menu_body', 'hooks/lineage_masteraccount/');
            $callback = $extension->object['com']['usercontrol'];
            $servers_count = $extension->getConfig('count_servers', 'lineage_connector', 'hooks', 'int');
            $compiled_body = null;
            for($i=1;$i<=$servers_count;$i++) {
                $compiled_body .= $template->assign(array('lineage_server_id', 'lineage_server_name'), array($i, $hook->get('lineage_connector')->getName($i)), $theme_body);
            }
            $callback->hook_item_settings .= $template->assign('lineage_menu_body', $compiled_body, $theme_head);
        }
    }

}


?>