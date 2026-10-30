<?php
session_start();
require_once 'conexao.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $sobrenome = trim($_POST['sobrenome'] ?? '');
    $telefone  = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $senha     = $_POST['senha'] ?? '';
    $senha2    = $_POST['senha2'] ?? '';

    if ($nome === '' || $sobrenome === '') {
        $erro = 'Preencha nome e sobrenome.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'E-mail inválido.';
    } elseif (strlen($telefone) < 10 || strlen($telefone) > 11) {
        $erro = 'Telefone inválido. Use DDD + número.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha precisa ter pelo menos 8 caracteres.';
    } elseif ($senha !== $senha2) {
        $erro = 'As senhas não conferem.';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM clientes WHERE email = ? OR telefone = ?");
            $stmt->execute([$email, $telefone]);
            if ($stmt->fetchColumn()) {
                $erro = 'Já existe um cadastro com esse e-mail ou telefone. Faça login.';
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO clientes (nome, sobrenome, email, telefone, senha_hash)
                     VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$nome, $sobrenome, $email, $telefone,
                                password_hash($senha, PASSWORD_DEFAULT)]);
                $id = $conn->lastInsertId();

                session_regenerate_id(true);
                $_SESSION['cliente_id'] = $id;
                $_SESSION['cliente_nome'] = $nome;
                header('Location: Agendamento.php');
                exit;
            }
        } catch (PDOException $e) {
            $erro = 'Não foi possível criar a conta. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Criar conta - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Criar conta</h1>
      <p class="subtitulo">Cadastre-se uma vez e agende quando quiser.</p>
      <?php if ($erro): ?>
        <p class="subtitulo" style="color:#ffb4b4;"><?= htmlspecialchars($erro) ?></p>
      <?php endif; ?>
      <form method="POST">
        <div class="linha">
          <div class="campo"><label for="nome">Nome</label>
            <input type="text" name="nome" id="nome" maxlength="50" required
                   value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>"></div>
          <div class="campo"><label for="sobrenome">Sobrenome</label>
            <input type="text" name="sobrenome" id="sobrenome" maxlength="50" required
                   value="<?= htmlspecialchars($_POST['sobrenome'] ?? '') ?>"></div>
        </div>
        <div class="campo"><label for="email">E-mail</label>
          <input type="email" name="email" id="email" maxlength="100" required
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
        <div class="campo"><label for="telefone">Telefone (com DDD)</label>
          <input type="tel" name="telefone" id="telefone" maxlength="20" required
                 value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>"></div>
        <div class="linha">
          <div class="campo"><label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" minlength="8" required></div>
          <div class="campo"><label for="senha2">Repita a senha</label>
            <input type="password" name="senha2" id="senha2" minlength="8" required></div>
        </div>
        <button type="submit" class="btn-agendar">Criar conta</button>
        <a class="btn-cancelar" href="entrar.php" style="margin-top:12px;">Já tenho conta</a>
      </form>
    </div>
  </main>
</body>
</html>