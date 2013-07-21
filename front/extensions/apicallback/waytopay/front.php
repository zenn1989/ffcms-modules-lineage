<?php

class api_waytopay_front
{
    public function load()
    {
        global $extension, $database, $constant, $hook;
        $service_id = $extension->getConfig('wp_service_id', 'lineage', 'components', 'int');
        $security_key = $extension->getConfig('wp_service_key', 'lineage', 'components');
        $count_mul = $extension->getConfig('wp_donate_price', 'lineage', 'components', 'int');
        $item_id = $extension->getConfig('wp_donate_id', 'lineage', 'components', 'int');
        // HTTP параметры:
        $out_summ = (float)$_REQUEST["wOutSum"];
        $inv_id   = (int)$_REQUEST["wInvId"];
        $is_sets  = (int)$_REQUEST["wIsTest"];
        $crc      = (string)$_REQUEST["wSignature"];
        $crc = strtoupper($crc);

        $my_crc = strtoupper(md5("$service_id:$out_summ:$inv_id:$security_key"));

        if ($my_crc != $crc) {
            return "ERROR_bad sign\n";
        }
        $stmt = $database->con()->prepare("SELECT * FROM {$constant->db['prefix']}_com_lineage_donate WHERE id = ?");
        $stmt->bindParam(1, $inv_id, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() != 1) {
            return "ERROR_bad invoice\n";
        }
        $result = $stmt->fetch();
        $count = $result['count'];
        $server_id = $result['server_id'];
        if($out_summ < $count * $count_mul) {
            return "ERROR_low price\n";
        }
        $query = array('id' => $server_id, 'action' => array('game.takeitem'), 'fields' => array('char_name' => $result['char_name'], 'item_id' => $item_id, 'count' => $count));
        $hook->get('lineage_connector')->query($server_id, $query);
        return "OK_".$inv_id;
    }
}


?>