<?php
$host = "localhost"; // ou o IP do seu servidor de banco de dados
$db = "gestao_escolar"; // Nome do banco de dados
$user = "root"; // Seu usuário MySQL
$pass = "senac"; // Sua senha MySQL (deixe vazio se não houver)

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>