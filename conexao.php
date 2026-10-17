<?php
$servidor = "localhost\\SQLEXPRESS";
$banco = "barbearia";

try {
    $conn = new PDO("sqlsrv:Server=$servidor;Database=$banco;TrustServerCertificate=1");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}