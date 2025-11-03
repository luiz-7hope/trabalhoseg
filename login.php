<?php
session_start();
require_once 'conexao.php'; // deve definir $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailCpf = trim($_POST['emailCpf']);
    $senha = $_POST['password'];
    $tipo = $_POST['userType'];

    if (!in_array($tipo, ['alunos', 'pais', 'professores'])) {
        echo "<script>alert('Tipo de usuário inválido!');window.location='index.html';</script>";
        exit;
    }

    // Consulta o banco
    $stmt = $pdo->prepare("SELECT * FROM $tipo WHERE emailCpf = ?");
    $stmt->execute([$emailCpf]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica senha (ajuste conforme seu banco)
    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario'] = $user['nome'];
        $_SESSION['tipo'] = $tipo;
        $_SESSION['logado'] = true;

        // Redireciona conforme o tipo
        switch ($tipo) {
            case 'alunos':
                header('Location: painel_aluno.php');
                break;
            case 'pais':
                header('Location: painel_pais.php');
                break;
            case 'professores':
                header('Location: painel_professor.php');
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
