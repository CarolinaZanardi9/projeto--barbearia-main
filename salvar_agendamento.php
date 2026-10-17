<?php
require_once 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');

function mensagem($texto) {
    echo "<!DOCTYPE html><html lang='pt-br'><head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <link rel='stylesheet' href='style.css'>
          <title>Agendamento Old Fellas</title></head><body>
          <main class='pagina-agendamento'>
          <div class='form-agendamento mensagem-agendamento'>
          <p>" . htmlspecialchars($texto) . "</p>
          <a class='btn-agendar' href='Agendamento.php'>Voltar</a>
          </div></main></body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Agendamento.php');
    exit;
}

// 1. Receber os dados do formulário
$nome            = trim($_POST['nome'] ?? '');
$sobrenome       = trim($_POST['sobrenome'] ?? '');
$telefone        = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
$email           = trim($_POST['email'] ?? '');
$servico_id      = (int) ($_POST['servico_id'] ?? 0);
$dia             = $_POST['dia'] ?? '';
$horario         = $_POST['horario'] ?? '';

if ($email === '') {
    $email = null;
}

// 2. Validar (nunca confie só no navegador)
if ($nome === '' || $sobrenome === '' || $telefone === '') {
    mensagem('Preencha nome, sobrenome e telefone.');
}
if ($servico_id <= 0) {
    mensagem('Escolha o serviço.');
}
$data = DateTime::createFromFormat('Y-m-d', $dia);
if (!$data) {
    mensagem('Data inválida.');
}
if ($dia < date('Y-m-d')) {
    mensagem('Não é possível agendar em uma data que já passou.');
}
if ($data->format('N') == 7) {
    mensagem('Não funcionamos aos domingos.');
}
if ($horario < '08:55' || $horario > '21:30') {
    mensagem('Horário fora do funcionamento (08:55 às 22:00).');
}

try {

    $stmt = $conn->prepare("SELECT profissionais_id FROM servicos WHERE id = ?");
    $stmt->execute([$servico_id]);
    $profissional_id = $stmt->fetchColumn();
    if (!$profissional_id) {
    mensagem('Serviço inválido.');
    }
    $conn->beginTransaction();

    // 3. O cliente já existe? (procura pelo telefone)
    $stmt = $conn->prepare("SELECT id FROM clientes WHERE telefone = ?");
    $stmt->execute([$telefone]);
    $cliente_id = $stmt->fetchColumn();

    if (!$cliente_id) {
        $stmt = $conn->prepare(
            "INSERT INTO clientes (nome, sobrenome, email, telefone) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$nome, $sobrenome, $email, $telefone]);
        $cliente_id = $conn->lastInsertId();
    }

    // 4. O horário já está ocupado para esse profissional?
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM agendamento
         WHERE profissionais_id = ? AND dia = ? AND horario = ? AND status_id <> 3"
    );
    $stmt->execute([$profissional_id, $dia, $horario]);
    if ($stmt->fetchColumn() > 0) {
        $conn->rollBack();
        mensagem('Esse horário já está ocupado. Escolha outro.');
    }

    // 5. Gravar o agendamento (status 1 = Agendado)
    $stmt = $conn->prepare(
    "INSERT INTO agendamento (clientes_id, servicos_id, profissionais_id, status_id, dia, horario)
    VALUES (?, ?, ?, 1, ?, ?)"
    );
    $stmt->execute([$cliente_id, $servico_id, $profissional_id, $dia, $horario]);

    $conn->commit();
    mensagem('Agendamento realizado com sucesso!');

}   catch (PDOException $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    mensagem('Erro ao salvar: ' . $e->getMessage());
}