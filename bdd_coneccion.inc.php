<?php
$mysqli = new mysqli("mysql.hostinger.com.ar", "u931227509_carta", "abadia12", "u931227509_carta");
if ($mysqli->connect_errno) {
    echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
/* Switch off auto commit to allow transactions*/
$mysqli->autocommit(FALSE); 
?>