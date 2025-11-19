<?php
session_start();
require_once "../conexao.php";
// ID do professor logado (exemplo)
$id_professor = 5;

$sql = "SELECT * FROM agenda_professor WHERE id_professor = $id_professor ORDER BY data_evento ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Agenda do Professor</title>
</head>
<body>

<h1>Agenda</h1>
<a href="agenda_adicionar.php">+ Novo Evento</a>
<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Título</th>
        <th>Data</th>
        <th>Hora</th>
        <th>Ações</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['titulo'] ?></td>
            <td><?= $row['data_evento'] ?></td>
            <td><?= $row['hora_evento'] ?></td>
            <td>
                <a href="agenda_excluir.php?id=<?= $row['id'] ?>">Excluir</a>
            </td>
        </tr>
    <?php } ?>

</table>

</body>
</html>