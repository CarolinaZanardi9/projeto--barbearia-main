```php
<?php
session_start();

// Remove os dados da sessão do cliente
unset($_SESSION['cliente_id'], $_SESSION['cliente_nome']);

// Encerra a sessão atual
session_destroy();

// Redireciona para a página principal da barbearia
header('Location: index.html');
exit;
?>
```