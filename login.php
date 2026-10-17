<?php
session_start();
require_once 'conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    $stmt = $conn->prepare("SELECT id, nome, senha_hash FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($senha, $u['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $u['id'];
        $_SESSION['usuario_nome'] = $u['nome'];
        header('Location: painel.php');
        exit;
    }
    $erro = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Login - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Área da equipe</h1>
      <?php if ($erro): ?>
        <p class="subtitulo"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>
      <form method="POST">
        <div class="campo">
          <label for="usuario">Usuário</label>
          <input type="text" name="usuario" id="usuario" required>
        </div>
        <div class="campo">
          <label for="senha">Senha</label>
          <input type="password" name="senha" id="senha" required>
        </div>
        <button type="submit" class="btn-agendar">Entrar</button>
      </form>
    </div>
  </main>
</body>
</html>