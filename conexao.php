<?php
define('HOST', 'localhost:3307');
define('USER', 'ROOT');
define('PASSWORD', '');
define('DB', 'bdpessoa');
$conn = new mysqli (HOST, USER, PASSWORD, DB);
if($conn --> connect_error){
    die("falha na conexão:" .$conn->connect_error);

}
//echo "conexão realizada com sucesso"
?>