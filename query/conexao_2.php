<?php

header('Content-Type: text/html; charset=utf-8');

$host = "localhost";//caso esteja usando o xampp ou wamp

$user = "root";// padr�o para xampp ou wamp � root

$pass = "Efz4641O";// padr�o para xampp ou wamp � ""

$db = "controle";// � o nome do banco que vc criou no phpmyadmin

$conn = mysqli_connect($host, $user, $pass, $db) or die (mysql_error());

//mysqli_set_charset($conn,"utf8");
$conn->set_charset("utf8");
$conn->query('SET NAMES utf8');

?>