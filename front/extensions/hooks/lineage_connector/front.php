<?php

class hook_lineage_connector_front implements hook_front
{
    private $socket = null;
    private $configure = array();
    private $init = false;
    private $allow_cache = array('login.status', 'game.status', 'game.online', 'game.pvptop', 'game.pktop', 'game.clantop');

    public function load()
    {
        global $extension;
        if(!$this->init) {
            $servers_count = $extension->getConfig('count_servers', 'lineage_connector', 'hooks', 'int');
            for($i=1;$i<=$servers_count;$i++) {
                $this->configure['server'][$i]['host'] = $extension->getConfig('server_'.$i.'_host', 'lineage_connector', 'hooks');
                $this->configure['server'][$i]['key'] = $extension->getConfig('server_'.$i.'_key', 'lineage_connector', 'hooks');
                $this->configure['server'][$i]['name'] = $extension->getConfig('server_'.$i.'_name', 'lineage_connector', 'hooks');
            }
            $this->init = true;
        }
        return $this;
    }

    public function getName($index)
    {
        return $this->configure['server'][$index]['name'];
    }

    public function query($index, $array_data)
    {
        global $system, $database, $constant, $extension;
        $cache_interval = $extension->getConfig('cache_time', 'lineage_connector', 'hooks', 'int');
        if($cache_interval < 30)
            $cache_interval = 30;
        $time = time();
        $time_cache_sql = $time - $cache_interval;
        $cache_allowed = $this->allowToCache($array_data['action']);
        if(sizeof($cache_allowed) > 0) {
            $queryList = $system->DbPrepareListdata($cache_allowed);
            $stmt = $database->con()->prepare("SELECT * FROM `{$constant->db['prefix']}_lineage_query` WHERE `query` in($queryList) AND `server_id` = ? AND `time` >= ?");
            $stmt->bindParam(1, $index, PDO::PARAM_INT);
            $stmt->bindParam(2, $time_cache_sql, PDO::PARAM_INT);
            $stmt->execute();
            $cached_data = array();
            while($query_result = $stmt->fetch()) {
                $cached_data[$query_result['query']] = unserialize($query_result['result']);
                $array_data['action'] = $system->valueUnsetInArray($query_result['query'], $array_data['action']);
            }
            $stmt = null;
        }
        $response = array();
        if(sizeof($array_data['action']) > 0) {
            if($this->socket[$index] == null) {
                $this->openSocket($index);
            }
            $this->http_build_query_for_curl($array_data, $post_data);
            curl_setopt($this->socket[$index], CURLOPT_URL, $this->configure['server'][$index]['host']."?key=".$this->configure['server'][$index]['key']);
            curl_setopt($this->socket[$index], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->socket[$index], CURLOPT_POST, true);
            curl_setopt($this->socket[$index], CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($this->socket[$index], CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($this->socket[$index], CURLOPT_BINARYTRANSFER, TRUE);
            curl_setopt($this->socket[$index], CURLOPT_POSTFIELDS, $post_data);
            $response = unserialize(curl_exec($this->socket[$index]));
            $this->saveResponse($index, $response, $time);
        }
        if(sizeof($cached_data) > 0) {
            foreach($cached_data as $socket_query=>$socket_result) {
                $response[$socket_query] = $socket_result;
            }
        }
        return $response;
    }

    private function saveResponse($index, $response, $time)
    {
        global $database, $constant;
        if(!is_array($response))
            return;
        foreach($response as $query=>$value) {
            if($this->allowedQueryCache($query)) {
                $tosave_value = serialize($value);
                $stmt = $database->con()->prepare("SELECT COUNT(*) FROM {$constant->db['prefix']}_lineage_query WHERE query = ? AND server_id = ?");
                $stmt->bindParam(1, $query, PDO::PARAM_STR);
                $stmt->bindParam(2, $index, PDO::PARAM_INT);
                $stmt->execute();
                $res = $stmt->fetch();
                $count = $res[0];
                $stmt = null;
                if($count > 0) {
                    $stmt = $database->con()->prepare("UPDATE {$constant->db['prefix']}_lineage_query SET `result` = ?, `time` = ? WHERE `query` = ? AND `server_id` = ?");
                    $stmt->bindParam(1, $tosave_value, PDO::PARAM_STR);
                    $stmt->bindParam(2, $time, PDO::PARAM_INT);
                    $stmt->bindParam(3, $query, PDO::PARAM_STR);
                    $stmt->bindParam(4, $index, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt = null;
                } else {
                    $stmt = $database->con()->prepare("INSERT INTO {$constant->db['prefix']}_lineage_query (`query`, `server_id`, `result`, `time`) VALUES (?, ?, ?, ?)");
                    $stmt->bindParam(1, $query, PDO::PARAM_STR);
                    $stmt->bindParam(2, $index, PDO::PARAM_INT);
                    $stmt->bindParam(3, $tosave_value, PDO::PARAM_STR);
                    $stmt->bindParam(4, $time, PDO::PARAM_INT);
                    $stmt->execute();
                    $stmt = null;
                }
            }
        }
    }

    private function allowedQueryCache($query)
    {
        if(in_array($query, $this->allow_cache))
            return true;
        return false;
    }

    private function allowToCache($queryes)
    {
        if(!is_array($queryes))
            return array();
        $result = array();
        foreach($queryes as $query) {
           if(in_array($query, $this->allow_cache)) {
               $result[] = $query;
           }
        }
        return $result;
    }

    private function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

        if ( is_object( $arrays ) ) {
            $arrays = get_object_vars( $arrays );
        }

        foreach ( $arrays AS $key => $value ) {
            $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
            if ( is_array( $value ) OR is_object( $value )  ) {
                $this->http_build_query_for_curl( $value, $new, $k );
            } else {
                $new[$k] = $value;
            }
        }
    }

    private function openSocket($index)
    {
        $this->socket[$index] = curl_init();
    }


}

?>