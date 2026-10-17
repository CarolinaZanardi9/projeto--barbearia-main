<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';

$id  = (int) ($_GET['id'] ?? 0);
$dia = $_GET['dia'] ?? date('Y-m-d');

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM agendamento WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: painel.php?dia=' . urlencode($dia));
exit;