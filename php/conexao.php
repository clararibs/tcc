<?php
    //parãmetros de conexão com BD
    define('HOST', '3.150.114.68');//define o nome do servidor
    define('USER', 'tcc_hedone');; //nome do usuário
    define('PASSWORD', '1hN^83}'); //define a senha de acesso ao BD
    define('DB', 'hedone_db'); //define o nome do Bando de Dados

     //parãmetros de conexão com BD
    //  define('HOST', 'localhost:3307');//define o nome do servidor
    //  define('USER', 'root');; //nome do usuário
    //  define('PASSWORD', ''); //define a senha de acesso ao BD
    //  define('DB', 'hedone_db'); //define o nome do Bando de Dados

    //criar um objeto de conexão
    $conn = new mysqli(HOST, USER, PASSWORD, DB);

    //checar a conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }
?>