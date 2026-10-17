<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';
date_default_timezone_set('America/Sao_Paulo');

$dia = $_GET['dia'] ?? date('Y-m-d');

$stmt = $conn->prepare(
    "SELECT a.id, a.horario, c.nome, c.sobrenome, c.telefone,
            s.nome AS servico, p.nome AS profissional, st.nome AS status
     FROM agendamento a
     JOIN clientes c ON c.id = a.clientes_id
     JOIN servicos s ON s.id = a.servicos_id
     JOIN profissionais p ON p.id = a.profissionais_id
     JOIN status_agendamento st ON st.id = a.status_id
     WHERE a.dia = ?
     ORDER BY a.horario"
);
$stmt->execute([$dia]);
$agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Painel - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento painel">
      <h1>Agendamentos</h1>

      <form method="GET" class="filtro-dia">
        <input type="date" name="dia" value="<?= htmlspecialchars($dia) ?>">
        <button type="submit" class="btn-agendar">Ver</button>
      </form>

      <?php if (count($agendamentos) === 0): ?>
        <p class="subtitulo">Nenhum agendamento neste dia.</p>
      <?php else: ?>
        <table>
          <tr>
            <th>Hora</th><th>Cliente</th><th>Telefone</th>
            <th>Serviço</th><th>Profissional</th><th>Status</th><th>Ações</th>
            <a href="logout.php" class="btn-cancelar">Sair</a>
          </tr>
          <?php foreach ($agendamentos as $a): ?>
          <tr>
            <td><?= substr($a['horario'], 0, 5) ?></td>
            <td><?= htmlspecialchars($a['nome'] . ' ' . $a['sobrenome']) ?></td>
            <td><?= htmlspecialchars($a['telefone']) ?></td>
            <td><?= htmlspecialchars($a['servico']) ?></td>
            <td><?= htmlspecialchars($a['profissional']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td>
                <a class="btn-editar" href="editar.php?id=<?= $a['id'] ?>">Editar</a>
                <a class="btn-excluir" href="excluir.php?id=<?= $a['id'] ?>&dia=<?= urlencode($dia) ?>"
                   onclick="return confirm('Deseja excluir este agendamento?')">Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>