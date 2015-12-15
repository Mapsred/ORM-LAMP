<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 2015-11-26
 * Time: 10:59
 */

function addLog($txt)
{
    file_put_contents("logs/access.log", date("[j/m/y H:i:s]")." - $txt \r\n", FILE_APPEND);
//    echo date("[j/m/y H:i:s]")." - $txt <br>"; #Decommenter uniquement pour débug
}

function addErrorLog($txt, $page)
{
    file_put_contents("logs/error.log", date("[j/m/y H:i:s]")." - class: $page - $txt \r\n", FILE_APPEND);
//    echo date("[j/m/y H:i:s]")." - class: $page - $txt"; #Decommenter uniquement pour débug
}

class log {

}