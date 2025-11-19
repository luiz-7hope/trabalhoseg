<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') { 
    header('Location: ../index.html'); 
    exit; 
}

require_once 'conexao.php';

$pdo = getConnection();
$professor_id = $_SESSION['id'];

$stmt = $pdo->prepare("
    SELECT t.*,
    (SELECT COUNT(*) FROM matriculas m WHERE m.turma_id = t.id AND m.status='ativa') AS total_alunos
    FROM turmas t
    JOIN turmas_disciplinas td ON td.turma_id = t.id AND td.professor_id = :pid
    GROUP BY t.id
");
$stmt->execute(['pid'=>$professor_id]);
$turmas = $stmt->fetchAll();

$nome_professor = $_SESSION['nome'] ?? 'Professor';
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minhas Turmas - GES</title>
<link rel="stylesheet" href="style_professor.css">


</head>

<body>
<div class="layout">

    <!-- MENU LATERAL -->
    <aside class="sidebar">
        <div class="logo">GES<br><span style="font-size:14px;">Painel do Professor</span></div>
        <nav>
            <a href="painel_professor.php">📢 Comunicados</a>
            <a class="active" href="minhas_turmas.php">📊 Minhas Turmas</a>
            <a href="logoout.php">Sair</a>
        </nav>
    </aside>

    <!-- CONTEÚDO -->
    <main class="content">

        <header class="topbar">
            <div class="user-info">
                <strong><?php echo htmlspecialchars($nome_professor); ?></strong>
                <small>Professor</small>
            </div>
        </header>

        <h2>📊 Minhas Turmas</h2>

        <?php if($turmas): ?>
        <div class="turmas-grid">

            <?php foreach($turmas as $t): ?>
            <div class="turma-card">

                <strong><?= htmlspecialchars($t['nome']); ?></strong>
                <div class="info">
                    <?= htmlspecialchars($t['serie']); ?>
                    — <?= $t['ano_letivo']; ?><br>
                    Alunos Ativos: <b><?= $t['total_alunos']; ?></b>
                </div>

                <div class="turma-actions">
                    <a class="btn-lancar" 
                       href="lancar_notas.php?turma_id=<?= $t['id']; ?>">
                       Lançar Notas
                    </a>

                    <a class="btn-config" 
                       href="configurar_turma.php?id=<?= $t['id']; ?>">
                       Configurar
                    </a>
                </div>

            </div>
            <?php endforeach; ?>

        </div>

        <?php else: ?>

            <div class="turma-card">
                <p>Nenhuma turma encontrada. Verifique associações em <b>turmas_disciplinas</b>.</p>
            </div>

        <?php endif; ?>

        <a class="back" href="painel_professor.php">← Voltar ao Painel</a>

    </main>
</div>
</body>
</html>
