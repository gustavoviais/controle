<?php

header('Content-Type: text/html; charset=utf-8');

$host = "localhost";//caso esteja usando o xampp ou wamp

$user = "root";// padrão para xampp ou wamp é root

$pass = "Efz4641O";// padrão para xampp ou wamp é ""

$db = "controle";// é o nome do banco que vc criou no phpmyadmin

$conn = mysqli_connect($host, $user, $pass, $db) or die (mysql_error());

//mysqli_set_charset($conn,"utf8");
$conn->set_charset("utf8");
$conn->query('SET NAMES utf8');

?>