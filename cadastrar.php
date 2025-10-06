<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Verifica se os campos esperados existem no POST
    if (isset($_POST['nome'], $_POST['emailCpf'], $_POST['senha'])) {
        
        // Recebe os dados do formulário
        $nome = trim($_POST['nome']);
        $emailCpf = trim($_POST['emailCpf']);
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        // Verifica se os campos estão preenchidos
        if (empty($nome) || empty($emailCpf) || empty($_POST['senha'])) {
            echo "<p>❌ Preencha todos os campos.</p>";
            echo "<p><a href='criar-conta.html'>Voltar</a></p>";
            exit;
        }

        // Verifica se já existe um usuário com o mesmo email/cpf
        $verifica = $conn->prepare("SELECT id FROM alunos WHERE email_cpf = ?");
        $verifica->bind_param("s", $emailCpf);
        $verifica->execute();
        $verifica->store_result();

        if ($verifica->num_rows > 0) {
            echo "<p>❌ Já existe uma conta com esse e-mail ou CPF.</p>";
            echo "<p><a href='criar-conta.html'>Tentar novamente</a></p>";
        } else {
            // Insere o novo usuário
            $stmt = $conn->prepare("INSERT INTO alunos (nome, email_cpf, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $emailCpf, $senha);

            if ($stmt->execute()) {
                echo "<p>✅ Conta criada com sucesso!</p>";
                echo "<p>Login: <strong>$emailCpf</strong></p>";
                echo "<p><a href='index.html'>Ir para login</a></p>";
            } else {
                echo "<p>❌ Erro ao cadastrar: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $verifica->close();
        $conn->close();

    } else {
        echo "<p>❌ Dados do formulário incompletos.</p>";
        echo "<p><a href='criar-conta.html'>Voltar</a></p>";
    }

} else {
    echo "<p>❌ Requisição inválida.</p>";
}
?>
