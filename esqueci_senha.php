<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

require 'conexao.php';
$pdo = $conn;

$baseUrl = 'https://detail-herbs-imminent.ngrok-free.dev'; // troque pelo link do ngrok se for usar online
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    $stmt = $pdo->prepare('SELECT id FROM clientes WHERE email = ?');
    $stmt->execute([$email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        $token = bin2hex(random_bytes(32));
        $hash  = hash('sha256', $token);

        $pdo->prepare('DELETE FROM redefinicao_senha WHERE clientes_id = ?')
            ->execute([$cliente['id']]);

        $pdo->prepare('INSERT INTO redefinicao_senha (clientes_id, token_hash, expira_em)
                       VALUES (?, ?, DATEADD(MINUTE, 30, GETDATE()))')
            ->execute([$cliente['id'], $hash]);

        $link = $baseUrl . '/redefinir_senha.php?token=' . $token;

        // Envio do e-mail
        $cfg  = require 'config_email.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['usuario'];
            $mail->Password   = $cfg['senha'];
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($cfg['usuario'], 'Old Fellas');
            $mail->addAddress($email);
            $mail->Subject = 'Redefinir sua senha - Old Fellas';
            $mail->Body    = "Clique no link para criar uma nova senha (vale por 30 minutos):\n\n$link";
            $mail->send();
        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail: ' . $mail->ErrorInfo);
            error_log('Link de redefinição: ' . $link); // plano B: o link aparece no terminal
        }
    }

    $msg = 'Se o e-mail estiver cadastrado, enviaremos as instruções.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Esqueci a senha - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Esqueci a senha</h1>
      <p class="subtitulo">Informe o e-mail da sua conta.</p>
      <?php if ($msg): ?>
        <p class="subtitulo"><?= htmlspecialchars($msg) ?></p>
      <?php endif; ?>
      <form method="POST">
        <div class="campo"><label for="email">E-mail</label>
          <input type="email" name="email" id="email" required></div>
        <button type="submit" class="btn-agendar">Enviar</button>
        <a class="btn-cancelar" href="entrar.php" style="margin-top:12px;">Voltar ao login</a>
      </form>
    </div>
  </main>
</body>
</html>