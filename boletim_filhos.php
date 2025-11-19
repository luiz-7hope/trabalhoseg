<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'pais') {
    header('Location: index.html');
    exit;
}

require_once 'conexao.php';

$nome = $_SESSION['nome'];
$pai_id = $_SESSION['id'];
$pdo = getConnection();

/* ========================================================
    BUSCAR FILHOS DO RESPONSÁVEL
======================================================== */
$sql_filhos = "
    SELECT 
        a.id as aluno_id,
        a.nome as aluno_nome,
        m.id as matricula_id,
        m.numero_matricula,
        t.nome as turma_nome,
        t.serie,
        t.ano_letivo
    FROM matriculas m
    INNER JOIN alunos a ON m.aluno_id = a.id
    INNER JOIN turmas t ON m.turma_id = t.id
    WHERE m.pai_responsavel_id = :pai_id AND m.status = 'ativa'
    ORDER BY a.nome
";
$stmt_filhos = $pdo->prepare($sql_filhos);
$stmt_filhos->execute(['pai_id' => $pai_id]);
$filhos = $stmt_filhos->fetchAll();

$aluno_selecionado = $_GET['aluno_id'] ?? ($filhos[0]['aluno_id'] ?? null);

if (!$aluno_selecionado) {
    die("Nenhum filho encontrado.");
}

/* ========================================================
    FILHO ATUAL
======================================================== */
$filho_atual = null;
foreach ($filhos as $f) {
    if ($f['aluno_id'] == $aluno_selecionado) {
        $filho_atual = $f;
        break;
    }
}

if (!$filho_atual) {
    die("Aluno não encontrado ou não vinculado a você.");
}

/* ========================================================
    PERÍODOS AVALIATIVOS
======================================================== */
$sql_periodos = "SELECT * FROM periodos_avaliativos WHERE ano_letivo = :ano ORDER BY ordem";
$stmt_periodos = $pdo->prepare($sql_periodos);
$stmt_periodos->execute(['ano' => $filho_atual['ano_letivo']]);
$periodos = $stmt_periodos->fetchAll();

/* ========================================================
    NOTAS DO BOLETIM (CROSS JOIN EVITA FALTAR PERÍODOS)
======================================================== */
$sql_notas = "
    SELECT 
        d.id as disciplina_id,
        d.nome as disciplina_nome,
        d.codigo as disciplina_codigo,
        pa.id as periodo_id,
        pa.nome as periodo_nome,
        pa.ordem as periodo_ordem,
        n.nota,
        n.faltas,
        n.observacoes,
        p.nome as professor_nome
    FROM disciplinas d
    CROSS JOIN periodos_avaliativos pa
    LEFT JOIN notas n ON d.id = n.disciplina_id 
        AND n.periodo_avaliativo_id = pa.id 
        AND n.matricula_id = :matricula_id
    LEFT JOIN professores p ON n.professor_id = p.id
    WHERE pa.ano_letivo = :ano
    ORDER BY d.nome, pa.ordem
";
$stmt_notas = $pdo->prepare($sql_notas);
$stmt_notas->execute([
    'matricula_id' => $filho_atual['matricula_id'],
    'ano' => $filho_atual['ano_letivo']
]);
$todas_notas = $stmt_notas->fetchAll();

/* ========================================================
    ORGANIZAR DADOS DO BOLETIM
======================================================== */
$boletim = [];
$total_faltas = 0;
$soma_medias = 0;
$count_disciplinas = 0;

foreach ($todas_notas as $nota) {
    $disc_id = $nota['disciplina_id'];

    if (!isset($boletim[$disc_id])) {
        $boletim[$disc_id] = [
            'nome'   => $nota['disciplina_nome'],
            'codigo' => $nota['disciplina_codigo'],
            'periodos' => [],
            'notas' => [],
            'faltas_total' => 0,
            'media' => null
        ];
    }

    $boletim[$disc_id]['periodos'][$nota['periodo_ordem']] = [
        'nome' => $nota['periodo_nome'],
        'nota' => $nota['nota'],
        'faltas' => $nota['faltas'],
        'observacoes' => $nota['observacoes'],
        'professor' => $nota['professor_nome']
    ];

    if ($nota['nota'] !== null) {
        $boletim[$disc_id]['notas'][] = floatval($nota['nota']);
    }

    if ($nota['faltas'] !== null) {
        $boletim[$disc_id]['faltas_total'] += intval($nota['faltas']);
        $total_faltas += intval($nota['faltas']);
    }
}

/* ========================================================
    CALCULAR MÉDIAS
======================================================== */
foreach ($boletim as $disc_id => &$disc) {
    if (count($disc['notas']) > 0) {
        $disc['media'] = array_sum($disc['notas']) / count($disc['notas']);
        $soma_medias += $disc['media'];
        $count_disciplinas++;
    } else {
        $disc['media'] = null; // garante que disciplina sem nota não exibe 0 indevidamente
    }
}

$media_geral = $count_disciplinas > 0
    ? $soma_medias / $count_disciplinas
    : null;

?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Boletim do Filho - GES</title>
<link rel="stylesheet" href="style_pais.css">
<style>
/* ——— estilos preservados ——— */
.filho-selector { background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.filho-selector select { width: 100%; padding: 12px; border: 2px solid  #004c6d; border-radius: 8px; font-size: 16px; background: white; }
.boletim-header { background: linear-gradient(135deg,  #004c6d 0%,  #004c6d 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 25px; text-align: center; }
.boletim-header h1 { margin: 0 0 10px 0; font-size: 28px; }
.boletim-info { display: flex; justify-content: center; gap: 30px; margin-top: 15px; flex-wrap: wrap; }
.boletim-info-item strong { display: block; font-size: 24px; margin-bottom: 5px; }
.boletim-table { width: 100%; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.boletim-table table { width: 100%; border-collapse: collapse; }
.boletim-table th { background:  #004c6d; color: white; padding: 15px; text-align: left; font-weight: 600; }
.boletim-table td { padding: 12px 15px; border-bottom: 1px solid #e0e0e0; }
.nota-cell { text-align: center; font-weight: bold; font-size: 16px; }
.nota-aprovado { color: #28a745; }
.nota-recuperacao { color: #ffc107; }
.nota-reprovado { color: #dc3545; }
.media-final { background: #e8f5e9 !important; font-weight: bold; font-size: 18px; }
.btn-print { background:  #004c6d; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin: 20px 0; display: inline-block; }
</style>
</head>
<body>
<div class="layout">

<!-- MENU LATERAL -->
<aside class="sidebar">
  <div class="logo">GES<br><span style="font-size:14px;">Painel dos Pais</span></div>
  <nav>
    <a href="painel_pais.php">📢 Comunicados</a>
    <a href="boletim_filhos.php" class="active">📘 Boletim dos Filhos</a>
    <a href="#">🗓️ Agenda Escolar</a>
    <a href="#">💬 Mensagens da Escola</a>
    <a href="#">⚙️ Configurações</a>
    <a href="logoout.php">Sair</a>
  </nav>
</aside>

<!-- CONTEÚDO PRINCIPAL -->
<main class="content">

<header class="topbar">
  <div class="pai-info">
    <div class="pai-foto"></div>
    <div class="pai-dados">
      <strong><?php echo htmlspecialchars($nome); ?></strong><br>
      <small>Responsável</small>
    </div>
  </div>
</header>

<!-- SELETOR DE FILHO -->
<?php if (count($filhos) > 1): ?>
<div class="filho-selector">
  <label><strong>Selecione o filho:</strong></label>
  <select onchange="window.location.href='boletim_filhos.php?aluno_id=' + this.value">
    <?php foreach ($filhos as $f): ?>
      <option value="<?php echo $f['aluno_id']; ?>"
        <?php echo ($f['aluno_id'] == $aluno_selecionado) ? 'selected' : ''; ?>>
        <?php echo htmlspecialchars($f['aluno_nome'] . ' - ' . $f['serie']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
<?php endif; ?>

<!-- CABEÇALHO -->
<div class="boletim-header">
  <h1>📋 Boletim Escolar</h1>
  <p><strong><?php echo htmlspecialchars($filho_atual['aluno_nome']); ?></strong></p>
  <p><?php echo htmlspecialchars($filho_atual['turma_nome'] . ' - ' . $filho_atual['serie']); ?></p>
  <p>Ano Letivo: <?php echo $filho_atual['ano_letivo']; ?></p>

  <div class="boletim-info">
    <div class="boletim-info-item">
      <strong>
      <?php echo $media_geral !== null ? number_format($media_geral, 2, ',', '.') : '-'; ?>
      </strong>
      <span>Média Geral</span>
    </div>

    <div class="boletim-info-item">
      <strong><?php echo $count_disciplinas; ?></strong>
      <span>Disciplinas</span>
    </div>

    <div class="boletim-info-item">
      <strong><?php echo $total_faltas; ?></strong>
      <span>Total de Faltas</span>
    </div>
  </div>
</div>

<button onclick="window.print()" class="btn-print">🖨️ Imprimir Boletim</button>

<!-- TABELA DO BOLETIM -->
<div class="boletim-table">
<table>
<thead>
<tr>
  <th>Disciplina</th>
  <?php foreach ($periodos as $p): ?>
    <th style="text-align:center;"><?php echo htmlspecialchars($p['nome']); ?></th>
  <?php endforeach; ?>
  <th style="text-align:center;">Média</th>
  <th style="text-align:center;">Faltas</th>
  <th style="text-align:center;">Situação</th>
</tr>
</thead>

<tbody>
<?php foreach ($boletim as $disc): ?>
<tr>
  <td>
    <strong><?php echo htmlspecialchars($disc['nome']); ?></strong><br>
    <small style="color:#666;"><?php echo htmlspecialchars($disc['codigo']); ?></small>
  </td>

  <!-- Notas dos bimestres -->
  <?php foreach ($periodos as $p): ?>
    <td class="nota-cell">
      <?php
      $nota = $disc['periodos'][$p['ordem']]['nota'] ?? null;
      if ($nota !== null) {
          $classe = $nota >= 7 ? 'nota-aprovado' : ($nota >= 5 ? 'nota-recuperacao' : 'nota-reprovado');
          echo '<span class="'.$classe.'">'.number_format($nota, 2, ',', '.').'</span>';
      } else {
          echo '<span style="color:#999;">-</span>';
      }
      ?>
    </td>
  <?php endforeach; ?>

  <!-- Média -->
  <td class="nota-cell media-final">
    <?php
    if ($disc['media'] !== null) {
        $classe = $disc['media'] >= 7 ? 'nota-aprovado' :
                 ($disc['media'] >= 5 ? 'nota-recuperacao' : 'nota-reprovado');

        echo '<span class="'.$classe.'">'.number_format($disc['media'], 2, ',', '.').'</span>';
    } else {
        echo '<span style="color:#999;">-</span>';
    }
    ?>
  </td>

  <!-- Faltas -->
  <td class="nota-cell"><?php echo $disc['faltas_total']; ?></td>

  <!-- Situação -->
  <td class="nota-cell">
    <?php
    if ($disc['media'] === null) {
        echo '<span style="color:#999;">-</span>';
    } elseif ($disc['media'] >= 7) {
        echo '<span class="nota-aprovado">✓ Aprovado</span>';
    } elseif ($disc['media'] >= 5) {
        echo '<span class="nota-recuperacao">⚠ Recuperação</span>';
    } else {
        echo '<span class="nota-reprovado">✗ Reprovado</span>';
    }
    ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- LEGENDA -->
<div style="margin-top:30px; padding:20px; background:white; border-radius:12px;">
  <h3>📊 Legenda</h3>
  <p><span class="nota-aprovado">●</span> <strong>Aprovado:</strong> Média maior que 7.0</p>
  <p><span class="nota-recuperacao">●</span> <strong>Recuperação:</strong> Média entre 5.0 e 6.9</p>
  <p><span class="nota-reprovado">●</span> <strong>Reprovado:</strong> Média menor que 5.0</p>
</div>

</main>
</div>
</body>
</html>
