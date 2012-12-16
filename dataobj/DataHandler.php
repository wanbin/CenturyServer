<?php

class DataHandler
{
    public static function createMC($name,$gameuid,$server='')
    {
        include_once './'.$name.'.php'; 
        static $_instance = NULL;
        if($_instance === NULL)
            $_instance = new $name($gameuid,$server);
        return $_instance;
    }
}

?>