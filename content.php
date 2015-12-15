<?php
/**
 * Created by PhpStorm.
 * User: Maps_red
 * Date: 10/12/2015
 * Time: 22:01
 */

if (isset($_GET['type'])) {
    if ($_GET['type'] === 'LogAccess') {
        $line = file('logs/access.log');
        $nb = 0;
        foreach ($line as $item) {
            echo "$nb --- ";
            echo $item."<br>";
            $nb++;
        }
    }
    if ($_GET['type'] === 'logError') {
        $line = file('logs/error.log');
        $nb = 0;
        foreach ($line as $item) {
            echo "$nb --- ";
            echo $item . "<br>";
            $nb++;
        }
    }
    if ($_GET['type'] === 'start') {
        require_once('testing.php');
    }
    if ($_GET['type'] === 'guide') {
        ?>
        <h2>Voici un exemple de ce que vous pouvez faire avec cet ORM</h2>
        <h3>Exemple de requête SELECT</h3>
        <br>
        <code>    $orm = new ORM();<br>
            $sql = new MysqlConnect($orm->getDatabase());
        </code>
        <h4>SELECT :</h4>
        <code>$fields = array("id, nom, prenom"); </code>
        <h5>ou encore</h5>
        <code>$fields = array("*"); </code>
        <h4>WHERE :</h4>
        <code>    $condition = array( <br>
            'where' => array('id !=' => 1),<br>
            'and' => array('name' = 'François')
            );
        </code>
        <h4>ORDER BY :</h4>
        <code>    $order = array("id" => "DESC", "ok" => "ASC");</code>
        <h4>GROUP BY :</h4>
        <code>    $groupby = array('id', "ok");</code>
        <h4>HAVING :</h4>
        <code>    $having = array("id >" => 1);
        </code>
        <h4>Jointures :</h4>
        <code>$join = "INNER JOIN";<br>
              $joinCritera = array("a.key", "b.key");
        </code>
        <br><br>
        <h4>Execution de la requête :</h4>
        <code>
            $sql->select($fields, $orm);<br>
            $sql->where($condition);<br>
            $sql->groupby($groupby);<br>
            $sql->having($having);<br>
            $sql->order($order);<br><br>
            $RESULT = $sql->exec();
        </code>
        <br>
        <span>$RESULT est un tableau contenant les résultats de la requête SELECT</span>

        <h3>Exemple de requête INSERT</h3>
        <br>
        <code>    $orm = new ORM();<br>
            $sql = new MysqlConnect($orm->getDatabase());
        </code>
        <h4>INSERT INTO</h4>
        <code>    $orm->setOk(35);<br>
            $orm->setKo(56);<br>
            $orm->setTest('dix');<br>
            $sql->insert($orm);<br>
            $sql->persist($orm);<br>
        </code>

        <h3>Exemple de requête UPDATE</h3>
        <br>
        <code>    $orm = new ORM();<br>
            $sql = new MysqlConnect($orm->getDatabase());
        </code>
        <h4>WHERE :</h4>
        <code>    $condition = array(<br>
            'where' => array(<br>
            'ok =' => 35,<br>
            'ko =' => 56,<br>
            'test LIKE' => 'dix'
            )<br>
            );
        </code>
        <h4>UPDATE :</h4>
        <code>
            $orm->setOk(25);<br>
            $orm->setKo(46);<br>
            $orm->setTest('vingt');<br>
            $sql->update($orm);<br>
            $sql->where($condition);<br>
            $sql->persist($orm);
        </code>

        <h3>Exemple de requête DELETE</h3>
        <code>    $orm = new ORM();<br>
            $sql = new MysqlConnect($orm->getDatabase());
        </code>
        <h4>WHERE : </h4>
        <code>    $condition = array(<br>
            'where' => array(<br>
            'id =' => 1
            )
        </code>
        <h4>DELETE :</h4>
        <code>    $sql->delete($orm);<br>
            $sql->where($condition);<br>
            $sql->persist($orm);
        </code>




        <?php
    }

    }else {
?>
    <div style="text-align: center">
    <h1>LAMP</h1>
    <h2>- ORM -</h2>
    </div>
    <div id="présentation">
        <h3 style="text-decoration: underline">Présentation :</h3>
        <p>Le projet ORM (Object Relational Mapper) est un projet semi-libre. Vous êtes donc libre de
            faire vos choix, tant que vous respectez les consignes ci-dessous.
            Pour rappel un ORM permet de représenter le contenu d’une base de données sous forme
            d’objets.</p>
    </div>
    <div id="consigne">
        <h3 style="text-decoration: underline">Consignes :</h3>
            Le projet doit être un ORM fonctionnel développé en PHP5
            <ul>
            <li>Il devra au minimum support MySQL</li>
            <li>Lors de la soutenance vous devrez présenter votre projet hébergé et fonctionnel sur
                un serveur Linux utilisant apache2. Ce serveur peut être chez un hébergeur ou simulé
                par une machine virtuelle locale.</li>
            <li>L’ORM doit avoir les fonctionnalités suivantes :
            <ul>
                <li>Sélection des données :<ul><li>
                            Avec les fonctionnalités de base de MySQL (conditions, jointures, tri)
                        </li></ul></li>
                <li>Édition de données sélectionnées</li>
                <li>Suppression de données</li>
                <li>Helpers : Au minimum comptage de données selectionnées et vérification de l’existence d’une donnée</li>
            </ul></li>
            <li>Seront loguées dans un fichier error.log les erreurs SQL ayant eu lieu</li>
            <li> Seront loguées dans un fichier access.log toutes les requêtes SQL effectuées</li>
            <li>Vous devrez également proposer un générateur de code (indépendant du reste du
                projet) créant un fichier PHP contenant la définition d’une classe de l’ORM. Celui-ci
                devra pouvoir être appelé directement en ligne de commande de la manière suivante :
                <ul>
                    <li>“php generator.php DBHost DBUser DBPass DBName tableName className”</li>
                    <li>“php generator.php localhost puck pass123 intranet users User” (cet exemple
                        génère donc un fichier User.php contenant la définition de la classe User.)</li>
                </ul></li>
        </ul>
        <p>Version PDF : <a href="Consigne.pdf">Consigne</a> </p>
    </div>
<?php
}





