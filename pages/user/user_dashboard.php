<?php
session_start();

// Proteger rota
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header('Location: user_login.php');
    exit;
}

require_once __DIR__ . '/../../includes/config.php';

$usuarioId = $_SESSION['usuario_id'];

// Buscar dados do usuário
$usuario = null;
try {
    $stmt = $conn->prepare('SELECT id, nome, email, telefone FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $usuarioId);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    $usuario = null;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Usuário</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; }
        .container { max-width: 900px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        h2 { margin-bottom: 10px; color: #2c3e50; }
        .info { margin-bottom: 20px; }
        .info p { margin: 5px 0; color: #34495e; }
        .card { padding: 15px; background: #ecf0f1; border-radius: 8px; margin-bottom: 15px; }
        .btn { display: inline-block; padding: 10px 15px; background: #3498db; color: #fff; border-radius: 6px; text-decoration: none; }
        .btn:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></h2>
        <div class="info">
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['usuario_email']) ?></p>
            <?php if ($usuario): ?>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($usuario['telefone'] ?? '') ?></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>Minhas Ações</h3>
            <p>Acompanhe o status do seu cadastro e atualize seus dados.</p>
            <a class="btn" href="user_logout.php">Sair</a>
        </div>
    </div>
</body>
</html>