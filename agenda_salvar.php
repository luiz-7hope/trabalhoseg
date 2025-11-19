<?php
session_start();
require_once 'conexao.php';

// Aqui você coloca o ID do professor logado
$id_professor = 5;

$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$data_evento = $_POST['data_evento'];
$hora_evento = $_POST['hora_evento'];
$cor = $_POST['cor'];

$sql = "INSERT INTO agenda_professor 
        (id_professor, titulo, descricao, data_evento, hora_evento, cor)
        VALUES 
        ('$id_professor', '$titulo', '$descricao', '$data_evento', '$hora_evento', '$cor')";

if ($conn->query($sql) === TRUE) {
    header("Location: agenda_listar.php");
    exit;
} else {
    echo "Erro: " . $conn->error;
}
?>
