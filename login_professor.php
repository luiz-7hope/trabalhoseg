<?php
include 'conexao.php';
session_start();

$emailCpf = $_POST['emailCpf'];
$password = $_POST['password'];

$sql = $pdo->prepare("SELECT * FROM professores WHERE emailCpf = :emailCpf AND senha = :senha");
$sql->bindParam(':emailCpf', $emailCpf);
$sql->bindParam(':senha', $password);
$sql->execute();

if ($sql->rowCount() > 0) {
    $user = $sql->fetch(PDO::FETCH_ASSOC);
    $_SESSION['usuario'] = $user['nome'];
    header("Location: professor_home.php");
    exit;
} else {
    echo "<script>alert('Credenciais inválidas para professor!');history.back();</script>";
}
?>
