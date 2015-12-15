<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 2015-12-01
 * Time: 11:05
 */

namespace Model\Interfaces;

interface DataBaseInterface {

    function connect();

    function disconnect();

    function querySelect($query);

    function query($query);

    /*SELECT REQUEST*/
    function select(array $fields, $class);

    function where(array $condition);

    function order(array $order);

    function having(array $having);

    function groupby(array $groupby);

    function limit($limit);

    function junction($join, array $joinCritera);

    function getAll($table);

    /*END SELECT REQUEST*/

    /*INSERT REQUEST*/
    function insert($class);

    /*UPDATE REQUEST*/
    function update($class);

    /*DELETE REQUEST*/
    function delete($class);

    function exec();

    function persist($class);

    function countRows();

    function getLog();

    function setLog($log);

    function getById($id);

    function setById($id);

    function deleteById($id);




}