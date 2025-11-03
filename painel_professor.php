<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') {
    header('Location: index.html');
    exit;
}

$msg = "";

if (isset($_POST["acao"]) && $_POST["acao"] === "editar") {
    $novo_nome = trim($_POST["novo_nome"] ?? "");
    if ($novo_nome) {
        $_SESSION["usuario"] = htmlspecialchars($novo_nome, ENT_QUOTES);
        $msg = "Nome atualizado com sucesso!";
    } else {
        $msg = "O nome não pode ficar vazio.";
    }
}

if (isset($_POST["acao"]) && $_POST["acao"] === "excluir") {
    session_unset();
    session_destroy();
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel do Professor</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>👨‍🏫 Painel do Professor</h1>
  <p>Bem-vindo(a), <strong><?php echo $_SESSION["usuario"]; ?></strong></p>
  <a href="logout.php" class="btn-logout">Sair</a>
</header>

<main>
  <section>
    <h2>Editar Perfil</h2>
    <p><?php echo $msg ?: "Atualize seu nome abaixo:"; ?></p>
    <form method="POST">
      <input type="hidden" name="acao" value="editar">
      <input type="text" name="novo_nome" value="<?php echo $_SESSION["usuario"]; ?>" required>
      <button class="btn btn-edit">Salvar</button>
    </form>
  </section>

  <section>
    <h2>Área do Professor</h2>
    <ul>
      <li><a href="#">Minhas turmas</a></li>
      <li><a href="#">Lançar notas</a></li>
      <li><a href="#">Mensagens dos alunos</a></li>
    </ul>
  </section>

  <section>
    <h2>Excluir Conta</h2>
    <form method="POST" onsubmit="return confirm('Excluir permanentemente esta conta?');">
      <input type="hidden" name="acao" value="excluir">
      <button class="btn btn-delete">Excluir Conta</button>
    </form>
  </section>
</main>
</body>
</html>
