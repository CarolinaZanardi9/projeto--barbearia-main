<?php
// sair.php
session_start();
unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);
header('Location: index.html');
exit;