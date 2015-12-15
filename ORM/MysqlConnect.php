<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 2015-12-01
 * Time: 11:11
 */

namespace ORM;

require_once('autoload.php');

use Model\Interfaces\DataBaseInterface;

class MysqlConnect implements DataBaseInterface {

    protected $_config = array();
    protected $_link;
    protected $_result;
    protected $_request;
    protected $port = 3306;
    protected $selectTable;
    protected $log; //1 pour enregistrer les logs
    protected $id;
    protected $type; //1 pour Delete - 2 pour Get - 3 pour Set


    public function __construct(array $config) {
        if (count($config) !== 4) {
            throw new \InvalidArgumentException('Nombre de paramètres invalide');
        }
        $this->_config = $config;
        $this->log = 1;
    }

    public function setLog($log)
    {
        if (!is_numeric($log)){
            $error = 'Erreur, veuillez entrer 0 ou 1';
            addErrorLog($error,'MysqlConnect - setLog');
            $this->printError($error);
            exit;
        }
        $this->log = $log;
    }

    public function getLog()
    {
        if ($this->log === 1) {
            return 'Les logs sont activés';
        }else {
            return 'Les logs sont désactivés';
        }
    }

    public function connect() {
        if ($this->_link === NULL) {
            list($host, $user, $password, $database) = $this->_config;
            try {
                $this->_link = new \PDO('mysql:host='.$host.';port='.$this->port.';dbname='.$database, $user, $password);
                $this->_link->exec("SET CHARACTER SET utf8");
                $this->_link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            catch (\Exception $e) {
                if ($this->log === 1) {
                    addErrorLog($e->getMessage(), 'mysqlClass (connect)'); //On met à jour les logs avec l'erreur
                }
                die('Erreur : ' . $e->getMessage());
            }
            return $this->_link;
        }
        return $this->_link;
    }

    public function disconnect() {
        if ($this->_link === null) {
            return false;
        }
        unset($this->_link);
        return true;
    }

    public function querySelect($query) {
        try {
        if (!is_string($query) || empty($query)) {
            throw new \InvalidArgumentException('La requête spécifiée n\'est pas valide');
        }
        $sql = $this->connect();
        $reponse = $sql->query($query);
        $reponse->setFetchMode(\PDO::FETCH_ASSOC);
        $data = array();
        $nb = 0;
        while ($resultat = $reponse->fetch()) {
            $data[$nb] = $resultat;
            $nb++;
        }
        $this->_result = $data;
        if(!$this->_result = $data) {
            $error = $sql->errorInfo();
            $error = $error[2];
            if ($this->log === 1) {
                addErrorLog($error,'MysqlConnect - querySelect');
            }

            $this->printError('Erreur en executant la requête '.$error);
        }
            if ($this->log === 1) {
                addLog($query);
            }
            $this->printRequest($query);
            return $this->_result;
        }catch(\Exception $e) {
            if ($this->log === 1) {
                addErrorLog($e->getMessage(),'MysqlConnect - querySelect');
            }
            $this->printError('Une erreur s\'est produite, veuillez vérifier que tous vos champs entrés sont corrects');

        }
        return false;
    }

    public function query($query)
    {
        $sql = $this->connect();
        $stmt = $sql->prepare($query);
        $stmt->execute();
        $this->printRequest($query);
        if ($this->log === 1) {
            addLog($query);
        }

    }

    public function countRows()
    {
        return $this->_result!==NULL
        ? count($this->_result) : 0;
    }

    public function __destruct(){
        $this->disconnect();
    }

    /*SELECT REQUEST*/
    public function select(array $fields, $class)
    {
        $nbFields = count($fields);
        if ($nbFields <= 0){
            $error = "Il n'y a aucun champs pour la sélection";
            if ($this->log === 1) {
               addErrorLog($error, "MysqlConnect - select");
            }
            $this->printError($error);
            exit;
        }

        $clas = array();
        $nb = 0;
        $field = array();
        //On vérifie que les champs demandés existent
        foreach (get_class_methods($class) as $classe) {
            if (stristr($classe, 'get')) {
                $field[$nb] =  explode ( 'get' , $classe);
                $field[$nb] =  $field[$nb][1];
                $clas[$nb] = $classe;
                $nb++;
            }
        }
        unset($clas[count($clas)-1]); //On supprime le champs Database
        unset($field[count($field)-1]); //On supprime le champs Database
        foreach ($fields as $item) {
            $item = ucfirst($item);
            if (!in_array($item, $field) && $item != "*") {
                $error = "Le champs $item n'existe pas dans la table $class->tablename";
                if ($this->log === 1) {
                    addErrorLog($error,'MySqlConnect - select');
                }
                $this->printError($error);
                exit;
            }
        }
        $this->_request = "SELECT";
        for ($i = 0; $i < $nbFields; $i++) {
               $param =  $fields[$i];
            $this->_request.= " $param";
            if ($i !== $nbFields-1) {
                $this->_request.=",";
            }
        }
        $this->_request.= " FROM $class->tablename";
    }

    public function where(array $condition)
    {
        $nbWhere = count($condition);
        if ($nbWhere <= 0){
            $error = "Il n'y a aucun champs pour la clause where";
            if ($this->log === 1) {
                addErrorLog($error, "MysqlConnect - where");
            }
            $this->printError($error);
            exit;
        }
        if (!$this->_request) {
            $this->_request = "";
        }
        $conditions = array();
        $nb = 0;
        foreach ($condition as $item => $items) {
            $conditions[$nb] = " ".strtoupper($item);
            $nb++;
        }
        $nbb = 0;

        foreach (array_values($condition) as $item) {
            $nb = 0;
            foreach ($item as $items => $itemss) {
                $param1 = $items;
                $param2 = $this->escape($itemss);
                $conditions[$nbb].= " $param1 $param2";
                if ($nb !== count($item)-1) {
                    $conditions[$nbb].= " AND";
                }
                $nb++;
            }
            $nbb++;
        }
        foreach ($conditions as $condi) {
            $this->_request .= $condi;
        }
    }

    public function groupby(array $groupby)
    {
        $nbGroupBy = count($groupby);
        if ($nbGroupBy <= 0){
            $error = "Il n'y a aucun champs pour la clause groupby";
            if ($this->log === 1) {
                addErrorLog($error, "MysqlConnect - groupby");
            }
            $this->printError($error);
            exit;
        }
        $this->_request.= " GROUP BY";
        for ($i = 0; $i < $nbGroupBy; $i++) {
            $param = $this->escape($groupby[$i]);
            $this->_request.= " $param";
            if ($i !== $nbGroupBy-1) {
                $this->_request.=",";
            }
        }
    }

    public function having(array $having )
    {
        $nbHaving = count($having);
        if ($nbHaving <= 0){
            $error = "Il n'y a aucun champs pour la clause having";
            if ($this->log === 1) {
                addErrorLog($error, "MysqlConnect - having");
            }
            $this->printError($error);
            exit;
        }
        $this->_request.=" HAVING";

        $nb = 0;
        foreach ($having as $item => $value) {
            $param = $this->escape($value);
            $this->_request.= " $item $param";

            if ($nb !== $nbHaving-1){
                $this->_request.=" AND";
            }
            $nb++;
        }
    }

    public function junction($join, array $joinCritera)
    {
        $nbHaving = count($joinCritera);
        if ($nbHaving !== 2){
            $error = "Il faut 2 critères pour la jointure";
            if ($this->log === 1) {
                addErrorLog($error, "MysqlConnect - junction");
            }
            $this->printError($error);
            exit;
        }
        $this->_request.= " $join B ON";

        for ($i = 0; $i < $nbHaving; $i++) {
            $this->_request.= " $joinCritera[$i]";
            if ($i !== $nbHaving-1) {
                $this->_request.=" =";
            }
        }
    } //TODO à finir

    public function order(array $order)
    {
        $nbOrder = count($order);
        if ($nbOrder <= 0){
            $error = "Il n'y a aucun champs pour la clause order";
            if ($this->log === 1) {
                addErrorLog($error, "MysqlConnect - order");
            }
            $this->printError($error);
            exit;
        }
        $this->_request.= " ORDER BY";
        $nb = 0;
        foreach ($order as $item => $value) {
            $param =$value;
            $this->_request.= " $item $param";

            if ($nb !== $nbOrder-1){
                $this->_request.=",";
            }
            $nb++;
        }
    }

    public function getAll($table) {
        $request = "SELECT * FROM  $table";
        return $this->querySelect($request);

    }

    public function limit($limit)
    {
        $this->_request= " LIMIT $limit";
    }

    /*END SELECT REQUEST*/

    /*INSERT REQUEST*/
    public function insert($class)
    {
        $this->_request = "INSERT INTO $class->tablename";
        $clas = array(); //Les fonctions get
        $fields = array(); //Les noms de variable récupérées depuis les fonctions get
        $name = "";
        $value = "";
        $nb = 0;
        foreach (get_class_methods($class) as $classe) {
            if (stristr($classe, 'get')) {
                $fields[$nb] =  explode ( 'get' , $classe);
                $fields[$nb] =  $fields[$nb][1];
                $clas[$nb] = $classe;
                $nb++;
            }
        }
        unset($clas[count($clas)-1]); //On supprime le champs Database du tableau
        unset($fields[count($fields)-1]); //On supprime le champs Database du tableau
        $nb = count($fields);

        for ($i = 0; $i < count($clas); $i++) {
            if ($result = $class->$clas[$i]()) {//true si la fonction get retourne quelque chose, false autrement
                $param = $fields[$i];
                $name.=strtolower($param);
                $param2 = $this->escape($result);
                $value.=strtolower($param2);
                if ($i !== $nb-1){
                    $name.=",";
                    $value.=",";
                }
            }
        }
        $this->_request.= " ($name) \nVALUES ($value)";
        foreach (get_class_methods($class) as $classe) { //on remet à zéro les variables de la class
            if (stristr($classe, 'set')) {
                $class->$classe(null);
                $nb++;
            }
        }
    }

    /*UPDATE REQUEST*/
    public  function update($class)
    {
        $this->_request = "UPDATE $class->tablename\nSET";
        $clas = array(); //Les fonctions get
        $fields = array(); //Les noms de variable récupérées depuis les fonctions get
        $value = "";
        $nb = 0;
        foreach (get_class_methods($class) as $classe) {
            if (stristr($classe, 'get')) {
                $fields[$nb] =  explode ( 'get' , $classe);
                $fields[$nb] =  $fields[$nb][1];
                $clas[$nb] = $classe;
                $nb++;
            }
        }
        unset($clas[count($clas)-1]); //On supprime le champs Database
        unset($fields[count($fields)-1]); //On supprime le champs Database
        $nb = count($fields);

        for ($i = 0; $i < count($clas); $i++) {
            if ($result = $class->$clas[$i]()) {//true si la fonction get retourne quelque chose, false autrement
                $param = $this->escape($result);
                $param1 = strtolower($fields[$i]);
                $value.= "$param1 = $param";

                if ($i !== $nb-1){ $value.=","; }
            }
        }
        $this->_request.= " $value";
        foreach (get_class_methods($class) as $classe) { //on remet à zéro les variables de la class
            if (stristr($classe, 'set')) {
                $class->$classe(null);
                $nb++;
            }
        }
    }

    public function delete($class)
    {
        $this->_request = "DELETE FROM $class->tablename\n";

    }

    public function persist($class){

        if (!isset ($this->type)) {
            $request = $this->_request;
            $this->_request= null;
            $this->query($request);
        }

        if (isset($this->type)) {
            $table = $class->tablename;
            $field ="";
            $classSet = array();
            $classGet = array();
            $fields = array();
            $nb = 0;
            $nbb = 0;
            //On vérifie que les champs demandés existent
            foreach (get_class_methods($class) as $classe) {
                if (stristr($classe, 'getId')) {
                    $field =  explode ( 'get' , $classe);
                    $field =  strtolower($field[1]);
                }
                if (stristr($classe, 'set')) {
                    $classSet[$nb] =  $classe;
                    $fields[$nb] =  explode ( 'set' , $classe);
                    $fields[$nb] =  strtolower($fields[$nb][1]);
                    $nb++;
                }
                if (stristr($classe, 'get')) {
                    $classGet[$nbb] = $classe;
                    $nbb++;
                }
            }
            unset($classGet[count($classGet)-1]); //On supprime le champs Database du tableau

            if (!empty($field)) {
                if ($this->type === 1) {
                    $this->_request = "DELETE FROM $table WHERE id = $this->id";
                    $this->type = 0;
                    $this->id = 0;
                    $this->query($this->_request);

                }elseif($this->type === 2) {
                    $this->_request = "SELECT * FROM $table WHERE id = $this->id";
                    $this->type = 0;
                    $this->id = 0;
                    $data = $this->querySelect($this->_request);
                    foreach ($classSet as $item) {
                        $param =  explode ( 'set' , $item);
                        $param =  strtolower($param[1]);
                        $class->$item($data[0][$param]);
                    }

                }elseif($this->type === 3) {
                    $request = "SET ";
                    $fields = array();
                    $nb = 0;
                    $nbTotal = 0;
                    foreach ($classGet as $item) {
                        if (!empty($class->$item())) {
                            $nbTotal++;
                            $fields[$nb] =  explode ( 'get' , $item);
                            $fields[$nb] =  strtolower($fields[$nb][1]);
                            $nb++;
                        }
                    }
                    $nb = 0;
                    foreach ($fields as $item) {
                        $theclass = 'get'.ucfirst($item);
                        if (!empty($class->$theclass())) { //get function
                            $params = $this->escape($class->$theclass());
                            $request.= "$fields[$nb] = $params";
                            if ($nb < $nbTotal-1) { $request.=', ';  $nb++;}
                        }
                    }
                    $this->_request = "UPDATE $table $request WHERE id = $this->id";
                    $this->type = 0;
                    $this->id = 0;
                    $this->query($this->_request);
                }
            }

        }
    }

    public function exec()
    {
        $request = $this->_request;
        $this->_request= null;
        return $this->querySelect($request);
    }

    public function printRequest($query){
        $request = $query;
        echo('<div class="alert alert-info">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Requête !</strong> '.$request.'. <strong>'.$this->countRows().'</strong> résultats
            </div>
        ');
    }

    public function printError($error){

        echo('
            <div class="alert alert-danger">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Erreur !</strong> '.$error.'
            </div>


        ');
    }

    public function deleteById($id)
    {
        $this->id = $id;
        $this->type = 1;
    }

    public function getById($id)
    {
        $this->id = $id;
        $this->type = 2;
    }

    public function setById($id)
    {
        $this->id = $id;
        $this->type = 3;
    }

    public function escape($string) { // Met la string entre guillemet et la protège -- addslashes
        if (!is_string($string)) {
            return $string;
        }
        $string = addslashes($string);
        $string = "'$string'";
        return $string;
    }

}