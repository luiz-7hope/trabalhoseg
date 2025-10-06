<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['emailCpf'], $_POST['password'], $_POST['userType'])) {

        $emailCpf = trim($_POST['emailCpf']);
        $senha = $_POST['password'];
        $userType = $_POST['userType']; // aluno, professor ou pais

        // Define a tabela conforme o tipo de usuário
        switch ($userType) {
            case 'aluno':
                $tabela = 'alunos';
                break;
            case 'professor':
                $tabela = 'professores';
                break;
            case 'pais':
                $tabela = 'pais';
                break;
            default:
                echo "<script>
                        alert('❌ Tipo de usuário inválido.');
                        window.history.back();
                      </script>";
                exit;
        }

        // Consulta o banco pelo email/CPF
        $stmt = $conn->prepare("SELECT id, senha, nome FROM $tabela WHERE email_cpf = ?");
        $stmt->bind_param("s", $emailCpf);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashSenha, $nome);
            $stmt->fetch();

            if (password_verify($senha, $hashSenha)) {
                // Login OK → iniciar sessão
                session_start();
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $nome;
                $_SESSION['user_type'] = $userType;

                echo "<script>
                        alert('✅ Login bem-sucedido! Bem-vindo(a) $nome.');
                        window.location.href = 'portal.php';
                      </script>";
            } else {
                echo "<script>
                        alert('❌ Senha incorreta.');
                        window.history.back();
                      </script>";
            }

        } else {
            echo "<script>
                    alert('❌ Usuário não encontrado.');
                    window.history.back();
                  </script>";
        }

        $stmt->close();
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
