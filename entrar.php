<?php
session_start();
require_once 'conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $senha = $_POST['senha'] ?? '';

    $stmt = $conn->prepare("SELECT id, nome, senha_hash FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    $c = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($c && $c['senha_hash'] && password_verify($senha, $c['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['cliente_id'] = $c['id'];
        $_SESSION['cliente_nome'] = $c['nome'];
        header('Location: Agendamento.php');
        exit;
    }
    $erro = 'E-mail ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Entrar - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Entrar</h1>
      <p class="subtitulo">Acesse sua conta para agendar.</p>
      <?php if ($erro): ?>
        <p class="subtitulo" style="color:#ffb4b4;"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>
      <form method="POST">
        <div class="campo"><label for="email">E-mail</label>
          <input type="email" name="email" id="email" required></div>
        <div class="campo"><label for="senha">Senha</label>
          <input type="password" name="senha" id="senha" required></div>
        <button type="submit" class="btn-agendar">Entrar</button>
        <a class="btn-cancelar" href="cadastro.php" style="margin-top:12px;">Criar conta</a>
      </form>
    </div>
  </main>
</body>
</html>