<?php
if (php_sapi_name() !== 'cli') {
    die('Execute pelo terminal.');
}
require_once 'conexao.php';

if ($argc < 4) {
    die("Uso: php criar_usuario.php \"Nome\" usuario senha\n");
}

$nome    = $argv[1];
$usuario = $argv[2];
$hash    = password_hash($argv[3], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nome, usuario, senha_hash) VALUES (?, ?, ?)");
$stmt->execute([$nome, $usuario, $hash]);
echo "Usuário criado.\n";