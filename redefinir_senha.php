<?php
require 'conexao.php';
$pdo = $conn;

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$hash  = hash('sha256', $token);
$erro = '';
$sucesso = false;

$stmt = $pdo->prepare('SELECT id, clientes_id FROM redefinicao_senha
                       WHERE token_hash = ? AND expira_em > GETDATE()');
$stmt->execute([$hash]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    $erro = 'Link inválido ou expirado. Peça um novo.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova     = $_POST['nova_senha'] ?? '';
    $confirma = $_POST['confirma_senha'] ?? '';

    if (strlen($nova) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($nova !== $confirma) {
        $erro = 'A confirmação não é igual à nova senha.';
    } else {
        $pdo->prepare('UPDATE clientes SET senha_hash = ? WHERE id = ?')
            ->execute([password_hash($nova, PASSWORD_DEFAULT), $pedido['clientes_id']]);

        $pdo->prepare('DELETE FROM redefinicao_senha WHERE id = ?')
            ->execute([$pedido['id']]);

        $sucesso = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Redefinir senha - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Redefinir senha</h1>

      <?php if ($erro): ?>
        <p class="subtitulo" style="color:#ffb4b4;"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>

      <?php if ($sucesso): ?>
        <p class="subtitulo">Senha alterada com sucesso!</p>
        <a class="btn-agendar" href="entrar.php">Entrar</a>
      <?php elseif ($pedido): ?>
        <form method="POST">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
          <div class="campo"><label for="nova_senha">Nova senha</label>
            <input type="password" name="nova_senha" id="nova_senha" minlength="8" required></div>
          <div class="campo"><label for="confirma_senha">Confirmar nova senha</label>
            <input type="password" name="confirma_senha" id="confirma_senha" minlength="8" required></div>
          <button type="submit" class="btn-agendar">Salvar</button>
        </form>
      <?php else: ?>
        <a class="btn-cancelar" href="esqueci_senha.php">Pedir novo link</a>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>