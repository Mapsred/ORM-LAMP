<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 2015-10-22
 * Time: 15:14
 */

function __autoload($classname) {

    $filename = str_replace("\\", '/', $classname).".php";

    require_once($filename);
    require_once('logs.php');
    require_once ('config.php');

}

