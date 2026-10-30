<?php
session_start();
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

function confirmacao($r, $dia, $horario, $nome) {
    $valor = ($r['preco'] > 0) ? 'R$ ' . number_format($r['preco'], 2, ',', '.') : 'A combinar';
    $data  = date('d/m/Y', strtotime($dia));
    $hora  = substr($horario, 0, 5);

    $texto = "Olá! Acabei de agendar na Old Fellas:\n"
           . "Nome: $nome\nServiço: {$r['servico']}\nProfissional: {$r['profissional']}\n"
           . "Data: $data\nHorário: $hora";
    $zap = "https://wa.me/5511976062871?text=" . rawurlencode($texto);

    echo "<!DOCTYPE html><html lang='pt-br'><head>
          <meta charset='UTF-8'>
          <meta name='viewport' content='width=device-width, initial-scale=1.0'>
          <link rel='stylesheet' href='style.css'>
          <title>Agendamento confirmado - Old Fellas</title></head><body>
          <main class='pagina-agendamento'><div class='form-agendamento'>
          <h1>Agendamento confirmado!</h1>
          <p class='subtitulo'>Esperamos você na Old Fellas, " . htmlspecialchars($nome) . ".</p>
          <ul class='resumo'>
            <li><strong>Serviço:</strong> " . htmlspecialchars($r['servico']) . "</li>
            <li><strong>Profissional:</strong> " . htmlspecialchars($r['profissional']) . "</li>
            <li><strong>Data:</strong> $data</li>
            <li><strong>Horário:</strong> $hora</li>
            <li><strong>Valor:</strong> $valor</li>
          </ul>
          <a class='btn-agendar btn-whatsapp' href='$zap' target='_blank'>Enviar no WhatsApp</a>
          <a class='btn-cancelar' href='index.html' style='margin-top:12px;'>Voltar ao site</a>
          </div></main></body></html>";
    exit;
}

// Só quem está logado agenda
if (!isset($_SESSION['cliente_id'])) {
    header('Location: entrar.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Agendamento.php');
    exit;
}

$cliente_id = (int) $_SESSION['cliente_id'];
$nome       = $_SESSION['cliente_nome'];

// 1. Receber os dados do formulário
$servico_id = (int) ($_POST['servico_id'] ?? 0);
$dia        = $_POST['dia'] ?? '';
$horario    = $_POST['horario'] ?? '';

// 2. Validar (nunca confie só no navegador)
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
    // 3. Quem executa esse serviço?
    $stmt = $conn->prepare("SELECT profissionais_id FROM servicos WHERE id = ?");
    $stmt->execute([$servico_id]);
    $profissional_id = $stmt->fetchColumn();
    if (!$profissional_id) {
        mensagem('Serviço inválido.');
    }

    // 4. O horário já está ocupado para esse profissional?
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM agendamento
         WHERE profissionais_id = ? AND dia = ? AND horario = ? AND status_id <> 3"
    );
    $stmt->execute([$profissional_id, $dia, $horario]);
    if ($stmt->fetchColumn() > 0) {
        mensagem('Esse horário já está ocupado. Escolha outro.');
    }

    // 5. Gravar o agendamento (status 1 = Agendado)
    $stmt = $conn->prepare(
        "INSERT INTO agendamento (clientes_id, servicos_id, profissionais_id, status_id, dia, horario)
         VALUES (?, ?, ?, 1, ?, ?)"
    );
    $stmt->execute([$cliente_id, $servico_id, $profissional_id, $dia, $horario]);

    // 6. Confirmação com o resumo
    $stmt = $conn->prepare(
        "SELECT s.nome AS servico, s.preco, p.nome AS profissional
         FROM servicos s
         JOIN profissionais p ON p.id = s.profissionais_id
         WHERE s.id = ?"
    );
    $stmt->execute([$servico_id]);
    confirmacao($stmt->fetch(PDO::FETCH_ASSOC), $dia, $horario, $nome);

} catch (PDOException $e) {
    error_log($e->getMessage());
    mensagem('Não foi possível concluir o agendamento. Tente novamente em instantes.');
}