<?php

$servername = "3.150.114.68";
$username = "tcc_hedone";
$password = "1hN^83}";
$dbnome = "hedone_db"

$conn = new mysqli($servername, $username, $password, $dbnome);

if($conn --> connect_error){
    die("Falha na conexão: ". $conn->connect_error);
}
?>