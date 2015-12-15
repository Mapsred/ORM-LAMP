<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 2015-12-01
 * Time: 14:31
 */

require_once('autoload.php');

require_once('logs.php');
use ORM\MysqlConnect;
use Model\Database\ORM;
$orm = new ORM();
$sql = new MysqlConnect($orm->getDatabase());
/*Ordre de critères :
 * SELECT
 * FROM
 * JOIN
 * WHERE
 * GROUP BY
 * HAVING
 * ORDER BY
 */

$choix = 0;

switch ($choix) {
    case 0 :
        select();
        break;
    case 1 :
        insert();
        break;
    case 2 :
        update();
        break;
    case 3 :
        delete();
        break;
    case 4 :
        setById();
        break;
    case 5 :
        getById();
        break;
    case 6 :
        deleteById();
        break;
    default :
        hello();
}

function select(){
    /**Select Request**/
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());


    $fields = array("*");
    $condition = array(
        'where' => array(
            'id !=' => 1
        )
    );
    $order = array("id" => "DESC", "ok" => "ASC");
    $groupby = array('id', "ok");
    $having = array("id >" => 1);




    $sql->select($fields, $orm);
    $sql->where($condition);
//    $sql->groupby($groupby);
//    $sql->order($order);
//    $sql->having($having);

    $RESULT = $sql->exec();

//    var_dump($RESULT); #Affiche le tableau des résultats
//$join = "INNER JOIN";
//$joinCritera = array("a.key", "b.key");
//$sql->junction($join, $joinCritera);

    /**END Select Request**/

}

function insert(){
    /**Insert Request**/
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());

    //On va mettre ok à 35, ko à 56 et test à dix

    $orm->setOk(35);
    $orm->setKo(56);
    $orm->setTest('dix');
    $sql->insert($orm);
    $sql->persist($orm);
    /**END Insert Request**/
}

function update(){
    /**Update Request**/
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());

//    On va modifier ok à 25 au lieu de 35, ko à 46 au lieu de 56 et test à vingt au lieu de dix
    $condition = array(
        'where' => array(
            'ok =' => 35,
            'ko =' => 56,
            'test LIKE' => 'dix'
        )
    );

    $orm->setOk(25);
    $orm->setKo(46);
    $orm->setTest('vingt');
    $sql->update($orm);
    $sql->where($condition);
    $sql->persist($orm);
    /**END Update Request**/

}

function delete(){
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());
    /**DELETE Request**/

    $condition = array(
        'where' => array(
            'id =' => 1
        )
    );
    $sql->delete($orm);
    $sql->where($condition);
    $sql->persist($orm);
    /**END DELETE Request**/
}

function setById(){
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());
    $fields = array("id", "ok", "test");
    $condition = array(
        'where' => array(
            'id =' => 2
        )
    );

    $sql->select($fields, $orm);
    $sql->where($condition);
    $sql->setById('1');
    $orm->setKo('15');
    $orm->setTest("Applaudir ' ' '");
    $sql->persist($orm);

}

function getById(){
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());
    $sql->getById('1');
    $sql->persist($orm);

}

function deleteById(){
    $orm = new ORM();
    $sql = new MysqlConnect($orm->getDatabase());

    $sql->deleteById('1');
    $sql->persist($orm);

}

function hello(){

}

/*-------------------------------------------*/





