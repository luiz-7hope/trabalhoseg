<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_nome = trim($_POST['novo_nome']);
    $id = $_SESSION['usuario_id'];

    if ($novo_nome !== '') {
        $sql = "UPDATE usuarios SET nome = '$novo_nome' WHERE id = '$id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['usuario_nome'] = $novo_nome;
            echo "<script>alert('Nome atualizado com sucesso!'); window.location='painel.php';</script>";
        } else {
            echo "<script>alert('Erro ao atualizar nome!'); window.location='painel.php';</script>";
        }
    } else {
        echo "<script>alert('O nome não pode estar vazio!'); window.location='painel.php';</script>";
    }
}
?>
