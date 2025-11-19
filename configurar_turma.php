<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') { header('Location: ../index.html'); exit; }
require_once 'conexao.php';

$pdo = getConnection();
$professor_id = $_SESSION['id'];
$turma_id = intval($_GET['id'] ?? 0);
if (!$turma_id) { header('Location: minhas_turmas.php'); exit; }

$disciplinas = $pdo->query("SELECT * FROM disciplinas ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selecionadas = $_POST['disciplinas'] ?? [];

    // Remover registros deste professor e turma
    $pdo->prepare("DELETE FROM turmas_disciplinas WHERE turma_id=? AND professor_id=?")
        ->execute([$turma_id, $professor_id]);

    // Inserir novos
    $ins = $pdo->prepare("INSERT INTO turmas_disciplinas (turma_id, disciplina_id, professor_id) VALUES (?,?,?)");
    foreach ($selecionadas as $did) {
        $ins->execute([$turma_id, intval($did), $professor_id]);
    }
    header("Location: minhas_turmas.php");
    exit;
}

// disciplinas já associadas
$assoc = $pdo->prepare("SELECT disciplina_id FROM turmas_disciplinas WHERE turma_id=? AND professor_id=?");
$assoc->execute([$turma_id, $professor_id]);
$disc_ass = array_column($assoc->fetchAll(), 'disciplina_id');
?>
<!doctype html><html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Configurar Turma</title>
<style>
body {
    background: #eef5fa;
    font-family: Arial;
    padding: 40px;
}

h3 {
    color: #1a237e;
    font-size: 30px;
    margin-bottom: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    overflow: hidden;
}

thead th {
    background: #1565c0;
    color: white;
    padding: 14px;
    font-size: 16px;
}

tbody tr:nth-child(even) {
    background: #f4f8ff;
}

tbody td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
    background: #e8f0ff;
}

a {
    padding: 10px 18px;
    background: #1565c0;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}

a:hover {
    background: #0d47a1;
}
</style>
</head><body style="font-family:Arial;padding:20px">
<h3>Configurar Turma</h3>
<form method="post">
  <p>Marque as disciplinas que você leciona nesta turma:</p>
  <?php foreach($disciplinas as $d): ?>
    <label style="display:block;margin-bottom:6px">
      <input type="checkbox" name="disciplinas[]" value="<?php echo $d['id']; ?>"
             <?php if(in_array($d['id'],$disc_ass)) echo 'checked'; ?>>
      <?php echo htmlspecialchars($d['nome']); ?>
    </label>
  <?php endforeach; ?>
  <button type="submit">Salvar</button>
</form>
<p><a href="minhas_turmas.php">Voltar</a></p>
</body></html>
