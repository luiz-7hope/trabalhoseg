<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') {
    header('Location: ../index.html');
    exit;
}
require_once 'conexao.php';

$pdo = getConnection();
$prof_id = $_SESSION['id'];
$turma_id = intval($_GET['turma_id'] ?? 0);
if (!$turma_id) { header('Location: minhas_turmas.php'); exit; }

// buscar alunos matriculados
$stmt = $pdo->prepare("SELECT m.id as matricula_id, a.nome, a.id as aluno_id 
FROM matriculas m 
JOIN alunos a ON a.id = m.aluno_id 
WHERE m.turma_id = ? AND m.status = 'ativa' 
ORDER BY a.nome");
$stmt->execute([$turma_id]);
$alunos = $stmt->fetchAll();

// disciplinas do professor
$stmt = $pdo->prepare("SELECT d.id, d.nome 
FROM turmas_disciplinas td 
JOIN disciplinas d ON d.id = td.disciplina_id 
WHERE td.turma_id=? AND td.professor_id=?");
$stmt->execute([$turma_id, $prof_id]);
$disciplinas = $stmt->fetchAll();

// periodos avaliativos
$periodos = $pdo->query("SELECT * FROM periodos_avaliativos 
WHERE ano_letivo = YEAR(CURDATE()) ORDER BY ordem")->fetchAll();

if (!$periodos) {
    $periodos = $pdo->query("SELECT * FROM periodos_avaliativos 
    ORDER BY ano_letivo DESC, ordem")->fetchAll();
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Lançar Notas</title>
    <link rel="stylesheet" href="style_professor.css">

<style>
:root{
  --main:#004c6d;
  --main-light:#0a6d97;
  --bg:#e9f3f8;
  --card:#ffffff;
  --muted:#6b7280;
  --radius:14px;
  font-family: "Inter", system-ui, -apple-system,'Segoe UI',Roboto,Arial;
}

body{
  margin:0;
  padding:40px 0;
  background:var(--bg);
  display:flex;
  justify-content:center;
}

.container{
  width:100%;
  max-width:1300px;
  padding:20px;
}

.card{
  background:var(--card);
  border-radius:var(--radius);
  padding:35px;
  box-shadow:0 12px 38px rgba(0,0,0,0.12);
  border:1px solid rgba(0,76,109,0.10);
  backdrop-filter: blur(4px);
}

h3{
  margin-top:0;
  text-align:center;
  font-size:28px;
  font-weight:700;
  color:var(--main);
  letter-spacing:.5px;
}

.table-wrapper{
  margin-top:25px;
  overflow-x:auto;
}

table{
  width:100%;
  border-collapse:collapse;
  background:white;
  border-radius:12px;
  overflow:hidden;
  box-shadow:0 8px 20px rgba(0,0,0,0.07);
}

thead{
  background:linear-gradient(135deg,var(--main),var(--main-light));
  color:white;
}

thead th{
  padding:14px;
  font-size:15px;
  font-weight:600;
  text-align:center;
  letter-spacing:.3px;
}

tbody td{
  padding:16px 12px;
  border-bottom:1px solid #e5e7eb;
  vertical-align:top;
  background:white;
}

tbody tr:nth-child(even){
  background:#f3f9fc;
}

tbody tr:hover{
  background:#e6f3f8;
  transition:.2s;
}

.aluno-nome{
  font-weight:600;
  color:#0c3547;
  font-size:15px;
}

.periodo{
  margin-bottom:10px;
}

.periodo small{
  display:block;
  font-size:13px;
  color:var(--muted);
  margin-bottom:3px;
}

.periodo input{
  width:100px;
  padding:8px 10px;
  border-radius:8px;
  border:1px solid #cbd5e1;
  background:#f8fafc;
  font-size:14px;
  transition:.2s;
}

.periodo input:focus{
  outline:none;
  border-color:var(--main-light);
  box-shadow:0 0 0 3px rgba(0,76,109,0.28);
}

.btn{
  margin-top:30px;
  padding:14px 26px;
  font-size:17px;
  font-weight:700;
  color:white;
  background:linear-gradient(135deg,var(--main),var(--main-light));
  border:none;
  border-radius:10px;
  display:block;
  margin-left:auto;
  cursor:pointer;
  box-shadow:0 6px 18px rgba(0,76,109,0.35);
  transition:.2s;
}

.btn:hover{
  transform:scale(1.05);
  box-shadow:0 8px 22px rgba(0,76,109,0.42);
}

.back-link{
  display:inline-block;
  margin-top:28px;
  text-decoration:none;
  font-weight:600;
  color:var(--main);
  font-size:15px;
  transition:.3s;
}

.back-link:hover{
  color:var(--main-light);
  text-decoration:underline;
}
</style>

</head>

<body>
<div class="container">
<div class="card">

<h3>Lançar Notas — Turma <?php echo htmlspecialchars($turma_id); ?></h3>

<?php if(empty($disciplinas)): ?>
    <p>Você não tem disciplinas configuradas para esta turma. 
       <a href="configurar_turma.php?id=<?php echo $turma_id; ?>">Configurar agora</a></p>

<?php else: ?>

<form method="post" action="salvar_notas.php">
    <input type="hidden" name="turma_id" value="<?php echo $turma_id; ?>">

    <div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <?php foreach($disciplinas as $d): ?>
                    <th><?php echo htmlspecialchars($d['nome']); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach($alunos as $a): ?>
                <tr>
                    <td class="aluno-nome"><?php echo htmlspecialchars($a['nome']); ?></td>

                    <?php foreach($disciplinas as $d): ?>
                        <td>
                            <?php foreach($periodos as $p): ?>
                                <div class="periodo">
                                    <small><?php echo htmlspecialchars($p['nome']); ?></small>
                                    <input type="number" step="0.01" min="0" max="100"
                                    name="nota[<?php echo $a['matricula_id']; ?>][<?php echo $d['id']; ?>][<?php echo $p['id']; ?>]">
                                </div>
                            <?php endforeach; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <button class="btn" type="submit">Salvar Notas</button>
</form>

<?php endif; ?>

<a class="back-link" href="painel_professor.php">← Voltar</a>

</div>
</div>
</body>
</html>
