<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'professores') { header('Location: ../index.html'); exit; }
require_once 'conexao.php';

$pdo = getConnection();
$prof_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: minhas_turmas.php'); exit; }

$turma_id = intval($_POST['turma_id'] ?? 0);
$notas = $_POST['nota'] ?? [];

foreach ($notas as $matricula_id => $discs) {
    foreach ($discs as $disc_id => $periodos) {
        foreach ($periodos as $periodo_id => $valor) {
            // normalizar
            $valor = $valor === '' ? null : (float) str_replace(',', '.', $valor);
            if ($valor === null) continue;

            $sql = "INSERT INTO notas (matricula_id, disciplina_id, periodo_avaliativo_id, nota, professor_id)
                    VALUES (?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE nota = VALUES(nota), professor_id = VALUES(professor_id), data_lancamento = CURRENT_TIMESTAMP";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$matricula_id, $disc_id, $periodo_id, $valor, $prof_id]);
        }
    }
}

header("Location: lancar_notas.php?turma_id={$turma_id}&msg=ok");
exit;
