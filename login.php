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
            // Senha incorreta
            echo "<script>
                    alert('❌ Senha incorreta, tente novamente!');
                    window.history.back(); // Volta para a tela de login
                  </script>";
        }
    } else {
        // Usuário não encontrado
        echo "<script>
                alert('⚠️ Usuário não encontrado!');
                window.history.back(); // Volta para a tela de login
              </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    // Requisição inválida
    echo "<script>
            alert('❌ Requisição inválida.');
            window.history.back();
          </script>";
}
?>
