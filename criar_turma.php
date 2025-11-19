<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') { header('Location: ../index.html'); exit; }
require_once 'conexao.php';

$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $ano = intval($_POST['ano_letivo']);
    $serie = trim($_POST['serie']);
    $turno = $_POST['turno'];
    $capacidade = intval($_POST['capacidade']) ?: 30;

    $stmt = $pdo->prepare("INSERT INTO turmas (nome, ano_letivo, serie, turno, capacidade_maxima) VALUES (?,?,?,?,?)");
    $stmt->execute([$nome,$ano,$serie,$turno,$capacidade]);

    header('Location: painel_professor.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Criar Turma</title></head>
<body style="font-family:Arial;padding:20px">
  <h3>Criar Turma</h3>
  <form method="post">
    <label>Nome</label><br><input name="nome" required><br>
    <label>Ano letivo</label><br><input name="ano_letivo" type="number" value="<?php echo date('Y'); ?>" required><br>
    <label>Série</label><br><input name="serie" required><br>
    <label>Turno</label><br>
    <select name="turno">
      <option value="matutino">Matutino</option>
      <option value="vespertino">Vespertino</option>
      <option value="noturno">Noturno</option>
      <option value="integral">Integral</option>
    </select><br>
    <label>Capacidade</label><br><input name="capacidade" type="number" value="30"><br><br>
    <button type="submit">Criar</button>
  </form>
  <p><a href="painel_professor.php">Voltar</a></p>
</body></html>