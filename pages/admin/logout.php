<?php
session_start();

// Encerrar sessão e redirecionar
$_SESSION = [];
session_destroy();

header('Location: login.php?mensagem=' . urlencode('Você saiu com sucesso.')); 
exit;
?>