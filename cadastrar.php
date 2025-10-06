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
            echo "<script>
                    alert('❌ Preencha todos os campos.');
                    window.history.back();
                  </script>";
            exit;
        }

        // Verifica se já existe um usuário com o mesmo email/cpf
        $verifica = $conn->prepare("SELECT id FROM alunos WHERE email_cpf = ?");
        $verifica->bind_param("s", $emailCpf);
        $verifica->execute();
        $verifica->store_result();

        if ($verifica->num_rows > 0) {
            echo "<script>
                    alert('❌ Já existe uma conta com esse e-mail ou CPF.');
                    window.location.href = 'criar-conta.html';
                  </script>";
        } else {
            // Insere o novo usuário
            $stmt = $conn->prepare("INSERT INTO alunos (nome, email_cpf, senha) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nome, $emailCpf, $senha);

            if ($stmt->execute()) {
                // Cadastro OK → mostra pop-up e redireciona
                echo "<script>
                        if (confirm('✅ Conta criada com sucesso!\\nDeseja voltar para o login?')) {
                            window.location.href = 'index.html';
                        } else {
                            window.location.href = 'criar-conta.html';
                        }
                      </script>";
            } else {
                echo "<script>
                        alert('❌ Erro ao cadastrar: " . addslashes($stmt->error) . "');
                        window.history.back();
                      </script>";
            }

            $stmt->close();
        }

        $verifica->close();
        $conn->close();

    } else {
        echo "<script>
                alert('❌ Dados do formulário incompletos.');
                window.history.back();
              </script>";
    }

} else {
    echo "<script>
            alert('❌ Requisição inválida.');
            window.history.back();
          </script>";
}
?>
