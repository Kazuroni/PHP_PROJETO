<?php
$servername = '127.0.0.1';
$username = 'root';
$password = "";
$dbname = 'produtobd';

try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Banco Conectado";

    $conn->exec("CREATE DATABASE IF NOT EXISTS produtodb");

    $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $createTableUsuario = $conn->exec("CREATE TABLE IF NOT EXISTS USUARIO (
        cd_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
        nm_email VARCHAR(100) NOT NULL,
        cd_senha VARCHAR(250) NOT NULL,
        CONSTRAINT un_email UNIQUE(nm_email)
    );");
} catch (PDOException $e) {
    echo "Connection Failed: " . $e->getMessage();
}
?>