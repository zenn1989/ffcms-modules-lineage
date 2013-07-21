<?php
class logging
{
    public static function log($data)
    {
        $dmy = date('d-m-y');
        $query_time = date('d-m-y H:i:s');
        $file = root . "/log/" . $dmy . "_log.txt";
        $query = "[reqeust][{$query_time}]: ".$data."\r\n";
        file_put_contents($file, $query, FILE_APPEND | LOCK_EX);
    }
}

?>