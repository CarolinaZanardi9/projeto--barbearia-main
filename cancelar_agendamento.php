<?php
session_start();
require_once 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['cliente_id'])) {
    header('Location: entrar.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST'
    || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    header('Location: meus_agendamentos.php?msg=erro');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

try {
    // O agendamento precisa ser DESTE cliente
    $stmt = $conn->prepare(
        "SELECT dia, horario, status_id FROM agendamento WHERE id = ? AND clientes_id = ?"
    );
    $stmt->execute([$id, $_SESSION['cliente_id']]);
    $a = $stmt->fetch(PDO::FETCH_ASSOC);

    $quando = $a ? strtotime($a['dia'] . ' ' . substr($a['horario'], 0, 5)) : 0;

    if (!$a || $a['status_id'] != 1 || $quando <= time()) {
        header('Location: meus_agendamentos.php?msg=erro');
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE agendamento SET status_id = 3 WHERE id = ? AND clientes_id = ?"
    );
    $stmt->execute([$id, $_SESSION['cliente_id']]);

    header('Location: meus_agendamentos.php?msg=cancelado');
    exit;
} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: meus_agendamentos.php?msg=erro');
    exit;
}