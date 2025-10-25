<?php
session_start();

require_once __DIR__ . '/../../includes/config.php';

// Se já estiver logado, redireciona para admin.php
if (isset($_SESSION['admin_logado']) && $_SESSION['admin_logado'] === true) {
    header('Location: admin.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Ajuste simples: autenticação baseada em tabela administradores
    try {
        $stmt = $conn->prepare('SELECT id, usuario, senha_hash FROM administradores WHERE usuario = ? LIMIT 1');
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($admin = $resultado->fetch_assoc()) {
            if (password_verify($senha, $admin['senha_hash'])) {
                $_SESSION['admin_logado'] = true;
                $_SESSION['admin_usuario'] = $admin['usuario'];
                $_SESSION['admin_id'] = $admin['id'];
                header('Location: admin.php');
                exit;
            } else {
                $erro = 'Usuário ou senha incorretos.';
            }
        } else {
            $erro = 'Usuário não encontrado.';
        }
    } catch (Exception $e) {
        $erro = 'Erro ao autenticar: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - SESAP-RN</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #34495e; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        button { width: 100%; padding: 10px; background: #3498db; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #2980b9; }
        .erro { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Administrativo</h2>
        <?php if (!empty($erro)): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="usuario">Usuário</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>