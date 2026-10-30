<?php
session_start();
require_once 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');

if (!isset($_SESSION['cliente_id'])) {
    header('Location: entrar.php');
    exit;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$stmt = $conn->prepare(
    "SELECT a.id, a.dia, a.horario, a.status_id,
            s.nome AS servico, s.preco, p.nome AS profissional, st.nome AS status
     FROM agendamento a
     JOIN servicos s ON s.id = a.servicos_id
     JOIN profissionais p ON p.id = a.profissionais_id
     JOIN status_agendamento st ON st.id = a.status_id
     WHERE a.clientes_id = ?
     ORDER BY a.dia DESC, a.horario DESC"
);
$stmt->execute([$_SESSION['cliente_id']]);
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

$classes = [1 => 'agendado', 2 => 'concluido', 3 => 'cancelado'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Meus agendamentos - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento">
      <h1>Meus agendamentos</h1>
      <p class="subtitulo">
        Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?> ·
        <a href="Agendamento.php">Novo agendamento</a> ·
        <a href="sair.php">Sair</a>
      </p>

      <?php if (($_GET['msg'] ?? '') === 'cancelado'): ?>
        <p class="aviso-ok">Agendamento cancelado.</p>
      <?php elseif (($_GET['msg'] ?? '') === 'erro'): ?>
        <p class="aviso-erro">Não foi possível cancelar esse agendamento.</p>
      <?php endif; ?>

      <?php if (count($lista) === 0): ?>
        <p class="subtitulo">Você ainda não tem agendamentos.</p>
      <?php else: ?>
        <ul class="agendamentos-lista">
          <?php foreach ($lista as $a):
              $quando = strtotime($a['dia'] . ' ' . substr($a['horario'], 0, 5));
              $pode   = ($a['status_id'] == 1 && $quando > time());
              $valor  = $a['preco'] > 0 ? 'R$ ' . number_format($a['preco'], 2, ',', '.') : 'A combinar';
          ?>
          <li class="item-agendamento">
            <h3><?= htmlspecialchars($a['servico']) ?></h3>
            <p><?= date('d/m/Y', strtotime($a['dia'])) ?> às <?= substr($a['horario'], 0, 5) ?>
               · com <?= htmlspecialchars($a['profissional']) ?></p>
            <p>Valor: <?= $valor ?></p>
            <span class="status status-<?= $classes[$a['status_id']] ?? 'agendado' ?>">
              <?= htmlspecialchars($a['status']) ?>
            </span>
            <?php if ($pode): ?>
              <form method="POST" action="cancelar_agendamento.php"
                    onsubmit="return confirm('Cancelar este agendamento?');">
                <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <button type="submit" class="btn-cancelar-ag">Cancelar agendamento</button>
              </form>
            <?php endif; ?>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>