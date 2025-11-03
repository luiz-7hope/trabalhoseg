<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit;
}

$id = $_SESSION['usuario_id'];

$sql = "DELETE FROM usuarios WHERE id = '$id'";
if ($conn->query($sql) === TRUE) {
    session_unset();
    session_destroy();
    echo "<script>alert('Conta excluída com sucesso!'); window.location='index.html';</script>";
} else {
    echo "<script>alert('Erro ao excluir conta!'); window.location='painel.php';</script>";
}
?>
