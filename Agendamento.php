  <?php
  session_start();
  require_once 'conexao.php';
  if (!isset($_SESSION['cliente_id'])) {
      header('Location: entrar.php');
      exit;
  }
  ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="style.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
  />
  <title>Agendamento Old Fellas</title>
  <link
    rel="website icon"
    type="png"
    href="assets/Imagem do WhatsApp de 2025-04-21 à(s) 17.16.58_ec01c9a0.png"
  />
  </head>
  <body>
    <main class="pagina-agendamento">
  <div class="form-agendamento">
    <h1>Agende seu horário</h1>
       <p class="subtitulo">Olá, <?= htmlspecialchars($_SESSION['cliente_nome']) ?> ·
     <a href="meus_agendamentos.php">Meus agendamentos</a> ·
     <a href="sair.php">Sair</a></p>

    <form method="POST" action="salvar_agendamento.php">

      <div class="campo">
        <label for="servico">Serviço</label>
        <select name="servico_id" id="servico" required>
          <option value="">Selecione</option>
          <?php
          $servicos = $conn->query(
            "SELECT s.id, s.nome, s.preco, p.nome AS profissional
            FROM servicos s
            JOIN profissionais p ON p.id = s.profissionais_id"
          );
          foreach ($servicos as $s) {
          $preco = $s['preco'] > 0
            ? 'R$ ' . number_format($s['preco'], 2, ',', '.')
            : 'a combinar';
          echo "<option value='" . $s['id'] . "'>"
            . htmlspecialchars($s['nome'] . " - " . $preco . " (com " . $s['profissional'] . ")")
            . "</option>";
          }
          ?>
        </select>
      </div>

      <div class="linha">
        <div class="campo">
          <label for="dia">Data</label>
          <input type="date" name="dia" id="dia" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="campo">
          <label for="horario">Horário</label>
          <input type="time" name="horario" id="horario" min="09:00" max="21:30" step="1800" required>
        </div>
      </div>

      <button type="submit" class="btn-agendar">Agendar</button>
    </form>
  </div>
</main>

  </body>
  </html>