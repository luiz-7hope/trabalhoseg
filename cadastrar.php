<?php
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $emailCpf = trim($_POST['emailCpf']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $tipo = $_POST['tipoUsuario'];

    if (!in_array($tipo, ['alunos', 'pais', 'professores'])) {
        die('Tipo de usuário inválido');
    }

    $check = $pdo->prepare("SELECT * FROM $tipo WHERE emailCpf = ?");
    $check->execute([$emailCpf]);
    if ($check->rowCount() > 0) {
        echo '<script>alert("Usuário já cadastrado!");window.location="criar-conta.html";</script>';
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO $tipo (nome, emailCpf, senha) VALUES (?, ?, ?)");
    if ($stmt->execute([$nome, $emailCpf, $senha])) {
        echo '<script>alert("Cadastro realizado com sucesso!");window.location="index.html";</script>';
    } else {
        echo '<script>alert("Erro ao cadastrar!");window.location="criar-conta.html";</script>';
    }
}
?>