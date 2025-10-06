<?php
session_start();
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emailCpf = trim($_POST["emailCpf"]);
    $senha = $_POST["password"];

    // Prevenção básica contra SQL Injection
    $stmt = $conn->prepare("SELECT id, senha FROM alunos WHERE email_cpf = ?");
    $stmt->bind_param("s", $emailCpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $senha_hash);
        $stmt->fetch();

        if (password_verify($senha, $senha_hash)) {
            // Login bem-sucedido
            $_SESSION["usuario_id"] = $id;
            header("Location: painel.php");
            exit();
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "Usuário não encontrado.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Requisição inválida.";
}
?>
