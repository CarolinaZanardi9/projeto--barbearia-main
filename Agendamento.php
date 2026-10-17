<?php require_once 'conexao.php'; ?>
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
    <p class="subtitulo">Preencha os dados abaixo e garanta seu atendimento.</p>

    <form method="POST" action="salvar_agendamento.php">

      <div class="linha">
        <div class="campo">
          <label for="nome">Nome</label>
          <input type="text" name="nome" id="nome" maxlength="50" required>
        </div>
        <div class="campo">
          <label for="sobrenome">Sobrenome</label>
          <input type="text" name="sobrenome" id="sobrenome" maxlength="50" required>
        </div>
      </div>

      <div class="campo">
        <label for="telefone">Telefone</label>
        <input type="tel" name="telefone" id="telefone" maxlength="20" required>
      </div>

      <div class="campo">
        <label for="email">E-mail (opcional)</label>
        <input type="email" name="email" id="email" maxlength="100">
      </div>

      <div class="campo">
        <label for="servico">Serviço</label>
        <select name="servico_id" id="servico" required>
          <option value="">Selecione</option>
          <?php
          $servicos = $conn->query(
          "SELECT s.id, s.nome, p.nome AS profissional
          FROM servicos s
          JOIN profissionais p ON p.id = s.profissionais_id"
          );
            foreach ($servicos as $s) {
            echo "<option value='" . $s['id'] . "'>"
           . htmlspecialchars($s['nome'] . " - com " . $s['profissional'])
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