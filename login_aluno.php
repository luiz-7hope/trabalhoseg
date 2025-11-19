<?php
session_start();
require_once 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailCpf = trim($_POST['emailCpf']);
    $senha = $_POST['password'];
    $tipo = $_POST['userType'];

    // 🔒 Verifica se o tipo é válido
    if (!in_array($tipo, ['alunos', 'pais', 'professores'])) {
        echo "<script>alert('Tipo de usuário inválido!');window.location='index.html';</script>";
        exit;
    }

    // 🔍 Busca o usuário na tabela correspondente
    $stmt = $pdo->prepare("SELECT * FROM $tipo WHERE emailCpf = ?");
    $stmt->execute([$emailCpf]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ⚙️ Verifica senha: aceita hash ou texto puro (compatibilidade)
    $senhaCorreta = false;
    if ($user) {
        if (password_verify($senha, $user['senha'])) {
            $senhaCorreta = true;
        } elseif ($user['senha'] === $senha) {
            // Compatibilidade temporária com senhas antigas (sem hash)
            $senhaCorreta = true;
        }
    }

    if ($user && $senhaCorreta) {
        // 🟢 Cria sessão
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario'] = $user['nome'];
        $_SESSION['tipo'] = $tipo;
        $_SESSION['logado'] = true;

        // 🚀 Redireciona conforme o tipo de usuário
        switch ($tipo) {
            case 'alunos':
                header('Location: aluno_home.php');
                break;
            case 'pais':
                header('Location: pai_home.php');
                break;
            case 'professores':
                header('Location: professor_home.php');
                break;
            default:
                header('Location: index.html');
                break;
        }
        exit;
    } else {
        echo "<script>alert('Usuário ou senha incorretos!');window.location='index.html';</script>";
        exit;
    }
} else {
    header('Location: index.html');
    exit;
}
?>
