<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';

// SALVAR (quando o formulário é enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = (int) $_POST['id'];
    $dia     = $_POST['dia'];
    $horario = $_POST['horario'];
    $status  = (int) $_POST['status_id'];

    // 1. descobre o profissional deste agendamento
    $stmt = $conn->prepare("SELECT profissionais_id FROM agendamento WHERE id = ?");
    $stmt->execute([$id]);
    $profissional = $stmt->fetchColumn();

    // 2. existe OUTRO agendamento no mesmo dia, horário e profissional?
    $stmt = $conn->prepare(
        "SELECT COUNT(*) FROM agendamento
         WHERE dia = ? AND horario = ? AND profissionais_id = ? AND id <> ?"
    );
    $stmt->execute([$dia, $horario, $profissional, $id]);

    if ($stmt->fetchColumn() > 0) {
        $erro = "Esse horário já está ocupado para este profissional.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE agendamento SET dia = ?, horario = ?, status_id = ? WHERE id = ?"
        );
        $stmt->execute([$dia, $horario, $status, $id]);
        header('Location: painel.php?dia=' . urlencode($dia));
        exit;
    }
}

// BUSCAR o agendamento para preencher o formulário
$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT id, dia, horario, status_id FROM agendamento WHERE id = ?");
$stmt->execute([$id]);
$ag = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ag) {
    header('Location: painel.php');
    exit;
}

$statusLista = $conn->query("SELECT id, nome FROM status_agendamento")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>Editar - Old Fellas</title>
</head>
<body>
  <main class="pagina-agendamento">
    <div class="form-agendamento painel">
      <h1>Editar agendamento</h1>
      <?php if (!empty($erro)): ?>
        <p class="subtitulo"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="id" value="<?= $ag['id'] ?>">

        <label>Dia</label>
        <input type="date" name="dia" value="<?= htmlspecialchars($ag['dia']) ?>" required>

        <label>Horário</label>
        <input type="time" name="horario" value="<?= substr($ag['horario'], 0, 5) ?>" required>

        <label>Status</label>
        <select name="status_id">
          <?php foreach ($statusLista as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id'] == $ag['status_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="botoes-editar">       
        <button type="submit" class="btn-agendar">Salvar</button>
        <a href="painel.php?dia=<?= urlencode($ag['dia']) ?>" class="btn-cancelar">Cancelar</a>
        </div> 
      </form>
    </div>
  </main>
</body>
</html>