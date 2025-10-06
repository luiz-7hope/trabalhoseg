<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Painel do Aluno</title>
</head>
<body>
  <h1>Bem-vindo ao Painel do Aluno</h1>
  <p>Você está logado com sucesso!</p>
  <a href="logout.php">Sair</a>
</body>
</html>
