<?php
require_once('ORM/MysqlConnect.php');
require_once('autoload.php');
use ORM\MysqlConnect;

$sql_serveur = $argv[1];
$sql_utilisateur = $argv[2];
$sql_password = $argv[3];
$sql_bd = $argv[4];
$tableName = $argv[5];
$className = $argv[6];

$filename = $className.'.php';

//php generator.php 5.135.191.187 game_test 90sqPDD1b5 game_test actions Game
//php generator.php 5.135.191.187 maps_red 9aEuULSDQtyJMbK2 orm action ORM
// update($table, array $data, $where=""

$database = array($sql_serveur, $sql_utilisateur, $sql_password, $sql_bd);
$mysql = new MysqlConnect($database);

$requete = "SHOW COLUMNS FROM $tableName";

$columns = $mysql->querySelect($requete);
$nb = 0;
foreach ($columns as $column) {
    $data = $columns;
    $nb++;
}

$gen = NULL;
$gen .= "<?php \n";
$gen.= "namespace Model\\Database;";
$gen.= "\t\n class $className { \n";
$gen.= "\tprotected ".'$sql_serveur'."= '$sql_serveur';\n";
$gen.= "\tprotected ".'$sql_utilisateur'."= '$sql_utilisateur';\n";
$gen.= "\tprotected ".'$sql_password'."= '$sql_password';\n";
$gen.= "\tprotected ".'$sql_bd'."= '$sql_bd';\n";
$gen.= "\tpublic ".'$tablename'."= '$tableName';\n";


foreach ($data as $item) {
    $gen.="\tprivate $$item[0];\n";
}
$gen.= "\tprotected ".'$database;'."\n\n";

$gen.="\tfunction __construct() {\n";
$gen.="\t\t".'$this->database = array($this->sql_serveur, $this->sql_utilisateur, $this->sql_password, $this->sql_bd);';
$gen.= "\n\t}";

foreach ($data as $item) {
    $maj = ucfirst($item[0]);
    $gen.="\n\tpublic function set$maj(".'$item'.") { \n";
    $gen.= "\t\t".'$this->'."$item[0] = ".'$item;';
    $gen.= "\n\t}";
}

foreach ($data as $item) {
    $maj = ucfirst($item[0]);
    $gen.="\n\tpublic function get$maj() { \n";
    $gen.= "\t\t".'return $this->'."$item[0];";
    $gen.= "\n\t}";
}


$gen.="\n\tpublic function getDatabase() {";
$gen.="\n\t\treturn ".'$this->database;';
$gen.="\n\t}\n";


$gen.= "\n }";


$debug = 0;

if (isset($debug) && $debug == 1){
    file_put_contents('logs/generator.log', $gen);
}else {
    file_put_contents('Model/Database/'.$filename, $gen);
}

//exec('perm.sh');
exec('chown -R supinternet:supinternet *');


