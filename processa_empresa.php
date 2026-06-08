<?php
session_start();

// RECEBE OS DADOS ENVIADOS PELO FORMULÁRIO POST
$nomeEmpresa = trim($_POST["nomeEmpresa"] ?? '');
$cnpj        = trim($_POST["cnpj"]        ?? '');
$usuario     = trim($_POST["usuario"]     ?? '');
$email       = trim($_POST["email"]       ?? '');
$senha       = $_POST["senha"]            ?? '';

// VALIDAÇÃO: campos vazios
if (empty($nomeEmpresa) || empty($cnpj) || empty($usuario) || empty($email) || empty($senha)) {
    $erro = "Erro! Por favor, preencha todos os campos!";
}
// VALIDAÇÃO: senha curta
elseif (strlen($senha) < 8) {
    $erro = "A senha precisa ter pelo menos 8 caracteres.";
}
else {
    // Guarda os dados do cadastro na sessão
    $_SESSION['logado']        = true;
    $_SESSION['tipo']          = 'empresa';
    $_SESSION['nome']          = $nomeEmpresa;
    $_SESSION['usuario']       = $usuario;
    $_SESSION['email']         = $email;
    $_SESSION['cnpj']          = $cnpj;
    $_SESSION['cadastro_novo'] = true; // flag para exibir boas-vindas

    // Redireciona para o dashboard com os dados
    header("Location: dashboard_empresa.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>FreelynSJ — Cadastro de Empresa</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div style="text-align:center; padding:60px 20px;">
    <h2>Ops, algo deu errado!</h2>
    <p><?php echo htmlspecialchars($erro); ?></p>
    <a href="index.html">← Voltar e tentar novamente</a>
  </div>
</body>
</html>
