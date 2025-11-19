<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'alunos') { header('Location: ../index.html'); exit; }
require_once 'conexao.php';

$pdo = getConnection();

$aluno_id = $_SESSION['id'];

// Buscar notas por disciplina e período
$sql = "
SELECT d.nome AS disciplina, p.nome AS periodo, p.ordem, n.nota
FROM notas n
JOIN disciplinas d ON d.id = n.disciplina_id
JOIN periodos_avaliativos p ON p.id = n.periodo_avaliativo_id
JOIN matriculas m ON m.id = n.matricula_id
WHERE m.aluno_id = ?
ORDER BY d.nome, p.ordem
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$aluno_id]);
$rows = $stmt->fetchAll();

// transformar em matriz [disciplina][periodo] = nota
$boletim = [];
$periodos = [];
foreach ($rows as $r) {
    $boletim[$r['disciplina']][$r['periodo']] = $r['nota'];
    if (!in_array($r['periodo'], $periodos)) $periodos[] = $r['periodo'];
}
?>
<!doctype html><html lang="pt-BR">
  <head><meta charset="utf-8">
  <title>Boletim</title>
  <link rel="stylesheet" href="style_professor.css">
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
</head>
<body style="font-family:Arial;padding: 100px;px">
<h3>Boletim</h3>
<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr><th>Disciplina</th>
      <?php foreach($periodos as $p): ?><th><?php echo htmlspecialchars($p); ?></th><?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($boletim as $disc => $vals): ?>
      <tr>
        <td><?php echo htmlspecialchars($disc); ?></td>
        <?php foreach($periodos as $p): ?>
          <td><?php echo isset($vals[$p]) ? number_format($vals[$p],2,',','.') : '-'; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p><a href="painel_aluno.php">Voltar</a></p>
</body></html>
