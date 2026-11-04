<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';

// só aceita POST com o token certo (vem do painel)
$token = $_SESSION['csrf'] ?? '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $token === ''
    || !hash_equals($token, $_POST['csrf'] ?? '')) {
    header('Location: painel.php');
    exit;
}

$id  = (int) ($_POST['id'] ?? 0);
$dia = $_POST['dia'] ?? date('Y-m-d');

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM agendamento WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: painel.php?dia=' . urlencode($dia));
exit;